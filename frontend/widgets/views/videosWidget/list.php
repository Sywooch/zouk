<?php
/**
 * @var \common\models\Video[] $videos
 */

echo '<div class="row">';
foreach ($videos as $video) {
    if ($video->entity == \common\models\Video::ENTITY_YOUTUBE) {
        echo $this->render('videoYoutube', ['video' => $video]);
    }
}
echo '</div>';