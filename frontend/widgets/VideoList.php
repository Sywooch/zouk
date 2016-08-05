<?php
namespace frontend\widgets;

use common\models\Item;
use common\models\TagEntity;
use common\models\Tags;
use common\models\Video;
use yii\data\Pagination;

class VideoList extends \yii\bootstrap\Widget
{

    const ITEM_LIST_DISPLAY_MAIN = 'main';

    public $lastId = 0;

    public $onlyItem = false;

    public $searchTag = "";

    public $userId = false;

    public $display = self::ITEM_LIST_DISPLAY_MAIN;

    public $limit = false;

    public function init()
    {
    }

    public function run()
    {
        $videos = $this->getAllItems($this->lastId, $this->searchTag, $this->userId, $this->limit);
        return $this->render(
            'videosWidget/list',
            [
                'videos'     => $videos,
                'autoPlay'   => false,
                'onlyVideos' => true,
            ]
        );
    }

    public function getAllItems($lastId = 0, $searchTag = "", $userId = false, $limit = false)
    {
        $query = Video::find()->from([
            "v" => Video::tableName(),
        ])->joinWith(['items i'])->andWhere('i.deleted = 0')->orderBy('id DESC');
        // Определяем за какой период будем показывать
        if (!empty($limit)) {
            $query = $query->limit((int)$limit);
        } else {
            $query = $query->limit(100);
        }
        if ($lastId != 0) {
            $query = $query->andWhere('i.id < :id', [':id' => $lastId]);
        }
        if (!empty($userId)) {
            $query = $query->andWhere('i.user_id = :userId', [':userId' => $userId]);
        }

        if (!empty($searchTag)) {
            if (is_array($searchTag)) {
                $tagsId = $searchTag;
            } else {

                $tags = Tags::find()->where(['name' => $searchTag])->all();

                $tagsId = [];
                foreach ($tags as $tag) {
                    $tagsId[] = (int)$tag->id;
                }
            }

            if (count($tagsId) > 0) {
                $query = $query
                    ->andWhere('(SELECT COUNT(*) as tagCount FROM `' . TagEntity::tableName() . '` te WHERE te.entity = "' . TagEntity::ENTITY_ITEM . '" AND te.entity_id = i.id  AND te.tag_id IN (' . join(',', $tagsId) . ')) > 0');
            }
        }


        return $query->all();
    }
}