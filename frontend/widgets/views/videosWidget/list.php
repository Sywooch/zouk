<?php
/**
 * @var \common\models\Video[] $videos
 * @var bool $autoPlay
 * @var bool $onlyVideos
 */

use frontend\widgets\ModalDialogsWidget;

echo '<div class="row"><div class="col-md-12">';
foreach ($videos as $video) {
    if ($video->entity == \common\models\Video::ENTITY_YOUTUBE) {
        echo $this->render('videoYoutube', ['video' => $video, 'autoPlay' => $autoPlay]);
    }
}
echo '</div></div>';

if (!$onlyVideos) {
    echo ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_SHOW_VIDEO]);
}
