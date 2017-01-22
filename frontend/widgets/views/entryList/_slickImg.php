<?php
/**
 * @var \common\models\Img $img
 */

use yii\helpers\Html;

echo Html::tag(
    'div',
    Html::tag('div', '', ['style' => "background-image:url('{$img->short_url}')", 'class' => 'background-img', 'data-img-url' => $img->short_url]),
    ['class' => 'block-entry-pic hide block-imgs cp']
);