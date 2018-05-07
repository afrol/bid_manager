<?php

return [
    'cache' => [
        'class' => 'yii\caching\FileCache',
    ],
    'yandex' => [
        'class' => 'app\components\YandexDirect\ApiYandex',
    ],
    'token' => [
        'class' => 'app\components\Token\TokenManager',
        'classModel' => 'app\models\ApiToken',
        'availableTokenIds' => [1, 2, 3, 4, 5],
    ],
    'log' => [
        'traceLevel' => YII_DEBUG ? 3 : 0,
        'flushInterval' => 1,
        'targets' => [
            'sentry' => [
                'class' => 'app\components\log\SentryTarget',
                'dsn' => '',
                'levels' => ['error', 'warning'],
                'context' => true,
                'exportInterval' => 1,
            ],
            'db' => [
                'class' => 'app\components\log\BidTarget',
                'levels' => ['info'],
                'categories' => ['bidLog'],
                'logTable' => 'log_bid',
                'db' => $db['db'],
                'logVars' => [],
                'exportInterval' => 1,
                'prefix' => function ($message) {
                    list($text) = $message;
                    return $text['ad_group_id'] ?? null;
                }
            ],
            'schedule' => [
                'class' => 'yii\log\DbTarget',
                'levels' => ['info', 'warning', 'error'],
                'categories' => [
                    'schedule',
                    'scheduleUP',
                    'scheduleDown',
                    'scheduleCreate',
                    'scheduleApi',
                    'scheduleError',
                    'grepBid',
                    'grepGroup',
                    'grepCampaign',
                    'scheduleEmpty',
                    'scheduleBidNotFound',
                    'scheduleTest',
                ],
                'logTable' => 'log_schedule',
                'db' => $db['db'],
                'logVars' => [],
                'exportInterval' => 1,
                'prefix' => function ($message) {
                    list($text) = $message;
                    return $text['ad_group_id'] ?? null;
                }
            ],
        ],
    ],
];
