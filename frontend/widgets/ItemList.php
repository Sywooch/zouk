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
        $condition = [];

        $query = Item::find();
        $pagination = new Pagination([
            'defaultPageSize' => 5,
            'totalCount'      => $query->count(),
        ]);

        return $query->orderBy('id')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->where('id > :id', [':id' => $lastId])
            ->all();
    }
}