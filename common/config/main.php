<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                'author/<id_author:\d+>/reviews' => 'review/review/index',
                'review/<action>/<id:\d+>' => 'review/review/<action>',
                'review/<action>' => 'review/review/<action>',
                'city/<action>' => 'city/city/<action>',
                '' => 'review/review/index',
            ]
        ],
    ],
    'modules' => [
        'city' => [
            'class' => 'frontend\modules\city\City',
            'defaultRoute' => 'city',
        ],
        'review' => [
            'class' => 'frontend\modules\review\Review',
            'defaultRoute' => 'review',
        ],
    ],
    'language' => 'ru-RU',
    'timezone' => 'Europe/Moscow',
];