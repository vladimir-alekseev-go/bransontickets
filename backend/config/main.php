<?php

use yii\i18n\PhpMessageSource;
use webvimark\modules\UserManagement\components\UserConfig;
use webvimark\modules\UserManagement\models\UserVisitLog;
use yii\log\FileTarget;
use webvimark\modules\UserManagement\UserManagementModule;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id'                  => 'app-backend',
    'basePath'            => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap'           => ['log'],
    'modules'             => [
        'user-management' => [
            'class'           => UserManagementModule::class,

            'on beforeAction' => static function (yii\base\ActionEvent $event) {
                if ($event->action->uniqueId === 'user-management/auth/login') {
                    $event->action->controller->layout = 'loginLayout.php';
                }
            },
        ],
    ],
    'components' => [
        'urlManager'   => array(
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
            'rules'           => array(
                ''      => 'site/index',
                'login' => 'site/login',
            ),
        ),
        'user'         => [
            'class'         => UserConfig::class,

            // Comment this if you don't want to record user logins
            'on afterLogin' => static function ($event) {
                UserVisitLog::newVisitor($event->identity->id);
            }
        ],
        /*'session' => [
            'name' => '_backendSessionId',
            'cookieParams' => [
                'domain' => $params['domain'],
            ],
        ],*/
        'log'          => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'  => FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
//        'view'         => [
//            'theme' => [
//                'pathMap' => [
////                    '@webvimark/modules/UserManagement/views/layouts' => '@backend/views/layouts'
////                    '@app/views' => '@vendor/dmstr/yii2-adminlte-asset/example-views/yiisoft/yii2-app'
//                ],
//            ],
//        ],
        'i18n'         => [
            'translations' => [
                'back*' => [
                    'class'            => PhpMessageSource::class,
                    //'basePath' => '@app/messages',
                    'sourceLanguage'   => 'en-EN',
                    'forceTranslation' => true,
                    'fileMap'          => [
                        'back' => 'back.php',
                        'app'  => 'app.php',

                    ],
                ],
            ]
        ],
    ],
    'params' => $params,
];
