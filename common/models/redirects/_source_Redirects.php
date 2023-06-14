<?php

namespace common\models\redirects;

use Yii;

/**
 * This is the model class for table "redirects".
 *
 * @property int $id
 * @property string $status_code
 * @property string $old_url
 * @property string|null $new_url
 * @property string|null $category
 * @property int|null $item_id
 * @property string|null $created_at
 */
class _source_Redirects extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'redirects';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status_code', 'old_url'], 'required'],
            [['item_id'], 'integer'],
            [['created_at'], 'safe'],
            [['status_code', 'category'], 'string', 'max' => 16],
            [['old_url', 'new_url'], 'string', 'max' => 256],
            [['old_url'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status_code' => 'Status Code',
            'old_url' => 'Old Url',
            'new_url' => 'New Url',
            'category' => 'Category',
            'item_id' => 'Item ID',
            'created_at' => 'Created At',
        ];
    }
}
