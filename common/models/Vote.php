<?php
namespace common\models;

use frontend\models\Lang;
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

    const ENTITY_ITEM    = Item::THIS_ENTITY;
    const ENTITY_COMMENT = Comment::THIS_ENTITY;

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
            if ($user->reputation < Item::MIN_REPUTATION_ITEM_VOTE) {
                // Если только пользователь не отменяет свои дизлайки
                if (!($vote->vote == self::VOTE_DOWN && $voteAdd == self::VOTE_DOWN)) {
                    return [
                        'vote'  => 0,
                        'count' => $model->getVoteCount(),
                        'error' => Lang::t('ajax', 'noReputationVote'),
                    ];
                }
            }
        } else if ($entity == self::ENTITY_COMMENT) {
            $model = Comment::findOne($id);
            if ($user->reputation < Comment::MIN_REPUTATION_COMMENT_VOTE) {
                // Если только пользователь не отменяет свои дизлайки
                if (!($vote->vote == self::VOTE_DOWN && $voteAdd == self::VOTE_DOWN)) {
                    return [
                        'vote'  => 0,
                        'count' => $model->getVoteCount(),
                        'error' => Lang::t('ajax', 'noReputationVote'),
                    ];
                }
            }
        }


        if (!empty($model)) {
            if ($vote->vote == self::VOTE_UP) {
                if ($voteAdd == self::VOTE_UP) {
                    // убираем up
                    $vote->vote = self::VOTE_NONE;
                    $model->addVote(-1);
                    $model->addReputation(VoteModel::ADD_REPUTATION_CANCEL_UP);
                } else {
                    // ставим down
                    $vote->vote = self::VOTE_DOWN;
                    $model->addVote(-2);
                    $model->addReputation(VoteModel::ADD_REPUTATION_CANCEL_UP);
                    $model->addReputation(VoteModel::ADD_REPUTATION_DOWN);
                }
            } elseif ($vote->vote == self::VOTE_DOWN) {
                if ($voteAdd == self::VOTE_UP) {
                    // ставим up
                    $vote->vote = self::VOTE_UP;
                    $model->addVote(2);
                    $model->addReputation(VoteModel::ADD_REPUTATION_CANCEL_DOWN);
                    $model->addReputation(VoteModel::ADD_REPUTATION_UP);
                } else {
                    // убираем down
                    $vote->vote = self::VOTE_NONE;
                    $model->addVote(1);
                    $model->addReputation(VoteModel::ADD_REPUTATION_CANCEL_DOWN);
                }
            } else {
                if ($voteAdd == self::VOTE_UP) {
                    // ставим up
                    $vote->vote = self::VOTE_UP;
                    $model->addVote(1);
                    $model->addReputation(VoteModel::ADD_REPUTATION_UP);
                } else {
                    // ставим down
                    $vote->vote = self::VOTE_DOWN;
                    $model->addVote(-1);
                    $model->addReputation(VoteModel::ADD_REPUTATION_DOWN);
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
