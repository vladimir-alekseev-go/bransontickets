<?php

namespace common\models;

use yii\base\Model;

/**
 * Password reset request form
 */
class CustumerForm extends Model
{
    public $first_name;
    public $last_name;
    public $phone;
    public $email;
    public $zip_code;

    public function rules()
    {
        return [
            [['first_name', 'last_name', 'phone', 'email', 'zip_code'], 'required'],
            [['zip_code'], 'integer'],
            [['zip_code'], 'string', 'max' => 6, 'min' => 5],
            [['first_name', 'last_name'], 'match', 'pattern' => '/^([A-z\-\s]+)$/'],
            [['first_name', 'last_name'], 'string', 'min' => 2],
            [['email', 'first_name', 'last_name'], 'string', 'max' => 64],
            [['email'], 'email'],
            [['phone'], 'match', 'pattern' => '/^([\d\-]+)$/'],
            [['phone'], 'string', 'min' => 10, 'max' => 10],
        ];
    }
//
//    public function isAgree($attribute, $params)
//    {
//        if (!$this->agree) {
//            $this->addError($attribute, 'Need to accept terms');
//        }
//    }
//
//    public function isCount($attribute, $params)
//    {
//        if (!$this->count) {
//            $this->addError($attribute, 'Need to take the ticket');
//        }
//    }

    public function register()
    {
        $Custumer = new Custumer();
        $res = $Custumer->create(
            [
                "email"     => $this->email,
                "firstName" => $this->first_name,
                "lastName"  => $this->last_name,
                "phone"     => $this->phone,
                "zipCode"   => $this->zip_code,
            ]
        );

        if (!$res && $Custumer->getErrors()) {
            $this->addErrors($Custumer->getErrors());
        }

        return $res;
    }

    public function update($tripium_id)
    {
        $Custumer = new Custumer();
        $res = $Custumer->create(
            [
                "id"        => $tripium_id,
                "email"     => $this->email,
                "firstName" => $this->first_name,
                "lastName"  => $this->last_name,
                "phone"     => $this->phone,
                "zipCode"   => $this->zip_code,
            ]
        );

        if (!$res && $Custumer->getErrors()) {
            $this->addErrors($Custumer->getErrors());
        }

        return $res;
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $tripium_id = User::getCustomerTripiumID();

        if ($tripium_id) {
            return $this->update($tripium_id);
        }

        return $this->register();
    }
}
