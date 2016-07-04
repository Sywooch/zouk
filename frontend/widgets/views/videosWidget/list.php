<?php
/**
 * @var \common\models\Video[] $videos
 */

use frontend\widgets\ModalDialogsWidget;

echo '<div class="row"><div class="col-md-12">';
foreach ($videos as $video) {
    if ($video->entity == \common\models\Video::ENTITY_YOUTUBE) {
        echo $this->render('videoYoutube', ['video' => $video]);
    }
}
echo '</div></div>';

echo ModalDialogsWidget::widget(['action' => ModalDialogsWidget::ACTION_MODAL_SHOW_VIDEO]);
