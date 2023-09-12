<?php

namespace common\models;

use yii\base\Model;

class Ticket extends Model
{
    public const SMOKING_NS = 'NS';
    public const SMOKING_S = 'S';
    public const SMOKING_E = 'E';

    private $old_rate;

    public $id;
    public $name;
    public $description;
    public $seats;
    public $qty;
    public $retail_rate;
    public $special_rate;
    public $fit_rate;
    public $tripium_rate;
    public $info;
    public $bed_type_id;
    public $child_ages = [];
    public $first_name;
    public $last_name;
    public $number_of_beds;
    public $smoking_preference;
    public $rate_key;
    public $cancellation;
    public $result_rate;
    public $price;
    public $non_refundable;
    public $confirmation;
    public $supplementary;
    public $pricelineRoomInfo;

    public function loadData($data)
    {
        if (!empty($data['id'])) {
            $this->id = $data['id'];
        }
        if (!empty($data['name'])) {
            $this->name = $data['name'];
        }
        if (!empty($data['description'])) {
            $this->description = $data['description'];
        }
        if (!empty($data['seats'])) {
            $this->seats = $data['seats'];
        }
        if (!empty($data['qty'])) {
            $this->qty = $data['qty'];
        }
        if (isset($data['retailRate'])) {
            $this->retail_rate = number_format($data['retailRate'], 2, '.', '');
        }
        if (!empty($data['specialRate'])) {
            $this->special_rate = number_format($data['specialRate'], 2, '.', '');
        }
        if (isset($data['fitRate'])) {
            $this->fit_rate = number_format($data['fitRate'], 2, '.', '');
        }
        if (!empty($data['tripiumRate'])) {
            $this->tripium_rate = number_format($data['tripiumRate'], 2, '.', '');
        }
        if (!empty($data['info'])) {
            $this->info = $data['info'];
        }
        if (!empty($data['bedTypeId'])) {
            $this->bed_type_id = $data['bedTypeId'];
        }
        if (!empty($data['childAges'])) {
            $this->child_ages = $data['childAges'];
        }
        if (!empty($data['firstName'])) {
            $this->first_name = $data['firstName'];
        }
        if (!empty($data['lastName'])) {
            $this->last_name = $data['lastName'];
        }
        if (!empty($data['numberOfBeds'])) {
            $this->number_of_beds = $data['numberOfBeds'];
        }
        if (!empty($data['smokingPreference'])) {
            $this->smoking_preference = $data['smokingPreference'];
        }
        if (!empty($data['rateKey'])) {
            $this->rate_key = $data['rateKey'];
        }
        if (!empty($data['cancellation'])) {
            $this->cancellation = $data['cancellation'];
        }
        if (!empty($data['nonRefundable'])) {
            $this->non_refundable = $data['nonRefundable'];
        }
        if (!empty($data['confirmation'])) {
            $this->confirmation = $data['confirmation'];
        }
        if (!empty($data['pricelineRoomInfo'])) {
            $this->pricelineRoomInfo = $data['pricelineRoomInfo'];
        }
        $this->supplementary = $data['supplementary'] === true;

        $this->result_rate = number_format($this->special_rate ? $this->special_rate : $this->retail_rate, 2, '.', '');

        if (!empty($data['resultRate'])) {
            $this->result_rate = number_format($data['resultRate'], 2, '.', '');
        }

        if ($this->retail_rate !== $this->result_rate) {
            $this->old_rate = $this->retail_rate;
        }

        $this->price = $this->special_rate > 0 ? $this->special_rate : $this->retail_rate;
    }

    public function getOld_rate()
    {
        return $this->old_rate;
    }

    public function getSaved()
    {
        return $this->special_rate ? ($this->retail_rate - $this->special_rate) : 0;
    }

    /**
     * Return hash of Data
     */
    public function getHashData()
    {
        return md5(
            implode(
                '.',
                [
                    $this->name,
                    $this->description,
                ]
            )
        );
    }

    /**
     * @return string|null
     */
    public function getRoomId(): ?string
    {
        if (!empty($this->pricelineRoomInfo['roomId'])) {
            return $this->pricelineRoomInfo['roomId'];
        }
        return null;
    }

    /**
     * @return int|null
     */
    public function getRoomCapacity(): ?int
    {
        if (!empty($this->pricelineRoomInfo['capacity'])) {
            return (int)$this->pricelineRoomInfo['capacity'];
        }
        return null;
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
     * @return string|null
     */
    public function getSmoking(): ?string
    {
        return self::getSmokingList()[$this->smoking_preference] ?? null;
    }
}
