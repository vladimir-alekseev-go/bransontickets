<?php

namespace common\models\form;

use common\models\priceLine\PriceLine;
use common\models\TrBasket;
use common\models\TrPosPlHotels;
use common\tripium\Tripium;
use Exception;
use Throwable;
use Yii;

class PlHotelReservationForm extends GeneralReservationForm
{
    /**
     * @var TrPosPlHotels
     */
    public $model;

    /**
     * @var array
     */
//    public $roomTypes = [];

    /**
     * @var int $roomId
     */
    public $roomId;

    /**
     * @var string $ppnBundle
     */
    public $ppnBundle;

    /**
     * @var array $roomType
     */
    private $roomType;

    /**
     * @var bool $agreeOverwriteOrder
     */
    public $agreeOverwriteOrder = false;

    /**
     * @var TrBasket
     */
    public $basket;

    public function __construct(array $attributes = [], $config = [])
    {
        $this->basket = TrBasket::build();
        $this->setModel($attributes['model']);
//        if (isset($attributes['packageId'])) {
//            $this->packageId = $attributes['packageId'];
//        }
        if (!empty($this->basket->hasHotel())) {
            $this->packageId = $this->basket->hasHotel();
        }

        if (isset($attributes['roomId'])) {
            $this->roomId = $attributes['roomId'];
        }
        if (isset($attributes['ppnBundle'])) {
            $this->ppnBundle = $attributes['ppnBundle'];
        }

        parent::__construct($attributes, $config);
    }

    public function init(): void
    {
        parent::init();

        $this->searchHotel = new SearchPlHotel();
        $this->searchHotel->load(Yii::$app->getRequest()->get());
        $this->rooms = $this->searchHotel->room;
        $this->arrivalDate = $this->searchHotel->arrivalDate;
        $this->departureDate = $this->searchHotel->departureDate;

        if (!$this->loadFromPackage()) {
            $this->initRooms();
        }
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
            $extraRequests = [];
            for ($k = 0, $kMax = count($this->rooms); $k < $kMax; $k++) {
                $firstName[] = self::attributeFirstName($k);
                $lastName[] = self::attributeLastName($k);
                $extraRequests[] = self::attributeSmoking($k);
            }
            $ar[] = [array_merge($firstName, $lastName), 'required'];
            $ar[] = [array_merge($firstName, $lastName), 'string', 'max' => 64];
            $ar[] = [$extraRequests, 'safe'];
        }
        $ar[] = ['special_requests', 'string', 'max' => 1024];
        $ar[] = ['ppnBundle', 'string', 'max' => 3000];
        $ar[] = ['agreeOverwriteOrder', 'boolean'];

        return $ar;
    }

    /**
     * Set model
     *
     * @param TrPosPlHotels $model
     */
    private function setModel(TrPosPlHotels $model): void
    {
        $this->model = $model;
    }

    /**
     * @return bool
     */
    public function loadFromPackage(): bool
    {
        if ($this->getPackage() === null || empty($this->getPackage()->getTickets())) {
            return false;
        }

//        $this->rooms = [];
//        foreach ($this->getPackage()->getTickets() as $k => $ticket) {
//            $this->rooms[] = [
//                'adult' => $ticket->qty,
//                'age' => $ticket->child_ages,
//                'children' => $ticket->child_ages ? count($ticket->child_ages) : 0,
//            ];
//        }
        $this->initRooms();

        foreach ($this->getPackage()->getTickets() as $k => $ticket) {
            $this->setAttributes(
                [
                    self::attributeFirstName($k) => $ticket->first_name,
                    self::attributeLastName($k) => $ticket->last_name,
                    self::attributeSmoking($k) => $ticket->smoking_preference
                ]
            );
        }
        $this->setAttributes(
            [
                'packageId' => $this->getPackage()->package_id,
                'special_requests' => $this->getPackage()->getComments(),
            ]
        );
//        $this->arrivalDate = $this->getPackage()->getStartDataTime()->format('m/d/Y');
//        $this->departureDate = $this->getPackage()->getEndDataTime()->format('m/d/Y');

        return true;
    }

    public function load($data, $formName = null)
    {
        $loadResult = parent::load($data, $formName);

        if (!empty($this->packageId)) {
            $this->agreeOverwriteOrder = true;
        }

        return $loadResult;
    }

    /**
     * @return array
     */
    public function getRoomTypes(): array
    {
        if (empty($this->model->roomTypes())) {
            $tripium = new Tripium();
            $hotels = $tripium->getPLHotels(
                $this->searchHotel->getArrivalDate(),
                $this->searchHotel->getDepartureDate(),
                count($this->searchHotel->room),
                $this->searchHotel->getAdultsCount(),
                $this->searchHotel->getChildrenCount(),
                null,
                [$this->model->id_external]
            );
            if (!empty($hotels[0])) {
                $this->model->setPriceLineData($hotels[0]);
            }
        }

        return $this->model->roomTypes();
    }

    /**
     * @return array|null
     */
    public function getRoomType(): ?array
    {
        if (!empty($this->roomType)) {
            return $this->roomType;
        }

        if (!$this->ppnBundle) {
            return null;
        }

        $tripium = new Tripium();
        if ($roomType = $tripium->getPLHotelPrice($this->ppnBundle)) {
            $this->roomType = $roomType;
        }
        return $this->roomType;
    }

    public function isNonRefundable()
    {
        if ($this->getPackage()) {
            return $this->getPackage()->isNonRefundable();
        }
        if (!$this->getRoomType()) {
            return null;
        }
        return $this->getRoomType()['prices'][0]['nonRefundable'];
    }

    /**
     * @return bool
     * @throws Throwable
     */
    public function addToCart(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        try {
            $this->basket = TrBasket::build(true);
            if ($packageId = $this->basket->hasHotel()) {
                $this->basket->removePackage($packageId, false);
            }
            if ($this->basket->set(TrPosPlHotels::TYPE, $this)) {
                return true;
            }
            $this->addErrors($this->basket->getErrors());
        } catch (Exception $e) {
            $this->addErrors(['addToCart' => $e->getMessage()]);
        }

        return false;
    }

    /**
     * @return array
     */
    public function requestAddToBasket(): array
    {
        $request = [
            'id' => $this->model->id_external,
            'date' => $this->getArrivalDate() ? $this->getArrivalDate()->format('m/d/Y') : null,
            'endDate' => $this->getDepartureDate() ? $this->getDepartureDate()->format('m/d/Y') : null,
            'category' => TrPosPlHotels::TYPE,
            'comments' => $this->special_requests,
            'tickets' => [],
            'ppnBundle' => $this->ppnBundle
        ];
        foreach ($this->rooms as $k => $room) {
            $request['tickets'][] = [
                'firstName' => $this->{self::attributeFirstName($k)},
                'lastName' => $this->{self::attributeLastName($k)},
                'smokingPreference' => $this->{self::attributeSmoking($k)},
                'qty' => $this->rooms[$k]['adult'],
                'childAges' => !empty($room['age']) ? $room['age'] : null,
                'coupon' => ['type' => '$', 'value' => 0],
            ];
        }
//        if (!empty($this->packageId)) {
//            $request['packageId'] = $this->packageId;
//        }

        return $request;
    }

    /**
     * @return PriceLine
     */
    public function getPriceLine(): PriceLine
    {
        $priceLine = new PriceLine();
        $priceLine->loadData($this->roomType['prices'][0]['priceline']);
        return $priceLine;
    }

    /**
     * @param array $roomType
     *
     * @return bool
     */
    public function inBasket(array $roomType): bool
    {
        return $this->basket->hasRoomType($roomType);
    }
}
