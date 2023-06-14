<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "site_section".
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property string $section
 * @property int $status
 * @property int $sort
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class _source_SiteSection extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'site_section';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'url', 'section', 'status', 'sort'], 'required'],
            [['status', 'sort'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'url'], 'string', 'max' => 64],
            [['section'], 'string', 'max' => 16],
            [['section'], 'unique'],
            [['url'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'url' => 'Url',
            'section' => 'Section',
            'status' => 'Status',
            'sort' => 'Sort',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
