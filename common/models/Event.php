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
 * Event model
 *
 * @property integer     $id
 * @property integer     $user_id
 * @property string      $title
 * @property string      $description
 * @property integer     $date
 * @property integer     $country
 * @property string      $city
 * @property string      $vk
 * @property string      $fb
 * @property int         $like_count
 * @property int         $show_count
 * @property string      $alias
 * @property int         $deleted
 * @property integer     $date_update
 * @property integer     $date_create
 *
 * @property Video[]     $videos
 * @property Music[]     $sounds
 * @property Img[]       $imgs
 * @property TagEntity   $tagEntity
 * @property User        $user
 */
class Event extends VoteModel
{

    const THIS_ENTITY = 'event';

    const MAX_IMG_EVENT   = 5;

    const MIN_REPUTATION_EVENT_CREATE                       = -4;
    const MIN_REPUTATION_EVENT_VOTE                         = -4;
    const MIN_REPUTATION_FOR_ADD_REPUTATION_EVENT_VOTE_LIKE = -3;
    const MAX_REPUTATION_FOR_ADD_REPUTATION_EVENT_VOTE_LIKE = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event';
    }

    public function getTitle()
    {
        return htmlspecialchars($this->title);
    }

    public function getTitle2()
    {
        return strip_tags($this->title);
    }

    public function getShortDescription($length = 500, $end = '...')
    {
        $charset = 'UTF-8';
        $token = '~';
        $description = $this->description;
        $description = preg_replace("'<blockquote[^>]*?>.*?</blockquote>'si"," ",$description);
        $str = strip_tags($description);
        $str = str_replace("\n", ' ', $str);
        $str = str_replace("\r", ' ', $str);
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
        return [];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getTagEntity()
    {
        return $this->hasMany(TagEntity::className(), ['entity_id' => 'id'])->andOnCondition([TagEntity::tableName() . '.entity' => self::THIS_ENTITY]);
    }

    public function addVote($changeVote)
    {
        $this->like_count += $changeVote;
    }

    public function getVoteCount()
    {
        return $this->like_count;
    }

    public function getImgs()
    {
        return $this->hasMany(Img::className(), ['id' => 'entity_2_id'])
            ->viaTable(EntityLink::tableName(), ['entity_1_id' => 'id'], function ($query) {
                /** @var ActiveQuery $query */
                $query->onCondition(['entity_1' => self::THIS_ENTITY, 'entity_2' => Img::THIS_ENTITY])
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
            ->andWhere(['entity_1' => self::THIS_ENTITY, 'entity_2' => Img::THIS_ENTITY, 'entity_1_id' => $this->id])
            ->orderBy(['sort' => SORT_ASC]);
        return $query->all();
    }

    public function saveImgs($imgs)
    {
        if (count($imgs) > 0) {
            $imgs = array_map('intval', $imgs);
            $imgs = array_unique($imgs);
            $imgs = array_slice($imgs, 0, self::MAX_IMG_EVENT);
            EntityLink::deleteAll(
                [
                    'AND',
                    'entity_1 = :entity_1',
                    'entity_1_id = :entity_1_id',
                    'entity_2 = :entity_2',
                    ['NOT IN', 'entity_2_id', $imgs],
                ],
                [
                    ':entity_1'    => self::THIS_ENTITY,
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
                    ':entity_1'    => self::THIS_ENTITY,
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
                    $entityLink->entity_1 = self::THIS_ENTITY;
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
            EntityLink::deleteAll(['entity_1' => self::THIS_ENTITY, 'entity_1_id' => $this->id, 'entity_2' => Img::THIS_ENTITY]);
        }
    }

    public function saveTags($tag)
    {
        if (is_array($tag)) {
            $tag = array_shift($tag);
        }
        TagEntity::addTag(trim($tag), Tags::TAG_GROUP_ALL, self::THIS_ENTITY, $this->id);
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
                while ($findEvent = Event::find()->where($where, $params)->one()) {
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
            $params = ['event/view', 'alias' => $this->alias];
            $params = array_merge($params, $addParams);
            return Url::to($params, $scheme);
        } else {
            $params = ['event/view', 'index' => $this->id];
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
            'eventId' => $this->id,
            'userId' => $user->id,
        ];
        $paramsOther = [
            'entity' => self::THIS_ENTITY,
            'eventId' => $this->id,
            'userId' => $modelUserId,
        ];

        if ($addReputation == VoteModel::ADD_REPUTATION_CANCEL_UP) {
            // - хозяину события за отмену лайка
            Reputation::addReputation($modelUserId, Reputation::ENTITY_VOTE_LIKE_SELF_EVENT_CANCEL, $paramsSelf);
        } elseif ($addReputation == VoteModel::ADD_REPUTATION_UP) {
            // + хозяину события за лайк
            Reputation::addReputation($modelUserId, Reputation::ENTITY_VOTE_LIKE_SELF_EVENT, $paramsSelf);
            // Если раньше не было оценки, пользователь ставит лайк и его репутация маленькая, тогда добавим ему репутации
            if ($user->reputation < self::MAX_REPUTATION_FOR_ADD_REPUTATION_EVENT_VOTE_LIKE &&
                $user->reputation > self::MIN_REPUTATION_FOR_ADD_REPUTATION_EVENT_VOTE_LIKE
            ) {
                // + текущему пользователю за лайк
                Reputation::addReputation($user->id, Reputation::ENTITY_VOTE_LIKE_OTHER_EVENT, $paramsOther);
            }
        } elseif ($addReputation == VoteModel::ADD_REPUTATION_CANCEL_DOWN) {
            // + хозяину события за отмену дизлайка
            Reputation::addReputation($modelUserId, Reputation::ENTITY_VOTE_DISLIKE_SELF_EVENT_CANCEL, $paramsSelf);
            // + текущему пользователю за отмену дизлайка
            Reputation::addReputation($user->id, Reputation::ENTITY_VOTE_DISLIKE_OTHER_EVENT_CANCEL, $paramsOther);
        } elseif ($addReputation == VoteModel::ADD_REPUTATION_DOWN) {
            // - хозяину события за дизлайк
            Reputation::addReputation($modelUserId, Reputation::ENTITY_VOTE_DISLIKE_SELF_EVENT, $paramsSelf);
            // - текущему пользователю за дизлайк
            Reputation::addReputation($user->id, Reputation::ENTITY_VOTE_DISLIKE_OTHER_EVENT, $paramsOther);
        }
    }
}
