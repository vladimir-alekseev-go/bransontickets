<?php

namespace common\models;

class FeedbackSettings extends _source_FeedbackSettings
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                [['email'], 'email']
            ]
        );
    }

    public static function getData()
    {
        return self::find()->asArray()->one();
    }
}
