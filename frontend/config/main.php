<?php

use common\events\UserEvents;
use common\models\User;
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
    'timeZone' => 'America/Chicago',
    'components' => [
        'request' => [
            'enableCookieValidation'=>true,
        ],
        'user' => [
            'identityClass' => User::class,
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
            'on ' . \yii\web\User::EVENT_AFTER_LOGIN => [UserEvents::class, 'handleAfterLogin'],
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
                '/'                                                                                => 'site/index',
                'search/'                                                                          => 'site/search',
                'sign-up/'                                                                         => 'site/signup',
                'sign-in/'                                                                         => 'site/login',
                'logout/'                                                                          => 'site/logout',
                'confirm/'                                                                         => 'site/confirm',
                'loginajax/'                                                                       => 'site/loginajax',
                'forgot-pass/'                                                                     => 'site/requestpasswordreset',
                'resetpassword/'                                                                   => 'site/resetpassword',
                'about/'														                   => 'site/about',
                'cookies-policy/'											                       => 'site/cookies-policy',
                'profile/'                                                                         => 'profile/index',
                'cart/'														                       => 'order/cart',
                'order/delete-vatation-package/'						                           => 'order/delete-vatation-package',
                'cart/cancellation-policy'									                       => 'order/cancellation-policy',
                'cart/price-line-terms-conditions'					                               => 'order/price-line-terms-conditions',
                'cart/price-line-privacy-policy'					                               => 'order/price-line-privacy-policy',
                'cart-cancellation-policy-vacation-package'				                           => 'order/cancellation-policy-vacation-package',
                $params['sectionsUrl']['shows'].'/'                                                => 'shows/index',
                $params['sectionsUrl']['shows'].'/popup-compare'                                   => 'shows/popup-compare',
                $params['sectionsUrl']['shows'].'/<code:[\d\w\-]+>'                                => 'shows/detail',
                $params['sectionsUrl']['shows'].'/<code:[\d\w\-]+>/tickets/<date:[\d\-_:]+>'       => 'shows/tickets',
                $params['sectionsUrl']['shows'].'/<code:[\d\w\-]+>/tickets/<date:[\d\- :]+>'       => 'shows/tickets-redirect',
                $params['sectionsUrl']['attractions'].'/'                                          => 'attractions/index',
                $params['sectionsUrl']['attractions'].'/popup-compare'                             => 'attractions/popup-compare',
                $params['sectionsUrl']['attractions'].'/<code:[\d\w\-]+>'                          => 'attractions/detail',
                $params['sectionsUrl']['attractions'].'/<code:[\d\w\-]+>/tickets/<date:[\d\-_:]+>' => 'attractions/tickets',
                $params['sectionsUrl']['attractions'].'/<code:[\d\w\-]+>/tickets/<date:[\d\- :]+>' => 'attractions/tickets-redirect',
                'lodging'                                                                          => 'lodging/index',
                'lodging/popup-compare'                                                            => 'lodging/popup-compare',
                'lodging/<code:[\d\w\-]+>'                                                         => 'lodging/detail',
                'lodging/<code:[\d\w\-]+>/reservation/<hashId:[\d\w\-]+>/'                         => 'lodging/reservation',
                'vacation-packages/'                                                               => 'packages/index',
                'vacation-packages/<code:[\d\w\-]+>'                                               => 'packages/detail',
            ],
        ],
    ],
    'params' => $params,
];
