<?php
/**
 * @var \common\models\Video $video
 */

use yii\helpers\Html;

$duration = $video->getDuration();

echo Html::tag(
    'div',
    Html::a(
        Html::tag(
            'div',
            Html::tag('span', '', ['class' => 'glyphicon glyphicon-film']) . Html::tag('span', $duration),
            ['class' => 'block-video-duration']
        )
        . Html::tag(
            'div',
            Html::tag('span', '', ['class' => 'glyphicon glyphicon-play']),
            ['class' => 'block-video-play']
        )
        . Html::tag('div', '', ['style' => "background-image:url('{$video->getThumbnailUrl(2)}')", 'class' => 'background-img']),
        $video->original_url,
        [
            'target' => '_blank',
            'class' => 'video-link',
            'data-video-id' => $video->entity_id,
            'data-title' => $video->video_title,
        ]
    ),
    ['class' => 'block-entry-pic hide']
);