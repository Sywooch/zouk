<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Reputation
 *
 * @property integer $id
 * @property integer $user_id
 * @property string  $entity
 * @property string  $msg
 * @property integer $value
 * @property integer $date_update
 * @property integer $date_create
 */
class Reputation extends ActiveRecord
{

    const ENTITY_NONE                           = 'none';
    const ENTITY_VOTE_LIKE_SELF_ITEM            = 'voteLikeSelfItem';
    const ENTITY_VOTE_LIKE_SELF_ITEM_CANCEL     = 'voteLikeSelfItemCancel';
    const ENTITY_VOTE_DISLIKE_SELF_ITEM         = 'voteDislikeSelfItem';
    const ENTITY_VOTE_DISLIKE_SELF_ITEM_CANCEL  = 'voteDislikeSelfItemCancel';
    const ENTITY_VOTE_DISLIKE_OTHER_ITEM        = 'voteDislikeOtherItem';
    const ENTITY_VOTE_DISLIKE_OTHER_ITEM_CANCEL = 'voteDislikeOtherItemCancel';
    const ENTITY_VOTE_LIKE_OTHER_ITEM           = 'voteLikeOtherItem';

    public static function notSelfChange()
    {
        return [
            self::ENTITY_VOTE_LIKE_SELF_ITEM,
            self::ENTITY_VOTE_LIKE_SELF_ITEM_CANCEL,
            self::ENTITY_VOTE_DISLIKE_SELF_ITEM,
            self::ENTITY_VOTE_DISLIKE_SELF_ITEM_CANCEL,
            self::ENTITY_VOTE_DISLIKE_OTHER_ITEM,
            self::ENTITY_VOTE_DISLIKE_OTHER_ITEM_CANCEL,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reputation';
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
            'entity'      => 'Сущность',
            'msg'         => 'ID сущности',
            'value'       => 'Значение',
            'date_update' => 'Date Update',
            'date_create' => 'Date Create',
        ];
    }

    /**
     * @param null   $userId Кому изменить
     * @param string $entity Действие
     * @param array  $params Дополнительные параметры
     *
     * @return bool
     */
    public static function addReputation($userId = null, $entity = self::ENTITY_NONE, $params = [])
    {
        if (empty($userId) ||
            ($entity == self::ENTITY_VOTE_DISLIKE_OTHER_ITEM) ||
            !($findUser = User::findOne([$userId]))
        ) {
            $findUser = User::thisUser();
            $userId = $findUser->id;
        }
        if (empty($findUser)) {
            return false;
        }
        $pItemId = $params['itemId'] ? $params['itemId'] : '?';
        $pUserId = $params['userId'] ? $params['userId'] : '?';

        if ($pUserId == $userId && in_array($entity, self::notSelfChange())) {
            return false;
        }

        if ($entity == self::ENTITY_VOTE_LIKE_SELF_ITEM) {
            $params['msg'] = "Запись {$pItemId} понравилась пользователю {$pUserId}.";
            $params['value'] = 5;
        } else if ($entity == self::ENTITY_VOTE_LIKE_SELF_ITEM_CANCEL) {
            $params['msg'] = "Отмена: Запись {$pItemId} понравилась пользователю {$pUserId}.";
            $params['value'] = -5;
        } else if ($entity == self::ENTITY_VOTE_DISLIKE_SELF_ITEM) {
            $params['msg'] = "Запись {$pItemId} не понравилась пользователю {$pUserId}.";
            $params['value'] = -5;
        } else if ($entity == self::ENTITY_VOTE_DISLIKE_SELF_ITEM_CANCEL) {
            $params['msg'] = "Отмена: Запись {$pItemId} не понравилась пользователю {$pUserId}.";
            $params['value'] = 5;
        } else if ($entity == self::ENTITY_VOTE_DISLIKE_OTHER_ITEM) {
            $params['msg'] = "Не понравилась запись {$pItemId}.";
            $params['value'] = -1;
        } else if ($entity == self::ENTITY_VOTE_DISLIKE_OTHER_ITEM_CANCEL) {
            $params['msg'] = "Отмена: Не понравилась запись {$pItemId}.";
            $params['value'] = 1;
        } else if ($entity == self::ENTITY_VOTE_LIKE_OTHER_ITEM) {
            $params['msg'] = "Понравилась запись {$pItemId}.";
            $params['value'] = 1;
        }


        $reputation = new Reputation();
        $reputation->user_id = $userId;
        $reputation->msg = isset($params['msg']) ? $params['msg'] : '';
        $reputation->value = isset($params['value']) ? $params['value'] : 0;
        $reputation->save();
        $findUser->reputation += $reputation->value;
        $findUser->save();
    }
}
