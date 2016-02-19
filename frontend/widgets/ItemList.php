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
        return Item::find()->orderBy('id DESC')
            ->limit(10)
            ->from(["t" => Item::tableName()])
            ->where('t.id > :id', [':id' => $lastId])
            ->joinWith(['videos', 'tagEntity', 'tagEntity.tags'])
            ->all();
    }
}