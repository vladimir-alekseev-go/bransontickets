<?php

use phtamas\yii2\imageprocessor\Component;
use yii\caching\FileCache;

defined('DEFAULT_TIMEZONE') or define('DEFAULT_TIMEZONE', 'America/Chicago');
date_default_timezone_set(DEFAULT_TIMEZONE);

return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'name'       => 'Branson Tickets',
    'timeZone'   => DEFAULT_TIMEZONE,
    'components' => [
        'cache' => [
            'class' => FileCache::class,
            'cachePath' => '@common/runtime/cache',
        ],
        'imageProcessor' => [
            'class'          => Component::class,
            'jpegQuality'    => 70,
            'pngCompression' => 9,
        ],
    ],
];
