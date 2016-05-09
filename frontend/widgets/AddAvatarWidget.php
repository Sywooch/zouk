<?php
namespace frontend\widgets;

use yii\data\Pagination;

class AddAvatarWidget extends \yii\bootstrap\Widget
{

    public function init()
    {
    }

    public function run()
    {
        return $this->render(
            'addAvatarWidget/view',
            []
        );
    }

}