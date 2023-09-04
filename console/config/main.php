<?php

use yii\log\FileTarget;
use webvimark\modules\UserManagement\UserManagementModule;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'modules'=>[
        'user-management' => [
            'class' => UserManagementModule::class,
            'controllerNamespace'=>'vendor\webvimark\modules\UserManagement\controllers', // To prevent yii help from crashing
        ],
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
	    	'useFileTransport' => false,
	    	'transport' => [
	            'class' => 'Swift_SmtpTransport',
	            'host' => 'smtp.mandrillapp.com',
	            'username' => 'Tripium',
	            'password' => 'myxA2oC35Av6AzVY00Z2qw',
	            'port' => '587',
	            'encryption' => 'tls',
	        ],
        ],
    ],
    'params' => $params,
];
