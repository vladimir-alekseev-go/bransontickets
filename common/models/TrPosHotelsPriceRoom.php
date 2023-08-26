<?php

namespace common\models;

use common\tripium\Tripium;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

class TrPosHotelsPriceRoom extends _source_TrPosHotelsPriceRoom
{
    use PricesExtensionTrait;

    public const MAIN_CLASS = TrPosHotels::class;

    public const ANY_TIME = 'Any time';
    public const TYPE_ID = 5;
    /**
     * @deprecated
     */
    public const type = 'hotels_room';
    public const TYPE = 'hotels_room';

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [['end'], 'validateDates'],
                [['name'], 'trim'],
            ]
        );
    }

    /**
     * @param $params
     *
     * @return array
     */
    public function getSourceData($params)
    {
        $tripium = new Tripium;
        $res = $tripium->getPosHotelsPrice($params);
        $this->statusCodeTripium = $tripium->statusCode;
        return $res;
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getMain()
    {
        return $this->hasOne(TrPosHotels::class, ['id_external' => 'id_external_item'])
            ->viaTable(TrPosRoomTypes::tableName(), ['id_external' => 'id_external']);
    }
}
