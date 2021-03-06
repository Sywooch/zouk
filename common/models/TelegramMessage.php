<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * TelegramMessage model
 *
 * @property integer $id         Идентификатор
 * @property integer $user_id
 * @property integer $chat_id
 * @property integer $message_id
 * @property string $text
 * @property string $status
 * @property string $bot_name
 * @property integer $date_create
 * @property integer $date_update
 */
class TelegramMessage extends ActiveRecord
{
    const STATUS_READ = 'read';
    const STATUS_PROCESSED = 'processed';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'telegram_messages';
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
            [['user_id', 'chat_id', 'message_id', 'date_update', 'date_create'], 'integer'],
            [['text', 'status', 'bot_name'], 'string'],
        ];
    }

}
