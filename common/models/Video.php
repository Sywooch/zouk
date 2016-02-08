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
 * @property string  $entity
 * @property string  $entity_id
 * @property string  $originalUrl
 * @property string  $video_title
 * @property integer $date_update
 * @property integer $date_create
 */
class Video extends ActiveRecord
{

    const ENTITY_NONE    = 'none';
    const ENTITY_YOUTUBE = 'youtube';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'video';
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
            'entity_id'   => 'ID сущности',
            'originalUrl' => 'url',
            'video_title' => 'Заголовок видео',
            'date_update' => 'Date Update',
            'date_create' => 'Date Create',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'id']);
    }

    public function parseUrl($url)
    {
        $entity = self::ENTITY_NONE;
        $entity_id = "";
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
            $entity = self::ENTITY_YOUTUBE;
            $entity_id = $match[1];

            $content = file_get_contents("http://youtube.com/get_video_info?video_id=" . $entity_id);
            parse_str($content, $ytarr);

            $this->video_title = $ytarr['title'];
        }

        $this->entity = $entity;
        $this->entity_id = $entity_id;
        $this->originalUrl = $url;
    }
}
