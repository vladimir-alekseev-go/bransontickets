<?php

namespace common\models;

use Exception;
use Yii;
use common\components\ExpiryDateValidator;

class PaymentFormAddCard extends PaymentForm
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
        	[['card_number', 'expiry_date','cvv_code', 'name_card'], 'required'],
        	[['name_card', 'street_address_1', 'street_address_2', 'country', 'city', 'state'], 'string'],
        	[['same_as_billing'], 'safe'],
        	[['expiry_date'], ExpiryDateValidator::class],
            [['cvv_code'], 'string', 'min' => 3, 'max' => 4],
            [['zip'], 'number'],
        ];
    }

    /**
     * Payment by new card.
     *
     * @return null|array
     * @throws Exception
     */
 	public function pay(): ?array
    {
        if ($this->validate()) {
            $Basket = TrBasket::build();
            /**
             * @var User $user
             */
            $user = Yii::$app->user->identity;

            $tripium_id = Custumer::getID();

        	$service = [
                "card" => [
                    "cardNumber" => str_replace(" ", "", $this->card_number),
                    "expiration" => str_replace(["/", " "], "", $this->expiry_date),
                    "cvv"        => $this->cvv_code
                ],
    			"billing" => [
			        "country" => !empty($this->getCountryList()[$this->country]) ? $this->getCountryList()[$this->country] : '',
			        "address1" => $this->street_address_1,
			        "address2" => $this->street_address_2,
			        "city" => $this->city,
			        "state" => $this->state,
			        "zipCode" => $this->zip,
			    ],
        	    "accountHolder" => $this->name_card,
        	];

        	if ($this->same_as_billing && $user) {
        		$service["billing"]["firstName"] = $user->first_name;
        		$service["billing"]["lastName"] = $user->last_name;
        		$service["billing"]["state"] = $user->state;
        		$service["billing"]["city"] = $user->city;
        		$service["billing"]["zip"] = $user->zip_code;
        		$service["billing"]["address1"] = $user->address;
        		$service["billing"]["contacts"]["phone"] = $user->phone;
        		$service["billing"]["contacts"]["email"] = $user->email;
        	}

        	$tripium_id = $user !== null && !empty($user->tripium_id) ? $user->tripium_id : $tripium_id;

        	if ($r = $Basket->order($tripium_id, $service)) {
        		return $r;
        	}

            $this->errors = $Basket->errors;
            $this->addErrors($Basket->getErrors());
            return null;
        }
        return null;
    }
}
