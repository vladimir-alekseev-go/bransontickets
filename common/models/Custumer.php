<?php

namespace common\models;

use Yii;
use yii\base\Model;

use common\tripium\Tripium;

class Custumer extends Model
{
    public $tripium_id;
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $address;
    public $city;
    public $state;
    public $zip_code;
    public $country;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tripium_id', 'first_name', 'last_name', 'phone', 'email', 'zip_code'], 'required'],
            [['tripium_id'], 'integer'],
            [['first_name', 'last_name'], 'match', 'pattern' => '/^([A-z\-\s]+)$/'],
            [['first_name', 'last_name'], 'string', 'min'=>2],
            [['email', 'first_name', 'last_name'], 'string', 'max' => 64],
            ['email', 'email'],
            [['state'], 'string', 'max' => 32],
            [['phone'], 'match', 'pattern' => '/^([\d\-]+)$/'],
            [['phone'], 'string', 'min'=>10, 'max'=>10],
            [['address'], 'string', 'max' => 128],
            [['zip_code'], 'integer'],
            [['zip_code'], 'string', 'max' => 6, 'min' => 5],
        ];
    }

    /**
     * Get Custumer from session
     * @return Custumer
     */
    public static function get()
    {
        $Custumer = new Custumer();
        $Custumer->setAttributes(Yii::$app->session->get("tripium_custumer"));
        return $Custumer;
    }

    /**
     * Get Customer id from session
     * @return int
     */
    public static function getID()
	{
        return self::get()->tripium_id;
	}

	/**
	 * Create user on the POS
	 * @param [] $data
	 * @param int $user_id
	 * @return bool | []
	 */
	public function create($data, $user_id = null)
	{
		$tripium = new Tripium;
		$res = $tripium->postCustomer($data);

		if (!empty($res["globalErrors"])) {
    		if (is_array($res["globalErrors"])) {
    			$res["globalErrors"] = array_shift($res["globalErrors"]);
    		}
    		if (is_array($res["globalErrors"])) {
    			$res["globalErrors"] = array_shift($res["globalErrors"]);
    		}
    		$this->addError("errors",$res["globalErrors"]);
    		return false;
    	} else {
			Yii::$app->session->set("tripium_custumer", [
			    "tripium_id" => $res["id"],
			    "first_name" => $res["firstName"],
			    "last_name" => $res["lastName"],
			    "email" => $res["email"],
			    "phone" => $res["phone"],
			    "address" => $res["address"],
			    "city" => $res["city"],
			    "state" => $res["state"],
			    "zip_code" => $res["zipCode"],
			    "country" => $res["country"],
			]);

			if ($user_id) {
				$User = User::find()->where(['id' => $user_id])->one();
				$User->tripium_id = $res["id"];
				$User->save();
				User::getCurrentUser(true);
			}

    		return $res;
    	}
	}

	/**
	 * Re create user on the POS
	 * @param int $user_id
	 * @return bool | []
	 */
	public function reCreate($user_id = null)
	{
		if (!$user_id) {
			$user = User::getCurrentUser();
			$userCustomerTripium = User::getCustomerTripium();
			$user_id = $user["id"];
		} else {
		    $userCustomerTripium = User::find()->where(['id' => $user_id])->one();
		}

		return $this->create([
    		"email" => $userCustomerTripium->email,
			"firstName" => $userCustomerTripium->first_name,
			"lastName" => $userCustomerTripium->last_name,
			"phone" => $userCustomerTripium->phone,
			"address" => $userCustomerTripium->address,
			"city" => $userCustomerTripium->city,
			"state" => $userCustomerTripium->state,
			"zipCode" => $userCustomerTripium->zip_code,
			"country" => $userCustomerTripium->country,
    	], $user_id);
	}
}
