<?php

namespace common\models\priceLine;

use Yii;

/**
 * This is the model class for table "new_price_line_hotels".
 *
 * @property int $id
 * @property int $external_id
 * @property int $status
 * @property string $query
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class _source_NewPriceLineHotels extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'new_price_line_hotels';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['external_id', 'status', 'query'], 'required'],
            [['external_id', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['query'], 'string', 'max' => 1028],
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
            'status' => 'Status',
            'query' => 'Query',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
