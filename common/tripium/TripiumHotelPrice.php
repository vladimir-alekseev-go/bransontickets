<?php

namespace common\tripium;

use DateTime;
use Exception;
use yii\base\Model;

/**
 * @property string $hotelType
 * @property string $vendorId
 * @property bool   $supplementary
 * @property string $cover
 * @property string $name
 * @property float  $retailRate
 * @property float  $specialRate
 * @property int    $capacity
 * @property bool   $nonRefundable
 * @property string $schemaId
 */
class TripiumHotelPrice extends Model
{
    private $time;
    private $start;
    private $end;
    private $checkInPolicy;
    private $productPolicy;
    private $groupKey;
    private $policy;

    public $id;
    public $hotelType;
    public $vendorId;
    public $supplementary;
    public $cover;
    public $name;
    public $retailRate;
    public $specialRate;
    public $capacity;
    public $nonRefundable;
    public $schemaId;

    public const GENERAL_FIELDS = [
        'id',
        'time',
        'start',
        'end',
        'checkInPolicy',
        'hotelType',
        'vendorId',
        'cover',
        'name',
        'retailRate',
        'specialRate',
        'capacity',
        'schemaId',
        'productPolicy',
        'groupKey',
        'policy',
    ];

    public const BOOLEAN_FIELDS = [
        'supplementary',
        'nonRefundable',
    ];

    public function loadData($data)
    {
        $this->setFields(self::GENERAL_FIELDS, $data);
        $this->setBooleanFields(self::BOOLEAN_FIELDS, $data);
    }

    private function setFields($fields, $data): void
    {
        foreach ($fields as $field) {
            if (!empty($data[$field])) {
                $this->$field = $data[$field];
            }
        }
    }

    private function setBooleanFields($fields, $data): void
    {
        foreach ($fields as $field) {
            if ($data[$field] !== null) {
                $this->$field = $data[$field];
            }
        }
    }

    public function getHash(): string
    {
        return md5(serialize($this));
    }

    public function getProductPolicy(): ?array
    {
        return $this->productPolicy;
    }

    public function getArrivalDate(): DateTime
    {
        try {
            return new DateTime($this->start);
        } catch (Exception $e) {
            return new DateTime();
        }
    }

    public function getDepartureDate(): DateTime
    {
        try {
            return new DateTime($this->end);
        } catch (Exception $e) {
            return new DateTime();
        }
    }

    public function getAge(): int
    {
        return (int)explode(':', explode(',', $this->groupKey)[0])[1];
    }

    public function getChildren(): array
    {
        $ch = explode(',', $this->groupKey)[1] ?? null;
        if ($ch) {
            $ages = explode(':', $ch)[1] ?? null;
            if ($ages) {
                return explode('_', $ages);
            }
        }
        return [];
    }

    public function getPolicy(): array
    {
        if (is_array($this->policy)) {
            return $this->policy;
        }
        return [];
    }
}
