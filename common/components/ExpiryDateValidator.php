<?php

namespace common\components;

use yii\validators\Validator;

class ExpiryDateValidator extends Validator
{
    public function init(): void
    {
        parent::init();
        $this->message = 'Invalid expiry date.';
    }

    public function validateAttribute($model, $attribute): void
    {
        if (strlen($model->$attribute) === 7) {
            $month = (int)substr($model->$attribute, 0, 2);
            $year = (int)substr($model->$attribute, 5, 2);

            if ($year < (int)date('y')) {
                $model->addError($attribute, $this->message);
            } elseif ($month > 12) {
                $model->addError($attribute, $this->message);
            } elseif ($year === (int)date('y') && $month < (int)date('m')) {
                $model->addError($attribute, $this->message);
            }
        } else {
            $model->addError($attribute, $this->message);
        }
    }
}
