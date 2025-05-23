<?php

use dmstr\cookieconsent\components\CookieConsentHelper;
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
        'cookieConsentHelper' => [
            'class' => CookieConsentHelper::class
        ],
        'cache' => [
            'class' => FileCache::class,
            'cachePath' => '@common/runtime/cache',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
	    	'useFileTransport' => false,
            'viewPath' => '@common/mail',
	    	/*'transport' => [
	            'class' => 'Swift_SmtpTransport',
	            'host' => 'smtp.mandrillapp.com',
	            'username' => 'Tripium',
	            'password' => 'myxA2oC35Av6AzVY00Z2qw',
	            'port' => '587',
	            'encryption' => 'tls',
	        ],*/
        ],
        'imageProcessor' => [
            'class'          => Component::class,
            'jpegQuality'    => 70,
            'pngCompression' => 9,
            'define' => [
                'servicesBanners' => [
			        'process' => [
			            ['autorotate'],
			            ['resize', 'width' => 1920, 'height' => 400, 'scaleTo' => 'cover'],
			            ['crop', 'width' => 1920, 'height' => 400],
			        ],
			    ],
                'itemsPhotos' => [
			        'process' => [
			            ['autorotate'],
			            ['resize', 'width' => 1920, 'height' => 1024, 'scaleTo' => 'fit', 'only' => 'down'],
			        ],
			    ],
                'itemsPhotosPreview' => [
			        'process' => [
			            ['autorotate'],
			            ['resize', 'width' => 260, 'height' => 180, 'scaleTo' => 'cover'],
			            ['crop', 'width' => 260, 'height' => 180, 'x' => 'center - ' . floor(260/2), 'y' => 'center - ' . floor(180/2)],
			        ],
			    ],
                'itemsPreview' => [
			        'process' => [
			            ['autorotate'],
			            ['resize', 'width' => 260, 'height' => 190, 'scaleTo' => 'cover'],
			            ['crop', 'width' => 260, 'height' => 190, 'x' => 'center - ' . floor(260/2), 'y' => 'center - ' . floor(190/2)],
			        ],
			    ],
				'vacationPackageImage' => [
			        'process' => [
			            ['autorotate'],
			            ['resize', 'width' => 840, 'height' => 380, 'scaleTo' => 'cover'],
			            ['crop', 'width' => 840, 'height' => 380, 'x' => 'center - ' . floor(840/2), 'y' => 'center - ' . floor(380/2)],
			        ],
			    ],
			    'vacationPackagePreview' => [
			        'process' => [
			            ['autorotate'],
			            ['resize', 'width' => 260, 'height' => 180, 'scaleTo' => 'cover'],
			            ['crop', 'width' => 260, 'height' => 180, 'x' => 'center - ' . floor(260/2), 'y' => 'center - ' . floor(180/2)],
			        ],
			    ],
                'showsSeatMap' => [
                    'process' => [
                        ['autorotate'],
                        ['resize', 'width' => 1024, 'height' => 768, 'scaleTo' => 'fit', 'only' => 'down'],
                    ],
                ],
            ]
        ],
    ],
];
