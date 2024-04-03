<?php

namespace frontend\models;

use DateTime;
use yii\base\Model;

class ScheduleForm extends Model
{
    private $date;

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['date', 'trim'],
            [['date'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'], //xss protection
        ];
    }

    /**
     * @param string $date
     * @return void
     */
    public function setDate($date = null)
    {
        try {
            $this->date = new DateTime($date);
        } catch (\Exception $e) {}
    }

    /**
     * @return null|DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}
