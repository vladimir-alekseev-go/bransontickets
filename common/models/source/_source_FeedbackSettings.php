<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "feedback_settings".
 *
 * @property int $id
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $email
 */
class _source_FeedbackSettings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'feedback_settings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['address'], 'string', 'max' => 256],
            [['phone'], 'string', 'max' => 64],
            [['email'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'address' => 'Address',
            'phone' => 'Phone',
            'email' => 'Email',
        ];
    }
}
