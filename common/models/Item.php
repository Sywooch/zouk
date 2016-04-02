<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer     $id
 * @property integer     $user_id
 * @property string      $title
 * @property string      $description
 * @property int         $like_count
 * @property int         $show_count
 * @property string      $alias
 * @property int         $deleted
 * @property integer     $date_update
 * @property integer     $date_create
 *
 * @property Video[]     $videos
 * @property Music[]     $sounds
 * @property TagEntity[] $tagEntity
 */
class Item extends VoteModel
{

    const THIS_ENTITY = 'item';

    const MAX_VIDEO_ITEM = 5;
    const MAX_SOUND_ITEM = 10;

    const MIN_REPUTATION_ITEM_CREATE                       = -4;
    const MIN_REPUTATION_ITEM_VOTE                         = -4;
    const MIN_REPUTATION_FOR_ADD_REPUTATION_ITEM_VOTE_LIKE = -3;
    const MAX_REPUTATION_FOR_ADD_REPUTATION_ITEM_VOTE_LIKE = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'item';
    }

    public function getTitle()
    {
        return htmlspecialchars($this->title);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'      => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date_create', 'date_update'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['date_update'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description'], 'default', 'value' => ''],
            [['title'], 'required'],
            [['date_update', 'date_create'], 'integer'],
            [['title', 'alias'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 2048],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'userId'      => 'Пользователь',
            'title'       => 'Заголовок',
            'description' => 'Описание',
            'likeCount'   => 'Голосов',
            'showCount'   => 'Показов',
            'alias'       => 'Алиас',
            'date_update' => 'Date Update',
            'date_create' => 'Date Create',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getTagEntity()
    {
        return $this->hasMany(TagEntity::className(), ['entity_id' => 'id'])->andOnCondition([TagEntity::tableName() . '.entity' => TagEntity::ENTITY_ITEM]);
    }

    public function addVote($changeVote)
    {
        $this->like_count += $changeVote;
    }

    public function getVoteCount()
    {
        return $this->like_count;
    }

    public function getVideos()
    {
        return $this->hasMany(Video::className(), ['id' => 'entity_2_id'])
            ->viaTable(EntityLink::tableName(), ['entity_1_id' => 'id'], function ($query) {
                $query->onCondition(['entity_1' => Item::THIS_ENTITY, 'entity_2' => Video::THIS_ENTITY]);
            });
    }

    public function getSounds()
    {
        return $this->hasMany(Music::className(), ['id' => 'entity_2_id'])
            ->viaTable(EntityLink::tableName(), ['entity_1_id' => 'id'], function ($query) {
                $query->onCondition(['entity_1' => Item::THIS_ENTITY, 'entity_2' => Music::THIS_ENTITY]);
            });
    }

    public function saveVideos($videosUrl, $user_id)
    {
        $itemVideoCount = 0;
        $videos = [];
        foreach ($videosUrl as $url) {
            if (!empty($url)) {
                $itemVideoCount++;
                if ($itemVideoCount > self::MAX_VIDEO_ITEM) {
                    break;
                }
                $video = new Video();
                $video = $video->parseUrlToModel($url);
                $needSave = false;
                if ($video->isNewRecord) {
                    $video->user_id = $user_id;
                    $needSave = true;
                }
                if ($video->video_title == "" || $video->duration == 0) {
                    $video->updateProperties();
                    $needSave = true;
                }
                if (!isset($videos[$video->entity][$video->entity_id]) &&
                    (!$needSave || $video->save())
                ) {
                    $videos[$video->entity][$video->entity_id] = $video;
                    $itemVideo = new EntityLink();
                    $itemVideo->entity_1 = Item::THIS_ENTITY;
                    $itemVideo->entity_1_id = $this->id;
                    $itemVideo->entity_2 = Video::THIS_ENTITY;
                    $itemVideo->entity_2_id = $video->id;
                    $itemVideo->save();
                }
            }
        }
    }

    public function saveSounds($sounds)
    {
        if (count($sounds) > 0) {
            $sounds = array_map('intval', $sounds);
            $sounds = array_unique($sounds);
            $sounds = array_slice($sounds, 0, self::MAX_SOUND_ITEM);
            EntityLink::deleteAll(
                [
                    'AND',
                    'entity_1 = :entity_1',
                    'entity_1_id = :entity_1_id',
                    'entity_2 = :entity_2',
                    ['NOT IN', 'entity_2_id', $sounds],
                ],
                [
                    ':entity_1'    => Item::THIS_ENTITY,
                    ':entity_1_id' => $this->id,
                    ':entity_2'    => Music::THIS_ENTITY,
                ]
            );
            $soundObjs = $this->sounds;
            $soundsId = [];
            foreach ($soundObjs as $sound) {
                $soundsId[] = $sound->id;
            }
            foreach ($sounds as $soundId) {
                if (!in_array($soundId, $soundsId)) {
                    $entityLink = new EntityLink();
                    $entityLink->entity_1 = Item::THIS_ENTITY;
                    $entityLink->entity_1_id = $this->id;
                    $entityLink->entity_2 = Music::THIS_ENTITY;
                    $entityLink->entity_2_id = $soundId;
                    $entityLink->save();
                }
            }
        } else {
            EntityLink::deleteAll(['entity_1' => Item::THIS_ENTITY, 'entity_1_id' => $this->id, 'entity_2' => Music::THIS_ENTITY]);
        }
    }

    public function saveTags($tags)
    {
        foreach ($tags as $tag) {
            TagEntity::addTag(trim($tag), Tags::TAG_GROUP_ALL, TagEntity::ENTITY_ITEM, $this->id);
        }
    }

    public function addShowCount()
    {
        $this->show_count++;
        $this->save();
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->alias == "") {
                $title = $this->encodestring($this->title);
                $alias = $this->toAscii($title);
                $baseAlias = substr($alias, 0, 250);
                $alias = $baseAlias;
                $i = 1;
                $wheres = ['alias = :alias'];
                $params[':alias'] = $alias;
                if (!is_null($this->id)) {
                    $wheres[] = 'id <> :id';
                    $params = [':id' => $this->id];
                }
                $where = join(' AND ', $wheres);
                while ($findItem = Item::find()->where($where, $params)->one()) {
                    $alias = $baseAlias . '-' . $i;
                    $params[':alias'] = $alias;
                    $i++;
                    if ($i > 30) {
                        $alias = '';
                        break;
                    }
                }
                $this->alias = $alias;
            }
            return true;
        }
        return false;
    }

    public function getUrl($scheme = false, $addParams = [])
    {
        if ($this->alias) {
            $params = ['list/view', 'alias' => $this->alias];
            $params = array_merge($params, $addParams);
            return Url::to($params, $scheme);
        } else {
            $params = ['list/view', 'index' => $this->id];
            $params = array_merge($params, $addParams);
            return Url::to($params, $scheme);
        }
    }


    public function addReputation($addReputation)
    {
        $user = User::thisUser();
        $modelUserId = $this->user_id;
        $paramsSelf = [
            'entity' => self::THIS_ENTITY,
            'itemId' => $this->id,
            'userId' => $user->id,
        ];
        $paramsOther = [
            'entity' => self::THIS_ENTITY,
            'itemId' => $this->id,
            'userId' => $modelUserId,
        ];

        if ($addReputation == VoteModel::ADD_REPUTATION_CANCEL_UP) {
            // - хозяину записи за отмену лайка
            Reputation::addReputation($modelUserId, Reputation::ENTITY_VOTE_LIKE_SELF_ITEM_CANCEL, $paramsSelf);
        } elseif ($addReputation == VoteModel::ADD_REPUTATION_UP) {
            // + хозяину записи за лайк
            Reputation::addReputation($modelUserId, Reputation::ENTITY_VOTE_LIKE_SELF_ITEM, $paramsSelf);
            // Если раньше не было оценки, пользователь ставит лайк и его репутация маленькая, тогда добавим ему репутации
            if ($user->reputation < Item::MAX_REPUTATION_FOR_ADD_REPUTATION_ITEM_VOTE_LIKE &&
                $user->reputation > Item::MIN_REPUTATION_FOR_ADD_REPUTATION_ITEM_VOTE_LIKE
            ) {
                // + текущему пользователю за лайк
                Reputation::addReputation($user->id, Reputation::ENTITY_VOTE_LIKE_OTHER_ITEM, $paramsOther);
            }
        } elseif ($addReputation == VoteModel::ADD_REPUTATION_CANCEL_DOWN) {
            // + хозяину записи за отмену дизлайка
            Reputation::addReputation($modelUserId, Reputation::ENTITY_VOTE_DISLIKE_SELF_ITEM_CANCEL, $paramsSelf);
            // + текущему пользователю за отмену дизлайка
            Reputation::addReputation($user->id, Reputation::ENTITY_VOTE_DISLIKE_OTHER_ITEM_CANCEL, $paramsOther);
        } elseif ($addReputation == VoteModel::ADD_REPUTATION_DOWN) {
            // - хозяину записи за дизлайк
            Reputation::addReputation($modelUserId, Reputation::ENTITY_VOTE_DISLIKE_SELF_ITEM, $paramsSelf);
            // - текущему пользователю за дизлайк
            Reputation::addReputation($user->id, Reputation::ENTITY_VOTE_DISLIKE_OTHER_ITEM, $paramsOther);
        }
    }
}
