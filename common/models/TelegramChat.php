<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * TelegramChat model
 *
 * @property integer $id
 * @property integer $chat_id
 * @property integer $lang_id
 * @property string $params
 * @property integer $date_create
 * @property integer $date_update
 */
class TelegramChat extends ActiveRecord
{

    const PARAMS_LAST_COMMAND = 'last_command';
    const PARAMS_ADD_VIDEO_SETTINGS = 'add_video_settings';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'telegram_chats';
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
            [['chat_id', 'lang_id', 'date_update', 'date_create'], 'integer'],
            [['params'], 'string'],
        ];
    }


    /**
     * @return array
     */
    public function getParams() : array
    {
        return json_decode($this->params ?: '', true) ?: [];
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = json_encode($params);
    }


    /**
     * @param string $key
     * @param $value
     */
    public function setParamsByKey(string $key, $value)
    {
        $params = $this->getParams();
        $params[$key] = $value;
        $this->params = json_encode($params, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param string $key
     */
    public function removeParamsByKey(string $key)
    {
        $params = $this->getParams();
        unset($params[$key]);
        $this->params = json_encode($params, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param string $key
     * @param string $defaultValue
     * @return mixed|string
     */
    public function getParamsByKey(string $key, $defaultValue = '')
    {
        $params = $this->getParams();
        return $params[$key] ?? $defaultValue;
    }
}
