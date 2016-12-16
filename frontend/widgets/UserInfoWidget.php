<?php
namespace frontend\widgets;

use common\models\Item;
use yii\bootstrap\Widget;

class UserInfoWidget extends Widget
{

    /** @var Item */
    public $item;

    public function init()
    {
    }

    public function run()
    {
        $author = $this->item->user;
        return $this->render(
            'userInfoWidget/info',
            [
                'item'   => $this->item,
                'author' => $author,
            ]
        );
    }

}