<?php

namespace common\models;

use common\tripium\Tripium;

abstract class TrPosHotelsPrice extends _source_TrPosHotelsPriceExtra
{
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
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getMain()
    {
        return $this->hasOne(TrPosHotels::class, ['id_external' => 'id_external_item'])
            ->viaTable(TrPosRoomTypes::tableName(), ['id_external' => 'id_external']);
    }
}
