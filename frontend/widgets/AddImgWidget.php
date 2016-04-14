<?php
namespace frontend\widgets;

use yii\data\Pagination;

class AddImgWidget extends \yii\bootstrap\Widget
{

    public function init()
    {
    }

    public function run()
    {
        return $this->render(
            'addImgWidget/view',
            []
        );
    }

}