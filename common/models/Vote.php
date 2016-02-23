<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $vote
 * @property string  $entity
 * @property integer $entity_id
 * @property integer $date_update
 * @property integer $date_create
 */
class Vote extends ActiveRecord
{

    const ENTITY_ITEM = 'item';

    const VOTE_NONE = 0;
    const VOTE_DOWN = 1;
    const VOTE_UP   = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vote';
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['date_create', 'date_update'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['date_update'],
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
            [['vote'], 'default', 'value' => self::VOTE_NONE],
            [['date_update', 'date_create'], 'integer'],
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
            'vote'        => 'Голос',
            'entity'      => 'Сущность',
            'entity_id'   => 'ID сущности',
            'date_update' => 'Date Update',
            'date_create' => 'Date Create',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'id']);
    }

    public static function addVote($entity, $id, $voteAdd)
    {
        $user = User::thisUser();
        $vote = Vote::findOne(['entity' => $entity, 'entity_id' => $id, 'user_id' => $user->id]);
        if (empty($vote)) {
            $vote = new Vote();
            $vote->entity = $entity;
            $vote->entity_id = $id;
            $vote->user_id = $user->id;
        }

        /** @var VoteModel $model */
        $model = null;
        if ($entity == self::ENTITY_ITEM) {
            $model = Item::findOne($id);
        }


        if (!empty($model)) {
            $modelUserId = $model->user_id;
            $paramsSelf = [
                'entity' => $entity,
                'itemId' => $id,
                'userId' => $user->id,
            ];
            $paramsOther = [
                'entity' => $entity,
                'itemId' => $id,
                'userId' => $modelUserId,
            ];
            if ($vote->vote == self::VOTE_UP) {
                if ($voteAdd == self::VOTE_UP) {
                    // убираем up
                    $vote->vote = self::VOTE_NONE;
                    $model->addVote(-1);
                    Reputation::addReputation($modelUserId, Reputation::ENTITY_VOTE_LIKE_SELF_ITEM_CANCEL, $paramsSelf); // - хозяину записи за отмену лайка
                } else {
                    // ставим down
                    $vote->vote = self::VOTE_DOWN;
                    $model->addVote(-2);
                    Reputation::addReputation($modelUserId, Reputation::ENTITY_VOTE_LIKE_SELF_ITEM_CANCEL, $paramsSelf); // - хозяину записи за отмену лайка
                    Reputation::addReputation($modelUserId, Reputation::ENTITY_VOTE_DISLIKE_SELF_ITEM, $paramsSelf); // - хозяину записи за дизлайк
                    Reputation::addReputation($user->id, Reputation::ENTITY_VOTE_DISLIKE_OTHER_ITEM, $paramsOther); // - текущему пользователю за дизлайк
                }
            } elseif ($vote->vote == self::VOTE_DOWN) {
                if ($voteAdd == self::VOTE_UP) {
                    // ставим up
                    $vote->vote = self::VOTE_UP;
                    $model->addVote(2);
                    Reputation::addReputation($modelUserId, Reputation::ENTITY_VOTE_DISLIKE_SELF_ITEM_CANCEL, $paramsSelf); // + хозяину записи за отмену дизлайка
                    Reputation::addReputation($modelUserId, Reputation::ENTITY_VOTE_LIKE_SELF_ITEM, $paramsSelf); // + хозяину записи за лайк
                    Reputation::addReputation($user->id, Reputation::ENTITY_VOTE_DISLIKE_OTHER_ITEM_CANCEL, $paramsOther); // + текущему пользователю за отмену дизлайка
                } else {
                    // убираем down
                    $vote->vote = self::VOTE_NONE;
                    $model->addVote(1);
                    Reputation::addReputation($modelUserId, Reputation::ENTITY_VOTE_DISLIKE_SELF_ITEM_CANCEL, $paramsSelf); // + хозяину записи за отмену дизлайка
                    Reputation::addReputation($user->id, Reputation::ENTITY_VOTE_DISLIKE_OTHER_ITEM_CANCEL, $paramsOther); // + текущему пользователю за отмену дизлайка
                }
            } else {
                if ($voteAdd == self::VOTE_UP) {
                    // ставим up
                    $vote->vote = self::VOTE_UP;
                    $model->addVote(1);
                    Reputation::addReputation($modelUserId, Reputation::ENTITY_VOTE_LIKE_SELF_ITEM, $paramsSelf); // + хозяину записи за лайк
                } else {
                    // ставим down
                    $vote->vote = self::VOTE_DOWN;
                    $model->addVote(-1);
                    Reputation::addReputation($modelUserId, Reputation::ENTITY_VOTE_DISLIKE_SELF_ITEM, $paramsSelf); // - хозяину записи за дизлайк
                    Reputation::addReputation($user->id, Reputation::ENTITY_VOTE_DISLIKE_OTHER_ITEM, $paramsOther); // - текущему пользователю за дизлайк
                }
            }
        }

        if ($vote->save()) {
            if (!empty($model)) {
                $model->save();
            }
        }

        return [
            'vote'  => $vote->vote,
            'count' => $model->getVoteCount(),
        ];
    }
}
