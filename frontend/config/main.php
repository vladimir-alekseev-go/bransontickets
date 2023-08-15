<?php

use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\BootstrapPluginAsset;
use yii\web\AssetManager;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'assetManager' => [
            'class'   => AssetManager::class,
            'bundles' => [
                BootstrapAsset::class       => [
                    'css' => [
                        'https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css',
                    ],
                ],
                BootstrapPluginAsset::class => [
                    'js' => [
                        'https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js',
                    ],
                ]
            ]
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
        	'showScriptName'=>false,
      		'suffix' => '/',
            'rules' => [
                ['pattern' => 'favicon', 'route' => 'site/favicon', 'suffix' => '.ico'],
                '/'													      => 'site/index',
                'sign-up/'												  => 'site/signup',
                'sign-in/'												  => 'site/login',
                'logout/'												  => 'site/logout',
                $params['sectionsUrl']['shows'].'/<code:[\d\w\-]+>'       => 'shows/detail',
                $params['sectionsUrl']['attractions'].'/<code:[\d\w\-]+>' => 'attractions/detail',
            ],
        ],
    ],
    'params' => $params,
];
