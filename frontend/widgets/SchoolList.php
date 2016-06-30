<?php
namespace frontend\widgets;

use common\models\School;

class SchoolList extends \yii\bootstrap\Widget
{

    const SCHOOL_LIST_DISPLAY_MAIN = 'main';
    const SCHOOL_LIST_DISPLAY_MINI = 'mini';

    const ORDER_BY_ID        = 'order_by_id';
    const ORDER_BY_DATE      = 'order_by_date';
    const ORDER_BY_LIKE      = 'order_by_like';
    const ORDER_BY_SHOW      = 'order_by_show';
    const ORDER_BY_LIKE_SHOW = 'order_by_like_show';

    const DATE_CREATE_BEFORE = 'before';
    const DATE_CREATE_AFTER = 'after';
    const DATE_CREATE_ALL = 'all';

    public $lastDate = 0;

    public $lastIds = [];

    public $onlySchool = false;

    public $orderBy = self::ORDER_BY_ID;

    public $dateCreateType = self::DATE_CREATE_ALL;

    public $userId = false;

    public $display = self::SCHOOL_LIST_DISPLAY_MAIN;

    public $limit = false;

    public function init()
    {
    }

    public function run()
    {
        $schools = $this->getAllSchools($this->lastIds, $this->lastDate, $this->orderBy, $this->dateCreateType, $this->userId, $this->limit);
        return $this->render(
            'schoolList/list',
            [
                'schools'        => $schools,
                'onlySchool'     => $this->onlySchool,
                'dateCreateType' => $this->dateCreateType,
                'display'        => $this->display,
            ]
        );
    }

    public function getAllSchools($lastIds = [], $lastDate = 0, $orderBy = self::ORDER_BY_LIKE_SHOW, $dateCreateType = self::DATE_CREATE_ALL, $userId = false, $limit = false)
    {
        $query = School::find()->from(["t" => School::tableName()])->andWhere('t.deleted = 0')->addSelect('*');
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
        } elseif ($dateCreateType == self::DATE_CREATE_ALL) {
            $query = $query->limit(100);
        } elseif ($dateCreateType == self::DATE_CREATE_BEFORE) {
            $query = $query->andWhere('t.date <= :date', [':date' => time()])->limit(100);
        } elseif ($dateCreateType == self::DATE_CREATE_AFTER) {
            $query = $query->andWhere('t.date >= :date', [':date' => time()])->limit(100);
        }

        if (!empty($userId)) {
            $query = $query->andWhere('user_id = :userId', [':userId' => $userId]);
        }

        $query = $query->with(['tagEntity', 'tagEntity.tags']);

        return $query->all();
    }
}