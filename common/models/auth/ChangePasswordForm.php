<?php

namespace common\models\auth;

use common\models\User;
use yii\base\Model;
use Yii;

/**
 * Change Password Form
 */
class ChangePasswordForm extends Model
{
    public $password;
    public $password_new;
    public $password_repeat;

    public function rules()
    {
        return [
            [['password_new', 'password_repeat'], 'string', 'min' => 6],
//            [['password', 'password_new', 'password_repeat'], 'required'],
            [['password', 'password_new', 'password_repeat'], 'checkAll'],
            ['password_repeat', 'compare', 'compareAttribute' => 'password_new', 'message' => "Passwords don't match"],
        ];
    }

    public function checkAll($attribute, $params)
    {
        $user = User::getCurrentUser();

        if ($user && $user->password_hash && empty($this->attributes["password"])) {
            $this->addError("password", "Need to fill");
        }
        if (empty($this->attributes["password_new"])) {
            $this->addError("password_new", "Need to fill");
        }
        if (empty($this->attributes["password_repeat"])) {
            $this->addError("password_repeat", "Need to fill");
        }

        if (!empty($this->attributes["password"])) {
            $user = User::getCurrentUser();

            if ($user->password_hash && !$user->validatePassword($this->attributes["password"])) {
                $this->addError("password", "Password is wrong!");
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'password'        => 'Current Password',
            'password_new'    => 'New Password',
            'password_repeat' => 'Confirm New Password',
        ];
    }

    public function changePassword()
    {
        if ($this->validate()) {
            $user = User::getCurrentUser();

            if ($user && $user->password_hash && empty($this->attributes["password"])) {
                return false;
            }

            $user->setPassword($this->password_new);
            $user->generateAuthKey();
            if ($user->save()) {
                return true;
            }
        }

        return false;
    }
}
