<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'instagram' => [
                    'class' => \kotchuprik\authclient\Instagram::class,
                    'clientId' => 'd51f35124f174e6eb3eecad79d1b76eb',
                    'clientSecret' => '6dd1dc8da8834537971d1bb651661bf3',
                ],
            ],
        ],
    ],
];
