<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property int $status
 * @property string|null $username
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $email
 * @property int|null $fb_id
 * @property int|null $tw_id
 * @property string|null $gp_id
 * @property int|null $tripium_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $logined_at
 * @property string|null $auth_key
 * @property string|null $password_hash
 * @property string|null $password_reset_token
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $city
 * @property string|null $zip_code
 * @property string|null $state
 *
 * @property TrBasket[] $trBaskets
 */
class _source_Users extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'created_at', 'updated_at', 'logined_at'], 'required'],
            [['status', 'fb_id', 'tw_id', 'tripium_id'], 'integer'],
            [['created_at', 'updated_at', 'logined_at'], 'safe'],
            [['username', 'first_name', 'last_name', 'email', 'auth_key', 'password_hash', 'password_reset_token', 'city'], 'string', 'max' => 64],
            [['gp_id', 'phone', 'state'], 'string', 'max' => 32],
            [['address'], 'string', 'max' => 128],
            [['zip_code'], 'string', 'max' => 8],
            [['username'], 'unique'],
            [['email'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'username' => 'Username',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'fb_id' => 'Fb ID',
            'tw_id' => 'Tw ID',
            'gp_id' => 'Gp ID',
            'tripium_id' => 'Tripium ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'logined_at' => 'Logined At',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'phone' => 'Phone',
            'address' => 'Address',
            'city' => 'City',
            'zip_code' => 'Zip Code',
            'state' => 'State',
        ];
    }

    /**
     * Gets query for [[TrBaskets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrBaskets()
    {
        return $this->hasMany(TrBasket::class, ['user_id' => 'id']);
    }
}
