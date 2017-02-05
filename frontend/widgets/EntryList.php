<?php
namespace frontend\widgets;

use common\models\Event;
use common\models\form\SearchEntryForm;
use common\models\Item;
use common\models\School;
use common\models\TagEntity;
use common\models\Tags;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use yii\db\ActiveQuery;

class EntryList extends \yii\bootstrap\Widget
{
    const ITEM_LIST_DISPLAY_MAIN = 'main';
    const ITEM_LIST_DISPLAY_MINI = 'mini';

    const ORDER_BY_ID        = 'order_by_id';
    const ORDER_BY_LIKE      = 'order_by_like';
    const ORDER_BY_SHOW      = 'order_by_show';
    const ORDER_BY_LIKE_SHOW = 'order_by_like_show';
    const ORDER_BY_DATE      = 'order_by_date';

    const DATE_CREATE_LAST  = 'last';
    const DATE_CREATE_WEEK  = 'week';
    const DATE_CREATE_MONTH = 'month';
    const DATE_CREATE_ALL   = 'popular';

    public $page = 0;

    public $onlyItem = false;

    public $orderBy = self::ORDER_BY_ID;

    public $dateCreateType = self::DATE_CREATE_LAST;

    /** @var SearchEntryForm */
    public $searchEntryForm = null;

    public $userId = false;

    public $display = self::ITEM_LIST_DISPLAY_MAIN;

    public $limit = false;

    public $addModalShowVideo = false;

    public $addModalShowImg = true;

    public $entityTypes = [Item::THIS_ENTITY, Event::THIS_ENTITY, School::THIS_ENTITY];

    public $blockAction = '';

    private $_tagsId = null;

    public function init()
    {
    }

    public function run()
    {
        $items = $this->getEntries();
//        $items = $this->getAllItems(Item::find(), $this->lastId, $this->orderBy, $this->dateCreateType, $search, $this->userId, $this->limit);
        return $this->render(
            'entryList/list',
            [
                'items'             => $items,
                'onlyItem'          => $this->onlyItem,
                'dateCreateType'    => $this->dateCreateType,
                'display'           => $this->display,
                'limit'             => $this->limit,
                'addModalShowVideo' => $this->addModalShowVideo,
                'addModalShowImg'   => $this->addModalShowImg,
                'searchEntryForm'   => $this->searchEntryForm,
                'blockAction'       => $this->blockAction,
                'countEntities'     => count($this->entityTypes),
            ]
        );
    }

    public function getEntries()
    {
        $entries = [];

        if (in_array(Item::THIS_ENTITY, $this->entityTypes)) {
            $itemQuery = Item::find()->from(["t" => Item::tableName()])->andWhere('t.deleted = 0')->addSelect('*');
            $this->addFilterBySearchText($itemQuery);
            $this->addSort($itemQuery, $this->orderBy);
            
            $dataProviderItem = new ActiveDataProvider([
                'query' => $itemQuery,
                'pagination' => [
                    'pageSize' => 10,
                    'page' => $this->page,
                ],
            ]);

            $items = $dataProviderItem->getModels();
            foreach ($items as $item) {
                $entries[] = $item;
            }
        }

        if (in_array(Event::THIS_ENTITY, $this->entityTypes)) {
            $eventQuery = Event::find()->from(["t" => Event::tableName()])->andWhere('t.deleted = 0')->addSelect('*');
            $this->addFilterBySearchText($eventQuery);
            if (count($this->entityTypes) == 1) {
                $this->addSort($eventQuery, $this->orderBy);
            } else {
                $this->addSort($eventQuery, self::ORDER_BY_DATE);
            }
            if (!empty($this->searchEntryForm->date_from)) {
                $eventQuery->andWhere(['>=', 'date', $this->searchEntryForm->date_from]);
            }
            if (!empty($this->searchEntryForm->date_to)) {
                $eventQuery->andWhere(['<=', 'date', $this->searchEntryForm->date_to]);
            }

            $pageSize = 4;
            if (count($this->entityTypes) == 1) {
                $pageSize = 10;
            }

            $dataProviderEvent = new ActiveDataProvider([
                'query'      => $eventQuery,
                'pagination' => [
                    'pageSize' => $pageSize,
                    'page'     => $this->page,
                ],
            ]);

            $events = $dataProviderEvent->getModels();
            if (count($events) > 0) {
                $offsetStep = count($entries) / count($events);
                $offset = $offsetStep;
                foreach ($events as $event) {
                    array_splice($entries, $offset, 0, [$event]);
                    $offset += $offsetStep + 1;
                }
            }
        }

        if (in_array(School::THIS_ENTITY, $this->entityTypes)) {
            $schoolQuery = School::find()->from(["t" => School::tableName()])->andWhere('t.deleted = 0')->addSelect('*');
            $this->addFilterBySearchText($schoolQuery);
            $this->addSort($schoolQuery, self::ORDER_BY_LIKE_SHOW);

            $dataProviderSchool = new ActiveDataProvider([
                'query' => $schoolQuery,
                'pagination' => [
                    'pageSize' => 4,
                    'page' => $this->page,
                ],
            ]);

            $schools = $dataProviderSchool->getModels();
            if (count($schools) > 0) {
                $offsetStep = count($entries) / count($schools);
                $offset = $offsetStep;
                foreach ($schools as $school) {
                    array_splice($entries, $offset, 0, [$school]);
                    $offset += $offsetStep + 1;
                }
            }
        }

        return $entries;
    }

    public function getSearchTag()
    {
        if (is_null($this->_tagsId)) {
            $search = isset($this->searchEntryForm) ? $this->searchEntryForm->search_text : '';
            $tagsId = [];
            if (!empty($search)) {
                $tags = Tags::find()->where(['like', 'name', $search])->all();

                foreach ($tags as $tag) {
                    $tagsId[] = (int)$tag->id;
                }

            }
            $this->_tagsId = $tagsId;
        }
        return $this->_tagsId;
    }


    /**
     * @param ActiveQuery $query
     */
    public function addFilterBySearchText($query)
    {
        $search = isset($this->searchEntryForm) ? $this->searchEntryForm->search_text : '';
        if (empty($search)) {
            return;
        }
        $filters = ['or'];
        $tagsId = $this->getSearchTag();
        $countFilter = 0;
        if (count($tagsId) > 0) {
            $filters[] = '(SELECT COUNT(*) as tagCount FROM `' . TagEntity::tableName() . '` te WHERE te.entity = "' . TagEntity::ENTITY_ITEM . '" AND te.entity_id = t.id  AND te.tag_id IN (' . join(',', $tagsId) . ')) > 0';
            $countFilter++;
        } else {
            $filters[] = '0=1';
        }

        $filters[] = ['like', 'title', $search];
        $filters[] = ['like', 'description', $search];
        $countFilter += 2;

        if ($countFilter > 0) {
            $query->andFilterWhere($filters);
        }
    }

    /**
     * @param ActiveQuery $query
     * @param $orderBy
     */
    public function addSort($query, $orderBy)
    {
        // Определяем сортировку
        if ($orderBy == self::ORDER_BY_ID) {
            $query->orderBy(['id' => SORT_DESC]);
        } elseif ($orderBy == self::ORDER_BY_LIKE) {
            $query->orderBy(['like_count' => SORT_DESC]);
        } elseif ($orderBy == self::ORDER_BY_SHOW) {
            $query->orderBy(['show_count' => SORT_DESC]);
        } elseif ($orderBy == self::ORDER_BY_LIKE_SHOW) {
            $query->addSelect(['(like_count * 15 + show_count) as like_show_count'])->orderBy(['like_show_count' => SORT_DESC]);
        } elseif ($orderBy == self::ORDER_BY_DATE) {
            $query->orderBy(['date' => SORT_DESC]);
        }
    }

}