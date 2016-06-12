<?php
namespace common\models;

use frontend\models\Lang;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Alarm
 *
 * @property integer $id
 * @property integer $user_id
 * @property string  $entity
 * @property integer $entity_id
 * @property integer $msg
 * @property integer $date_update
 * @property integer $date_create
 */
class Alarm extends ActiveRecord
{

    const ENTITY_ITEM    = 'item';
    const ENTITY_EVENT   = Event::THIS_ENTITY;
    const ENTITY_COMMENT = 'comment';


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'alarm';
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
            'user_id'     => 'Пользователь',
            'entity'      => 'Сущность',
            'entity_id'   => 'ID сущности',
            'msg'         => 'Текст жалабоы',
            'date_update' => 'Date Update',
            'date_create' => 'Date Create',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'id']);
    }

    public static function addAlarm($entity, $entityId, $msg)
    {
        $countHour = self::find()->where('date_create >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 1 HOUR))')->count();
        if ($countHour < 10) {
            $model = new Alarm();
            $model->user_id = User::thisUser()->id;
            $model->entity = $entity;
            $model->entity_id = $entityId;
            $model->msg = $msg;
            return $model->save();
        }
        return false;
    }
}
