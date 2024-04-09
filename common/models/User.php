<?php

namespace common\models;

use common\tripium\Tripium;
use Yii;
use yii\base\Exception;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;

/**
 * User model
 */
class User extends _source_Users implements IdentityInterface
{
    public const STATUS_INACTIVE = 0;
    public const STATUS_ACTIVE   = 1;
    public const STATUS_REGISTER = 2;

    public const STATE_USA    = 'US';
	public const STATE_CANADA = 'CA';
	public const STATE_OTHER  = '';

	public const SCENARIO_PROFILE = 'profile';

    public $withoutTripium = false;
    public $withoutBasket  = false;

    public const FACEBOOK = 'facebook';
    public const TWITTER  = 'twitter';
    public const GOOGLE   = 'google';

	public function behaviors()
    {
       return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at', 'logined_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fb_id', 'tw_id', 'gp_id', 'tripium_id'], 'integer'],
        	['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_REGISTER]],
            [['updated_at', 'created_at', 'logined_at', 'city'], 'safe'],
            [['zip_code'], 'integer'],
            [['zip_code'], 'string', 'min' => 5, 'max' => 6],
            [['phone', 'state'], 'string', 'max' => 32],
            [['email', 'username', 'password_hash','password_reset_token','first_name', 'last_name'], 'string', 'max' => 64],
            [['address'], 'string', 'max' => 128],
            [['email', 'username'], 'unique'],
            [['email', 'username'], 'trim'],
            ['email', 'email'],
            [['phone'], 'match', 'pattern' => '/^([\d\-]+)$/', 'on' => self::SCENARIO_PROFILE],
            [['phone'], 'string', 'min'=>10, 'max'=>10, 'on' => self::SCENARIO_PROFILE],
            [['first_name', 'last_name'], 'match', 'pattern' => '/^([A-zА-я\-\s]+)$/', 'on' => self::SCENARIO_PROFILE],
            [['first_name', 'last_name'], 'string', 'min'=>2, 'on' => self::SCENARIO_PROFILE],
            [['first_name', 'last_name', 'email', 'phone', 'zip_code'], 'required', 'on' => self::SCENARIO_PROFILE],

        ];
    }

	public function beforeSave($insert)
	{
		$save = [];
		$save["email"] = $this->email;
		$save["firstName"] = $this->first_name;
		$save["lastName"] = $this->last_name;
		$save["phone"] = $this->phone;
		$save["address"] = $this->address;
		$save["city"] = $this->city;
		$save["state"] = $this->state;
		$save["zipCode"] = $this->zip_code;

		if ($this->getOldAttribute("email") && $this->email != $this->getOldAttribute("email")) {
			if ($this->fb_id || $this->tw_id || $this->gp_id) {
				$this->addError("email", "You can't change email, it has connection with social network");
			} else {
				$this->username = $this->email;
			}
		}

		if (!$this->withoutTripium) {
			if (!$this->tripium_id) {
				$tripium = new Tripium;
		    	$result = $tripium->postCustomer($save);

		    	if (!empty($result["id"])) {
		    		$this->tripium_id = $result["id"];
		    	}
			} else {
				$tripium = new Tripium;
				$saveTripium = $save;
				$saveTripium["id"] = $this->tripium_id;
		    	$result = $tripium->postCustomer($saveTripium);
			}

	    	if (!empty($result["errors"]["email"])) {
	    		$this->addError("email", $result["errors"]["email"][0]);
	    	}

	    	if (!$this->isNewRecord) {
	    	    if (!empty($result["errors"]["phone"])) {
                    $this->addError("phone", $result["errors"]["phone"][0]);
                }
	    	    if (!empty($result["errors"]["firstName"])) {
                    $this->addError("first_name", $result["errors"]["firstName"][0]);
                }
	    	    if (!empty($result["errors"]["lastName"])) {
                    $this->addError("last_name", $result["errors"]["lastName"][0]);
                }
	    	    if (!empty($result["errors"]["address"])) {
                    $this->addError("address", $result["errors"]["address"][0]);
                }
	    	    if (!empty($result["errors"]["city"])) {
                    $this->addError("city", $result["errors"]["city"][0]);
                }
	    	    if (!empty($result["errors"]["state"])) {
                    $this->addError("state", $result["errors"]["state"][0]);
                }
	    	    if (!empty($result["errors"]["zipCode"])) {
                    $this->addError("zip_code", $result["errors"]["zipCode"][0]);
                }
	    	}
		}

    	if ($this->getErrors()) {
    		return false;
    	}
		return parent::beforeSave($insert);
	}

	public function afterSave($insert, $data)
	{
		parent::afterSave($insert, $data);

		// set the connect between basket with user
		//if (!$this->withoutBasket) {
		//	$b = new Basket;
		//	$b->setForUser($this->id);
		//}
		// updating orders of users
//		if(!$this->withoutTripium && $this->tripium_id)
//		{
//			$Orders = new Orders;
//			$Orders->updateFromTripium($this->tripium_id);
//		}
	}

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     *
     * @param mixed $token
     * @param null  $type
     *
     * @return void|IdentityInterface|null
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
    	return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
    	if (!$this->password_hash) {
            return false;
        }

        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     *
     * @throws Exception
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public static function socialAuth($client): bool
    {
    	$name = $client->getName();

        if ($name === self::FACEBOOK) {
            return self::authByFacebook($client);
        }

        if ($name === self::TWITTER) {
            return self::authByTwitter($client);
        }

        if ($name === self::GOOGLE) {
            return self::authByGoogle($client);
        }

        return false;
    }

    public static function authByGoogle($client): bool
    {
        $attributes = $client->getUserAttributes();

        /**
         * @var User $u
         */
        $u = self::find()->where(["gp_id" => $attributes["id"]])->one();

        if ($u) {
            return $u->authById();
        }

        $u = self::find()->where(["email" => $attributes["email"]])->one();
        if ($u) {
            $u->withoutTripium = true;
            $u->setAttributes(
                [
                    "status"     => (int)$u->status === self::STATUS_INACTIVE
                        ? self::STATUS_INACTIVE : self::STATUS_ACTIVE,
                    "gp_id"      => $attributes["id"],
                    "first_name" => empty($u->first_name) ? $attributes["given_name"] : $u->first_name,
                    "last_name"  => empty($u->last_name) ? $attributes["family_name"] : $u->last_name,
                ]
            );
            $u->save();
        } else {
            $u = new self();
            $u->setAttributes(
                [
                    "status"     => self::STATUS_ACTIVE,
                    "gp_id"      => $attributes["id"],
                    "first_name" => $attributes["given_name"],
                    "last_name"  => $attributes["family_name"],
                    "username"   => $attributes["email"],
                    "email"      => $attributes["email"],
                ]
            );
            $u->save();
        }
        return $u->authById();
    }

    public static function authByFacebook($client): bool
    {
        $attributes = $client->getUserAttributes();

        /**
         * @var User $u
         */
        $u = self::find()->where(["fb_id" => $attributes["id"]])->one();

        if ($u) {
            return $u->authById();
        }

        if (!empty($attributes['email'])) {
            $u = self::find()->where(["email" => $attributes["email"]])->one();
            if ($u) {
                $u->fb_id = $attributes["id"];
                $u->save();
                return $u->authById();
            }
        } else {
            $attributes["email"] = self::FACEBOOK . $attributes["id"] . '@auto-generated.com';
        }

        $u = new self();
        $name = explode(" ", $attributes["name"]);
        $u->load(
            [
                "User" => [
                    "status"     => self::STATUS_ACTIVE,
                    "fb_id"      => $attributes["id"],
                    "first_name" => $name[0],
                    "last_name"  => $name[1],
                    "username"   => $attributes["email"],
                    "email"      => $attributes["email"],
                ]
            ]
        );
        $u->save();

        return $u->authById();
    }

    public static function authByTwitter($client): bool
    {
        $attributes = $client->getUserAttributes();

        /**
         * @var User $u
         */
        $u = self::find()->where(["tw_id" => $attributes["id"]])->one();

        if (!$u) {
            $u = new self();
            $name = explode(" ", $attributes["name"]);
            $u->load(
                [
                    "User" => [
                        "status"     => self::STATUS_ACTIVE,
                        "tw_id"      => $attributes["id"],
                        "first_name" => $name[0],
                        "last_name"  => $name[1],
                        "username"   => "twitter_" . $attributes["id"],
                    ]
                ]
            );
            $u->save();
            return $u->authById($u);
        }

        return $u->authById($u);
    }

    public function authById($rememberMe = true): bool
    {
        if (Yii::$app->user->login($this, $rememberMe ? 3600 * 24 * 3 : 0)) {
            return true;
        }

        return false;
    }

    /**
     * @param bool $force
     *
     * @return User|null
     */
    public static function getCurrentUser($force = false): ?User
    {
		if (!Yii::$app->user->isGuest) {
			if ($force) {
			    Yii::$app->user->identity->refresh();
			}
			return Yii::$app->user->identity;
		}
		return null;
	}

	public static function getCustomerTripium()
	{
		if (!Yii::$app->user->isGuest) {
		    return Yii::$app->user->identity;
		}

        return Custumer::get();
    }

	public static function getCustomerTripiumID()
	{
		if (!Yii::$app->user->isGuest) {
		    return Yii::$app->user->identity->tripium_id;
		}

        return Custumer::getID();
    }

	public function isProfileComplete()
	{
	    $model = clone $this;
		$model->scenario = self::SCENARIO_PROFILE;
    	return $model->validate();
	}

	public static function getStateList(): array
	{
		return [
			self::STATE_USA => 'USA',
			self::STATE_CANADA => 'Canada',
			self::STATE_OTHER => 'Other',
		];
	}

	public static function getStateValue($val): string
	{
		$ar = self::getStateList();

		return $ar[$val] ?? $val;
	}

	public function isSocialRegister()
	{
		if (!$this->id) {
            return null;
        }

		if ($this->fb_id || $this->tw_id || $this->gp_id) {
            return true;
        }

        return false;
    }

	public function updateFromTripium($user_id, $recreate = false)
	{
		$Tripium = new Tripium;

		$user = self::findOne($user_id);

		if (!$user) {
            return false;
        }

		$res = $Tripium->getCustomer($user->tripium_id);

		if (!$res) {
            return false;
        }

		$user->first_name = $res['firstName'];
		$user->last_name = $res['lastName'];
		$user->address = $res['address'];
		$user->city = $res['city'];
		$user->state = $res['state'];
		$user->zip_code = $res['zipCode'];
		$user->phone = $res['phone'];
		$user->email = $res['email'];
		if ($dirty = $user->getDirtyAttributes()) {
			$user->save();
		}

		if (!empty($res['errorCode']) && $res['errorCode'] == Tripium::CUSTOMER_WAS_NOT_FOUND && $recreate) {
			$Custumer = new Custumer;
			$res = $Custumer->reCreate($user_id);
		}

		return $res;
	}

    public static function getStatusList()
    {
        return [
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_ACTIVE => 'Active',
        ];
    }

    public static function getStatusValue($val)
    {
        return self::getStatusList()[$val] ?? $val;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        $address = ['', '', ''];

        if (trim($this->address) !== '') {
            $address[0] = $this->address;
        }
        if (trim($this->city) !== '') {
            $address[1] .= $this->city . ' ';
        }
        if (trim($this->state) !== '') {
            $address[1] .= $this->state . ' ';
        }
        if (trim($this->zip_code) !== '') {
            $address[1] .= $this->zip_code;
        }
        $address[2] = $this->state !== self::STATE_OTHER ? self::getStateValue($this->state) : '';

        $address = array_filter(
            $address,
            static function ($item) {
                return !empty($item);
            }
        );

        return implode(', ', $address);
    }
}
