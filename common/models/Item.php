<?php
namespace common\models;

use common\models\User;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\Url;
use yii\web\IdentityInterface;

/**
 * Item model
 *
 * @property integer     $id
 * @property integer     $user_id
 * @property string      $title
 * @property string      $description
 * @property int         $like_count
 * @property int         $show_count
 * @property int         $likes
 * @property int         $dislikes
 * @property string      $alias
 * @property int         $deleted
 * @property integer     $date_update
 * @property integer     $date_create
 *
 * @property Video[]     $videos
 * @property Music[]     $sounds
 * @property Img[]       $imgs
 * @property TagEntity[] $tagEntity
 * @property User        $user
 */
class Item extends VoteModel
{

    const THIS_ENTITY = 'item';

    const MAX_IMG_ITEM   = 5;
    const MAX_VIDEO_ITEM = 5;
    const MAX_SOUND_ITEM = 10;

    const MIN_REPUTAION_BAD_ITEM_DELETE                    = 10;
    const MIN_REPUTATION_ITEM_CREATE                       = -4;
    const MIN_REPUTATION_ITEM_CREATE_NO_STOP_WORD          = 15;
    const MIN_REPUTATION_ITEM_VOTE                         = -4;
    const MIN_REPUTATION_FOR_ADD_REPUTATION_ITEM_VOTE_LIKE = -3;
    const MAX_REPUTATION_FOR_ADD_REPUTATION_ITEM_VOTE_LIKE = 100;


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

    public function getTitle2()
    {
        return strip_tags($this->title);
    }

    public function isStopWord($text = '')
    {
        return parent::isStopWord($this->title) !== false || parent::isStopWord($this->description) !== false;
    }

    public function getShortDescription($length = 500, $end = '...')
    {
        $charset = 'UTF-8';
        $token = '~';
        $description = $this->description;
        $description = preg_replace("'<blockquote[^>]*?>.*?</blockquote>'si", " ", $description);
        $str = $description;
        $str = strip_tags($str);
        $str = nl2br($str);
        $str = preg_replace('/\s+/', ' ', $str);
        if (mb_strlen($str, $charset) >= $length) {
            $wrap = wordwrap($str, $length, $token);
            $str_cut = mb_substr($wrap, 0, mb_strpos($wrap, $token, 0, $charset), $charset);
            $str_cut .= $end;
            return $str_cut;
        } else {
            return $str;
        }
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
            [['description'], 'string', 'max' => 20000],
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
        if ($changeVote > 0) {
            $this->likes += $changeVote;
        } else {
            $this->likes += abs($changeVote);
        }
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

    public function getImgs()
    {
        return $this->hasMany(Img::className(), ['id' => 'entity_2_id'])
            ->viaTable(EntityLink::tableName(), ['entity_1_id' => 'id'], function ($query) {
                /** @var ActiveQuery $query */
                $query->onCondition(['entity_1' => Item::THIS_ENTITY, 'entity_2' => Img::THIS_ENTITY])
                    ->orderBy(['sort' => SORT_DESC]);
            });
    }

    /**
     * @return Img[]
     */
    public function getImgsSort()
    {
        $query = Img::find()
            ->innerJoin(EntityLink::tableName(), Img::tableName() . '.id = `' . EntityLink::tableName() . '`.entity_2_id')
            ->andWhere(['entity_1' => Item::THIS_ENTITY, 'entity_2' => Img::THIS_ENTITY, 'entity_1_id' => $this->id])
            ->orderBy(['sort' => SORT_ASC]);
        return $query->all();
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

    public function saveImgs($imgs)
    {
        if (count($imgs) > 0) {
            $imgs = array_map('intval', $imgs);
            $imgs = array_unique($imgs);
            $imgs = array_slice($imgs, 0, self::MAX_IMG_ITEM);
            EntityLink::deleteAll(
                [
                    'AND',
                    'entity_1 = :entity_1',
                    'entity_1_id = :entity_1_id',
                    'entity_2 = :entity_2',
                    ['NOT IN', 'entity_2_id', $imgs],
                ],
                [
                    ':entity_1'    => Item::THIS_ENTITY,
                    ':entity_1_id' => $this->id,
                    ':entity_2'    => Img::THIS_ENTITY,
                ]
            );
            $entityLinksObj = EntityLink::find()->andWhere(
                [
                    'AND',
                    'entity_1 = :entity_1',
                    'entity_1_id = :entity_1_id',
                    'entity_2 = :entity_2',
                    ['IN', 'entity_2_id', $imgs],
                ],
                [
                    ':entity_1'    => Item::THIS_ENTITY,
                    ':entity_1_id' => $this->id,
                    ':entity_2'    => Img::THIS_ENTITY,
                ]
            )->all();
            $entityLinks = [];
            foreach ($entityLinksObj as $entityLink) {
                $entityLinks[$entityLink->entity_2_id] = $entityLink;
            }
            $imgsObjs = $this->imgs;
            $imgsId = [];
            foreach ($imgsObjs as $key => $img) {
                $imgsId[$key] = $img->id;
            }
            $index = 0;
            foreach ($imgs as $key => $imgId) {
                if (!in_array($imgId, $imgsId)) {
                    $entityLink = new EntityLink();
                    $entityLink->entity_1 = Item::THIS_ENTITY;
                    $entityLink->entity_1_id = $this->id;
                    $entityLink->entity_2 = Img::THIS_ENTITY;
                    $entityLink->entity_2_id = $imgId;
                    $entityLink->sort = $index++;
                    $entityLink->save();
                } else {
                    if (!empty($entityLinks[$imgId])) {
                        $entityLink = $entityLinks[$imgId];
                        $entityLink->sort = $index++;
                        $entityLink->save();
                    }
                }
            }
        } else {
            EntityLink::deleteAll(['entity_1' => Item::THIS_ENTITY, 'entity_1_id' => $this->id, 'entity_2' => Img::THIS_ENTITY]);
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

    public function getKeywords()
    {
        $keywords = [];
        $tags = $this->tagEntity;
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $name = $tag->tags->getName();
                if (!empty($name)) {
                    $keywords[] = $tag->tags->getName();
                }
            }
        }

        $description = strip_tags($this->description);
        $videos = $this->videos;
        if (!empty($videos)) {
            foreach ($videos as $video) {
                $description .= " " . $video->video_title;
            }
        }
        $sounds = $this->sounds;
        if (!empty($sounds)) {
            foreach ($sounds as $sound) {
                $description .= " " . $sound->getArtist() . " " . $sound->getTitle() . "; ";
            }
        }
        $keywords = array_merge($keywords, $this->extractKeywords($this->getTitle(), 3, 1, true));
        $minWorkOk = mb_strlen($description) > 400 ? 2 : 1;
        $keywords = array_merge($keywords, $this->extractKeywords($description, 3, $minWorkOk, true));
        return $keywords;
    }
}
