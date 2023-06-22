<?php

namespace common\helpers;

use Yii;

class Template
{
	/**
     * Return property of project
     *
     * @return string
     * @var string $property
     */
	public static function getProperty(string $property): ?string
    {
        return Yii::$app->params["site-template-properties"][$property] ?? null;
//        return Yii::$app->params["site-template"][$property] ?? Yii::$app->params["site-template-properties"][$property] ?? null;
	}
}
