<?php
//
//namespace common\models\form;
//
//use common\models\TrPosHotels;
//use common\models\TrPosPlHotels;
//use DateInterval;
//use DateTime;
//use Exception;
//use Yii;
//use yii\helpers\Json;
///** @deprecated  */
//class SearchPlHotel extends SearchHotelGeneral
//{
////    public const SORT_BY_PRICE = 'price';
////    public const SORT_PRICE_AVERAGE = 'hp';
////    public const SORT_PRICE_REVERSE = 'lp';
////    public const SORT_BY_GUEST_SCORE = 'gs';
////    public const SORT_BY_STAR_RATING = 'sr';
//
////    public const FIELD_SORT_BY_PRICE = 'hp';
//
////    public $by = self::SORT_BY_GUEST_SCORE;
//    public $model;
//
////    /**
////     * @return array
////     */
////    public function rules(): array
////    {
////        return array_merge(
////            parent::rules(),
////            [
////                [
////                    'fieldSort',
////                    'filter',
////                    'filter' => static function ($attr) {
////                        return array_key_exists($attr, self::sortList()) ? $attr : self::FIELD_SORT_PRICE_AVERAGE;
////                    }
////                ],
////            ]
////        );
////    }
//
//    /**
//     * @return array
//     */
//    public static function sortList(): array
//    {
//        return [
//            self::FIELD_SORT_PRICE_AVERAGE => 'Price low to high',
//            self::FIELD_SORT_PRICE_REVERSE => 'Price high to low',
//            self::FIELD_SORT_GUEST_SCORE => 'Guest score',
//            self::FIELD_SORT_STAR_RATING => 'Star rating low to high',
//            self::FIELD_SORT_STAR_RATING_DESC => 'Star rating high to low',
//        ];
//    }
//
//    /**
//     * @return array
//     */
//    public function attributeLabels(): array
//    {
//        return [
//            'title' => 'Search by hotel name',
//            'c' => 'Category',
//            'arrivalDate' => 'Arrival Date',
//            'departureDate' => 'Departure Date',
//            'priceFrom' => 'Price From',
//            'priceTo' => 'Price To',
//        ];
//    }
//
//    /**
//     * @return DateTime
//     */
//    protected function getDefaultDate(): DateTime
//    {
//        return (new DateTime())->add(new DateInterval('P30D'));
//    }
//
//    public function init(): void
//    {
//        parent::init();
//
//        $data = [];
//        if (!empty(Yii::$app->session->get('filterHotel'))) {
//            try {
//                $data = Json::decode(Yii::$app->session->get('filterHotel'));
//            } catch (Exception $e) {
//            }
//        }
//        if (empty($data)) {
//            $data = [
//                $this->formName() => [
//                    'arrivalDate' => $this->getDefaultDate()->format('m/d/Y'),
//                    'departureDate' => $this->getDefaultDate()->add(new DateInterval('P1D'))->format('m/d/Y'),
//                ]
//            ];
//        }
//        try {
//            if (new DateTime($data[$this->formName()]['departureDate'])
//                <= new DateTime($data[$this->formName()]['arrivalDate'])) {
//                $data[$this->formName()]['departureDate'] = (new DateTime($data[$this->formName()]['arrivalDate']))
//                    ->add(new DateInterval('P1D'))->format('m/d/Y');
//            }
//        } catch (Exception $e) {
//        }
//        $this->load($data);
//    }
//
//    public function load($data, $formName = null): bool
//    {
//        $res = parent::load($data, $formName);
//
//        $this->validate();
//
//        $data = $this->getAttributes(['room', 'arrivalDate', 'departureDate']);
//
//        Yii::$app->session->set('filterHotel', Json::encode([$this->formName() => $data]));
//
//        return $res;
//    }
////
////    /**
////     * @return array
////     */
////    public function getSortByList(): array
////    {
////        $sortPrice = $this->sort === 'asc' ? self::SORT_PRICE_AVERAGE : self::SORT_PRICE_REVERSE;
////        $sortCss = $this->sort === 'asc' ? 'sort-asc' : 'sort-desc';
////        $sort = $this->sort !== 'asc' ? 'asc' : 'desc';
////        return [
////            self::SORT_BY_PRICE => [
////                'name' => 'Price',
////                'sort' => $this->by === self::SORT_BY_PRICE ? $sortPrice : '',
////                'css-class' => $this->by === self::SORT_BY_PRICE ? $sortCss : '',
////                'data-sort' => $this->by === self::SORT_BY_PRICE ? $sort : 'desc',
////            ],
////            self::SORT_BY_GUEST_SCORE => [
////                'name' => 'Guest Score',
////                'sort' => $this->by === self::SORT_BY_GUEST_SCORE ? self::SORT_BY_GUEST_SCORE : '',
////                'css-class' => $this->by === self::SORT_BY_GUEST_SCORE ? 'sort-desc sort-desc-only' : 'sort-desc-only',
////                'data-sort' => 'desc',
////            ],
////            self::SORT_BY_STAR_RATING => [
////                'name' => 'Star Rating',
////                'sort' => $this->by === self::SORT_BY_STAR_RATING ? self::SORT_BY_STAR_RATING : '',
////                'css-class' => $this->by === self::SORT_BY_STAR_RATING ? $sortCss : '',
////                'data-sort' => $this->by === self::SORT_BY_STAR_RATING ? $sort : 'desc',
////            ]
////        ];
////    }
//
//    /**
//     * @return array
//     */
//    public static function getAmenities(): array
//    {
//        if (empty(self::$amenitiesCache)) {
//            $amenities = TrPosPlHotels::getActive()
//                ->select('amenities')
//                ->where(['not', 'amenities' => ''])
//                ->column();
//            $amenities2 = TrPosHotels::getActive()
//                ->select('amenities')
//                ->where(['not', 'amenities' => ''])
//                ->column();
//            $amenities = array_merge($amenities, $amenities2);
//            $result = [];
//            foreach ($amenities as $it) {
//                $ar = explode(';', $it);
//                $r = array_merge($result, $ar);
//                $result = $r;
//            }
//            $result = array_unique($result);
//            foreach ($result as $key => $it) {
//                $result[$key] = ucfirst($it);
//            }
//            sort($result);
//            foreach ($result as $v) {
//                if (!empty($v)) {
//                    self::$amenitiesCache[$v] = $v;
//                }
//            }
//        }
//        return self::$amenitiesCache;
//    }
//
//    /**
//     * @return string[]
//     */
//    public static function getCities(): array
//    {
//        $cities = TrPosPlHotels::find()->select('city')->distinct()->asArray()->column();
//        $cities2 = TrPosHotels::find()->select('city')->distinct()->asArray()->column();
//        $cities = array_merge($cities, $cities2);
//        $list = [];
//        foreach ($cities as $city) {
//            if (!empty($city)) {
//                $list[$city] = $city;
//            }
//        }
//        return $list;
//    }
//
//    /**
//     * @return array
//     */
//    public function getSliderPriceRange(): array
//    {
//        $max = TrPosPlHotels::getActive()->select(['max(min_rate) as max'])->column();
//        $max = !empty($max) ? $max[0] : 500;
//        $max = floor(($max + 100) / 100) * 100;
//        $max = ceil($max / 30) * 30;
//        return [
//            'value_from' => $this->priceFrom ?: 0,
//            'value_to'   => $this->priceTo ?: $max,
//            'min'        => 0,
//            'max'        => $max,
//        ];
//    }
//}
