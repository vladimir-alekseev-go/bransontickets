<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tr_orders".
 *
 * @property int $id
 * @property int $tripium_user_id
 * @property string $data
 * @property string $hash_summ
 * @property string $order_number
 * @property string $created_at
 * @property int $past
 * @property float|null $discount
 * @property float|null $coupon
 * @property string|null $updated_at
 * @property string|null $sdc_vouchers
 * @property string|null $created
 */
class _source_TrOrders extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tripium_user_id', 'data', 'hash_summ', 'order_number', 'created_at', 'past'], 'required'],
            [['tripium_user_id', 'past'], 'integer'],
            [['data', 'sdc_vouchers'], 'string'],
            [['created_at', 'updated_at', 'created'], 'safe'],
            [['discount', 'coupon'], 'number'],
            [['hash_summ'], 'string', 'max' => 32],
            [['order_number'], 'string', 'max' => 16],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tripium_user_id' => 'Tripium User ID',
            'data' => 'Data',
            'hash_summ' => 'Hash Summ',
            'order_number' => 'Order Number',
            'created_at' => 'Created At',
            'past' => 'Past',
            'discount' => 'Discount',
            'coupon' => 'Coupon',
            'updated_at' => 'Updated At',
            'sdc_vouchers' => 'Sdc Vouchers',
            'created' => 'Created',
        ];
    }
}
