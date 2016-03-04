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
 * @property integer $id
 * @property integer $user_id
 * @property string  $title
 * @property string  $description
 * @property int     $like_count
 * @property int     $show_count
 * @property string  $alias
 * @property int     $deleted
 * @property integer $date_update
 * @property integer $date_create
 */
class Item extends VoteModel
{

    const MAX_VIDEO_ITEM = 5;

    const MIN_REPUTATION_ITEM_CREATE  = -4;
    const MIN_REPUTATION_ITEM_VOTE    = -4;
    const MIN_REPUTATION_FOR_ADD_REPUTATION_ITEM_VOTE_LIKE = -3;
    const MAX_REPUTATION_FOR_ADD_REPUTATION_ITEM_VOTE_LIKE = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'item';
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
        return $this->hasMany(Video::className(), ['id' => 'video_id'])
            ->viaTable(ItemVideo::tableName(), ['item_id' => 'id']);
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
                if ($video->isNewRecord) {
                    $video->user_id = $user_id;
                }
                if (!isset($videos[$video->entity][$video->entity_id]) &&
                    (!$video->isNewRecord || $video->save())
                ) {
                    $videos[$video->entity][$video->entity_id] = $video;
                    $itemVideo = new ItemVideo();
                    $itemVideo->item_id = $this->id;
                    $itemVideo->video_id = $video->id;
                    $itemVideo->save();
                }
            }
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
                while ($findItem = Item::find()->where(['alias' => $alias])->andWhere('id <> :id', [':id' => $this->id])->all()) {
                    $alias = $baseAlias . '-' . $i;
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

    public function getUrl()
    {
        if ($this->alias) {
            return Url::to(['list/view', 'alias' => $this->alias]);
        } else {
            return Url::to(['list/view', 'index' => $this->id]);
        }
    }
}
