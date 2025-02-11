<?php

namespace common\models;

use common\models\form\SearchPosHotel;
use common\models\upload\UploadItemsPhotosHotel;
use common\models\upload\UploadItemsPhotosPreviewHotel;
use common\tripium\Tripium;
use DateInterval;
use DateTime;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

class TrPosHotels extends _source_TrPosHotels
{
    use ItemsExtensionTrait;

    public const CALL_US_TO_BOOK_YES = 1;
    public const CALL_US_TO_BOOK_NO = 0;

    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;

    public const STATUS_WL_ACTIVE = 1;
    public const STATUS_WL_INACTIVE = 0;

    public const DISPLAY_IN_WHERE_TO_STAY_YES = 1;
    public const DISPLAY_IN_WHERE_TO_STAY_NO = 0;

    public const TYPE = 'hotel';
    public const NAME = 'Lodging';
    public const NAME_PLURAL = 'Lodging';

    public const EXTERNAL_SERVICE_SDC = 'SDC';

    public const TYPE_ID = 5;

    public const priceClass = TrPosHotelsPriceRoom::class;

    private const ASSOCIATE_WITH_PRICE_LINE_HOTELS = [
        'Comfort Inn & Suites Branson Meadows' => 'Comfort Inn & Suites Branson Meadows',
        'Quality Inn West' => 'Quality Inn West',
        'Holiday Inn Express Branson-Green Mountain Drive' => 'Holiday Inn Express Branson- Green Mountain Drive',
        'Best Western Music Capital Inn' => 'Best Western Music Capital Inn',
        'Comfort Inn At Thousand Hills' => 'Comfort Inn At Thousand Hills',
        'Best Western Center Pointe Inn' => 'Best Western Center Pointe Inn',
    ];

    public static function getPricesByFilterFromTripium(SearchPosHotel $Search): array
    {
        $cache = Yii::$app->cache;
        $cacheKey = serialize(
            [
                'hotels',
                'start' => $Search->getDepartureDate()->format('Y-m-d'),
                'end' => $Search->getArrivalDate()->format('Y-m-d'),
                'adults' => $Search->getMaxAdults(),
                'childAge' => $Search->getMaxChildAge(),
            ]
        );
        $cacheData = $cache->get($cacheKey);

        if ($cacheData === false) {
            $query = self::getActive();
            $query->select([self::tableName() . '.external_id']);
            if (empty($query->column())) {
                return [];
            }
            $tripium = new Tripium();
            $res = $tripium->getPosHotelsPrice(
                $query->column(),
                $Search->getArrivalDate(),
                $Search->getDepartureDate(),
                $Search->getMaxAdults(),
                $Search->getMaxChildAge()
            );
            if ($tripium->statusCode !== Tripium::STATUS_CODE_SUCCESS) {
                return [];
            }
            $cacheData = $res ? ArrayHelper::map($res, 'vendorId', 'nightlyRate') : [];
            $cache->set($cacheKey, $cacheData, 60*60);
        }
        return $cacheData;
    }

    public static function setPrices(array &$items, array $prices): void
    {
        foreach ($items as $item) {
            $item->setAttributes(
                [
                    'min_rate'        => $prices[$item->external_id],
                    'min_rate_source' => $prices[$item->external_id],
                ]
            );
        }
    }

    public static function sortByPrice(array &$items, SearchPosHotel $Search): void
    {
        usort($items, static function ($a, $b) use ($Search)
        {
            if ($a->min_rate === $b->min_rate) {
                return 0;
            }
            $direction = $Search->fieldSort === $Search::FIELD_SORT_PRICE_REVERSE ? -1 : 1;
            return ($a->min_rate < $b->min_rate) ? -1 * $direction : 1 * $direction;
        });
    }

    public static function filterByPrice(array $items, SearchPosHotel $Search): array
    {
        if ($Search->priceFrom === null || $Search->priceTo === null) {
            return $items;
        }
        $ar = [];
        foreach ($items as $item) {
            if ($Search->priceFrom <= $item->min_rate && $item->min_rate <= $Search->priceTo) {
                $ar[] = $item;
            }
        }
        return $ar;
    }

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                [
                    ['theatre_id'],
                    'exist',
                    'skipOnError' => true,
                    'targetClass' => TrTheaters::class,
                    'targetAttribute' => ['theatre_id' => 'id_external']
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return array_merge(parent::attributeLabels(), ['show_in_footer' => 'Display In Footer']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRelatedCategories(): ActiveQuery
    {
        return $this->getTrPosHotelsCategories();
    }

    /**
     * @return ActiveQuery
     */
    public static function getAllCategories(): ActiveQuery
    {
        return TrPosHotelsCategories::find()
            ->joinWith('externalShow', false, 'INNER JOIN');
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getCategories(): ActiveQuery
    {
        return $this->hasMany(Categories::class, ['id_external' => 'id_external_category'])
            ->viaTable(TrPosHotelsCategories::tableName(), ['id_external_show' => 'id_external']);
    }

    /**
     * @return ActiveQuery
     */
    public static function getActualCategories(): ActiveQuery
    {
        return TrCategories::find()
            ->joinWith(
                [
                    'trPosHotelsCategories' => static function (ActiveQuery $query) {
                        $query->joinWith('externalShow', false, 'INNER JOIN');
                    }
                ],
                false,
                'INNER JOIN'
            )
            ->andOnCondition(self::getConditionActive());
    }

    /**
     * @return ActiveQuery
     */
    public function getRelatedPhotos(): ActiveQuery
    {
        return $this->getTrPosHotelsPhotoJoins();
    }

    /**
     * @return ActiveQuery
     * @deprecated Use getRelatedPhotos()
     */
    public function getItemsPhoto(): ActiveQuery
    {
        return $this->getTrPosHotelsPhotoJoins();
    }

    /**
     * @deprecated Use online data
     * @return ActiveQuery
     */
    public function getRoomTypes(): ActiveQuery
    {
        return $this->hasMany(TrPosRoomTypes::class, ['id_external_item' => 'id_external']);
    }

    /**
     * @deprecated Use online data
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getMain(): ActiveQuery
    {
        return $this->hasOne(__CLASS__, ['id_external' => 'id_external_item'])
            ->viaTable(TrPosRoomTypes::tableName(), ['id_external' => 'id_external']);
    }

    /**
     * @return array
     */
    public function getSourceData(): ?array
    {
        return (new Tripium())->getPosHotels($this->updateOnlyIdExternal);
    }

    /**
     * @param $data
     *
     * @return string
     */
    private static function contentItemHash($data): string
    {
        if (empty($data['gallery']['photos'])) {
            $data['gallery']['photos'] = [];
        }
        if (empty($data['gallery']['videos'])) {
            $data['gallery']['videos'] = [];
        }
        if (empty($data['gallery']['cover'])) {
            $data['gallery']['cover'] = '';
        }
        return md5(
            serialize(
                array_merge(
                    $data['gallery']['photos'],
                    $data['gallery']['videos'],
                    [$data['gallery']['cover']]
                )
            )
        );
    }

    /**
     * @param $result
     *
     * @return bool
     */
    private function needUpdateContent($result): bool
    {
        return $this->hash_image_content !== self::contentItemHash($result);
    }

    /**
     * @param $result
     *
     * @return bool
     */
    public function setVideo($result): bool
    {
        if (empty($result['gallery']['videos'])) {
            $result['gallery']['videos'] = [];
        }

        $this->setAttribute('videos', implode(';', $result['gallery']['videos']));

        return true;
    }

    /**
     * @return bool
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function setPhotoAndPreview(): bool
    {
        if (!$this->price_line) {
            return false;
        }

        if (!$this->updateImages) {
            return false;
        }

        $tripium = new Tripium();
        $result = $tripium->getHotelContent(
            true,
            $this->external_id,
            (new DateTime())->add(new DateInterval('P10D')),
            (new DateTime())->add(new DateInterval('P11D')),
            [
                ['adult' => 1]
            ]
        );

        if ($tripium->statusCode !== Tripium::STATUS_CODE_SUCCESS) {
            return false;
        }

        $this->setAttributes(
            [
                'min_rate'        => $result['nightlyRate'] ?? null,
                'min_rate_source' => $result['nightlyRate'] ?? null,
                'check_in'        => $result['checkInPolicy']['checkIn'] ?? null,
                'check_out'       => $result['checkInPolicy']['checkOut'] ?? null,
            ]
        );

        if (!$this->needUpdateContent($result)) {
            $this->save();
            return false;
        }

        $this->updatePreview($result['gallery']['cover']);

        $this->setVideo($result);

        /**
         * @var TrPosHotelsPhotoJoin[] $uploadedImages
         */
        $uploadedImages = $this->getRelatedPhotos()->with(['photo'])->all();

        foreach ($uploadedImages as $uploadedImage) {
            $uploadedImage->delete();
        }

        if (empty($result['gallery']['photos'])) {
            $result['gallery']['photos'] = [];
        }

        foreach ($result['gallery']['photos'] as $imageUrl) {

            $uploadItemsPhotos = new UploadItemsPhotosHotel();
            $uploadItemsPhotos->downloadByUrl($imageUrl);

            $uploadItemsPhotosPreview = new UploadItemsPhotosPreviewHotel();
            $uploadItemsPhotosPreview->downloadByUrl($imageUrl);

            if ($uploadItemsPhotos->id && $uploadItemsPhotosPreview->id) {
                $modelPhotoJoin = new TrPosHotelsPhotoJoin();
                $modelPhotoJoin->setAttributes(
                    [
                        'item_id'    => $this->id,
                        'preview_id' => $uploadItemsPhotosPreview->id,
                        'photo_id'   => $uploadItemsPhotos->id,
                        'hash'       => md5($imageUrl),
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

        $this->hash_image_content = self::contentItemHash($result);
        $this->save();

        return true;
    }

    /**
     * @deprecated Use online data
     * @return ActiveQuery
     */
    public static function actualMinPrice(): ActiveQuery
    {
        return TrPosHotelsPriceRoom::find()
            ->joinWith('main')
            ->select(
                [
                    self::tableName() . '.id',
                    self::tableName() . '.id_external',
                    'min_rate' => 'MIN(IF( special_rate <> 0, special_rate, retail_rate ))',
                    'min_rate_source' => 'MIN(retail_rate)',
                ]
            )
            ->andWhere('retail_rate > 0')
            ->groupby(self::tableName() . '.id_external');
    }

    /**
     * @return array
     */
    public static function getConditionActive(): array
    {
        return [self::tableName() . '.status' => 1];
    }

    /**
     * @return ActiveQuery
     */
    public static function getActive(): ActiveQuery
    {
        return self::find()->andOnCondition(self::getConditionActive());
    }

    /**
     * @deprecated should get online
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getPrices(): ActiveQuery
    {
        return $this->hasMany(TrPosHotelsPriceRoom::class, ['id_external' => 'id_external'])
            ->viaTable(TrPosRoomTypes::tableName(), ['id_external_item' => 'id_external']);
    }

    /**
     * @deprecated should get online
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getActivePrices(): ActiveQuery
    {
        return $this->getPrices()
            ->andOnCondition(TrPosHotelsPriceRoom::tableName() . '.start >= CURDATE( )');
    }

    /**
     * @deprecated should get online
     * @return ActiveQuery
     */
    public static function getAvailable(): ActiveQuery
    {
        return self::getActive()
            ->distinct()
            ->joinWith('activePrices', false, 'INNER JOIN');
    }

    /**
     * Return item url
     *
     * @param array  $options
     * @param string $urlManager
     *
     * @return string
     */
    public function getUrl($options = null, $urlManager = 'urlManager'): string
    {
        if (is_array($options)) {
            return Yii::$app->{$urlManager}->createUrl(
                array_merge(['lodging/detail'], $options, ['code' => $this->code])
            );
        }
        return Yii::$app->{$urlManager}->createUrl(['lodging/detail', 'code' => $this->code]);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getSimilar(): ActiveQuery
    {
        return self::getActive()
            ->joinWith('categories', false)
            ->andOnCondition(
                ['id_external_category' => ArrayHelper::getColumn($this->getCategories()->all(), 'id_external')]
            )
            ->andOnCondition(self::tableName() . '.id_external != ' . $this->id_external)
            ->orderBy(new Expression('rand()'));
    }

    /**
     * @return string
     */
    public function address(): string
    {
        return trim(
            $this->theatre->address1 . ' ' . $this->theatre->state . ' ' . $this->theatre->zip_code
        );
    }

    /**
     * Build query by $Search
     *
     * @param SearchPosHotel $Search
     *
     * @return ActiveQuery
     */
    public static function getByFilter($Search = null): ActiveQuery
    {
        $query = self::getActive();

        if (!empty($Search->starRating)) {
            $query->andWhere(['rating' => $Search->starRating]);
        }
        if (!empty($Search->city) && !empty($Search->city[0])) {
            $query->andWhere(['city' => $Search->city]);
        }
        if (!empty($Search->amenities) && !empty($Search->amenities[0])) {
            $query->andWhere(['like', 'amenities', $Search->amenities]);
        }

        if ($Search->title) {
            $query->joinWith('theatre')->andFilterWhere(
                [
                    'or',
                    ['like', self::tableName() . '.name', $Search->title],
                    ['like', TrTheaters::tableName() . '.name', $Search->title]
                ]
            );
        }

        if (!empty($Search->externalIds)) {
            $query->andWhere([self::tableName() . '.id_external' => $Search->externalIds]);
        }

        if (isset($Search->statusWl)) {
            $query->andWhere([self::tableName() . '.status_wl' => $Search->statusWl]);
        }

//        if (isset($Search->priceFrom, $Search->priceTo)) {
//            $query->andFilterWhere(['>=', self::tableName() . '.min_rate', $Search->priceFrom]);
//            $query->andFilterWhere(['<=', self::tableName() . '.min_rate', $Search->priceTo]);
//        }

//        $query->orderBy(
//            self::tableName() . '.min_rate ' .
//            ($Search->fieldSort === $Search::FIELD_SORT_PRICE_REVERSE ? 'DESC' : 'ASC')
//        );
        return $query;
    }
}
