<?php

namespace common\models;

use common\models\upload\UploadItemsPhotosHotel;
use common\models\upload\UploadItemsPhotosPreviewHotel;
use common\tripium\Tripium;
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

    /**
     * @var bool
     */
    public $updateVideo = false;

    /**
     * !!!!! Should move to utility!!!!!
     *
     * @param TrPosHotels[]|TrPosPlHotels[] $items
     */
    public static function clearDuplicate(array &$items): void
    {
        $pos = array_keys(self::ASSOCIATE_WITH_PRICE_LINE_HOTELS);
        $deletePlItems = [];
        foreach ($items as $item) {
            if ($item instanceof self && in_array($item->name, $pos, false)) {
                $deletePlItems[] = self::ASSOCIATE_WITH_PRICE_LINE_HOTELS[$item->name] ?: null;
            }
        }
        foreach ($items as $k => $item) {
            if ($item instanceof TrPosPlHotels && in_array($item->name, $deletePlItems, false)) {
                unset($items[$k]);
            }
        }
    }

    public function rules()
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
    public function attributeLabels()
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
    public static function getActualCategories()
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
     * @deprecated
     */
    public function getItemsPhoto(): ActiveQuery
    {
        return $this->getTrPosHotelsPhotoJoins();
    }

    /**
     * @return ActiveQuery
     */
    public function getRoomTypes(): ActiveQuery
    {
        return $this->hasMany(TrPosRoomTypes::class, ['id_external_item' => 'id_external']);
    }

    /**
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
    public function getSourceData()
    {
        $tripium = new Tripium();
        $res = $tripium->getPosHotels($this->updateOnlyIdExternal);
        $this->statusCodeTripium = $tripium->statusCode;
        return $res;
    }

    /**
     * @param $data
     *
     * @return string
     */
    private static function contentItemHash($data): string
    {
        return md5(serialize(array_values($data)) . serialize($data));
    }

    /**
     * @param $result
     *
     * @return bool
     */
    private function needUpdateContent($result): bool
    {
        if (empty($result['content']['IMAGE'])) {
            $result['content']['IMAGE'] = [];
        }
        if (empty($result['content']['VIDEO'])) {
            $result['content']['VIDEO'] = [];
        }
        if (empty($result['cover'])) {
            $result['cover'] = [];
        }
        return $this->hash_image_content !== self::contentItemHash(
                array_merge($result['content']['IMAGE'], $result['content']['VIDEO'], [$result['cover']])
            );
    }

    /**
     * @return bool
     */
    public function setVideo(): bool
    {
        if (!$this->updateVideo) {
            return false;
        }

        $tripium = new Tripium();
        $result = $tripium->getContent('hotel', $this->id_external, 'VIDEO');

        if (!$this->needUpdateContent($result)) {
            return false;
        }

        if (empty($result['content']['VIDEO'])) {
            $result['content']['VIDEO'] = [];
        }

        $this->setAttribute('videos', implode(';', ArrayHelper::getColumn($result['content']['VIDEO'], 'content')));
        $this->save();

        return true;
    }

    /**
     * @return bool
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function setPhotoAndPreview(): bool
    {
        if (!$this->updateImages) {
            return false;
        }

        $tripium = new Tripium();
        $result = $tripium->getContent('hotel', $this->id_external, 'IMAGES');

        if (!$this->needUpdateContent($result)) {
            return false;
        }

        if (empty($result['content']['IMAGE'])) {
            $result['content']['IMAGE'] = [];
        }

        $this->updatePreview($result['cover']);

        $newImages = ArrayHelper::getColumn($result['content']['IMAGE'], 'contentId');

        /**
         * @var TrPosHotelsPhotoJoin[] $uploadedImages
         */
        $uploadedImages = $this->getRelatedPhotos()->with(['photo'])->all();

        foreach ($uploadedImages as $uploadedImage) {
            if (!in_array($uploadedImage->photo->source_url, $newImages, true)) {
                $uploadedImage->delete();
            } elseif (count($result['content']['IMAGE']) > 0) {
                foreach ($result['content']['IMAGE'] as $k => $image) {
                    if ($image['contentId'] === $uploadedImage->photo->source_url) {
                        if (self::contentItemHash($image) === $uploadedImage->hash) {
                            unset ($result['content']['IMAGE'][$k]);
                        } else {
                            $uploadedImage->delete();
                        }
                    }
                }
            }
        }

        $roomTypesIds = TrPosRoomTypes::find()->select(['id_external'])->column();

        foreach ($result['content']['IMAGE'] as $image) {
            if (!empty($image['subentityId']) && !in_array($image['subentityId'], $roomTypesIds, true)) {
                continue;
            }

            $imageUrl = $image['contentId'];

            $tags = $image['tags'];
            if (($key = array_search('none', $tags, true)) !== false) {
                unset($tags[$key]);
            }

            $uploadItemsPhotos = new UploadItemsPhotosHotel();
            $uploadItemsPhotos->downloadByUrl($imageUrl);

            $uploadItemsPhotosPreview = new UploadItemsPhotosPreviewHotel();
            $uploadItemsPhotosPreview->downloadByUrl($imageUrl);

            if ($uploadItemsPhotos->id && $uploadItemsPhotosPreview->id) {
                $modelPhotoJoin = new TrPosHotelsPhotoJoin();
                $modelPhotoJoin->setAttributes(
                    [
                        'item_id' => $this->id,
                        'preview_id' => $uploadItemsPhotosPreview->id,
                        'photo_id' => $uploadItemsPhotos->id,
                        'subcategory' => $image['subcategory'],
                        'room_type_external_id' => $image['subentityId'],
                        'tags' => !empty($tags) ? "'" . implode("';'", $tags) . "'" : null,
                        'hash' => self::contentItemHash($image),
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

        $this->hash_image_content = self::contentItemHash(
            array_merge(
                !empty($result['content']['IMAGE']) ? $result['content']['IMAGE'] : [],
                !empty($result['content']['VIDEO']) ? $result['content']['VIDEO'] : [],
                !empty($result['cover']) ? [$result['cover']] : []
            )
        );
        $this->save();

        return true;
    }

    /**
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
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getPrices(): ActiveQuery
    {
        return $this->hasMany(TrPosHotelsPriceRoom::class, ['id_external' => 'id_external'])
            ->viaTable(TrPosRoomTypes::tableName(), ['id_external_item' => 'id_external']);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getActivePrices(): ActiveQuery
    {
        return $this->getPrices()
            ->andOnCondition(TrPosHotelsPriceRoom::tableName() . '.start >= CURDATE( )');
    }

    /**
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
     * @return string
     */
    public function getUrl(): string
    {
        return Yii::$app->urlManager->createUrl(['hotel/detail', 'code' => $this->code]);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getSimilar(): ActiveQuery
    {
        return self::getAvailable()
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
}
