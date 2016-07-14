<?php
/**
 *
 */

use yii\helpers\Html;
use yii\helpers\Url;

$video = \common\models\Video::getRandomVideo();
$duration = $video->getDuration();


?>

<div class="text-center margin-bottom">
    <h3>Случайное видео:</h3>
    <?php
    echo Html::a(
        Html::tag(
            'div',
            Html::tag('span', '', ['class' => 'glyphicon glyphicon-film']) . Html::tag('span', $duration),
            ['class' => 'block-video-duration']
        ) . Html::img($video->getThumbnailUrl(2), ['class' => 'medium-video-image-item']),
        $video->original_url,
        [
            'target' => '_blank',
            'class' => 'block-random-video-link margin-right-10 video-random-link',
            'data-video-id' => $video->entity_id,
            'data-video-url' => $video->getVideoUrl(true),
            'data-title' => $video->video_title,
            'data-random-video-url' => Url::to(['video/random']),
        ]
    );
    ?>
</div>