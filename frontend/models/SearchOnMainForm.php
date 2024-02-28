<?php

namespace frontend\models;

use yii\base\Model;
use yii\helpers\Url;

class SearchOnMainForm extends Model
{
    public $title;
    public $dateFrom;
    public $searchType;

    public function formName(): string
    {
        return 's';
    }

    public function rules()
    {
        return [
            [['searchType', 'title'], 'required'],
        ];
    }

    public static function types(): array
    {
        return [
            Url::to(['shows/index'])       => 'Shows',
            Url::to(['attractions/index']) => 'Attractions',
            Url::to(['lodging/index'])     => 'Lodging',
        ];
    }
}
