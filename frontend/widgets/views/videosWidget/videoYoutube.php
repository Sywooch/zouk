<?php
/**
 * @var \common\models\Video $video
 * @var bool $autoPlay
 */
use yii\helpers\Html;

$duration = $video->getDuration();
echo Html::a(
    Html::tag(
        'div',
        Html::tag('span', '', ['class' => 'glyphicon glyphicon-film']) . Html::tag('span', $duration),
        ['class' => 'block-video-duration']
    ) . Html::img($video->getThumbnailUrl(2), ['class' => 'main-video-image-item']),
    $video->original_url,
    [
        'target' => '_blank',
        'class' => 'block-preview-video-link margin-right-10 video-link' . ($autoPlay ? ' auto-play-video' : ''),
        'data-video-id' => $video->entity_id,
        'data-title' => $video->video_title,
    ]
);