<?php

namespace common\models\form;

use common\models\TrPosHotels;
use common\models\TrPosPlHotels;
use DateInterval;
use DateTime;
use Exception;
use yii\base\Model;

class SearchHotelGeneral extends Model
{
    public const DISPLAY_LIST = 'list';
    public const DISPLAY_GRID = 'grid';
    public const DISPLAY_MAP  = 'map';

    public const FIELD_SORT_PRICE_AVERAGE = 'lp';
    public const FIELD_SORT_PRICE_REVERSE = 'hp';
    public const FIELD_SORT_GUEST_SCORE = 'gs';
    public const FIELD_SORT_STAR_RATING = 'sr';
    public const FIELD_SORT_STAR_RATING_DESC = 'sr';

    public $title;
    public $arrivalDate;
    public $departureDate;
    public $priceFrom;
    public $priceTo;
    /**
     * @var int[]|int[][]
     */
    public $room = [['adult' => 2, 'children' => 0]];

    public $starRating;
    public $amenities;
    public $c;
    public $city;
    public $fieldSort;
    public $display;
    public $externalIds = [];

    /**
     * @var array
     */
    protected static $amenitiesCache = [];

    /**
     * @return string
     */
    public function formName(): string
    {
        return 's';
    }

    /**
     * @return string
     */
    public function searchName(): string
    {
        return 'Hotel';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['title', 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
            [['externalIds', 'c'], 'safe'],
            ['amenities', 'filter', 'filter' => [$this, 'filterAmenities']],
            ['city', 'filter', 'filter' => [$this, 'filterCities']],
            [['title'], 'filter', 'filter' => 'trim'],
            ['room', 'filter', 'filter' => [$this, 'filterRoom']],
            [
                'room',
                'required',
                'isEmpty' => static function ($value) {
                    return empty($value);
                }
            ],
            [['arrivalDate', 'departureDate'], 'filter', 'filter' => [$this, 'filterDate']],
            ['arrivalDate', 'filter', 'filter' => [$this, 'filterArrivalDate']],
            ['departureDate', 'filter', 'filter' => [$this, 'filterCheckoutDate']],
            [
                ['priceTo', 'priceFrom'],
                'filter',
                'filter' => static function ($attr) {
                    return !empty($attr) ? (int)$attr : null;
                }
            ],
//            [
//                'sort',
//                'filter',
//                'filter' => static function ($attr) {
//                    return in_array($attr, ['desc', 'asc']) ? $attr : 'asc';
//                }
//            ],
            [
                'fieldSort',
                'filter',
                'filter' => static function ($attr) {
                    return array_key_exists($attr, self::sortList()) ? $attr : self::FIELD_SORT_PRICE_AVERAGE;
                }
            ],
            ['starRating', 'filter', 'filter' => [$this, 'filterRating']],
            [
                'display',
                'filter',
                'filter' => static function ($attr) {
                    return in_array($attr, [self::DISPLAY_LIST, self::DISPLAY_GRID, self::DISPLAY_MAP], true)
                        ? $attr : self::DISPLAY_LIST;
                }
            ],
        ];
    }

    public static function sortList(): array
    {
        return [
            self::FIELD_SORT_PRICE_AVERAGE => 'Price low to high',
            self::FIELD_SORT_PRICE_REVERSE => 'Price high to low',
            self::FIELD_SORT_GUEST_SCORE => 'Guest Score',
            self::FIELD_SORT_STAR_RATING => 'Rating low to high',
            self::FIELD_SORT_STAR_RATING_DESC => 'Rating high to low',
        ];
    }

    /**
     * Filter Dates
     *
     * @param $attr
     *
     * @return string
     * @throws Exception
     */
    public function filterDate($attr): ?string
    {
        try {
            return (new DateTime($attr))->format('m/d/Y');
        } catch (Exception $e) {
            return (new DateTime())->format('m/d/Y');
        }
    }

    /**
     * Filter Checkout Date
     *
     * @param $attr
     *
     * @return string
     * @throws Exception
     */
    public function filterCheckoutDate($attr): ?string
    {
        $date = null;
        try {
            $date = (new DateTime($attr));
            if ($this->getArrivalDate() == $date) {
                $date->add(new DateInterval('P1D'));
            }
        } catch (Exception $e) {
            $date = (new DateTime());
            $date->add(new DateInterval('P1D'));
        }
        return $date->format('m/d/Y');
    }

    /**
     * Filter Checkout Date
     *
     * @param $attr
     *
     * @return string
     * @throws Exception
     */
    public function filterArrivalDate($attr): ?string
    {
        $date = null;
        try {
            $date = (new DateTime($attr));
            if ($date < new DateTime()) {
                $date = new DateTime();
            }
        } catch (Exception $e) {
            $date = (new DateTime());
        }
        return $date->format('m/d/Y');
    }

    /**
     * Filter room
     *
     * @param array $attr
     *
     * @return array
     */
    public function filterRoom($attr): array
    {
        if (!is_array($attr)) {
            return [];
        }
        foreach ($attr as $k => $v) {
            if (!is_array($v)) {
                unset($attr[$k]);
            } else {
                $attr[$k]['adult'] = (int)$attr[$k]['adult'];
                $attr[$k]['children'] = (int)$attr[$k]['children'];
                if (!empty($attr[$k]['age']) && !is_array($attr[$k]['age'])) {
                    unset($attr[$k]['age']);
                } elseif (!empty($attr[$k]['age'])) {
                    foreach ($attr[$k]['age'] as $j => $age) {
                        $attr[$k]['age'][$j] = (int)$attr[$k]['age'][$j];
                    }
                }
            }
        }
        return $attr;
    }

    /**
     * Filter amenities
     *
     * @param array $attr
     *
     * @return array
     */
    public function filterAmenities($attr): array
    {
        if (!is_array($attr)) {
            return [];
        }
        foreach ($attr as $k => $v) {
            $attr[(string)$k] = strtolower($v);
        }
        return $attr;
    }

    /**
     * Filter cities
     *
     * @param array $attr
     *
     * @return array
     */
    public function filterCities($attr): array
    {
        if (!is_array($attr)) {
            return [];
        }

        $cities = TrPosPlHotels::find()->select('city')->distinct()->asArray()->column();
        $cities2 = TrPosHotels::find()->select('city')->distinct()->asArray()->column();
        $cities = array_merge($cities, $cities2);

        foreach ($attr as $k => $v) {
            if (!in_array($v, $cities, false)) {
                unset($attr[$k]);
            }
        }
        return $attr;
    }

    /**
     * Filter Rating
     *
     * @param mixed $attr
     *
     * @return array
     */
    public function filterRating($attr): array
    {
        $result = [];
        if (!empty($attr) && is_array($attr)) {
            foreach ($attr as $k => $v) {
                $result[] = (int)$v;
            }
        }
        return $result;
    }

    /**
     * @return DateTime
     */
    public function getArrivalDate(): DateTime
    {
        try {
            return new DateTime($this->arrivalDate);
        } catch (Exception $e) {
        }
        try {
            return new DateTime();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @return DateTime
     */
    public function getDepartureDate(): DateTime
    {
        try {
            return new DateTime($this->departureDate);
        } catch (Exception $e) {
        }
        try {
            return new DateTime();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param int $minAge
     *
     * @return int
     */
    public function getMinAdultsCount($minAge): int
    {
        $ar = [];
        foreach ($this->room as $room) {
            $result = 0;
            $result += $room['adult'];
            if (!empty($room['age'])) {
                foreach ($room['age'] as $age) {
                    if ($age >= $minAge) {
                        $result++;
                    }
                }
            }
            $ar[] = $result;
        }
        return min($ar);
    }

    /**
     * @return int
     */
    public function getAdultsCount(): int
    {
        $result = 0;
        foreach ($this->room as $room) {
            $result += $room['adult'];
        }
        return $result;
    }

    /**
     * @return int
     */
    public function getChildrenCount(): int
    {
        $result = 0;
        foreach ($this->room as $room) {
            $result += $room['children'];
        }
        return $result;
    }

    /**
     * @param bool $new
     *
     * @return array
     */
    public static function getStarList($new = false): array
    {
        $res = [];
        for ($i = 5; $i > 0; $i--) {
            if ($new) {
                $res[$i] = '<span class="star-rating"><span class="star-rating-box">
				<span class="fon">
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                </span>
				<span class="val" style="width:' . ($i * 20) . '%;">
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                </span>
			</span></span>
		    <span><b>' . $i . ' stars</b></span> <i></i>';
            } else {
                $res[$i] = '<span class="rates">
                    <span class="fon">
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                    </span>
                    <span class="val" style="width:' . ($i * 20) . '%;">
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                    </span>
                </span>
                <span><b>' . $i . ' stars</b></span> <i></i>';
            }
        }
        return $res;
    }

    /**
     * @return array
     */
    public function getOrderBy(): array
    {
        if ($this->fieldSort === self::FIELD_SORT_PRICE_AVERAGE) {
            return ['min_rate' => SORT_ASC];
        }
        if ($this->fieldSort === self::FIELD_SORT_PRICE_REVERSE) {
            return ['min_rate' => SORT_DESC];
        }
        return [];
    }
}
