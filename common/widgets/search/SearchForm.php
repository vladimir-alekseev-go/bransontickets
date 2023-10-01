<?php

namespace common\widgets\search;

use Yii;
use yii\base\Model;

class SearchForm extends Model
{
    public $q;

    public function formName()
    {
        return '';
    }

    public function rules()
    {
        return [
            [['q'], 'required'],
            ['q', 'safe'],
            ['q', 'trim'],
            ['q', 'string', 'min'=>3],
            [['q'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'], //xss protection
        ];
    }

    public function attributeLabels()
    {
        return [
            'q' => 'The search phrase',
        ];
    }
}
