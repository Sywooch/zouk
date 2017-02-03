<?php
namespace common\models;

use common\models\User;
use frontend\models\Lang;
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
 * @property integer     $date_to
 * @property integer     $country
 * @property string      $city
 * @property string      $site
 * @property int         $like_count
 * @property int         $show_count
 * @property int         $likes
 * @property int         $dislikes
 * @property string      $alias
 * @property int         $deleted
 * @property integer     $date_update
 * @property integer     $date_create
 *
 * @property Img[]       $imgs
 * @property TagEntity[] $tagEntity
 * @property Location[]  $locations
 * @property User        $user
 */
class Event extends EntryModel
{

    const THIS_ENTITY = 'event';

    public function getThisEntity()
    {
        return self::THIS_ENTITY;
    }

    const MAX_IMG_EVENT      = 5;
    const MAX_LOCATION_EVENT = 5;

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
        return array_merge(
            parent::rules(),
            [
                [['description'], 'default', 'value' => ''],
                [['date'], 'required'],
//                ['date_to', 'compare', 'compareAttribute' => 'date', 'operator' => '>=', 'type' => 'number'],
                [['country'], 'integer'],
                [['city'], 'string', 'max' => 60],
                [['site'], 'string', 'max' => 120],
                [['date_to'], 'safe'],
            ]
        );
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [];
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

    public function getImgs()
    {
        return $this->hasMany(Img::className(), ['id' => 'entity_2_id'])
            ->viaTable(EntityLink::tableName(), ['entity_1_id' => 'id'], function ($query) {
                /** @var ActiveQuery $query */
                $query->onCondition(['entity_1' => $this->getThisEntity(), 'entity_2' => Img::THIS_ENTITY])
                    ->orderBy(['sort' => SORT_DESC]);
            });
    }

    public function getLocations()
    {
        return $this->hasMany(Location::className(), ['entity_id' => 'id'])->andOnCondition([Location::tableName() . '.entity' => Event::THIS_ENTITY]);
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

    public function saveTags($tags)
    {
        if (!is_array($tags)) {
            $tags = [$tags];
        }
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                TagEntity::addTag(trim($tag), Tags::TAG_GROUP_ALL, self::THIS_ENTITY, $this->id);
            }
        }
    }

    public function saveLocations($locations)
    {
        $user = User::thisUser();
        Location::deleteAll(['entity' => Event::THIS_ENTITY, 'entity_id' => $this->id]);
        if (is_array($locations)) {
            $locations = array_slice($locations, 0, Event::MAX_LOCATION_EVENT);
            foreach ($locations as $location) {
                $newLocation = new Location();
                $newLocation->user_id = $user->id;
                $newLocation->entity = Event::THIS_ENTITY;
                $newLocation->entity_id = $this->id;
                $newLocation->title = $location['title'];
                $newLocation->description = $location['description'];
                $newLocation->lat = $location['lat'];
                $newLocation->lng = $location['lng'];
                $newLocation->zoom = $location['zoom'];
                $newLocation->type = $location['type'];
                $newLocation->save();
            }
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

    public function getCountry()
    {
        $country = null;
        if (!empty($this->country)) {
            $country = Countries::findOne(['id' => $this->country]);
        }
        return $country;
    }

    public function getCountryText()
    {
        $country = $this->getCountry();
        if (!empty($country)) {
            $lang = Lang::$current->url;
            return $country->getLangCountries($lang);
        }
        return "";
    }

    public function getCity()
    {
        return htmlspecialchars($this->city);
    }

    public function getCountryCityText()
    {
        $countryText = $this->getCountryText();
        $city = $this->getCity();
        $countryCityText = $countryText;
        if (!empty($countryText) && !empty($city)) {
            $countryCityText .= ", ";
        }
        $countryCityText .= $city;
        if (empty($countryCityText)) {
            $countryCityText = " - ";
        }
        return $countryCityText;
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
        $keywords = array_merge($keywords, $this->extractKeywords($this->getTitle(), 3, 1, true));
        $minWorkOk = mb_strlen($description) > 400 ? 2 : 1;
        $keywords = array_merge($keywords, $this->extractKeywords($description, 3, $minWorkOk, true));
        return $keywords;
    }
}
