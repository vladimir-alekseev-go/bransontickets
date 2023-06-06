<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "feedback".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property int|null $subject_id
 * @property string $message
 * @property string|null $created_at
 *
 * @property FeedbackSubject $subject
 */
class _source_Feedback extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'feedback';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'email', 'message'], 'required'],
            [['subject_id'], 'integer'],
            [['message'], 'string'],
            [['created_at'], 'safe'],
            [['name', 'email'], 'string', 'max' => 128],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => FeedbackSubject::class, 'targetAttribute' => ['subject_id' => 'id']],
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
            'email' => 'Email',
            'subject_id' => 'Subject ID',
            'message' => 'Message',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(FeedbackSubject::class, ['id' => 'subject_id']);
    }
}
