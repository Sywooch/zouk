<?php
namespace frontend\widgets;

use common\models\Event;

class EventList extends \yii\bootstrap\Widget
{

    const EVENT_LIST_DISPLAY_MAIN = 'main';
    const EVENT_LIST_DISPLAY_MINI = 'mini';

    const ORDER_BY_ID   = 'order_by_id';
    const ORDER_BY_DATE = 'order_by_date';

    const DATE_CREATE_BEFORE = 'before';
    const DATE_CREATE_AFTER = 'after';
    const DATE_CREATE_ALL = 'all';

    public $lastId = 0;

    public $onlyEvent = false;

    public $orderBy = self::ORDER_BY_ID;

    public $dateCreateType = self::DATE_CREATE_ALL;

    public $userId = false;

    public $display = self::EVENT_LIST_DISPLAY_MAIN;

    public $limit = false;

    public function init()
    {
    }

    public function run()
    {
        $events = $this->getAllEvents($this->lastId, $this->orderBy, $this->dateCreateType, $this->userId, $this->limit);
        return $this->render(
            'eventList/list',
            [
                'events'         => $events,
                'onlyEvent'      => $this->onlyEvent,
                'dateCreateType' => $this->dateCreateType,
                'searchTag'      => $this->searchTag,
                'display'        => $this->display,
            ]
        );
    }

    public function getAllEvents($lastId = 0, $orderBy = self::ORDER_BY_DATE, $dateCreateType = self::DATE_CREATE_ALL, $userId = false, $limit = false)
    {
        $query = Event::find()->from(["t" => Event::tableName()])->andWhere('t.deleted = 0')->addSelect('*');
        if ($lastId != 0) {
            $query = $query->andWhere('t.id < :id', [':id' => $lastId]);
        }
        // Определяем сортировку
        if ($orderBy == self::ORDER_BY_ID) {
            $query = $query->orderBy('id DESC');
        } elseif ($orderBy == self::ORDER_BY_DATE) {
            $query = $query->orderBy('date DESC');
        }

        // Определяем за какой период будем показывать
        if (!empty($limit)) {
            $query = $query->limit((int)$limit);
        } elseif ($dateCreateType == self::DATE_CREATE_ALL) {
            $query = $query->limit(50);
        }

        if (!empty($userId)) {
            $query = $query->andWhere('user_id = :userId', [':userId' => $userId]);
        }

        $query = $query->with(['tagEntity', 'tagEntity.tags']);

        return $query->all();
    }
}