<?php

namespace common\models;

use common\models\form\SearchPlHotel;
use common\models\priceLine\NewPriceLineHotels;
use common\tripium\Tripium;
use DateInterval;
use DateTime;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class TrPosPlHotels extends _source_TrPosPlHotels
{
    use ItemsExtensionTrait;

    public const SMOKING_NS = 'NS';
    public const SMOKING_S = 'S';
    public const SMOKING_E = 'E';

    public const TAG_ORIGINAL_ON_SALE = 'On Sale';
    public const TAG_ORIGINAL_PREMIUM = 'Premium';
    public const TAG_ORIGINAL_FEATURED = 'Featured';
    public const TAG_ORIGINAL_FAMILY_PASS = 'Family Pass';
    public const TAG_ORIGINAL_LIMITED = 'Limited';

    public const joinCategoriesClass = TrPosPlHotelsCategories::class;
    public const photoJoinClass = TrPosPlHotelsPhotoJoin::class;

    public const TYPE = 'hotels';
    public const NAME = 'Lodging';
    public const NAME_PLURAL = 'Lodging';
    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;
    public const STATUS_WL_ACTIVE = 1;
    public const STATUS_WL_INACTIVE = 0;

    public const CALL_US_TO_BOOK_YES = 1;
    public const CALL_US_TO_BOOK_NO = 0;

    public const DISPLAY_IN_WHERE_TO_STAY_YES = 1;
    public const DISPLAY_IN_WHERE_TO_STAY_NO = 0;

    private $priceLineData;

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
     * @return array
     */
    public static function getSmokingList(): array
    {
        return [
            self::SMOKING_NS => 'Non-smoking',
            self::SMOKING_S => 'Smoking',
            self::SMOKING_E => 'Either',
        ];
    }

    /**
     * @param string $val
     *
     * @return string
     */
    public static function getSmokingValue($val): string
    {
        $ar = self::getSmokingList();

        return $ar[$val] ?? $val;
    }

    /**
     * Return item url
     *
     * @return string
     */
    public function getUrl(): string
    {
        return Yii::$app->urlManager->createUrl(['pl-hotel/detail', 'code' => $this->code]);
    }

    /**
     * @return ActiveQuery
     */
    public function getRelatedPhotos(): ActiveQuery
    {
        return $this->hasMany(self::photoJoinClass, ['item_id' => 'id']);
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
     */
    public static function getAvailable(): ActiveQuery
    {
        return self::getActive();
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getCategories(): ActiveQuery
    {
        return $this->hasMany(Categories::class, ['id_external' => 'id_external_category'])
            ->viaTable(TrPosPlHotelsCategories::tableName(), ['id_external_show' => 'id_external']);
    }

    /**
     * @param array $params
     *
     * @return array|null
     */
    public function getSourceData(array $params): ?array
    {
        try {
            $checkIn = new DateTime();
            if (isset($params['check_in']) && $params['check_in'] instanceof DateTime) {
                $checkIn = $params['check_in'];
            }
            $checkOut = new DateTime();
            $checkOut->add(new DateInterval('P1D'));
            if (isset($params['check_out']) && $params['check_out'] instanceof DateTime) {
                $checkOut = $params['check_out'];
            }
            $params['rooms'] = $params['rooms'] ?? 1;
            $params['adults'] = $params['adults'] ?? 2;
            $params['children'] = $params['children'] ?? 0;
            $params['sort_by'] = $params['sort_by'] ?? null;
            $tripium = new Tripium;
            $res = $tripium->getPLHotels(
                $checkIn,
                $checkOut,
                $params['rooms'],
                $params['adults'],
                $params['children'],
                $params['sort_by']
            );
            $this->statusCodeTripium = $tripium->statusCode;
            return $res;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Build query by $Search
     *
     * @param SearchPlHotel|null $Search
     *
     * @return ActiveQuery
     */
    public static function getByFilter(SearchPlHotel $Search = null): ActiveQuery
    {
        if (!$Search) {
            return self::getAvailable();
        }

        $query = self::getActive()->distinct();

        if ($Search->title) {
            $query->andWhere(['like', self::tableName() . '.name', $Search->title]);
        }

        if (isset($Search->statusWl)) {
            $query->andWhere([self::tableName() . '.status_wl' => $Search->statusWl]);
        }

        return $query;
    }

    /**
     * @param SearchPlHotel $Search
     *
     * @return array
     */
    public static function withPriceLine(SearchPlHotel $Search): array
    {
        $Tripium = new Tripium;
        $plHotels = $Tripium->getPLHotels(
            $Search->getArrivalDate(),
            $Search->getDepartureDate(),
            count($Search->room),
            $Search->getAdultsCount(),
            $Search->getChildrenCount(),
            $Search->fieldSort,
            $Search->externalIds
        );

        if ($Search->amenities) {
            foreach ($plHotels as $k => $item) {
                foreach ($item['amenities'] as $i => &$v) {
                    $v = strtolower($v);
                }
                unset($v);
                $has = false;
                foreach ($Search->amenities as $amenity) {
                    if (in_array($amenity, $item['amenities'], true)) {
                        $has = true;
                    }
                }
                if (!$has) {
                    unset($plHotels[$k]);
                }
            }
        }

        if (!empty($Search->city)) {
            foreach ($plHotels as $k => $item) {
                if (!in_array($item['city'], $Search->city, false)) {
                    unset($plHotels[$k]);
                }
            }
        }
        $externalIds = ArrayHelper::getColumn($plHotels, 'id');

        /**
         * @var TrPosPlHotels[] $items
         */
        $items = self::find()
            ->andWhere(['id_external' => $externalIds])
            ->with(['preview'])
            ->all();
        $items = ArrayHelper::index($items, 'id_external');
        $results = [];
        $shouldUpdateExternalId = [];
        foreach ($plHotels as $hotel) {
            $TrPosPlHotels = $items[$hotel['id']] ?? null;
            if (!empty($TrPosPlHotels)) {
                $TrPosPlHotels->setPriceLineData($hotel);
                if (($Search->priceFrom === null || $TrPosPlHotels->avgNightlyRate() >= $Search->priceFrom)
                    && ($Search->priceTo === null || $TrPosPlHotels->avgNightlyRate() <= $Search->priceTo)) {
                    $results[] = $TrPosPlHotels;
                }
            } else {
                $shouldUpdateExternalId[] = $hotel['id'];
            }
        }

        if ($shouldUpdateExternalId) {
            foreach ($shouldUpdateExternalId as $external_id) {
                $newPriceLineHotels = new NewPriceLineHotels(
                    [
                        'external_id' => $external_id,
                        'status' => NewPriceLineHotels::STATUS_NEW,
                        'query' => Json::encode([
                            'setStatus' => 1,
                            'check_in' => $Search->getArrivalDate(),
                            'check_out' => $Search->getDepartureDate(),
                            'rooms' => count($Search->room),
                            'adults' => $Search->getAdultsCount(),
                            'children' => $Search->getChildrenCount(),
                            'sort_by' => $Search->fieldSort
                        ]),
                    ]
                );
                $newPriceLineHotels->save();
            }
        }
        $idsByFilter = self::getByFilter($Search)->select('id')->column();
        foreach ($results as $k => $item) {
            if (!in_array($item->id, $idsByFilter, false)) {
                unset($results[$k]);
            }
        }
        if ($Search->starRating) {
            foreach ($results as $k => $item) {
                if (!in_array((int)$item->rating, $Search->starRating, false)) {
                    unset($results[$k]);
                }
            }
        }
        if (in_array(
            $Search->fieldSort,
            [
                SearchPlHotel::FIELD_SORT_STAR_RATING,
                SearchPlHotel::FIELD_SORT_STAR_RATING_DESC
            ],
            true
        )) {
            usort(
                $results,
                static function ($a, $b) use ($Search) {
                    if ($a->rating === $b->rating) {
                        return 0;
                    }
                    if ($Search->fieldSort === SearchPlHotel::FIELD_SORT_STAR_RATING_DESC) {
                        return ($a->rating > $b->rating) ? -1 : 1;
                    }
                    return ($a->rating < $b->rating) ? -1 : 1;
                }
            );
        }

        return $results;
    }

    /**
     * @param array $data
     */
    public function setPriceLineData(array $data): void
    {
        $this->priceLineData = $data;
    }

    /**
     * @return float
     */
    public function avgNightlyRate(): float
    {
        $avgNightlyRate = null;
        if (!empty($this->priceLineData['rooms'])) {
            foreach ($this->priceLineData['rooms'] as $room) {
                if (!empty($room['prices'])) {
                    foreach ($room['prices'] as $price) {
                        if (!empty($price['priceline']['display_price']) && (
                                $avgNightlyRate === null || $avgNightlyRate > $price['priceline']['display_price'])) {
                            $avgNightlyRate = $price['priceline']['display_price'];
                        }
                    }
                }
            }
        }
        return $avgNightlyRate ?? 0;
    }

    /**
     * @return string
     */
    public function address(): string
    {
        return $this->priceLineData['address'] ?: '';
    }

    /**
     * @return array
     */
    public function roomTypes(): array
    {
        return $this->priceLineData['rooms'] ?? [];
    }
}
