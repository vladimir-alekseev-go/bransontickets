<?php

namespace common\models\form;

use common\models\TrBasket;
use common\models\Package;
use DateTime;
use Exception;
use frontend\models\SearchHotel;
use yii\base\DynamicModel;

class GeneralReservationForm extends DynamicModel
{
    public const SMOKING_NS = 'NS';
    public const SMOKING_S = 'S';
    public const SMOKING_E = 'E';

    /**
     * @var SearchHotel $searchHotel
     */
    public $searchHotel;

    /**
     * @var string $arrivalDate
     */
    public $arrivalDate;

    /**
     * @var string $departureDate
     */
    public $departureDate;

    /**
     * @var array $rooms
     */
    public $rooms = [];

    /**
     * @var string $special_requests
     */
    public $special_requests;

    /**
     * @var string $packageId
     */
    public $packageId;

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
     * @param int $k
     *
     * @return string
     */
    public static function attributeFirstName($k): string
    {
        return 'room_' . $k . '_first_name';
    }

    /**
     * @param int $k
     *
     * @return string
     */
    public static function attributeLastName($k): string
    {
        return 'room_' . $k . '_last_name';
    }

    /**
     * @param int $k
     *
     * @return string
     */
    public static function attributeSmoking($k): string
    {
        return 'room_' . $k . '_smoking';
    }

    /**
     * Creates attributes
     */
    public function initRooms(): void
    {
        for ($k = 0, $kMax = count($this->rooms); $k < $kMax; $k++) {
            if (!$this->hasAttribute(self::attributeFirstName($k))) {
                $this->defineAttribute(self::attributeFirstName($k));
            }
            if (!$this->hasAttribute(self::attributeLastName($k))) {
                $this->defineAttribute(self::attributeLastName($k));
            }
            $this->defineAttribute('room_' . $k . '_bed_type');
            $this->defineAttribute(self::attributeSmoking($k));
            $this->setAttributes([self::attributeSmoking($k) => self::SMOKING_E]);
        }
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        $ar = [];
        for ($k = 0, $kMax = count($this->rooms); $k < $kMax; $k++) {
            $ar[self::attributeFirstName($k)] = 'Guest First Name';
            $ar[self::attributeLastName($k)] = 'Guest Last Name';
            $ar['room_' . $k . '_bed_type'] = 'Bedding requests';
            $ar[self::attributeSmoking($k)] = 'Smoking requests';
        }
        return $ar;
    }

    /**
     * Return Package
     *
     * @return Package|null
     */
    public function getPackage(): ?Package
    {
        $Basket = TrBasket::build();
        return $Basket->getPackage($this->packageId);
    }

    /**
     * @return array
     */
    public static function getSmokingList(): array
    {
        return [
            self::SMOKING_NS => 'Non-smoking',
            self::SMOKING_S => 'Smoking',
            self::SMOKING_E => 'Either',
        ];
    }

    /**
     * @return int
     */
    public function getDaysCount(): int
    {
        $interval = $this->searchHotel->getArrivalDate()->diff($this->searchHotel->getDepartureDate());
        return (int)$interval->format('%a');
    }
}
