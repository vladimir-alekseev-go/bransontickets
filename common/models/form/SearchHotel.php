<?php

namespace common\models\form;

use common\helpers\General;
use Exception;
use Yii;
use yii\helpers\Json;
/** @deprecated  */
class SearchHotel extends SearchHotelGeneral
{
//    public const ORDER_BY_MARKETING_LEVEL = 'marketing_level';

//    private $orderby;

    public $c;
//    public $by = self::ORDER_BY_MARKETING_LEVEL;
//    public $sortOrder;

//    /**
//     * @return array
//     */
//    public function rules(): array
//    {
//        return array_merge(
//            parent::rules(),
//            [
//                [['arrivalDate', 'departureDate', 'c',], 'safe'],
//                [
//                    'fieldSort',
//                    'filter',
//                    'filter' => static function ($attr) {
//                        return array_key_exists($attr, self::sortList()) ? $attr : self::FIELD_SORT_PRICE_AVERAGE;
//                    }
//                ],
//            ]
//        );
//    }

    /**
     * @return array
     */
    public static function sortList(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'title' => 'Search by hotel name',
            'c' => 'Category',
            'arrivalDate' => 'Arrival Date',
            'departureDate' => 'Departure Date',
            'priceFrom' => 'Price From',
            'priceTo' => 'Price To',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        $range = General::getDatePeriod();

        $this->arrivalDate = $range->start->format('m/d/Y');
        $this->departureDate = $range->end->format('m/d/Y');

        $data = [];
        if (!empty(Yii::$app->session->get('filterHotel'))) {
            try {
                $data = Json::decode(Yii::$app->session->get('filterHotel'));
            } catch (Exception $e) {
            }
        }
        $this->load($data);
    }

    public function load($data, $formName = null): bool
    {
        $res = parent::load($data, $formName);

        $this->validate();

        General::setRange($this->arrivalDate, $this->departureDate);

        $data = $this->getAttributes(['room', 'arrivalDate', 'departureDate']);

        Yii::$app->session->set('filterHotel', Json::encode([$this->formName() => $data]));

        return $res;
    }
}
