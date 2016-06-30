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

    const ENTITY_VOTE_LIKE_SELF_EVENT            = 'voteLikeSelfEvent';
    const ENTITY_VOTE_LIKE_SELF_EVENT_CANCEL     = 'voteLikeSelfEventCancel';
    const ENTITY_VOTE_DISLIKE_SELF_EVENT         = 'voteDislikeSelfEvent';
    const ENTITY_VOTE_DISLIKE_SELF_EVENT_CANCEL  = 'voteDislikeSelfEventCancel';
    const ENTITY_VOTE_DISLIKE_OTHER_EVENT        = 'voteDislikeOtherEvent';
    const ENTITY_VOTE_DISLIKE_OTHER_EVENT_CANCEL = 'voteDislikeOtherEventCancel';
    const ENTITY_VOTE_LIKE_OTHER_EVENT           = 'voteLikeOtherEvent';

    const ENTITY_VOTE_LIKE_SELF_SCHOOL            = 'voteLikeSelfSchool';
    const ENTITY_VOTE_LIKE_SELF_SCHOOL_CANCEL     = 'voteLikeSelfSchoolCancel';
    const ENTITY_VOTE_DISLIKE_SELF_SCHOOL         = 'voteDislikeSelfSchool';
    const ENTITY_VOTE_DISLIKE_SELF_SCHOOL_CANCEL  = 'voteDislikeSelfSchoolCancel';
    const ENTITY_VOTE_DISLIKE_OTHER_SCHOOL        = 'voteDislikeOtherSchool';
    const ENTITY_VOTE_DISLIKE_OTHER_SCHOOL_CANCEL = 'voteDislikeOtherSchoolCancel';
    const ENTITY_VOTE_LIKE_OTHER_SCHOOL           = 'voteLikeOtherSchool';

    const ENTITY_VOTE_LIKE_SELF_COMMENT            = 'voteLikeSelfComment';
    const ENTITY_VOTE_LIKE_SELF_COMMENT_CANCEL     = 'voteLikeSelfCommentCancel';
    const ENTITY_VOTE_DISLIKE_SELF_COMMENT         = 'voteDislikeSelfComment';
    const ENTITY_VOTE_DISLIKE_SELF_COMMENT_CANCEL  = 'voteDislikeSelfCommentCancel';

    public static function notSelfChange()
    {
        return [
            self::ENTITY_VOTE_LIKE_SELF_ITEM,
            self::ENTITY_VOTE_LIKE_SELF_ITEM_CANCEL,
            self::ENTITY_VOTE_DISLIKE_SELF_ITEM,
            self::ENTITY_VOTE_DISLIKE_SELF_ITEM_CANCEL,
            self::ENTITY_VOTE_DISLIKE_OTHER_ITEM,
            self::ENTITY_VOTE_DISLIKE_OTHER_ITEM_CANCEL,

            self::ENTITY_VOTE_LIKE_SELF_EVENT,
            self::ENTITY_VOTE_LIKE_SELF_EVENT_CANCEL,
            self::ENTITY_VOTE_DISLIKE_SELF_EVENT,
            self::ENTITY_VOTE_DISLIKE_SELF_EVENT_CANCEL,
            self::ENTITY_VOTE_DISLIKE_OTHER_EVENT,
            self::ENTITY_VOTE_DISLIKE_OTHER_EVENT_CANCEL,
            self::ENTITY_VOTE_LIKE_OTHER_EVENT,

            self::ENTITY_VOTE_LIKE_SELF_SCHOOL,
            self::ENTITY_VOTE_LIKE_SELF_SCHOOL_CANCEL,
            self::ENTITY_VOTE_DISLIKE_SELF_SCHOOL,
            self::ENTITY_VOTE_DISLIKE_SELF_SCHOOL_CANCEL,
            self::ENTITY_VOTE_DISLIKE_OTHER_SCHOOL,
            self::ENTITY_VOTE_DISLIKE_OTHER_SCHOOL_CANCEL,
            self::ENTITY_VOTE_LIKE_OTHER_SCHOOL,

            self::ENTITY_VOTE_LIKE_SELF_COMMENT,
            self::ENTITY_VOTE_LIKE_SELF_COMMENT_CANCEL,
            self::ENTITY_VOTE_DISLIKE_SELF_COMMENT,
            self::ENTITY_VOTE_DISLIKE_SELF_COMMENT_CANCEL,
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
        $pItemId = '?';
        $itemIdKeys = ['itemId', 'eventId', 'schoolId', 'id'];
        foreach ($itemIdKeys as $itemIdKey) {
            if (isset($params[$itemIdKey])) {
                $pItemId = $params[$itemIdKey];
                break;
            }
        }
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
        } else


        if ($entity == self::ENTITY_VOTE_LIKE_SELF_EVENT) {
            $params['msg'] = "Событие {$pItemId} понравилась пользователю {$pUserId}.";
            $params['value'] = 5;
        } else if ($entity == self::ENTITY_VOTE_LIKE_SELF_EVENT_CANCEL) {
            $params['msg'] = "Отмена: Событие {$pItemId} понравилась пользователю {$pUserId}.";
            $params['value'] = -5;
        } else if ($entity == self::ENTITY_VOTE_DISLIKE_SELF_EVENT) {
            $params['msg'] = "Событие {$pItemId} не понравилась пользователю {$pUserId}.";
            $params['value'] = -5;
        } else if ($entity == self::ENTITY_VOTE_DISLIKE_SELF_EVENT_CANCEL) {
            $params['msg'] = "Отмена: Событие {$pItemId} не понравилась пользователю {$pUserId}.";
            $params['value'] = 5;
        } else if ($entity == self::ENTITY_VOTE_DISLIKE_OTHER_EVENT) {
            $params['msg'] = "Не понравилось событие {$pItemId}.";
            $params['value'] = -1;
        } else if ($entity == self::ENTITY_VOTE_DISLIKE_OTHER_EVENT_CANCEL) {
            $params['msg'] = "Отмена: Не понравилось событие {$pItemId}.";
            $params['value'] = 1;
        } else if ($entity == self::ENTITY_VOTE_LIKE_OTHER_EVENT) {
            $params['msg'] = "Понравилось событие {$pItemId}.";
            $params['value'] = 1;
        } else


        if ($entity == self::ENTITY_VOTE_LIKE_SELF_SCHOOL) {
            $params['msg'] = "Школа {$pItemId} понравилась пользователю {$pUserId}.";
            $params['value'] = 5;
        } else if ($entity == self::ENTITY_VOTE_LIKE_SELF_SCHOOL_CANCEL) {
            $params['msg'] = "Отмена: Школа {$pItemId} понравилась пользователю {$pUserId}.";
            $params['value'] = -5;
        } else if ($entity == self::ENTITY_VOTE_DISLIKE_SELF_SCHOOL) {
            $params['msg'] = "Школа {$pItemId} не понравилась пользователю {$pUserId}.";
            $params['value'] = -5;
        } else if ($entity == self::ENTITY_VOTE_DISLIKE_SELF_SCHOOL_CANCEL) {
            $params['msg'] = "Отмена: Школа {$pItemId} не понравилась пользователю {$pUserId}.";
            $params['value'] = 5;
        } else if ($entity == self::ENTITY_VOTE_DISLIKE_OTHER_SCHOOL) {
            $params['msg'] = "Не понравилась школа {$pItemId}.";
            $params['value'] = -1;
        } else if ($entity == self::ENTITY_VOTE_DISLIKE_OTHER_SCHOOL_CANCEL) {
            $params['msg'] = "Отмена: Не понравилась школа {$pItemId}.";
            $params['value'] = 1;
        } else if ($entity == self::ENTITY_VOTE_LIKE_OTHER_SCHOOL) {
            $params['msg'] = "Понравилась школа {$pItemId}.";
            $params['value'] = 1;
        } else

        if ($entity == self::ENTITY_VOTE_LIKE_SELF_COMMENT) {
            $params['msg'] = "Комментарий {$pItemId} понравился пользователю {$pUserId}.";
            $params['value'] = 1;
        } else if ($entity == self::ENTITY_VOTE_LIKE_SELF_COMMENT_CANCEL) {
            $params['msg'] = "Отмена: Комментарий {$pItemId} понравился пользователю {$pUserId}.";
            $params['value'] = -1;
        } else if ($entity == self::ENTITY_VOTE_DISLIKE_SELF_COMMENT) {
            $params['msg'] = "Комментарий {$pItemId} не понравился пользователю {$pUserId}.";
            $params['value'] = -1;
        } else if ($entity == self::ENTITY_VOTE_DISLIKE_SELF_COMMENT_CANCEL) {
            $params['msg'] = "Отмена: Комментарий {$pItemId} не понравился пользователю {$pUserId}.";
            $params['value'] = 1;
        }


        $reputation = new Reputation();
        $reputation->user_id = $userId;
        $reputation->msg = isset($params['msg']) ? $params['msg'] : '';
        $reputation->value = isset($params['value']) ? $params['value'] : 0;
        $reputation->entity = $params['entity'];
        $reputation->save();
        $findUser->reputation += $reputation->value;
        $findUser->save();
    }
}
