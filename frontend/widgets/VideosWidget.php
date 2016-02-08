<?php
namespace frontend\widgets;

use common\models\Video;
use yii\data\Pagination;

class VideosWidget extends \yii\bootstrap\Widget
{

    /** @var Video[] $video */
    public $videos;

    public function init()
    {
    }

    public function run()
    {
        return $this->render(
            'videosWidget/list',
            [
                'videos' => $this->videos,
            ]
        );
    }
}