<?php

namespace common\models;

use common\tripium\Tripium;
use Exception;
use Yii;
use yii\base\Model;
use yii\helpers\Json;

/**
 * Payment Form
 */
class PaymentForm extends Model
{
    public $card_number;
    public $expiry_date;
    public $cvv_code;
    public $name_card;
    public $street_address_1;
    public $street_address_2;
    public $country;
    public $city;
    public $state;
    public $zip;
    public $axia_id;
    public $same_as_billing;
    public $errors;
    public $modify_request;

    public $coupon_code;

    public function rules(): array
    {
        return [
            [['cvv_code', 'axia_id'], 'required'],
            [['coupon_code'], 'safe'],
            [['cvv_code'], 'string', 'min' => 3, 'max' => 4],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'cvv_code'     => 'CVV Code',
            'card_number' => 'Card Number',
            'expiry_date' => 'Expiry Date',
        ];
    }

    /**
     * Payment by saving card.
     *
     * @return null|string[]
     * @throws Exception
     */
    public function pay(): ?array
    {
        if ($this->validate()) {
            $Basket = TrBasket::build();

            $tripium_id = Custumer::getID();
            $tripium_id = Yii::$app->user->identity && Yii::$app->user->identity->tripium_id ? Yii::$app->user->identity->tripium_id : $tripium_id;

            $service = [
                "cardID" => $this->axia_id,
                "card"   => [
                    "cvv" => $this->cvv_code
                ]
            ];

            if ($r = $Basket->order($tripium_id, $service)) {
                return $r;
            }

            $this->addErrors($Basket->getErrors());
            return null;
        }
        return null;
    }

    /**
     * Return county list
     *
     * @return array
     */
    public function getCountryList(): array
    {
        return [
            "USA",
            "Canada",
            "Other",
        ];
    }

    /**
     * Set Modify Request
     *
     * @var array $array
     */
    public function setModifyRequest($array)
    {
        $this->modify_request = Json::encode($array);
    }

    /**
     * Get Modify Request
     *
     * @return array|null
     */
    public function getModifyRequest(): ?array
    {
        if ($this->modify_request) {
            return Json::decode($this->modify_request);
        }

        return null;
    }

    /**
     * Return coupons list
     *
     * @return Coupon[]
     */
    public function getCoupons(): array
    {
        return (new Tripium())->getCouponsForOrder(
            $this->getModifyRequest()['order'],
            $this->getModifyRequest()['packageId'],
            $this->getModifyRequest()
        );
    }
}
