<?php
namespace frontend\widgets;

use common\models\Video;
use yii\data\Pagination;

class SoundWidget extends \yii\bootstrap\Widget
{

    public $music;

    public function init()
    {
    }

    public function run()
    {
        return $this->render(
            'soundWidget/one',
            [
                'music' => $this->music
            ]
        );
    }
}