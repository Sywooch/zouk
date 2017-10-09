<?php
namespace common\models;

use frontend\models\Lang;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;

/**
 * VkTaskCompleted model
 *
 * @property integer $id         Идентификатор
 * @property string  $type       Тип задания
 * @property integer $user_id    Пользователь
 * @property integer $vk_task_id Ссылка на задачу
 * @property integer $date_create
 * @property integer $date_update
 */
class VkTaskCompleted extends ActiveRecord
{
    const THIS_ENTITY = 'vk_task_completed';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vk_tasks_completed';
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
            [['user_id', 'vk_task_id', 'date_update', 'date_create'], 'integer'],
        ];
    }
    
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getVkTask()
    {
        return $this->hasOne(VkTask::class, ['id' => 'vk_task_id']);
    }
}
