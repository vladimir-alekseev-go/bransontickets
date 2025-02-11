<?php

namespace common\models\form;

use common\models\Itinerary;
use common\models\TrBasket;
use common\models\TrPosHotels;
use common\tripium\TripiumHotelPrice;
use Exception;
use yii\base\Model;

class HotelReserveForm extends Model
{
    public const SMOKING_NS = 'NS';
    public const SMOKING_S = 'S';
    public const SMOKING_E = 'E';

    /**
     * @var TrPosHotels
     */
    public $model;
    /**
     * @var TripiumHotelPrice
     */
    public $selectedRoomPrice;
    public $firstName;
    public $lastName;
    public $specialRequests;
    public $smoking;

    public static function getSmokingList(): array
    {
        return [
            self::SMOKING_NS => 'Non-smoking',
            self::SMOKING_S => 'Smoking',
            self::SMOKING_E => 'Either',
        ];
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['firstName', 'lastName'], 'required'],
            ['firstName', 'string', 'max' => 200],
            ['lastName', 'string', 'max' => 200],
            ['specialRequests', 'string', 'max' => 1024],
            ['smoking', 'in', 'range' => array_keys(self::getSmokingList())],
        ];
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
     * @param SearchPosHotel $searchHotel
     * @return bool
     */
    public function addToCart(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        try {
            $Basket = TrBasket::build(true);
            if ($Basket->set($this)) {
                return true;
            }
            $this->addErrors($Basket->getErrors());
        } catch (Exception $e) {
            $this->addErrors(['addToCart' => $e->getMessage()]);
        }

        return false;
    }

    /**
     * @param Itinerary      $itinerary
     *
     * @return array
     */
    public function requestAddToBasket($itinerary): array
    {
        $request = [
            'id'       => $this->model->external_id,
            'typeId'   => $this->selectedRoomPrice->schemaId,
            'date'     => $this->selectedRoomPrice->getArrivalDate()->format('m/d/Y'),
            'endDate'  => $this->selectedRoomPrice->getDepartureDate()->format('m/d/Y'),
            'category' => $this->selectedRoomPrice->hotelType === 'Priceline' ? 'hotels' : 'hotel',
            'comments' => $this->specialRequests,
            'tickets'  => [],
        ];
        $request['tickets'][] = [
            'id'        => $this->selectedRoomPrice->id,
            'name'      => $this->selectedRoomPrice->name,
            'coupon'    => ['type' => '$', 'value' => 0],
            "group" => [
                'adults' => $this->selectedRoomPrice->getAge(),
                'childAges' => $this->selectedRoomPrice->getChildren(),
                'firstName' => $this->firstName,
                'lastName'  => $this->lastName,
            ],
            'policy' => $this->selectedRoomPrice->getProductPolicy()
        ];
//        if (!empty($this->packageId)) {
//            $request['packageId'] = $this->packageId;
//        }
//        foreach ($this->getExtras() as $extra) {
//            $request['tickets'][] = [
//                'id'  => $extra->price_external_id,
//                'qty' => $this->{self::attributeNameExtra($extra->price_external_id, $k)}
//            ];
//        }
//echo '<pre>';var_dump($request);exit();
        return $request;
    }
}
