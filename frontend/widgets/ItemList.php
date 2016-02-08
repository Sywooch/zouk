<?php
namespace frontend\widgets;

use common\models\Item;
use yii\data\Pagination;

class ItemList extends \yii\bootstrap\Widget
{
    public function init()
    {
    }

    public function run()
    {
        $items = $this->getAllItems();

        return $this->render(
            'itemList/list',
            [
                'items' => $items,
            ]
        );
    }

    public function getAllItems($lastId = 0)
    {
        $query = Item::find();

        return $query->orderBy('id DESC')
            ->limit(10)
            ->where('id > :id', [':id' => $lastId])
            ->all();
    }
}