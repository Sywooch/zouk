<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;


class SoundManagerAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/soundmanager.css',
    ];
    public $js = [
        'js/soundmanager/soundmanager2-nodebug-jsmin.js',
        'js/soundmanager/music.js',
    ];
    public $depends = [
        AppAsset::class,
        JqueryAsset::class,
    ];
}
