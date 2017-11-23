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
 * VkTask model
 *
 * @property integer $id         Идентификатор
 * @property string $type       Тип задания
 * @property integer $user_id    Пользователь загрузивший картинку
 * @property string $group_id   Группа
 * @property integer $period     Периодичность (раз в сутки)
 * @property integer $time_start Постить не раньше этого времени
 * @property integer $time_end   Постить не позже этого времени
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property VkTaskCompleted[] $vkTaskCompleted
 * @property User $user
 */
class VkTask extends ActiveRecord
{
    const THIS_ENTITY = 'vk_task';

    const TYPE_RANDOM_VIDEO = 'type_random_video';
    const TYPE_BDAY = 'type_bday';

    const PERIOD_DAY = 86400;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vk_tasks';
    }

    public static function getTypeLabels()
    {
        return [
            self::TYPE_RANDOM_VIDEO => 'Случайное видео',
            self::TYPE_BDAY         => 'Поздравления с днем рождения',
        ];
    }

    public function getTypeLabel()
    {
        $labels = self::getTypeLabels();
        return $labels[$this->type] ?? '';
    }

    public static function getPeriodLabels()
    {
        return [
            self::PERIOD_DAY => 'Раз в день',
        ];
    }

    public function getPeriodLabel()
    {
        $labels = self::getPeriodLabels();
        return $labels[$this->period] ?? '';
    }

    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'type'          => 'Тип задания',
            'user_id'       => 'Пользователь загрузивший картинку',
            'group_id'      => 'Группа',
            'Периодичность' => 'Периодичность',
            'period'        => 'Частота постинга',
            'time_start'    => 'Постить не раньше этого времени',
            'time_end'      => 'Постить не позже этого времени',
            'create_time'   => 'Зарегистрирована',
            'update_time'   => 'Дата последнего изменения',
        ];
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
            [['user_id', 'period', 'time_start', 'time_end', 'date_update', 'date_create'], 'integer'],
            [['type', 'group_id'], 'string'],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVkTaskCompleted()
    {
        return $this->hasMany(VkTaskCompleted::class, ['vk_task_id' => 'id']);
    }

}
