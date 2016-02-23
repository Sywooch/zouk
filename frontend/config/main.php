<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id'                  => 'app-frontend',
    'basePath'            => dirname(__DIR__),
    'bootstrap'           => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components'          => [
        'user'         => [
            'identityClass'   => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'log'          => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager'   => [
            'enablePrettyUrl'     => true,
            'showScriptName'      => false,
            'enableStrictParsing' => true,
            'class'               => 'frontend\components\LangUrlManager',
            'rules'               => [
                '/'                                                            => 'site/index',
                'list/view/<alias:[\w-]+>'                                     => 'list/view',
                'POST vote/add'                                                => 'vote/add',
                'vote/<action:[\w-]+>/<entity:[\w-]+>/<id:[\d+]>/<vote:[\d+]>' => 'vote/<action>',
                '<controller:[\w-]+>/<action:[\w-]+>/<id:[\d+]>'               => '<controller>/<action>',
                '<controller:[\w-]+>/<action:[\w-]+>'                          => '<controller>/<action>',
            ],
        ],
        'request'      => [
            'class' => 'frontend\components\LangRequest',
        ],
        'language'     => 'ru-RU',
        'i18n'         => [
            'translations' => [
                '*' => [
                    'class'          => 'yii\i18n\PhpMessageSource',
                    'basePath'       => '@frontend/messages',
                    'sourceLanguage' => 'pseudo',
                    'fileMap'        => [],
                ],
            ],
        ],
        'translate'    => [
            'class'           => 'frontend\components\Translate',
            'translations'    => [
                '*' => [
                    'class'          => 'yii\i18n\PhpMessageSource',
                    'basePath'       => '@frontend/messages',
                    'sourceLanguage' => 'pseudo',
                    'fileMap'        => [],
                ],
            ],
            'defaultLanguage' => [
                "ru",
                "en",
            ],
        ],
        'cloudinary'   => [
            'class'  => 'common\components\CloudinaryComponent',
            'params' => [
                "cloud_name" => "dxommcjde",
                "api_key"    => "659157529696934",
                "api_secret" => "gkCIstEVyTmxPngwCwz9bEtYiQk",
            ],
        ],
    ],
    'params'              => $params,
];
