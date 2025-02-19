<?php

namespace common\models;

use common\behaviors\TimestampIfFieldChangeBehavior;
use common\helpers\General;
use common\helpers\Media;
use common\models\upload\UploadItemsPhotos;
use common\models\upload\UploadItemsPhotosPreview;
use common\models\upload\UploadItemsPreview;
use common\models\upload\UploadShowsSeatMap;
use DateTime;
use Exception;
use InvalidArgumentException;
use RuntimeException;
use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

trait ItemsExtensionTrait
{
    /**
     * @var array $updateOnlyIdExternal
     */
    public $updateOnlyIdExternal;

    public $errors_add = [];
    public $errors_update = [];
    public $added = [];
    public $updated = [];

    public $updateForce = false;
    public $updateForceImages = false;
    public $updatePlHotelDetail = false;
    public $updateImages = false;
    public $statusCodeTripium = null;

    public $buynowUrl;
    public $nearest_start;
    public $maxCountItemUpdate = 0;

    public function getBuyNowUrl(): ?string
    {
        return $this->buynowUrl;
    }

    public function setBuyNowUrl($url): ?string
    {
        return $this->buynowUrl = $url;
    }

    /**
     * This method have to be redefined
     *
     * @return ActiveQuery
     * @throws RuntimeException
     */
    public static function getAvailable(): ActiveQuery
    {
        throw new RuntimeException('This method have to be redefined');
    }

    /**
     * This method have to be redefined
     *
     * @return ActiveQuery
     * @throws RuntimeException
     */
    public function getCategories(): ActiveQuery
    {
        throw new RuntimeException('This method have to be redefined');
    }

    /**
     * This method have to be redefined
     *
     * @return ActiveQuery
     * @throws RuntimeException
     */
    public function getTrSimilar(): ActiveQuery
    {
        throw new RuntimeException('This method have to be redefined');
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'code' => [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
                'slugAttribute' => 'code',
                'ensureUnique' => true,
                'immutable' => false,
            ],
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => new Expression('NOW()'),
            ],
            'TimestampIfFieldChangeBehavior' => [
                'class' => TimestampIfFieldChangeBehavior::class,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }
        if (!empty($this->preview)) {
            $this->preview->delete();
        }
        if (!empty($this->image)) {
            $this->image->delete();
        }
        if (!empty($this->seat_map)) {
            $this->seat_map->delete();
        }
        foreach ($this->relatedPhotos as $itemPhoto) {
            if (!empty($itemPhoto->photo)) {
                $itemPhoto->photo->delete();
            }
            if (!empty($itemPhoto->preview)) {
                $itemPhoto->preview->delete();
            }
        }
        return true;
    }

    /**
     * Return Status List
     *
     * @return array
     */
    public static function getStatusList(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
        ];
    }

    /**
     * Return Status Value
     *
     * @param $val
     *
     * @return string
     */
    public static function getStatusValue($val): string
    {
        $ar = self::getStatusList();

        return $ar[$val] ?? $val;
    }

    /**
     * Return Status WL List
     *
     * @return array
     */
    public static function getStatusWlList(): array
    {
        return [
            self::STATUS_WL_ACTIVE => 'Active',
            self::STATUS_WL_INACTIVE => 'Inactive',
        ];
    }

    /**
     * Return Status WL Value
     *
     * @param $val
     *
     * @return string
     */
    public static function getStatusWlValue($val): string
    {
        $ar = self::getStatusWlList();

        return $ar[$val] ?? $val;
    }

    /**
     * Return Cancellation Policy Text
     *
     * @return string
     */
    public function getCancellationPolicyText(): string
    {
        if (!empty($this->cancel_policy_text)) {
            try {
                $ar = Json::decode($this->cancel_policy_text);
                if (!empty($ar) && is_array($ar)) {
                    return implode('. ', $ar) . '.';
                }

                if (!empty($ar)) {
                    return $ar;
                }
            } catch (Exception $e) {
                return $this->cancel_policy_text;
            }
        }

        return '';
    }

    public function	updateFromTripium($params = []): bool
    {
        $shows = self::find()
            ->select([
                         'id',
                         'id_external',
                         'hash_summ',
                         'min_rate',
                         'min_rate_source',
                         'status',
                     ])
            ->asArray()->indexBy('id_external');
        if ($this->updateOnlyIdExternal !== null) {
            $shows->andWhere(['id_external' => $this->updateOnlyIdExternal]);
        }
        $shows = $shows->all();

        // Theaters
        $Theaters = TrTheaters::find()->asArray()->indexBy('id_external')->all();

        if (method_exists($this, 'getAllCategories')) {
            $showsCategories = self::getAllCategories()->asArray()->all();
            $showsCategories = ArrayHelper::map(
                $showsCategories,
                'id_external_category',
                static function ($el) {
                    return $el['id_external_category'];
                },
                'id_external_show'
            );
        }

        $tripiumData = $this->getSourceData($params);

        if ($tripiumData === null) {
            return false;
        }

        $counter = 0;
        foreach ($tripiumData as $show) {
            $counter++;
            $external_id = $show['id'];
            $show['id'] = (int)$show['id'];

            if (!empty($shows[$show['id']]) && !$show['status'] && !$shows[$show['id']]['status']) {
                continue;
            }

            if ($this->maxCountItemUpdate > 0 && $this->maxCountItemUpdate < $counter) {
                continue;
            }
            if (isset($params['onlyIdExternal']) && !in_array($show['id'], $params['onlyIdExternal'], true)) {
                continue;
            }

            if ($this->updateOnlyIdExternal !== null && !in_array($show['id'], $this->updateOnlyIdExternal, false)) {
                continue;
            }

            $photos = [];
            if (!empty($show['photos'])) {
                foreach ($show['photos'] as $url) {
                    $photos[] = trim($url);
                }
            }

            $amenities = [];
            if (!empty($show['amenities'])) {
                foreach ($show['amenities'] as $amenitie) {
                    $amenities[] = trim($amenitie);
                }
            }

            $tags = [];
            if (!empty($show['tags'])) {
                foreach ($show['tags'] as $tag) {
                    $tags[] = trim($tag);
                }
            }

            $videos = [];
            if (!empty($show['videos'])) {
                foreach ($show['videos'] as $video) {
                    $videos[] = trim($video);
                }
            }

            $minRate = isset($shows[$show['id']]) && $shows[$show['id']]['min_rate'] !== null ?
                $shows[$show['id']]['min_rate'] * 1 : null;
            $minRateSource = isset($shows[$show['id']]) && $shows[$show['id']]['min_rate_source'] !== null ?
                $shows[$show['id']]['min_rate_source'] * 1 : null;

            $show['cover'] = !empty($show['cover']) ? $show['cover'] : (isset($photos[0]) ? $photos[0] : '');

            $strPhotos = implode(',', $photos);

            $dataShow = [
                'id_external' => $show['id'],
                'external_id' => $external_id,
                'name' => $show['name'],
                'description' => $show['description'],
                'status' => $params['setStatus'] ?? ($show['status'] ? 1 : 0),
                'location_external_id' => (
                    $this instanceof TrShows || $this instanceof TrAttractions || $this instanceof TrPosHotels)
                && !empty($show['theatre']['locationId']) ? $show['theatre']['locationId'] : $show['location'],
                'rank_level' => $show['rank'],
                'marketing_level' => (int)$show['marketingLevel'],
                'voucher_procedure' => $show['voucherProcedure'],
                'weekly_schedule' => $show['weeklySchedule'] ? 1 : 0,
                'on_special_text' => $show['onSpecialText'],
                'cast_size' => $show['castSize'] ?? null,
                'seats' => $show['seats'] ?? null,
                'show_length' => $show['showLength'] ?? null,
                'intermissions' => !empty($show['intermissions']) ? Json::encode($show['intermissions']) : null,
                'cut_off' => !empty($show['cutOff']) ? $show['cutOff'] : null,
                'tax_rate' => $show['taxRate'] ? (float)$show['taxRate'] : 0,
                'cancel_policy_text' => Json::encode($show['cancelPolicyText']),
                'photos' => (
                    $this instanceof TrShows || $this instanceof TrAttractions || $this instanceof TrPosHotels)
                    ? $strPhotos : null,
                'amenities' => implode(';', $amenities),
                'theatre_id' => !empty($show['theatre']['id']) ? $show['theatre']['id'] : null,
                'tags' => implode(';', $tags),
                'min_rate' => $minRate,
                'min_rate_source' => $minRateSource,
                'hours' => $show['hours'] ?? null,
                'call_us_to_book' => !empty($show['callUsToBook']) && ($show['callUsToBook'] === true || $show['callUsToBook'] == 'true') ? self::CALL_US_TO_BOOK_YES : self::CALL_US_TO_BOOK_NO,
                'external_service' => !empty($show['externalService']) ? $show['externalService'] : null,
                'rating' => $show['starRating'] ?? 0, // Price Line Hotels
                'review_rating' => $show['reviewRating'] ?? null, // Price Line Hotels
                'review_rating_desc' => $show['reviewRatingDesc'] ?? null, // Price Line Hotels
                'address' => $show['address'] ?? null, // Price Line Hotels
                'city' => $show['city'] ?? null, // Price Line Hotels
                'state' => $show['state'] ?? null, // Price Line Hotels
                'zip_code' => !empty($show['zipCode']) ? trim($show['zipCode']) : null, // Price Line Hotels
                'phone' => $show['phone'] ?? null, // Price Line Hotels
                'fax' => $show['fax'] ?? null, // Price Line Hotels
                'min_age' => (int)($show['minAge'] ?? 0), // Price Line Hotels
                'check_in' => !empty($show['checkIn']) ? date("H:i", strtotime($show['checkIn'])) : '', //All Hotels
                'check_out' => !empty($show['checkOut']) ? date("H:i", strtotime($show['checkOut'])) : '', //All Hotels
                'price_line' => (int)(!empty($show['externalService']) && $show['externalService'] === 'Priceline')
            ];

            if (!($this instanceof TrPosHotels)) {
                $dataShow['videos'] = implode(';', $videos);
            }

            $dataShow['hash_summ'] = md5(Json::encode($dataShow));

            $dataShow['photos'] = null; // delete after make the hash sum

            if (empty($Theaters[$show['theatre']['id']])
                || (!empty($Theaters[$show['theatre']['id']])
                    && $Theaters[$show['theatre']['id']]['hash_summ'] !== TrTheaters::makeHash($show['theatre']))) {
                $this->updateTheaters($show['theatre']);
            }

            if (!empty($show['categories']) && (array_diff(@$showsCategories[$show['id']] ? $showsCategories[$show['id']] : [], $show['categories'])
                    || array_diff($show['categories'], @$showsCategories[$show['id']] ? $showsCategories[$show['id']] : []))) {
                $this->setCategories($show);
            }

            if (empty($shows[$show['id']])) {
                $Shows = new self;
                $Shows->setAttributes($dataShow);

                if ($Shows->save()) {
                    if (($Shows instanceof TrPosHotels) && $Shows->price_line) {
                        $Shows->setPhotoAndPreviewPriceLine();
                    } else {
                        $Shows->updatePreview($show['cover']);
                        $Shows->setPhotoAndPreview($photos);
                    }
//                    if ($this instanceof TrShows && isset($show['seatMap'])) {
//                        $Shows->updateSeatMap($show['seatMap']);
//                    }

                    $setPlaceIds[] = $Shows->id;
                    $this->added[] = $Shows->id_external;

                } else {
                    $err = $Shows->getErrors();
                    if ($err) {
                        $this->errors_add[] = $err;
                    }
                }

            } else if($this->updateForce || ($dataShow['hash_summ'] !== $shows[$show['id']]['hash_summ'])) {

                $Shows = self::find()->where(['id_external' => $show['id']])->one();

                $Shows->setAttributes($dataShow);
                $Shows->updateImages = $this->updateImages;

                $dirtyLocs = $Shows->getDirtyAttributes(['address', 'city', 'state', 'zip_code', 'name', 'theatre_name']);

                if ($dirtyLocs) {
                    $Shows->location_lat = null;
                    $Shows->location_lng = null;
                }

                if ($Shows->save() || $this->updateForce) {
                    if (($Shows instanceof TrPosHotels) && $Shows->price_line) {
                        $Shows->setPhotoAndPreviewPriceLine();
                    } else {
                        $Shows->updatePreview($show['cover']);
                        $Shows->setPhotoAndPreview($photos);
                    }
//                    if ($this instanceof TrShows && isset($show['seatMap'])) {
//                        $Shows->updateSeatMap($show['seatMap']);
//                    }
                    if ($dirtyLocs) {
                        $setPlaceIds[] = $Shows->id;
                    }
                    $this->updated[] = $Shows->id_external;
                } else {
                    $err = $Shows->getErrors();
                    if ($err) {
                        $this->errors_update[] = $err;
                    }
                }
            }

            unset($shows[$show['id']]);
        }

        if (!isset($params['onlyIdExternal']) && count($tripiumData) > 10) {
            $models = self::find()->where(
                ['status' => 1, 'id_external' => ArrayHelper::getColumn($shows, 'id_external')]
            )->all();
            foreach ($models as $model) {
                $model->status = 0;
                $model->save();
            }
        }

//        try {
//            TrTheaters::setLocations();
//        } catch (Exception $e) {}

        self::updateMinPrice();

//       //have to find out why status can be 0, this is hot fix
//        $query = Yii::$app->db->createCommand('UPDATE '.TrPosPlHotels::tableName().' SET status = 1 WHERE id > 0;');
//        $query->execute();
        return true;
    }

    public function updateTheaters($item)
    {
        if (empty($item['id'])) {
            return false;
        }

        $Theater = TrTheaters::findOne(['id_external' => $item['id']]);

        $model = $Theater ?: new TrTheaters;

        $model->setAttributes([
                                  'id_external' => $item['id'],
                                  'name' => $item['address']['name'],
                                  'address1' => $item['address']['address1'],
                                  'address2' => $item['address']['address2'],
                                  'city' => $item['address']['city'],
                                  'state' => $item['address']['state'],
                                  'zip_code' => $item['address']['zipCode'],
                                  'directions' => $item['directions'],
                                  'status' => $item['status'] ? TrTheaters::STATUS_ACTIVE : TrTheaters::STATUS_INACTIVE,
                                  'image' => $item['image'],
                                  'contacts_phone' => $item['contacts']['phone'],
                                  'contacts_email' => $item['contacts']['email'],
                                  'contacts_fax' => $item['contacts']['fax'],
                                  'additional_phone' => $item['additionalPhone'],
                                  'hash_summ' => TrTheaters::makeHash($item),
                              ]);
        if ($model->save()) {
//            $model->updateLocation();
        } else if ($model->errors && !$Theater) {
            $this->errors_update[] = 'Theater update '. $model->name;
            $this->errors_update[] = $model->errors;
        }
    }

    public function setCategories($show): void
    {
        $class = $this->getRelatedCategories();
        $TrCategories = TrCategories::find()->asArray()->indexBy('id_external')->all();
        $ShowsCategories = new $class->modelClass;

        $ShowsCategories->deleteAll("id_external_show = ".$show["id"]);
        foreach($show["categories"] as $category_id) {
            if (!empty($TrCategories[$category_id])) {
                $ShowsCategories = new $class->modelClass;
                $ShowsCategories->setAttributes([
                                                    "id_external_show" => $show["id"],
                                                    "id_external_category" => $category_id,
                                                ]);
                $ShowsCategories->save();
            }
        }
    }

    /**
     * @deprecated use Media::getFileTime()
     */
    public function getFileTime($url)
    {
        return Media::getFileTime($url);
    }

    /**
     * @deprecated use Media::getRealUrl()
     */
    public function getRealUrl($url)
    {
        return Media::getRealUrl($url);
    }
//
//    /**
//     * Updating Seat Map
//     * @param string $url
//     */
//    public function updateSeatMap($url)
//    {
//        $this->refresh();
//        $url = Media::getRealUrl($url);
//        $fileTime = Media::getFileTime($url);
//        if ($this->updateForceImages || ($fileTime > 0 && (empty($this->seatMap->id)
//                    || (!empty($this->seatMap->id) && $url != $this->seatMap->source_url && !empty($url))
//                    || (!empty($this->seatMap->id) && $url == $this->seatMap->source_url && $fileTime > $this->seatMap->source_file_time)
//                )) || ($fileTime == -1 && empty($this->seatMap->id) && !empty($url))
//        ) {
//            $UploadShowsSeatMap = new UploadShowsSeatMap;
//            $UploadShowsSeatMap->downloadByUrl($url);
//
//            if ($UploadShowsSeatMap->id) {
//                if ($this->seatMap) {
//                    $this->seatMap->delete();
//                }
//                $this->seat_map_id = $UploadShowsSeatMap->id;
//                $this->save();
//            }
//
//        } else if (!empty($this->seatMap->id) && empty($url)) {
//            if ($this->seatMap) {
//                $this->seatMap->delete();
//            }
//            $this->seat_map_id = null;
//            $this->save();
//        }
//    }

    /**
     * Updating preview
     * @param string $url
     */
    public function updatePreview($url)
    {
        if (isset($this->update_preview) && $this->update_preview === 0) {
            return;
        }
        $this->refresh();
        $url = Media::getRealUrl($url);
        $fileTime = Media::getFileTime($url);

        if ($this->updateForceImages || ($fileTime > 0 && (empty($this->preview->id)
                    || (!empty($this->preview->id) && $url != $this->preview->source_url && !empty($url))
                    || (!empty($this->preview->id) && $url == $this->preview->source_url && $fileTime > $this->preview->source_file_time)
                )) || ($fileTime == -1 && !empty($url) && (
                    empty($this->preview->id) || $url !== $this->preview->source_url
                ))
        ) {
            $uploadItemsPreview = new UploadItemsPreview;
            $uploadItemsPreview->downloadByUrl($url);
            if ($uploadItemsPreview->id) {
                if ($this->preview) {
                    $this->preview->delete();
                }
                $this->preview_id = $uploadItemsPreview->id;
                $this->save();
            }

        } else if (!empty($this->preview->id) && empty($url)) {
            if ($this->preview) {
                $this->preview->delete();
            }
            $this->preview_id = null;
            $this->save();
        }
    }

    public function setPhotoAndPreview($photoUrls = [])
    {
        $this->refresh();

        foreach ($photoUrls as &$url) {
            $url = Media::getRealUrl($url);
        }
        unset($url);
        $photoUrls = array_filter(array_map('trim', $photoUrls));

        $newImages = array_values($photoUrls);

        $uploadedImages = $this->getRelatedPhotos()->with(['photo'])->all();

        foreach ($uploadedImages as $uploadedImage) {
            if (!in_array($uploadedImage->photo->source_url, $newImages)) {
                $uploadedImage->delete();
            } else {
                $fileTime = Media::getFileTime($uploadedImage->photo->source_url);
                if ($this->updateForceImages
                    || $fileTime > $uploadedImage->photo->source_file_time
                    || $fileTime == -1) {
                    $uploadedImage->delete();
                } else {
                    $k = array_search($uploadedImage->photo->source_url, $newImages, true);
                    unset($newImages[$k]);
                }
            }
        }

        foreach ($newImages as $imageUrl) {
            $classPhotoJoin = $this->getRelatedPhotos();
            $classPhotoJoin = $classPhotoJoin->modelClass;

            $uploadItemsPhotos = new UploadItemsPhotos();
            $uploadItemsPhotos->downloadByUrl($imageUrl);

            $uploadItemsPhotosPreview = new UploadItemsPhotosPreview();
            $uploadItemsPhotosPreview->downloadByUrl($imageUrl);

            if ($uploadItemsPhotos->id && $uploadItemsPhotosPreview->id) {
                $modelPhotoJoin = new $classPhotoJoin();
                $modelPhotoJoin->setAttributes(
                    [
                        'item_id' => $this->id,
                        'preview_id' => $uploadItemsPhotosPreview->id,
                        'photo_id' => $uploadItemsPhotos->id,
                        'hash' => 'x',
                    ]
                );
                $modelPhotoJoin->save();
            } else {
                $file = ContentFiles::find()->where(['id' => $uploadItemsPhotos->id])->one();
                if ($file) {
                    $file->delete();
                }
                $file = ContentFiles::find()->where(['id' => $uploadItemsPhotosPreview->id])->one();
                if ($file) {
                    $file->delete();
                }
            }
        }
    }

//    public function getLocations()
//    {
//        $cache = Yii::$app->cache;
//        $cacheData = $cache->get($this::TYPE. '.locations');
//
//        if ($cacheData === false)
//        {
//            $query = Locations::find()
//                ->innerJoin(
//                    $this::tableName(),
//                    Locations::tableName() . '.id_external = ' . $this::tableName() . '.location'
//                );
//            $cacheData = $query->asArray()->all();
//            $cache->set($this::TYPE. '.locations', $cacheData, 60*15);
//        }
//        return $cacheData;
//    }

    public static function prepareSearchResult($data, $query)
    {
        $len = 50;
        if($data)
        {
            foreach($data as &$show)
            {
                $ar = explode($query, strip_tags(htmlspecialchars_decode($show['description'])));
                if($ar) {
                    foreach ($ar as $k => &$it) {
                        if ($k == 0 && strlen($it) > $len) {
                            $it = '...' . substr($it, -$len);
                        } else {
                            if ($k == count($ar) - 1 && strlen($it) > $len) {
                                $it = substr($it, 0, $len) . '...';
                            } else {
                                if (strlen($it) > $len * 2) {
                                    $it = substr($it, 0, $len) . ' ... ' . substr($it, -$len);
                                }
                            }
                        }
                    }
                }
                $show['description'] = implode($query, $ar);
            }
        }
        return $data;
    }

    public function getDescriptionShort($len = 170)
    {
        $description = $this->description;

        $ar = explode(' ', strip_tags(htmlspecialchars_decode($this->description)));
        if ($ar) {
            $description = '';
            foreach ($ar as $k => $v) {
                if (strlen($description) < $len) {
                    $description .= ' ' . $v;
                } else {
                    $description .= '...';
                    break;
                }
            }
        }

        return $description;
    }

    public static function prepareDescription($data, $len = 170)
    {
        if ($data) {
            foreach ($data as &$show) {
                $ar = explode(' ', strip_tags(htmlspecialchars_decode($show['description'])));
                if ($ar) {
                    $show['description'] = '';
                    foreach ($ar as $k => $v) {
                        if (strlen($show['description']) < $len) {
                            $show['description'] .= ' ' . $v;
                        } else {
                            $show['description'] .= '...';
                            break;
                        }
                    }
                }
            }
        }
        return $data;
    }

    public function formatPhoneNumber($phone)
    {
        return General::formatPhoneNumber($phone);
    }

    public function getSearchAddress(): string
    {
        $result = [];
        if (!empty($this->theatre)) {
            if (!empty($this->theatre->address1)) {
                $result[] = $this->theatre->address1;
            }
            if (!empty($this->theatre->city)) {
                $result[] = $this->theatre->city;
            }
            if (!empty($this->theatre->state)) {
                $result[] = $this->theatre->state;
            }
            if (!empty($this->theatre->zip_code)) {
                $result[] = $this->theatre->zip_code;
            }
        } else {
            if (!empty($this->address1)) {
                $result[] = $this->address1;
            }
            if (!empty($this->city)) {
                $result[] = $this->city;
            }
            if (!empty($this->state)) {
                $result[] = $this->state;
            }
            if (!empty($this->zip_code)) {
                $result[] = $this->zip_code;
            }
        }

        return implode(', ', $result);
    }

    /**
     * @deprecated
     */
    public static function placeNearBySearch($params)
    {
        return General::placeNearBySearch($params);
    }

    /**
     * @param \common\models\form\Search|\common\models\form\SearchPosHotel|null $Search
     * @param null                         $controller
     *
     * @return array
     */
    public static function getMapData($Search = null, $controller = null)
    {
        if ($Search instanceof \common\models\form\SearchPosHotel) {
            $query = TrPosHotels::getByFilter($Search);
            $query->with(['preview', 'theatre']);
            $items = $query->all();
        } else {
            $query = self::getByFilter($Search)
                ->select(
                    [
                        self::tableName() . '.id_external',
                        self::tableName() . '.id',
                        self::tableName() . '.name',
                        'theatre_id',
                        'preview_id',
                        'code',
                        'tags',
                        $Search ? self::getAliasMinPrice() . '.min_rate' : 'min_rate',
                        $Search ? self::getAliasMinPrice() . '.min_rate_source' : 'min_rate_source',
                    ]
                )
                ->joinWith(
                    [
                        'theatre' => static function (ActiveQuery $query) {
                            $query->select(['id_external', 'name', 'address1', 'city', 'location_lat', 'location_lng']);
                        },
                        'preview'
                    ]
                )
                ->andWhere(['not', [self::tableName() . '.min_rate' => null]])
                ->andWhere(['<>', TrTheaters::tableName() . '.location_lat', 0])
                ->andWhere(['<>', TrTheaters::tableName() . '.location_lng', 0]);
            $query->orderBy(false);
            $items = $query->all();
        }

        return self::prepareMapData($items, $controller);

    }

    /**
     * @param array $items
     * @param       $controller
     *
     * @return array
     */
    public static function prepareMapData($items, $controller = null): array
    {
        $chack = [];
        $result = [];
        foreach ($items as $key => $it) {
            if (!empty($it->theatre)) {
                $theatre = [
                    'location_lat' => $it->theatre->location_lat,
                    'location_lng' => $it->theatre->location_lng,
                    'name' => $it->theatre->name,
                    'address1' => $it->theatre->address1,
                    'city' => $it->theatre->city,
                    'id_external' => $it->theatre->id_external,
                ];
            } else {
                $theatre = [
                    'location_lat' => $it->location_lat ?? null,
                    'location_lng' => $it->location_lng ?? null,
                ];
            }
            if (!$theatre['location_lat']) {
                continue;
            }
            $hash = md5($theatre['location_lat']+$theatre['location_lng']);
            if (isset($chack[$hash]) && $chack[$hash] > -1) {
                ++$chack[$hash];
                $theatre['location_lat'] = $theatre['location_lat']*1 + $chack[$hash]/100000;
                $theatre['location_lng'] = $theatre['location_lng']*1 + $chack[$hash]/100000;

            } else {
                $chack[$hash] = 0;
            }
            $result[] = [
                'id' => $it->id,
                'id_external' => $it->id_external,
                'name' => $it->name,
                'code' => $it->code,
                'min_rate' => $it->min_rate ?? null,
                'min_rate_source' => $it->min_rate_source ?? null,
                'theatre' => $theatre,
                'img' => $it->preview_id ? $it->preview->url : null,
                'url' => $it->getUrl(),
                'html' => $controller !== null ? $controller->renderPartial(
                    '@app/views/components/google-info-window',
                    [
                        'model' => $it
                    ]
                ) : '',
            ];

        }
        return $result;
    }

    /**
     * @return array
     */
    public static function getActualTrLocationsCash(): array
    {
        $cache = Yii::$app->cache;
        $cacheData = $cache->get(self::TYPE . '.TrLocations');

        if ($cacheData === false) {
            $cacheData = self::getActualTrLocations()->all();
            $cache->set(self::TYPE . '.TrLocations', $cacheData, 60 * 15);
        }
        return $cacheData;
    }

    /**
     * @return array
     */
    public static function getActualCategoriesCash(): array
    {
        $cache = Yii::$app->cache;
        $cacheData = $cache->get(self::TYPE . '.Categories');

        if ($cacheData === false) {
            $cacheData = self::getActualCategories()->orderBy(
//                'sort_' . (self::TYPE === TrPosHotels::TYPE ? TrPosPlHotels::TYPE : self::TYPE)
                'name'
            )->all();
            $cache->set(self::TYPE . '.Categories', $cacheData, 60 * 15);
        }
        return $cacheData;
    }

    public function getTypeName()
    {
        return self::NAME;
    }

    public function getIsFeatured()
    {
        return strpos($this->tags, 'Featured') !== false;
    }

    public function getIsOnSale()
    {
        $range = General::getDatePeriod();
        $isOnSale = false;

        if (isset($this->availablePrices)) {
            foreach ($this->availablePrices as $price) {
                $dtStart = new DateTime($price->start);
                $dtEnd = new DateTime($price->end);
                if ($dtStart >= $range->start && $dtStart <= new DateTime($range->end->format('Y-m-d 23:59:59')) && !empty($price->special_rate)) {
                    $isOnSale = $price->retail_rate != $price->special_rate ? true : $isOnSale;
                }
            }
        }

        return $isOnSale;
    }

    public static function updateMinPrice()
    {
        $query = Yii::$app->db->createCommand('UPDATE '.self::tableName().' SET min_rate = NULL, min_rate_source = NULL; UPDATE '.self::tableName().' LEFT JOIN ('.self::actualMinPrice()->createCommand()->getRawSql().') as minprice ON '.self::tableName().'.id = minprice.id SET '.self::tableName().'.min_rate = minprice.min_rate, '.self::tableName().'.min_rate_source = minprice.min_rate_source');
        $query->execute();
    }

    public static function getTagsValue($val)
    {
        $ar = self::getTagsList();

        return $ar[$val] ?? $val;
    }

    public static function getAliasMinPrice()
    {
        return 'min_price';
    }


    /**
     * Build query by $Search
     *
     * @param null|\common\models\form\Search $Search
     * @return ActiveQuery
     */
    public static function getByFilterAll($Search = null)
    {
        $select = [
            self::tableName().'.id',
            self::tableName().'.id_external',
            self::tableName().'.code',
//            self::tableName().'.order',
            self::tableName().'.name',
            self::tableName().'.description',
            self::tableName().'.location_external_id',
//            self::tableName().'.location_item_id',
            self::tableName().'.preview_id',
            self::tableName().'.theatre_id',
            self::tableName().'.tags',
            self::tableName().'.rank_level',
//            'location_item.location_sort',
            'IF('.self::getAliasMinPrice().'.min_rate > 0, '.self::getAliasMinPrice().'.min_rate, '.self::tableName().'.min_rate) as min_rate',
            'IF('.self::getAliasMinPrice().'.min_rate_source > 0, '.self::getAliasMinPrice().'.min_rate_source, '.self::tableName().'.min_rate_source) as min_rate_source'
        ];

        $query = self::getByFilter($Search);
        $select['status'] = '(1)';
        $query->select($select);

        $select['status'] = '(0)';
        $SearchClone = clone $Search;
        $SearchClone->without_availability = 1;
        $queryClone = self::getByFilter($SearchClone);
        $queryClone->select($select);

        $query->union($queryClone);

        $query = self::find()->select('*')->from(['r' => $query])->groupBy('code');

        $query ->orderby($Search->getOrderby());

        return $query;
    }

    /**
     * Resort items within tag
     *
     * @param array $items
     *
     * @return array
     */
    public static function reSort($items = [])
    {
        $groupList = [];
        $list = [];
        foreach ($items as $item) {
            $featured = in_array(self::TAG_ORIGINAL_FEATURED, explode(';', $item->tags), true);
            $premium = in_array(self::TAG_ORIGINAL_PREMIUM, explode(';', $item->tags), true);
            $k = (!empty($item->locationItem) ? $item->locationItem->location_sort : '0')
                . '_' . $item->status
                . '_' . (! $premium ? '' : self::TAG_ORIGINAL_PREMIUM)
                . '_' . (! $featured ? '' : self::TAG_ORIGINAL_FEATURED);
            $groupList[$k][] = $item;
        }
        foreach ($groupList as $key => $group) {
            if (substr($key, -2) !== '__') {
                shuffle($group);
            }
            $ar = array_merge($list, $group);
            $list = $ar;
        }
        return $list;
    }

    /**
     * @param bool $similarCategory
     *
     * @return ActiveQuery
     */
    public function getSimilar($similarCategory = true): ActiveQuery
    {
        $query = self::getAvailable()
            ->joinWith('categories')
            ->andOnCondition(self::tableName() . '.id_external != ' . $this->id_external)
            ->andOnCondition(
                [
                    'or',
                    [self::tableName() . '.marketing_level' => [1, 2, 3]],
                    [
                        'and',
                        [self::tableName() . '.marketing_level' => [8, 9]],
                        ['like', 'tags', self::TAG_ORIGINAL_FEATURED]
                    ]
                ]
            )
            ->orderBy(new Expression('rand()'));
        if ($similarCategory) {
            $query->andOnCondition(
                ['id_external_category' => $this->getCategories()->select('id_external')->column()]
            );
        }
        return $query;
    }

    /**
     * @return ActiveQuery
     */
    public function getAvailableSimilar(): ActiveQuery
    {
        return self::getAvailable()
            ->andOnCondition(
                [
                    self::tableName() . '.id_external' => $this->getTrSimilar()->select(
                        ['similar_external_id']
                    )->column()
                ]
            );
    }

    /**
     * @return array|string
     */
    public function getCancelPolicyText()
    {
        try {
            return Json::decode($this->cancel_policy_text);
        } catch (InvalidArgumentException $e) {
            return $this->cancel_policy_text;
        }
    }

    /**
     * @return string|null
     */
    public function getDirection(): ?string
    {
        if (!empty($this->theatre->directions)) {
            return $this->theatre->directions;
        }

        if(!empty($this->directions)) {
            return  $this->directions;
        }

        return null;
    }

    /**
     * Gets query for [[TrTheaters]].
     *
     * @return ActiveQuery
     */
    public function getTheatre(): ActiveQuery
    {
        return $this->hasOne(TrTheaters::class, ['id_external' => 'theatre_id']);
    }

    /**
     * @return string|null
     */
    public function getCheckIn(): ?string
    {
        return self::getCheckTime($this->check_in);
    }

    /**
     * @return string|null
     */
    public function getCheckOut(): ?string
    {
        return self::getCheckTime($this->check_out);
    }

    /**
     * @param $time
     *
     * @return string|null
     */
    private static function getCheckTime($time): ?string
    {
        if (!empty($time)) {
            $str = date('h:i&\nb\sp;A', strtotime($time));
            return $str === false ? null : $str;
        }
        return null;
    }

    protected function groupCalendarEvents(array $schedule, $booking = false): array
    {
        $maxPositionOnDay = 3;

        $counter = [];
        $events = [];
        $counterCurrent = [];
        foreach ($schedule as $s) {
            try {
                $startDate = new DateTime($s["start"]);
                $counter[$startDate->format("Y-m-d")] = !empty($counter[$startDate->format("Y-m-d")]) ? $counter[$startDate->format("Y-m-d")]+1 : 1;
            } catch (Exception $e) {}
        }

        $start_date = [];
        foreach ($schedule as $s) {
            try {
                $startDate = new DateTime($s["start"]);
                $counterCurrent[$startDate->format("Y-m-d")] = !empty($counterCurrent[$startDate->format("Y-m-d")]) ? $counterCurrent[$startDate->format("Y-m-d")]+1 : 1;

                $event = [
                    'start' => $startDate->format("Y-m-d"),
                    'url' => $this->getUrlTicket(
                        [
                            'date' => $s["start"],
                            'allotmentId' => $s['id_external'] ?? null,
                            'tickets-on-date' => $s["start"],
                            '#' => 'availability',
                        ]
                    ),
                    'className' => empty($start_date[$s["start"]]) && $s["special_rate"] ? 'has-sale' : '',
                ];
                if ($counterCurrent[$startDate->format("Y-m-d")] < $maxPositionOnDay
                    || (
                        $counterCurrent[$startDate->format("Y-m-d")] === $maxPositionOnDay
                        && $counter[$startDate->format("Y-m-d")] === $maxPositionOnDay
                    )) {
                    $events[] = array_merge(
                        [
                            'title' => isset($s['any_time']) && (int)$s['any_time'] === 1
                                ? 'Any Time' : $startDate->format("h:i A")
                        ],
                        $event
                    );
                } else if ($counter[$startDate->format("Y-m-d")] > $maxPositionOnDay && $counterCurrent[$startDate->format("Y-m-d")] === $maxPositionOnDay) {
                    $event['url'] = $this->getURL(
                        [
                            'on-date' => $startDate->format('Y-m-d'),
                            '#'       => 'availability',
                        ]
                    );
                    $events[] = array_merge(
                        [
                            'title' => 'more'
                        ],
                        $event
                    );
                }
                $start_date[$s["start"]]=1;
            } catch (Exception $e) {}
        }

        return $events;
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function getSelectedPeriodLowPrices(): ActiveQuery
    {
        /**
         * @var TrShows|TrAttractions $this
         */
        $range = General::getDatePeriod();

        $q = $this->getAvailablePrices()->andWhere(
            [
                'and',
                ['>=', 'start', $range->start->format('Y-m-d 00:00:00')],
                ['<=', 'start', $range->end->format('Y-m-d 23:59:59')],
            ]
        )->orderBy('price asc');
        if ($this instanceof TrShows) {
            $q->andWhere(['name' => 'ADULT']);
        }
        return $q;
    }

    public function getLocationLat(): float
    {
        return $this->theatre->location_lat ?? $this->location_lat;
    }

    public function getLocationLng(): float
    {
        return $this->theatre->location_lng ?? $this->location_lng;
    }
}
