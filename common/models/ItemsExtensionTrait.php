<?php

namespace common\models;

use common\helpers\General;
use common\helpers\Media;
use common\models\upload\UploadItemsPhotos;
use common\models\upload\UploadItemsPhotosPreview;
use common\models\upload\UploadItemsPreview;
use common\tripium\Tripium;
use DateTime;
use Exception;
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
    public function behaviors()
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

        return isset($ar[$val]) ? $ar[$val] : $val;
    }
	
	public function	updateFromTripium($params = [])
    {
        $shows = self::find()
		->select([
			'id', 
			'id_external', 
			'hash_summ',
		    'min_rate',
		    'min_rate_source'
		])
		->asArray()->indexBy('id_external');
		if ($this->updateOnlyIdExternal !== null) {
		    $shows->andWhere(['id_external' => $this->updateOnlyIdExternal]);
		}
		$shows = $shows->all();
		
    	// Theaters
    	$Theaters = TrTheaters::find()->asArray()->indexBy('id_external')->all();

        if (method_exists($this, 'getRelatedCategories')) {
            $showsCategories = $this->getRelatedCategories()->asArray()->all();
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

		if ($this->statusCodeTripium !== Tripium::STATUS_CODE_SUCCESS) {
			return false;
		}

		$counter = 0;
		foreach ($tripiumData as $show) {

		    $counter++;
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

            if ($this instanceof TrPosPlHotels) {
                $TrPosPlHotels = new TrPosPlHotels();
                $TrPosPlHotels->setPriceLineData($show);
                $minRate = $minRateSource = $TrPosPlHotels->avgNightlyRate();
                $show['status'] = 1;
                $show['theatre'] = [
                    'id' => '10000' . $show['id'],
                    'address' => [
                        'name' => $show['name'],
                        'address1' => $show['address'] ?? null,
                        'address2' => $show['address'] ?? null,
                        'city' => $show['city'] ?? null,
                        'state' => $show['state'] ?? null,
                        'zipCode' => $show['zipCode'] ?? null,
                    ],
                    'directions' => $show['directions'] ?? null,
                    'image' => $show['image'] ?? null,
                    'additionalPhone' => $show['additionalPhone'] ?? null,
                    'status' => 1,
                    'contacts' => [
                        'phone' => $show['phone'] ?? null,
                        'email' => $show['email'] ?? null,
                        'fax' => $show['fax'] ?? null,
                    ]
                ];
            }

            $show['cover'] = !empty($show['cover']) ? $show['cover'] : (isset($photos[0]) ? $photos[0] : '');

            $strPhotos = null;
            if ($this instanceof TrPosPlHotels) {
                $hotel = null;
                if ($this->updatePlHotelDetail) {
                    $tripium = new Tripium();
                    $hotel = $tripium->getPLHotelDetail($show['id']);
                    $show['checkIn'] = $hotel['checkIn'];
                    $show['checkOut'] = $hotel['checkOut'];
                }
                if ($hotel) {
                    $strPhotos = isset($hotel['photos']) ? implode(',', $hotel['photos']) : null;
                } else {
                    $photosCurrent = self::find()
                        ->select(['photos'])
                        ->where(['id_external' => (int)$show['id']])
                        ->asArray()
                        ->one();
                    $strPhotos = $photosCurrent['photos'] ?? null;
                }
            } else {
                $strPhotos = implode(',', $photos);
            }

			$dataShow = [
				'id_external' => (int)$show['id'],
				'name' => $show['name'],
 				'description' => $show['description'],
				'status' => $params['setStatus'] ?? ($show['status'] ? 1 : 0),
				'location_external_id' => (
                    $this instanceof TrShows || $this instanceof TrAttractions || $this instanceof TrPosHotels)
                    && !empty($show['theatre']['locationId']) ? $show['theatre']['locationId'] : $show['location'],
				'rank' => $show['rank'],
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
				'photos' => $strPhotos,
				'amenities' => implode(';', $amenities),
				'theatre_id' => !empty($show['theatre']['id']) ? $show['theatre']['id'] : null,
				'tags' => implode(';', $tags),
				'min_rate' => $minRate,
				'min_rate_source' => $minRateSource,
				'hours' => $show['hours'] ?? null,
			    'call_us_to_book' => !empty($show['callUsToBook']) && ($show['callUsToBook'] === true || $show['callUsToBook'] == 'true') ? self::CALL_US_TO_BOOK_YES : self::CALL_US_TO_BOOK_NO,
			    'external_service' => !empty($show['externalService']) ? $show['externalService'] : null,
                'rating' => $show['hotelRating'] ?? null, // Price Line Hotels
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
			];

            if (!($this instanceof TrPosHotels)) {
                $dataShow['videos'] = implode(';', $videos);
            }

			$dataShow['hash_summ'] = md5(Json::encode($dataShow));

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
                    if ($this instanceof TrPosHotels) {
                        $Shows->setVideo();
                    } else {
                        $Shows->updatePreview($show['cover']);
                    }
				    $Shows->setPhotoAndPreview();
				    
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
                    if ($this instanceof TrPosHotels) {
                        $Shows->updateVideo = $this->updateVideo;
                        $Shows->setVideo();
                    } else {
                        $Shows->updatePreview($show['cover']);
                    }
				    $Shows->setPhotoAndPreview();
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

        if (!($this instanceof TrPosPlHotels)) {
		    self::updateMinPrice();
        }

       //have to find out why status can be 0, this is hot fix
        $query = Yii::$app->db->createCommand('UPDATE '.TrPosPlHotels::tableName().' SET status = 1 WHERE id > 0;');
        $query->execute();
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
    
    public function updateTheaters($item)
    {
        if (empty($item['id'])) {
            return false;
        }
        
        $Theater = TrTheaters::findOne(['id_external' => $item['id']]);

        $model = $Theater ? $Theater : new TrTheaters;
        
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
            /*$model->updateLocation();*/
        } else if ($model->errors && !$Theater) {
			$this->errors_update[] = 'Theater update';
			$this->errors_update[] = $model->errors;
		}
    }
    
	public function setCategories($show)
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
     * @return array
     */
    public static function getActualCategoriesCash()
    {
        $cache = Yii::$app->cache;
        $cacheData = $cache->get(self::TYPE . '.Categories');

        if ($cacheData === false) {
            $cacheData = self::getActualCategories()->orderBy(
                'sort_' . (self::TYPE === TrPosHotels::TYPE ? TrPosPlHotels::TYPE : self::TYPE)
            )->all();
            $cache->set(self::TYPE . '.Categories', $cacheData, 60 * 15);
        }
        return $cacheData;
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
    
    /**
     * Return file time
     * @param string $url
     */
    public function getFileTime($url)
    {
        return Media::getFileTime($url);
    }

    /**
     * Return real url
     * @param string $url
     */
    public function getRealUrl($url)
    {
        return Media::getRealUrl($url);
    }
    
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

    public function setPhotoAndPreview()
    {
        $this->refresh();

        $photoUrls = explode(',', $this->photos);
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
                        'photo_id' => $uploadItemsPhotos->id
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

    public static function getTagsValue($val)
    {
        $ar = self::getTagsList();

        return isset( $ar[$val] ) ? $ar[$val] : $val;
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
            self::tableName().'.name',
            self::tableName().'.description',
            self::tableName().'.location_external_id',
            self::tableName().'.preview_id',
            self::tableName().'.theatre_id',
            self::tableName().'.tags',
            self::tableName().'.rank',
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
            $k = '0'
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

    public static function updateMinPrice()
    {
        $query = Yii::$app->db->createCommand('UPDATE '.self::tableName().' SET min_rate = NULL, min_rate_source = NULL; UPDATE '.self::tableName().' LEFT JOIN ('.self::actualMinPrice()->createCommand()->getRawSql().') as minprice ON '.self::tableName().'.id = minprice.id SET '.self::tableName().'.min_rate = minprice.min_rate, '.self::tableName().'.min_rate_source = minprice.min_rate_source');
        $query->execute();
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

    public function getLocationLat(): float
    {
        return $this->theatre->location_lat ?? $this->location_lat;
    }

    public function getLocationLng(): float
    {
        return $this->theatre->location_lng ?? $this->location_lng;
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
}
