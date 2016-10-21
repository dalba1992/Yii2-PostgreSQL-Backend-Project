<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

return [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'gii', 'services'],
    'controllerNamespace' => 'app\commands',
    'modules' => [
        'gii' => 'yii\gii\Module',
        'services' => 'app\modules\services\Module',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info', 'error', 'warning', 'trace'],
                    'categories' => ['stripe'],
                    'logFile' => '@app/runtime/logs/api/stripe.log',
                    'maxFileSize' => 1024 * 2,
                    'maxLogFiles' => 20,
                    'logVars' => [],
                    'prefix' => function ($message) {
                        return "";
                    }
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info', 'error', 'warning', 'trace'],
                    'categories' => ['tradegecko'],
                    'logFile' => '@app/runtime/logs/api/tradegecko.log',
                    'maxFileSize' => 1024 * 2,
                    'maxLogFiles' => 20,
                    'logVars' => [],
                    'prefix' => function ($message) {
                        return "";
                    }
                ],
            ],
        ],
        'db' => $db,
        'utils' => [
            'class' => 'app\components\Utils',
        ],
        'tradeGeckoHelper' => [
            'class' => 'app\components\TradeGeckoHelper',
        ],		
        'tradeGecko' => [
            'class' => 'app\components\TradeGecko',
            'url' => 'https://api.tradegecko.com/',
            'clientId' => '4b2a0296aae6d5ad2d11c37a26235e08229c0a688bbcbf594998e9774bf79834',
            'clientSecret' => '38e3f1b9f98da204efcb77a2791db8093089da8bfdae75fa22f0866fc9b64905',
            'token' => '80dfdd40eda11136bfd61c179cbc94c9b8ac177091b52f2412d8b37d81e11880' // Test: 80dfdd40eda11136bfd61c179cbc94c9b8ac177091b52f2412d8b37d81e11880 || Live: a56a8ae4e156e56f5676e84580fb4211ab86c043b7b664961ae1493f0086e962
        ],
        'date' => [
            'class' => 'app\components\DateHelper',
        ],
        'stripe' => [
            'class' => 'app\components\StripeHelper',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
//            'transport' => [
//                'class' => 'Swift_SmtpTransport',
//                'host' => 'ssl://smtp.gmail.com',
//                'username' => 'mail.server@east-wolf.com',
//                'password' => 'udYm*8Y=',
//                'port' => '465',
//            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ]
    ],
    'params' => $params,
];
