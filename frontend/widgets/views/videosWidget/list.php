<?php
/**
 * @var \common\models\Video[] $videos
 */

echo '<div class="row"><div class="col-md-12">';
foreach ($videos as $video) {
    if ($video->entity == \common\models\Video::ENTITY_YOUTUBE) {
        echo $this->render('videoYoutube', ['video' => $video]);
    }
}
echo '</div></div>';