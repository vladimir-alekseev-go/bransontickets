<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tr_prices".
 *
 * @property int $id
 * @property int $id_external
 * @property string $hash
 * @property string $hash_summ
 * @property string $start
 * @property string|null $end
 * @property string $name
 * @property string|null $description
 * @property float $retail_rate
 * @property float|null $special_rate
 * @property float|null $tripium_rate
 * @property int $available
 * @property int $sold
 * @property int $stop_sell
 * @property float $price
 * @property int $free_sell
 * @property int $allotment_external_id
 * @property int $price_external_id
 *
 * @property TrShows $external
 */
class _source_TrPrices extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_prices';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_external', 'hash', 'hash_summ', 'start', 'name', 'retail_rate', 'available', 'sold', 'stop_sell', 'allotment_external_id', 'price_external_id'], 'required'],
            [['id_external', 'available', 'sold', 'stop_sell', 'free_sell', 'allotment_external_id', 'price_external_id'], 'integer'],
            [['start', 'end'], 'safe'],
            [['retail_rate', 'special_rate', 'tripium_rate', 'price'], 'number'],
            [['hash', 'hash_summ'], 'string', 'max' => 32],
            [['name', 'description'], 'string', 'max' => 128],
            [['id_external'], 'exist', 'skipOnError' => true, 'targetClass' => TrShows::class, 'targetAttribute' => ['id_external' => 'id_external']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_external' => 'Id External',
            'hash' => 'Hash',
            'hash_summ' => 'Hash Summ',
            'start' => 'Start',
            'end' => 'End',
            'name' => 'Name',
            'description' => 'Description',
            'retail_rate' => 'Retail Rate',
            'special_rate' => 'Special Rate',
            'tripium_rate' => 'Tripium Rate',
            'available' => 'Available',
            'sold' => 'Sold',
            'stop_sell' => 'Stop Sell',
            'price' => 'Price',
            'free_sell' => 'Free Sell',
            'allotment_external_id' => 'Allotment External ID',
            'price_external_id' => 'Price External ID',
        ];
    }

    /**
     * Gets query for [[External]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExternal()
    {
        return $this->hasOne(TrShows::class, ['id_external' => 'id_external']);
    }
}
