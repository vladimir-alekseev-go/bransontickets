<?php

namespace common\models\form;

use common\models\priceLine\PriceLine;
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

    public function __construct(array $attributes = [], $config = [])
    {
        $this->setModel($attributes['model']);
//        if (isset($attributes['packageId'])) {
//            $this->packageId = $attributes['packageId'];
//        }

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
        if (!$this->getRoomType()) {
            return null;
        }
        return $this->getRoomType()['prices'][0]['nonRefundable'];
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
}
