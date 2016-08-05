<?php
/**
 * @var Video[] $videos
 * @var bool   $onlyItem
 * @var string $dateCreateType
 * @var string $searchTag
 * @var string $display
 * @var int    $limit
 */

use common\models\Video;
use frontend\models\Lang;
use frontend\widgets\VideoList;
use frontend\widgets\VideosWidget;


echo VideosWidget::widget([
    'videos' => $videos,
    'autoPlay' => false,
]);