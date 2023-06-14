<?php

namespace common\models\form;

use common\helpers\General;
/*use common\models\TrAttractions;
use common\models\TrPosHotels;*/
use common\models\TrPrices;
use common\models\TrShows;
/*use common\models\VacationPackage;*/
use DateTime;
use DateTimeInterface;
use Exception;
use yii\base\Model;

class Search extends Model
{
    public const DISPLAY_LIST = 'list';
    public const DISPLAY_GRID = 'grid';
    public const DISPLAY_MAP  = 'map';
    public const DISPLAY_HIDE  = 'hide';

    public const ORDER_BY_MARKETING_LEVEL = 'marketing_level';
    public const TIME_FROM = 8;
    public const TIME_TO = 23;

    public const FIELD_SORT_NAME_ASC = 'name_asc';
    public const FIELD_SORT_NAME_DESC = 'name_desc';
    public const FIELD_SORT_PRICE_ASC = 'price_asc';
    public const FIELD_SORT_PRICE_DESC = 'price_desc';
    public const FIELD_SORT_MARKETING_LEVEL = 'marketing_level';

    private $dateTimeFrom;
    private $dateTimeTo;
    private $orderby;

    public $title;
    public $dateFrom;
    public $dateTo;
    public $c;
    public $cr;
    public $l;
    public $sortOrder;
    public $display;
    public $priceFrom;
    public $priceTo;
    public $timeFrom = self::TIME_FROM;
    public $timeTo = self::TIME_TO;
    public $tags;
    public $cuisine;
    public $amenity;
    public $meals;

    public $without_availability = 0;
    public $model;
    public $externalIds;
    public $alternativeRate;
    public $fieldSort;

    /**
     * {@inheritdoc}
     */
    public function formName()
    {
        return 's';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'dateFrom',
                    'dateTo',
                    'c',
                    'cr',
                    'l',
                    'cuisine',
                    'meals',
                    'amenity',
                ],
                'safe'
            ],
            [
                'display',
                'filter',
                'filter' => static function ($attr) {
                    return in_array($attr, [self::DISPLAY_LIST, self::DISPLAY_GRID, self::DISPLAY_MAP], true)
                        ? $attr : self::DISPLAY_LIST;
                }
            ],
            [
                'fieldSort',
                'filter',
                'filter' => static function ($attr) {
                    return array_key_exists($attr, self::sortList()) ? $attr : self::FIELD_SORT_MARKETING_LEVEL;
                }
            ],
            [
                'tags',
                'filter',
                'filter' => function ($attr) {
                    if (!empty($attr) && !is_array($attr)) {
                        return null;
                    }

                    if (!empty($attr)) {
                        $ar = [];
                        foreach ($attr as $v) {
                            if (!empty($this->model) && array_key_exists($v, $this->model::getOriginalTagTitleList())) {
                                $ar[] = $v;
                            }
                        }
                        $ar = !empty($ar) ? $ar : null;
                        return $ar;
                    }
                    return null;
                }
            ],
            ['title', 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
            [
                ['priceTo', 'priceFrom'],
                'filter',
                'filter' => static function ($attr) {
                    return !empty($attr) ? (int)$attr : null;
                }
            ],
            [
                ['timeFrom', 'timeTo'],
                'filter',
                'filter' => static function ($attr) {
                    $attr = !empty($attr) ? (int)$attr : null;
                    if ($attr !== null && $attr < self::TIME_FROM) {
                        $attr = self::TIME_FROM;
                    }
                    if ($attr !== null && $attr > self::TIME_TO) {
                        $attr = self::TIME_TO;
                    }
                    return $attr;
                }
            ],
            ['alternativeRate', 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'title' => 'Search by ' . $this->searchName() . ' name',
            'c' => 'Category',
            'cr' => 'Category',
            'l' => 'Location',
            'datefrom' => 'Start Date',
            'dateto' => 'End Date',
            'tags' => 'Tags',
            'without_availability' => 'Without Availability',
            'alternativeRate' => 'Alternative Rate',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $this->initDate();
    }

    /**
     * {@inheritdoc}
     */
    public function load($data, $formName = null)
    {
        $res = parent::load($data, $formName);
        $this->validate();

        General::setRange($this->dateFrom, $this->dateTo);

        $this->initDate();

        return $res;
    }

    public static function sortList(): array
    {
        return [
            self::FIELD_SORT_NAME_ASC => 'Alphabetical A-Z',
            self::FIELD_SORT_NAME_DESC => 'Alphabetical Z-A',
            self::FIELD_SORT_PRICE_ASC => 'Price low to high',
            self::FIELD_SORT_PRICE_DESC => 'Price high to low',
            self::FIELD_SORT_MARKETING_LEVEL => 'Most popular',
        ];
    }

    public static function getSortByValue($sort): ?string
    {
        $ar = self::sortList();

        return $ar[$sort] ?? $sort;
    }

    /**
     * Set orderBy
     *
     * @param $orderBy
     */
    public function setOrderBy($orderBy): void
    {
        $this->orderby = $orderBy;
    }

    /**
     * Return orderBy
     *
     * @return array
     */
    public function getOrderBy(): array
    {
        $sortOrder = in_array(
            $this->fieldSort,
            [self::FIELD_SORT_NAME_DESC, self::FIELD_SORT_PRICE_DESC],
            true
        ) ? SORT_DESC : SORT_ASC;

        $by = in_array(
            $this->fieldSort,
            [self::FIELD_SORT_NAME_ASC, self::FIELD_SORT_NAME_DESC],
            true
        ) ? 'name' : null;

        $by = in_array(
            $this->fieldSort,
            [self::FIELD_SORT_PRICE_ASC, self::FIELD_SORT_PRICE_DESC],
            true
        ) ? 'min_rate' : $by;

        if ($this->fieldSort === self::FIELD_SORT_MARKETING_LEVEL) {
            $this->orderby = [
                "IF(LOCATE('Premium', tags)>0,100,0)+IF(LOCATE('Featured', tags)>0,50,0)" =>
                    $sortOrder === SORT_DESC ? SORT_ASC : SORT_DESC,
                "rank" => SORT_ASC,
                "name" => $sortOrder,
            ];
        } else {
            $this->orderby = $by ? [$by => $sortOrder] : [];
        }
        $this->orderby = array_merge(['status' => SORT_DESC, 'location_sort' => SORT_DESC], $this->orderby);

        return $this->orderby;
    }

    /**
     * Initial From and To date
     *
     * @throws Exception
     */
    protected function initDate()
    {
        $range = General::getDatePeriod();
        $this->dateFrom = $range->start->format("m/d/Y");
        $this->dateTo = $range->end->format("m/d/Y");
        $this->setDateTimeFrom($range->start);
        $this->setDateTimeTo($range->end);
    }

    /**
     * Set Date and Time From
     *
     * @param DateTimeInterface $dateTimeFrom
     */
    public function setDateTimeFrom(DateTimeInterface $dateTimeFrom): void
    {
        $this->dateTimeFrom = $dateTimeFrom > new DateTime() ? $dateTimeFrom : new DateTime();
        $this->dateFrom = $this->dateTimeFrom->format('m/d/Y');
    }

    /**
     * Set Date and Time To
     *
     * @param DateTimeInterface $dateTimeTo
     */
    public function setDateTimeTo(DateTimeInterface $dateTimeTo): void
    {
        $this->dateTimeTo = $dateTimeTo > new DateTime() ? $dateTimeTo : new DateTime();
        $this->dateTo = $this->dateTimeTo->format('m/d/Y');
    }

    /**
     * Get DateTime From
     *
     * @return DateTime
     */
    public function getDateTimeFrom()
    {
        return $this->dateTimeFrom;
    }

    /**
     * Get DateTime To
     *
     * @return DateTime
     */
    public function getDateTimeTo()
    {
        return $this->dateTimeTo;
    }

    /**
     * Return Search Name
     *
     * @return string
     */
    public function searchName()
    {
        if ($this->model instanceof TrShows) {
            $title = 'Show';
        } /*elseif ($this->model instanceof TrAttractions) {
            $title = 'Attraction';
        } elseif ($this->model instanceof TrPosHotels) {
            $title = 'Hotel';
        } elseif ($this->model instanceof VacationPackage) {
            $title = 'Vacation Package';
        }*/ else {
            $title = '';
        }
        return $title;
    }

    /**
     * @param array    $scheduleByDay
     * @param DateTime $date
     *
     * @return array
     */
    public static function prepareScheduleByDay(DateTime $date, array $scheduleByDay): array
    {
        foreach ($scheduleByDay as $key => $it) {
            for ($i = $date->format("w"); $i < 7 + (int)$date->format("w"); $i++) {
                $it["schedule"][$i >= 7 ? $i - 7 : $i * 1] = null;
                $it["minFamily"] = null;
                $it["minFamilySpecial"] = null;
                $it["minAdult"] = null;
                $it["minAdultSpecial"] = null;
            }

            if (!empty($it["availablePrices"])) {
                foreach ($it["availablePrices"] as $price) {
                    try {
                        $dt = new DateTime($price["start"]);

                        if (!empty($price["special_rate"]) || empty($it["schedule"][$dt->format("w")][$dt->format("U")])) {
                            $it["schedule"][$dt->format("w")][$dt->format("U")] = $price;
                        }
                        $price_rate = number_format(
                            $price["special_rate"] * 1 > 0 ? $price["special_rate"] * 1 : $price["retail_rate"] * 1,
                            2,
                            '.',
                            ''
                        );

                        $price["retail_rate"] = number_format($price["retail_rate"], 2, '.', '');

                        if (mb_strtoupper(trim($price["name"])) === TrPrices::PRICE_TYPE_FAMILY_PASS) {
                            if ($price_rate < $it["minFamily"] || $it["minFamily"] === null) {
                                $it["minFamily"] = $price["retail_rate"];
                                $it["minFamilySpecial"] = $price_rate !== $price["retail_rate"] ? $price_rate : null;
                            }
                        }

                        if (mb_strtoupper(trim($price["name"])) === TrPrices::NAME_ADULT) {
                            if ($price_rate < $it["minAdult"] || $it["minAdult"] === null) {
                                $it["minAdult"] = $price["retail_rate"];
                                $it["minAdultSpecial"] = $price_rate !== $price["retail_rate"] ? $price_rate : null;
                            }
                        }

                    } catch (Exception $e) {
                    }
                }
            }

            $scheduleByDay[$key] = $it;
        }

        return $scheduleByDay;
    }

    /**
     * @param array $scheduleByDay
     *
     * @return array
     */
    public static function prepareScheduleForDay(array $scheduleByDay): array
    {
        $scheduleForDay = [];

        foreach ($scheduleByDay as $key => $it) {
            if ($it["availablePrices"]) {
                foreach ($it["availablePrices"] as $price) {
                    try {
                        $dt = new DateTime($price["start"]);

                        $scheduleForDay[!empty($price['any_time']) ? 'Any Time' : $dt->format("h:iA")][$it["code"]] = [
                            "start" => $price["start"],
                            "name" => $it["name"],
                            "code" => $it["code"],
                        ];
                    } catch (Exception $e) {
                    }
                }
            }
        }

        return $scheduleForDay;
    }

    /**
     * @return array
     */
    public function getSliderPriceRange(): array
    {
        $price = $this->model::getAvailable()->select(['max(price) as max', 'min(price) as min'])->asArray()->one();
        $price['min'] = 0;
        $price['max'] = $price['max'] ?? 999;
        $price['max'] = ceil($price['max'] / 30) * 30;

        return [
            'value_from' => $this->priceFrom ?: $price['min'],
            'value_to'   => $this->priceTo ?: $price['max'],
            'min'        => 0,
            'max'        => $price['max'],
        ];
    }
}
