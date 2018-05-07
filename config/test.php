<?php

/**
 * @var $db array
 * @var $components array
 * @var $params array
 */
require __DIR__ . '/init.php';

$db = yii\helpers\ArrayHelper::merge(
    $db,
    require __DIR__ . '/test_db.php'
);

$components = require __DIR__ . '/components.php';
$components['token']['availableTokenIds'] = [2];

/**
 * Application configuration shared by all test types
 */
return [
    'id' => 'basic-tests',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'language' => 'en-US',
    'components' => [
        'mailer' => [
            'useFileTransport' => true,
        ],
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'user' => [
            'identityClass' => 'app\models\User',
        ],
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
        ],
    ] + $db + $components,
    'params' => $params,
];
