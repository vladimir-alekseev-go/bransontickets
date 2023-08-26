<?php

namespace common\models\form;

use common\models\TrPosHotels;
use common\models\TrPosHotelsPriceExtra;
use common\models\TrPosHotelsPriceRoom;
use frontend\models\SearchHotel;
use Yii;
use yii\db\ActiveQuery;

class HotelReservationForm extends GeneralReservationForm
{
    /**
     * @var TrPosHotels
     */
    public $model;

    /**
     * @var int $roomPriceExternalId
     */
    public $roomPriceExternalId;

    /**
     * @var bool $isChange
     */
    public $isChange;

    public function __construct(array $attributes = [], $config = [])
    {
        $this->setModel($attributes['model']);
        if (!empty($attributes['roomPriceExternalId'])) {
            $this->setRoomPriceExternalId($attributes['roomPriceExternalId']);
        }
        if (!empty($attributes['packageId'])) {
            $this->packageId = $attributes['packageId'];
        }
        if (isset($attributes['isChange'])) {
            $this->isChange = (bool)$attributes['isChange'];
        }
        $this->searchHotel = new SearchHotel();
        $this->searchHotel->load(Yii::$app->getRequest()->get());
        $this->rooms = $this->searchHotel->room;
        $this->arrivalDate = $this->searchHotel->arrivalDate;
        $this->departureDate = $this->searchHotel->departureDate;

        parent::__construct($attributes, $config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        $firstName = [];
        $lastName = [];
        $ar = [];
        if (count($this->rooms)) {
            for ($k = 0, $kMax = count($this->rooms); $k < $kMax; $k++) {
                $firstName[] = self::attributeFirstName($k);
                $lastName[] = self::attributeLastName($k);
            }
            $ar[] = [array_merge($firstName, $lastName), 'required'];
            $ar[] = [array_merge($firstName, $lastName), 'string', 'max' => 64];
        }
        $ar[] = ['special_requests', 'string', 'max' => 1024];

        for ($k = 0, $kMax = count($this->rooms); $k < $kMax; $k++) {
            foreach ($this->getExtras() as $extra) {
                $ar[] = [self::attributeNameExtra($extra->price_external_id, $k), 'integer'];
            }
        }

        return $ar;
    }

    public function init(): void
    {
        parent::init();

        $this->initRooms();
    }

    /**
     * Creates attributes
     */
    public function initRooms(): void
    {
        parent::initRooms();

        for ($k = 0, $kMax = count($this->rooms); $k < $kMax; $k++) {
            foreach ($this->getExtras() as $extra) {
                $name = self::attributeNameExtra($extra->price_external_id, $k);
                if (!$this->hasAttribute($name)) {
                    $this->defineAttribute($name);
                    $this->setAttributes([$name => 0]);
                }
            }
        }
    }

    /**
     * @param int $id
     * @param int $k
     *
     * @return string
     */
    public static function attributeNameExtra($id, $k): string
    {
        return 'extra_count_' . $k . '_' . $id;
    }

    /**
     * Set model
     *
     * @param TrPosHotels $model
     */
    private function setModel(TrPosHotels $model): void
    {
        $this->model = $model;
    }

    /**
     * @param int $roomPriceExternalId
     */
    private function setRoomPriceExternalId(int $roomPriceExternalId): void
    {
        $this->roomPriceExternalId = $roomPriceExternalId;
    }

    /**
     * @return ActiveQuery
     */
    public function getRoomTypes(): ActiveQuery
    {
        $search = $this->searchHotel;
        return $this->model->getRoomTypes()
            ->distinct()
            ->with(
                [
                    'photos' => static function (ActiveQuery $query) {
                        $query->with(['preview']);
                    }
                ]
            )
            ->innerJoinWith(
                [
                    'trPosHotelsPriceRooms' => static function (ActiveQuery $query) use ($search) {
                        $query->distinct();
                        $query->select(['id_external', 'name', 'price_external_id', 'price', 'capacity']);
                        $query->andWhere(['>=', 'start', $search->getArrivalDate()->format('Y-m-d')]);
                        $query->andWhere(['<', 'start', $search->getDepartureDate()->format('Y-m-d')]);
                    },
                ]
            );
    }

    /**
     * @return ActiveQuery
     */
    public function getRoomType(): ActiveQuery
    {
        return $this->getRoomTypes()->andOnCondition(
            [TrPosHotelsPriceRoom::tableName() . '.price_external_id' => $this->roomPriceExternalId]
        );
    }

    /**
     * @return ActiveQuery
     */
    public function getDays(): ActiveQuery
    {
        return TrPosHotelsPriceRoom::find()
            ->andOnCondition([TrPosHotelsPriceRoom::tableName() . '.price_external_id' => $this->roomPriceExternalId])
            ->andOnCondition(['>=', 'start', $this->searchHotel->getArrivalDate()->format('Y-m-d')])
            ->andOnCondition(['<', 'start', $this->searchHotel->getDepartureDate()->format('Y-m-d')]);
    }

    /**
     * @return ActiveQuery
     */
    public function getExtra(): ActiveQuery
    {
        /**
         * @var TrPosHotelsPriceRoom $trPosHotelsPriceRoom
         */
        $trPosHotelsPriceRoom = TrPosHotelsPriceRoom::find()
            ->select(['id_external'])
            ->where(['price_external_id' => $this->roomPriceExternalId])
            ->one();
        $id_external = $trPosHotelsPriceRoom->id_external ?? null;
        return TrPosHotelsPriceExtra::find()
            ->select(['id_external', 'price_external_id', 'name', 'price'])
            ->distinct()
            ->andOnCondition([TrPosHotelsPriceExtra::tableName() . '.id_external' => $id_external])
            ->andOnCondition(['>=', 'start', $this->searchHotel->getArrivalDate()->format('Y-m-d')])
            ->andOnCondition(['<', 'start', $this->searchHotel->getDepartureDate()->format('Y-m-d')]);
    }

    /**
     * @return TrPosHotelsPriceRoom[]
     */
    public function getExtras(): array
    {
        return $this->getExtra()->all();
    }

    /**
     * @return float
     */
    public function getTotalPrice(): float
    {
        return $this->getDays()->sum('price');
    }

    /**
     * @param int $k
     *
     * @return array
     */
    public function requestAddToBasket(int $k): array
    {
        /**
         * @var TrPosHotelsPriceRoom $price
         */
        $price = TrPosHotelsPriceRoom::find()->where(['price_external_id' => $this->roomPriceExternalId])->one();

        $request = [
            'id' => $this->model->id_external,
            'typeId' => $price->id_external,
            'date' => $this->getArrivalDate()->format('m/d/Y'),
            'endDate' => $this->getDepartureDate()->format('m/d/Y'),
            'category' => TrPosHotels::TYPE,
            'comments' => $this->special_requests,
            'tickets' => [],
        ];
        $request['tickets'][] = [
            'id' => $this->roomPriceExternalId,
            'firstName' => $this->{self::attributeFirstName($k)},
            'lastName' => $this->{self::attributeLastName($k)},
            'qty' => $this->rooms[$k]['adult'],
            'childAges' => !empty($this->rooms[$k]['age']) ? $this->rooms[$k]['age'] : null,
            'coupon' => ['type' => '$', 'value' => 0],
        ];
        if (!empty($this->packageId)) {
            $request['packageId'] = $this->packageId;
        }
        foreach ($this->getExtras() as $extra) {
            $request['tickets'][] = [
                'id' => $extra->price_external_id,
                'qty' => $this->{self::attributeNameExtra($extra->price_external_id, $k)}
            ];
        }
//echo '<pre>';var_dump($request);exit();
        return $request;
    }
}
