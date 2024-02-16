<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tr_geo_hotels".
 *
 * @property int $id
 * @property string $destination_id
 * @property string $description
 * @property int $active
 * @property string $hash_summ
 */
class _source_TrGeoHotels extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_geo_hotels';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['destination_id', 'description', 'active', 'hash_summ'], 'required'],
            [['active'], 'integer'],
            [['destination_id', 'hash_summ'], 'string', 'max' => 64],
            [['description'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'destination_id' => 'Destination ID',
            'description' => 'Description',
            'active' => 'Active',
            'hash_summ' => 'Hash Summ',
        ];
    }
}
