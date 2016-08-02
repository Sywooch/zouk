<?php
/**
 * @var Video $video
 */

use common\models\Video;
use frontend\models\Lang;
use yii\bootstrap\Html;
use yii\helpers\Url;

$duration = $video->getDuration();
?>
<div class="text-center margin-bottom">
    <h3><?= Lang::t('main', 'randomVideo') ?></h3>
    <?php
    echo Html::a(
        Html::tag(
            'div',
            Html::tag('span', '', ['class' => 'glyphicon glyphicon-film']) . Html::tag('span', $duration),
            ['class' => 'block-video-duration']
        ) . Html::img($video->getThumbnailUrl(2), ['class' => 'medium-video-image-item']),
        $video->original_url,
        [
            'target'                => '_blank',
            'class'                 => 'block-random-video-link margin-right-10 video-random-link',
            'data-video-id'         => $video->entity_id,
            'data-video-url'        => $video->getVideoUrl(true),
            'data-title'            => $video->video_title,
            'data-random-video-url' => Url::to(['video/random']),
        ]
    );
    ?>
</div>