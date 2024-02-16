<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tr_pos_hotels_price_room".
 *
 * @property int $id
 * @property int|null $id_external
 * @property string $name
 * @property string $hash
 * @property string $hash_summ
 * @property string $start
 * @property string|null $end
 * @property string|null $description
 * @property float $retail_rate
 * @property float|null $special_rate
 * @property float|null $tripium_rate
 * @property int|null $available
 * @property int|null $sold
 * @property int $stop_sell
 * @property int $free_sell
 * @property int $any_time
 * @property float $price
 * @property int $price_external_id
 * @property int $rank_level
 * @property float|null $alternative_rate
 * @property int|null $capacity
 *
 * @property TrPosRoomTypes $external
 */
class _source_TrPosHotelsPriceRoom extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_pos_hotels_price_room';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_external', 'available', 'sold', 'stop_sell', 'free_sell', 'any_time', 'price_external_id', 'rank_level', 'capacity'], 'integer'],
            [['name', 'hash', 'hash_summ', 'start', 'retail_rate', 'price', 'price_external_id'], 'required'],
            [['start', 'end'], 'safe'],
            [['retail_rate', 'special_rate', 'tripium_rate', 'price', 'alternative_rate'], 'number'],
            [['name', 'description'], 'string', 'max' => 128],
            [['hash', 'hash_summ'], 'string', 'max' => 32],
            [['hash'], 'unique'],
            [['id_external'], 'exist', 'skipOnError' => true, 'targetClass' => TrPosRoomTypes::class, 'targetAttribute' => ['id_external' => 'id_external']],
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
            'name' => 'Name',
            'hash' => 'Hash',
            'hash_summ' => 'Hash Summ',
            'start' => 'Start',
            'end' => 'End',
            'description' => 'Description',
            'retail_rate' => 'Retail Rate',
            'special_rate' => 'Special Rate',
            'tripium_rate' => 'Tripium Rate',
            'available' => 'Available',
            'sold' => 'Sold',
            'stop_sell' => 'Stop Sell',
            'free_sell' => 'Free Sell',
            'any_time' => 'Any Time',
            'price' => 'Price',
            'price_external_id' => 'Price External ID',
            'rank_level' => 'Rank',
            'alternative_rate' => 'Alternative Rate',
            'capacity' => 'Capacity',
        ];
    }

    /**
     * Gets query for [[External]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExternal()
    {
        return $this->hasOne(TrPosRoomTypes::class, ['id_external' => 'id_external']);
    }
}
