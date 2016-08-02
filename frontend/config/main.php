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
                '/'                                                        => 'site/index',
                'sitemap.xml'                                              => 'site/sitemap',
                'user/<account:[\w-]+>'                                    => 'account/view',
                'event/<alias:[\w-]+>'                                     => 'event/view',
                'events/<year:\d+>'                                        => 'event/year',
                'events/<year:\d+>/<month:\d+>'                            => 'event/month',
                'events/<action:[\w-]+>'                                   => 'event/<action>',
                'events/<action:[\w-]+>/<id:\d+>'                          => 'event/<action>',
                'school/<alias:[\w-]+>'                                    => 'school/view',
                'schools/<action:[\w-]+>'                                  => 'school/<action>',
                'schools/<action:[\w-]+>/<id:\d+>'                         => 'school/<action>',
                'list/view/<alias:[\w-]+>'                                 => 'list/view',
                'list/view/<alias:[\w-]+>/<comment:[\w-]+>'                => 'list/view',
                'video/random'                                             => 'site/randomvideo',
                'POST vote/add'                                            => 'vote/add',
                'vote/<action:[\w-]+>/<entity:[\w-]+>/<id:\d+>/<vote:\d+>' => 'vote/<action>',
                '<controller:[\w-]+>/<action:[\w-]+>/<id:[\d+]>'           => '<controller>/<action>',
                '<controller:[\w-]+>/<action:[\w-]+>'                      => '<controller>/<action>',
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
        'yandexDisk'   => [
            'class'  => 'common\components\YandexDiskComponent',
            'key'    => 'yandexDiskMain',
            'keyImg' => 'yandexDisKProzouk',
            'keys'   => [
            ],
        ],
        'google'       => [
            'class'        => 'common\components\GoogleComponent',
            'googleApiKey' => 'AIzaSyDCS1tWgpmOSfalYsWpLHbIy_YVDXu0l5A',
        ],
        'audioInfo'    => [
            'class' => 'common\components\GetidComponent',
        ],
    ],
    'params'              => $params,
];
