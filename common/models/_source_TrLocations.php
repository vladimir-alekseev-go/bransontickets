<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tr_locations".
 *
 * @property int $id
 * @property int $external_id
 * @property string $name
 * @property string|null $description
 * @property string|null $hash_summ
 */
class _source_TrLocations extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_locations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['external_id', 'name'], 'required'],
            [['external_id'], 'integer'],
            [['name'], 'string', 'max' => 128],
            [['description'], 'string', 'max' => 2048],
            [['hash_summ'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'external_id' => 'External ID',
            'name' => 'Name',
            'description' => 'Description',
            'hash_summ' => 'Hash Summ',
        ];
    }
}
