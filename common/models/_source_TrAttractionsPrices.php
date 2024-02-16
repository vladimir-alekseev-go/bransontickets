<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tr_attractions_prices".
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
 * @property int $free_sell
 * @property float $price
 * @property int $any_time
 * @property int $price_external_id
 * @property int $rank_level
 * @property float|null $alternative_rate
 *
 * @property TrAdmissions $external
 */
class _source_TrAttractionsPrices extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_attractions_prices';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_external', 'hash', 'hash_summ', 'start', 'name', 'retail_rate', 'available', 'sold', 'stop_sell', 'price_external_id', 'rank_level'], 'required'],
            [['id_external', 'available', 'sold', 'stop_sell', 'free_sell', 'any_time', 'price_external_id', 'rank_level'], 'integer'],
            [['start', 'end'], 'safe'],
            [['retail_rate', 'special_rate', 'tripium_rate', 'price', 'alternative_rate'], 'number'],
            [['hash', 'hash_summ'], 'string', 'max' => 32],
            [['name', 'description'], 'string', 'max' => 128],
            [['id_external'], 'exist', 'skipOnError' => true, 'targetClass' => TrAdmissions::class, 'targetAttribute' => ['id_external' => 'id_external']],
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
            'free_sell' => 'Free Sell',
            'price' => 'Price',
            'any_time' => 'Any Time',
            'price_external_id' => 'Price External ID',
            'rank_level' => 'Rank',
            'alternative_rate' => 'Alternative Rate',
        ];
    }

    /**
     * Gets query for [[External]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExternal()
    {
        return $this->hasOne(TrAdmissions::class, ['id_external' => 'id_external']);
    }
}
