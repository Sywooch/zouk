<?php
namespace frontend\widgets;

use common\models\Music;
use common\models\Video;
use yii\data\Pagination;

class AddMusicWidget extends \yii\bootstrap\Widget
{

    public function init()
    {
    }

    public function run()
    {
        return $this->render(
            'addMusicWidget/view',
            []
        );
    }

}