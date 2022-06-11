<?php
return [
    'timeZone' => 'America/Argentina/Tucuman',
    'language' => 'es',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'assetManager' => [
            'linkAssets' => true,
            'appendTimestamp' => true,
        ],
        'jwt' => [
            'class' => 'sizeg\jwt\Jwt',
            'key'   => '6Tc]K85JKffgY1/XcDXC&)oqjz@-7;|8/',
        ],
        'cache' => [
            'class' => 'yii\redis\Cache',
        ],
        'session' => [
            'class' => 'yii\redis\Session',
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'localhost',
            'port' => 6379,
            'database' => 0,
        ],
        'formatter' => [
            'defaultTimeZone' => 'America/Argentina/Tucuman',
            'dateFormat' => 'dd/MM/yyyy',
            'datetimeFormat' => 'dd/MM/yyyy H:mm',
            'decimalSeparator' => ',',
            'thousandSeparator' => '.',
            'locale' => 'es-AR'
        ],
    ],
];
