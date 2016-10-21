<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'tb-concierge',
    'name'=>'TrendyButler - Concierge',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'version' => '1.1',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'false',
            'enableCsrfValidation'=>false,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'message' => [
            'class' => 'app\components\Helper',
        ],
        'data'=>[
            'class'=>'app\components\DataHelper'
        ],
        'utils' => [
            'class' => 'app\components\Utils',
        ],
        'tradeGeckoHelper' => [
            'class' => 'app\components\TradeGeckoHelper',
        ],
        'date' => [
            'class' => 'app\components\DateHelper',
        ],
        'stripe' => [
            'class' => 'app\components\StripeHelper',
	    ],
        'orderstats' => [
            'class' => 'app\components\OrderStats'

        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager'=> [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'members/<status:(active|declined|paused|cancelled|confirming|register|signup)>'=> '/members/index',
                'member/update-stripe-status'=> '/members/update-stripe-status',

                'members/list/<status>' => 'members/list',
                'inventory/list/<shop>' => 'inventory/list',

                'services/tradegecko/list/<status>' => 'services/tradegecko/list',
                'inventory/delete-variant/<variantId:\d+>' => 'inventory/variantdelete',
                'stripe/monthly-charges' => 'api/charges-log/index'
            ]
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_AWSTransport',
                'accessKeyId' => $params['sesmail']['accesskey'],
                'secretKey' => $params['sesmail']['secretkey'],
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logVars' => [null],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['api'],
                    'logFile' => '@app/runtime/logs/api/api.log',
                    'maxFileSize' => 1024 * 2,
                    'maxLogFiles' => 20,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['stripe'],
                    'logFile' => '@app/runtime/logs/api/stripe.log',
                    'maxFileSize' => 1024 * 2,
                    'maxLogFiles' => 20,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['stripe_dump'],
                    'logFile' => '@app/runtime/logs/api/stripe_dump.log',
                    'maxFileSize' => 1024 * 2,
                    'maxLogFiles' => 20,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['liteview'],
                    'logFile' => '@app/runtime/logs/api/liteview.log',
                    'maxFileSize' => 1024 * 2,
                    'maxLogFiles' => 20,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['tradegecko'],
                    'logFile' => '@app/runtime/logs/api/tradegecko.log',
                    'maxFileSize' => 1024 * 2,
                    'maxLogFiles' => 20,
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'tradeGecko' => [
            'class' => 'app\components\TradeGecko',
            'url' => 'https://api.tradegecko.com/',
            'clientId' => $params['tradegecko']['clientId'],
            'clientSecret' => $params['tradegecko']['clientSecret'],
            'token' => $params['tradegecko']['token']
        ],
        'liteview' => [
            'class' => 'app\components\Liteview',
            'username' => 'demo', // live: trendybutler
            'appKey' => 'demo user appkey', // live: fb1a6ef987ca93ec2f7abb8fd4113f6f
            'debug' => true
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ]
    ],
    'modules' => [
        'api' => [
            'class' => 'app\modules\api\Module'
        ],
        'services' => [
            'class' => 'app\modules\services\Module',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*']
    ];

    $config['bootstrap'][] = 'gii';
    //  $config['modules']['gii'] = 'yii\gii\Module';
    $config['modules']['gii']=[
        'class' =>  'yii\gii\Module',
        'allowedIPs' => ['*'],
    ];
}

return $config;
