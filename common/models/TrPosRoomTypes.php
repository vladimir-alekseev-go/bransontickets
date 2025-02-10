<?php

namespace common\models;

use common\models\form\SearchHotel;
use common\tripium\Tripium;
use common\tripium\TripiumUpdater;
use DateInterval;
use DatePeriod;
use yii\db\ActiveQuery;
use yii\helpers\Json;
use common\models\TrPosHotels;

/**
 * @deprecated
 */
class TrPosRoomTypes extends _source_TrPosRoomTypes
{
    public $statusCodeTripium = null;

    /**
     * @return ActiveQuery
     */
    public function getPhotos()
    {
        return $this->hasMany(TrPosHotelsPhotoJoin::class, ['room_type_external_id' => 'id_external']);
    }

    /**
     * @return array
     */
    public function getSourceData()
    {
        $tripium = new Tripium;
        $result = $tripium->getPosHotels();
        $this->statusCodeTripium = $tripium->statusCode;
        return $result;
    }

    /**
     * @return bool
     */
    public function updateFromTripium()
    {
        $dataArray = self::find()->select('id, id_external, id_external_item, hash_summ')->asArray()->all();
        $tmp = [];
        foreach ($dataArray as $data) {
            $tmp[$data['id_external_item'] . '_' . $data['id_external']] = $data;
        }
        $dataArray = $tmp;
        unset($tmp);

        $tripiumData = $this->getSourceData();

        if ($this->statusCodeTripium !== Tripium::STATUS_CODE_SUCCESS) {
            return false;
        }

        foreach ($tripiumData as $data) {
            if ($data['roomTypes']) {
                foreach ($data['roomTypes'] as $roomType) {
                    $dataAdmission = [
                        'id_external' => $roomType['id'],
                        'id_external_item' => $roomType['vendorId'],
                        'name' => trim($roomType['name']),
                    ];

                    $dataAdmission['hash_summ'] = md5(Json::encode($dataAdmission));
                    $hash = $data['id'] . '_' . $roomType['id'];

                    if (empty($dataArray[$hash])) {
                        $model = new self;
                        $model->setAttributes($dataAdmission);
                        $model->save();
                        $TripiumUpdater = new TripiumUpdater(
                            [
                                'models' => [
                                    [
                                        'class' => TrPosHotels::class,
                                        'params' => [
                                            'updateForce' => true,
                                            'updateImages' => true,
                                            'updateOnlyIdExternal' => [$roomType['vendorId']]
                                        ]
                                    ],
                                ]
                            ]
                        );
                        $TripiumUpdater->run();
                    } elseif ($dataAdmission['hash_summ'] !== $dataArray[$hash]['hash_summ']) {
                        $model = self::find()->where(
                            ['id_external' => $roomType['id'], 'id_external_item' => $data['id']]
                        )->one();
                        $model->setAttributes($dataAdmission);
                        $model->save();
                    }
                    unset($dataArray[$hash]);
                }
            }
        }

        // delete
        if (!empty($dataArray)) {
            foreach ($dataArray as $data) {
                self::deleteAll(
                    'id_external = ' . $data['id_external'] . ' and id_external_item = ' . $data['id_external_item']
                );
            }
        }
        return true;
    }

    public function getTrPosHotelsPriceRoomsByFilter(SearchHotel $SearchHotel): ActiveQuery
    {
        $dateFormat = [];
        $range = new DatePeriod($SearchHotel->getArrivalDate(), new DateInterval('P1D'), $SearchHotel->getDepartureDate());
        foreach ($range as $d) {
            $dateFormat[] = $d->format('Y-m-d');
        }
        $subQuery = TrPosHotelsPriceRoom::find()
            ->select(['id_external', 'count' => 'count(id_external)'])
            ->where(['start' => $dateFormat, 'id_external' => $this->id_external])
            ->andFilterWhere(['>=', 'capacity', $SearchHotel->getMinAdultsCount($this->getExternalItem()->one()->min_age)])
            ->groupBy('id_external, name, capacity')
            ->having(['count' => count($dateFormat)]);

        $query = TrPosHotelsPriceRoom::find()
            ->distinct()
            ->select(['capacity', 'name', TrPosHotelsPriceRoom::tableName() . '.id_external', 'price', 'price_external_id'])
            ->where([TrPosHotelsPriceRoom::tableName() . '.id_external' => $this->id_external])
            ->andFilterWhere(['>=', TrPosHotelsPriceRoom::tableName() . '.start', $SearchHotel->getArrivalDate()->format('Y-m-d')])
            ->andFilterWhere(['<', TrPosHotelsPriceRoom::tableName() . '.start', $SearchHotel->getDepartureDate()->format('Y-m-d')]);

        $query->innerJoin(
            '(' . $subQuery->createCommand()->getRawSql() . ') as actual',
            'actual.id_external = ' . TrPosHotelsPriceRoom::tableName() . '.id_external'
        );

        return $query;
    }
}
