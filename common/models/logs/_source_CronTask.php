<?php

namespace common\models\logs;

use Yii;

/**
 * This is the model class for table "cron_task".
 *
 * @property int $id
 * @property string|null $type
 * @property string|null $data
 * @property string|null $status
 * @property string|null $created_at
 * @property string|null $started_at
 * @property string|null $finished_at
 */
class _source_CronTask extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cron_task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['data'], 'string'],
            [['created_at', 'started_at', 'finished_at'], 'safe'],
            [['type', 'status'], 'string', 'max' => 16],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'data' => 'Data',
            'status' => 'Status',
            'created_at' => 'Created At',
            'started_at' => 'Started At',
            'finished_at' => 'Finished At',
        ];
    }
}
