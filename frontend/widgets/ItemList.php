<?php
namespace frontend\widgets;

use common\models\Item;
use common\models\TagEntity;
use common\models\Tags;
use yii\data\Pagination;

class ItemList extends \yii\bootstrap\Widget
{

    const ITEM_LIST_DISPLAY_MAIN = 'main';
    const ITEM_LIST_DISPLAY_MINI = 'mini';

    const ORDER_BY_ID        = 'order_by_id';
    const ORDER_BY_LIKE      = 'order_by_like';
    const ORDER_BY_SHOW      = 'order_by_show';
    const ORDER_BY_LIKE_SHOW = 'order_by_like_show';

    const DATE_CREATE_LAST  = 'last';
    const DATE_CREATE_WEEK  = 'week';
    const DATE_CREATE_MONTH = 'month';
    const DATE_CREATE_ALL   = 'popular';

    public $lastId = 0;

    public $onlyItem = false;

    public $orderBy = self::ORDER_BY_ID;

    public $dateCreateType = self::DATE_CREATE_LAST;

    public $searchTag = "";

    public $userId = false;

    public $display = self::ITEM_LIST_DISPLAY_MAIN;

    public $limit = false;

    public function init()
    {
    }

    public function run()
    {
        $items = $this->getAllItems($this->lastId, $this->orderBy, $this->dateCreateType, $this->searchTag, $this->userId, $this->limit);
        return $this->render(
            'itemList/list',
            [
                'items'          => $items,
                'onlyItem'       => $this->onlyItem,
                'dateCreateType' => $this->dateCreateType,
                'searchTag'      => $this->searchTag,
                'display'        => $this->display,
            ]
        );
    }

    public function getAllItems($lastId = 0, $orderBy = self::ORDER_BY_ID, $dateCreateType = self::DATE_CREATE_LAST, $searchTag = "", $userId = false, $limit = false)
    {
        $query = Item::find()->from(["t" => Item::tableName()])->andWhere('t.deleted = 0')->addSelect('*');
        if ($lastId != 0) {
            $query = $query->andWhere('t.id < :id', [':id' => $lastId]);
        }
        // Определяем сортировку
        if ($orderBy == self::ORDER_BY_ID) {
            $query = $query->orderBy('id DESC');
        } elseif ($orderBy == self::ORDER_BY_LIKE) {
            $query = $query->orderBy('like_count DESC');
        } elseif ($orderBy == self::ORDER_BY_SHOW) {
            $query = $query->orderBy('show_count DESC');
        } elseif ($orderBy == self::ORDER_BY_LIKE_SHOW) {
            $query = $query->addSelect(['(like_count * 15 + show_count) as like_show_count'])->orderBy('like_show_count DESC');
        }
        // Определяем за какой период будем показывать
        if (!empty($limit)) {
            $query = $query->limit((int)$limit);
        } elseif ($dateCreateType == self::DATE_CREATE_LAST) {
            $query = $query->limit(10);
        } elseif ($dateCreateType == self::DATE_CREATE_ALL) {
            $query = $query->limit(50);
        } elseif ($dateCreateType == self::DATE_CREATE_WEEK) {
            $query = $query->andWhere('t.date_create >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 1 WEEK))');
        } elseif ($dateCreateType == self::DATE_CREATE_MONTH) {
            $query = $query->andWhere('t.date_create >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 1 MONTH))');
        }

        if (!empty($userId)) {
            $query = $query->andWhere('user_id = :userId', [':userId' => $userId]);
        }

        if ($searchTag != "") {
            $tags = Tags::findAll(['name' => $searchTag]);

            $tagsId = [];
            foreach ($tags as $tag) {
                $tagsId[] = (int)$tag->id;
            }

            if (count($tagsId) > 0) {
                $query = $query
                    ->andWhere('(SELECT COUNT(*) as tagCount FROM `' . TagEntity::tableName() . '` te WHERE te.entity = "' . TagEntity::ENTITY_ITEM . '" AND te.entity_id = t.id  AND te.tag_id IN (' . join(',', $tagsId) . ')) > 0');
            }
        }
        $query = $query->with(['videos', 'tagEntity', 'tagEntity.tags']);

        return $query->all();
    }
}