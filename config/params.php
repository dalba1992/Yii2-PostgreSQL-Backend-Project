<?php

return [
    'adminEmail' => 'admin@example.com',
    'campaignEmail' => [
        'concierge@trendybutler.com' => 'Trendy Butler Concierge'
    ],
    'stripe' => [
        'stripesecretkey'  => 'sk_test_UBGIMQxnENiwpB78R2LKNdkw',
        'stripepublishkey' => 'pk_test_1N0uiYrdbk6ZtAlZCyS3oFOc'
    ],
    'defaultRole' => 'admin',

    'sesmail' => [
        'accesskey' => 'AKIAJ4CNFH5GNAP426YQ',
        'secretkey' => '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'
    ],
    'subscription_plan' => '55',  //$55 Subscription Price
    'coupon' => ['amount_off' =>'1000', // Discount Amount in Cents
        'percent_off' =>''    // currently no use on trendy so null
    ] ,

    'staticPath' => dirname(__DIR__)."/data/static/",
    'orderStepsPath'=> 'images/order-steps/',
    'avatarPath' => 'images/users/',

    'tradegecko' => [
        //'token' => 'a56a8ae4e156e56f5676e84580fb4211ab86c043b7b664961ae1493f0086e962',
        'clientId' => '3f1c716c67e5f371f2b6f84005b9472016b83c5ab7dba339ce7a77c363440627',
        'clientSecret' => '77693ab9e3679b065cd8ac6065d5d5334f0b20757afd1da159a101d9de72b373',
        'token' => 'd92d23f4f2bd30ab67bb25420d59972351d02495dc74de4f26ce7f40c185aaf1',
        'shipping_charge' => '13.50'
    ],

    'liteview' => [
        'username' => 'demo',
        'apikey' => '97bb6271771bc2d6970d7ded6409bfed',
    ],
    'tradegeckoapi'=>[
        '0'=>'236c41d1c88f9d4a1b814c1db9669c8294c43db944f49af7de3bee0b9a25581f',
        '1'=>'c98c9e5098a57d4601a24fec718cd8967586b11f03e31fd0e7835876c0bdc737',
        '2'=>'0e2aeba42311646706550a622ba8327dee6bf1d18b30743aa2a9c94664bb7fb5',
        '3'=>'2d6d22d28589159055f5f53beeb325e336907f608f761e12d403534d9ba2db2a',
        '4'=>'a05b05acc15594df6e8c7764f285eff4af09c40d94fb3878dffb1d7d1af616f5',
        '5'=>'6d89130532823ca8835782f977a2d3dcada3de47387cf31297ccf3947c848149',
        '6'=>'4e1660749bf2055e015500f9fb933dce508bf4e62f6a246dc09fe1959cfe75b7',
        '7'=>'9a38884332094b1ffcd7f7f88113bce33f824de7de17cfcac4c4a677732dff92',
        '8'=>'dc343f5821e28b8f284450189c840822bd3a18ff2fcafac0ffbdbbee60cc1f45',
        '9'=>'d2dd9106aa3c699acffd6af4c3ce7f9b5eb2d988beb8111db5a6c8637aeabcdd',
    ],

    'topTypesKeywords' => [
        'shirt',
        'top',
        'button',
        'jacket',
    ],
    'topSizeTypesKeywords' => [
        'sm' => [
            'small'
        ],
        'med' => [
            'medium'
        ],
        'lrg' => [
            'large'
        ],
        'xl' => [
            'x-large'
        ],
        'xxl' => [
            '2x'
        ],
        'xxxl' => [],
        'xs' => []
    ],
    'bottomTypesKeywords' => [
        'bottom',
        'pants',
        'shorts',
        'jeans'
    ],

    //@todo delete tradegecko dev mode option and dependent code for production server. This is a rather primitive, but only option for testing at this point (June 04, 2015)
    'tgDevMode' => false, // set this to true when testing

    'orderConfirmationEmail'    => ['office@trendy-butler.com'=>'Trendy Butler'], // for emails sent when an order is created at tradegecko
    'shippingConfirmationEmail' => ['office@trendy-butler.com'=>'Trendy Butler'], // for emails sent when an order is sent to liteview for packing
];
