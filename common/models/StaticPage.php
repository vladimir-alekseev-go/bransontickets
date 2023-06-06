<?php

namespace common\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

class StaticPage extends _source_StaticPage
{
    public const INACTIVE = 0;
    public const ACTIVE   = 1;

    /**
     * @return array|null
     * @throws InvalidConfigException
     */
    public static function getMenu(): ?array
    {
        /**
         * @var self[] $pages
         */
        $pages = self::find()->where(['status' => self::ACTIVE])->all();

        $relativeUrl = '/' . Yii::$app->request->getPathInfo();

        $menu = [];

        foreach ($pages as $page) {
            $menu[] = ['label' => $page->title, 'url' => $page->url, 'active' => $relativeUrl === $page->url];
        }

        return $menu;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class'      => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['updated_at', 'created_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value'      => new Expression('NOW()'),
            ],
        ];
    }

    public static function getStatusList(): array
    {
        return [
            self::INACTIVE => 'Inactive',
            self::ACTIVE   => 'Active',
        ];
    }

    public static function getStatusValue($val): string
    {
        $ar = self::getStatusList();

        return $ar[$val] ?? $val;
    }
}
