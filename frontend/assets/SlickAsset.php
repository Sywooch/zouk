<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;


class SlickAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.css',
        'css/slick/slick-theme.css',
    ];
    public $js = [
        '//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js',
    ];
    public $depends = [
        AppAsset::class,
        JqueryAsset::class,
    ];
}
