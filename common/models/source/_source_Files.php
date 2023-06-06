<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "files".
 *
 * @property int $id
 * @property string $dir
 * @property string $path
 * @property string $file_name
 * @property string $file_source_name
 * @property int $file_source_time
 * @property string|null $file_source_url
 * @property string|null $created_at
 */
class _source_Files extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dir', 'path', 'file_name', 'file_source_name'], 'required'],
            [['file_source_time'], 'integer'],
            [['created_at'], 'safe'],
            [['dir'], 'string', 'max' => 32],
            [['path'], 'string', 'max' => 64],
            [['file_name', 'file_source_name'], 'string', 'max' => 128],
            [['file_source_url'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dir' => 'Dir',
            'path' => 'Path',
            'file_name' => 'File Name',
            'file_source_name' => 'File Source Name',
            'file_source_time' => 'File Source Time',
            'file_source_url' => 'File Source Url',
            'created_at' => 'Created At',
        ];
    }
}
