<?php
namespace common\models;

use frontend\widgets\EventList;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\IdentityInterface;

/**
 * Comment model
 *
 * @property integer $id
 * @property integer $user_id
 * @property string  $entity
 * @property integer $entity_id
 * @property integer $parent_id
 * @property string  $description
 * @property int     $like_count
 * @property int     $likes
 * @property int     $dislikes
 * @property int     $deleted
 * @property integer $date_update
 * @property integer $date_create
 */
class Comment extends VoteModel
{
    const THIS_ENTITY = 'comment';

    const ENTITY_ITEM = Item::THIS_ENTITY;
    const ENTITY_EVENT = Event::THIS_ENTITY;
    const ENTITY_SCHOOL = School::THIS_ENTITY;

    const MAX_VIDEO_ITEM = 5;

    const MIN_REPUTATION_COMMENT_CREATE = -4;
    const MIN_REPUTATION_COMMENT_VOTE   = -4;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comment';
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
            [['description', 'entity', 'entity_id', 'parent_id'], 'required'],
            [['date_update', 'date_create', 'entity_id', 'parent_id'], 'integer'],
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
            'user_id'     => 'Пользователь',
            'entity'      => 'Сущность',
            'entity_id'   => 'ID сущности',
            'parent_id'   => 'Родительский комментарий',
            'description' => 'Описание',
            'like_count'  => 'Голосов',
            'deleted'     => 'Удален',
            'date_update' => 'Date Update',
            'date_create' => 'Date Create',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
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

    public function getEntityModel()
    {
        if ($this->entity == self::ENTITY_ITEM) {
            return Item::findOne(['id' => $this->entity_id]);
        }
    }

    public function getUrl($scheme = false)
    {
        $entityModel = $this->getEntityModel();

        if (!empty($entityModel)) {
            return $entityModel->getUrl($scheme, ['comment' => 'comment-' . $this->id]);
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
            Reputation::addReputation($modelUserId, Reputation::ENTITY_VOTE_LIKE_SELF_COMMENT_CANCEL, $paramsSelf);
        } elseif ($addReputation == VoteModel::ADD_REPUTATION_UP) {
            // + хозяину записи за лайк
            Reputation::addReputation($modelUserId, Reputation::ENTITY_VOTE_LIKE_SELF_COMMENT, $paramsSelf);
        } elseif ($addReputation == VoteModel::ADD_REPUTATION_CANCEL_DOWN) {
            // + хозяину записи за отмену дизлайка
            Reputation::addReputation($modelUserId, Reputation::ENTITY_VOTE_DISLIKE_SELF_COMMENT_CANCEL, $paramsSelf);
        } elseif ($addReputation == VoteModel::ADD_REPUTATION_DOWN) {
            // - хозяину записи за дизлайк
            Reputation::addReputation($modelUserId, Reputation::ENTITY_VOTE_DISLIKE_SELF_COMMENT, $paramsSelf);
        }
    }
}
