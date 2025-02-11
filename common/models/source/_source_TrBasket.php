<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tr_basket".
 *
 * @property int $id
 * @property string $session_id
 * @property int|null $user_id
 * @property string|null $data
 * @property string $updated_at
 * @property string|null $reserve_at
 * @property int|null $accept_terms
 * @property string|null $coupon_data
 *
 * @property Users $user
 */
class _source_TrBasket extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_basket';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['session_id'], 'required'],
            [['user_id', 'accept_terms'], 'integer'],
            [['data'], 'string'],
            [['updated_at', 'reserve_at'], 'safe'],
            [['session_id'], 'string', 'max' => 36],
            [['coupon_data'], 'string', 'max' => 2048],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'session_id' => 'Session ID',
            'user_id' => 'User ID',
            'data' => 'Data',
            'updated_at' => 'Updated At',
            'reserve_at' => 'Reserve At',
            'accept_terms' => 'Accept Terms',
            'coupon_data' => 'Coupon Data',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }
}
