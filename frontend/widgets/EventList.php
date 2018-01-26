<?php
namespace frontend\widgets;

use common\models\EntryModel;
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

    public $lastDate = 0;

    public $lastIds = [];

    public $onlyEvent = false;

    public $orderBy = self::ORDER_BY_ID;

    public $dateCreateType = self::DATE_CREATE_ALL;

    public $userId = false;

    public $display = self::EVENT_LIST_DISPLAY_MAIN;

    public $limit = false;

    public $events = false;

    public $statuses = [EntryModel::STATUS_APPROVED];
    
    public function init()
    {
    }

    public function run()
    {
        if ($this->events === false) {
            $events = $this->getAllEvents($this->lastIds, $this->lastDate, $this->orderBy, $this->dateCreateType, $this->userId, $this->limit);
        } else {
            $events = $this->events;
        }
        return $this->render(
            'eventList/list',
            [
                'events'         => $events,
                'onlyEvent'      => $this->onlyEvent,
                'dateCreateType' => $this->dateCreateType,
                'display'        => $this->display,
            ]
        );
    }

    public function getAllEvents($lastIds = [], $lastDate = 0, $orderBy = self::ORDER_BY_DATE, $dateCreateType = self::DATE_CREATE_ALL, $userId = false, $limit = false)
    {
        $query = Event::find()
            ->from(["t" => Event::tableName()])
            ->andWhere([
                't.deleted' => 0,
                't.status' => $this->statuses,
            ])
            ->addSelect('*');
        if ($lastDate != 0) {
            if ($dateCreateType == self::DATE_CREATE_AFTER) {
                $query = $query->andWhere('t.date >= :date', [':date' => $lastDate]);
            } else {
                $query = $query->andWhere('t.date <= :date', [':date' => $lastDate]);
            }
            if (!empty($lastIds)) {
                $query = $query->andWhere(['not in', 'id', $lastIds]);
            }
        }
        // Определяем сортировку
        if ($orderBy == self::ORDER_BY_ID) {
            $query = $query->orderBy('id DESC');
        } elseif ($orderBy == self::ORDER_BY_DATE) {
            if ($dateCreateType == self::DATE_CREATE_AFTER) {
                $query = $query->orderBy('date ASC');
            } else {
                $query = $query->orderBy('date DESC');
            }
        }

        // Определяем за какой период будем показывать
        if (!empty($limit)) {
            $query = $query->limit((int)$limit);
        } elseif ($dateCreateType == self::DATE_CREATE_ALL) {
            $query = $query->limit(20);
        } elseif ($dateCreateType == self::DATE_CREATE_BEFORE) {
            $query = $query->andWhere('t.date <= :date', [':date' => time()])->limit(20);
        } elseif ($dateCreateType == self::DATE_CREATE_AFTER) {
            $query = $query->andWhere('t.date >= :date', [':date' => time()])->limit(20);
        }

        if (!empty($userId)) {
            $query = $query->andWhere('user_id = :userId', [':userId' => $userId]);
        }

        $query = $query->with(['tagEntity', 'tagEntity.tags']);

        return $query->all();
    }
}