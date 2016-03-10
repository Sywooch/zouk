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
 * @property string  $original_url
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
            'original_url' => 'url',
            'video_title' => 'Заголовок видео',
            'date_update' => 'Date Update',
            'date_create' => 'Date Create',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'id']);
    }

    public function getYoutubeId($url)
    {
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
            return $match[1];
        }
        return "";
    }

    public function getThumbnailUrl()
    {
        $url = '';

        if ($this->entity == self::ENTITY_YOUTUBE) {
            $url = 'http://img.youtube.com/vi/' . $this->entity_id . '/1.jpg';
        }

        return $url;
    }

    public static function getModel($entity, $entity_id)
    {
        static $models = [];

        if (!isset($models[$entity][$entity_id])) {
            $model = self::findOne(['entity' => $entity, 'entity_id' => $entity_id]);
            $models[$entity][$entity_id] = $model;
        }

        return $models[$entity][$entity_id];
    }

    public function parseUrlToModel($url)
    {
        $entity = self::ENTITY_NONE;
        $entity_id = "";

        if ($youtubeId = $this->getYoutubeId($url)) {
            $entity = self::ENTITY_YOUTUBE;
            $entity_id = $youtubeId;

            if ($findModel = self::getModel($entity, $entity_id)) {
                return $findModel;
            }

            $key = Yii::$app->params['youtubeApiKey'];
            $content = file_get_contents("https://www.googleapis.com/youtube/v3/videos?id=" . $entity_id . "&key=" . $key . "&fields=items(id,snippet(title),statistics)&part=snippet,statistics");
            $content = json_decode($content, true);
            $title = isset($content['items'][0]['snippet']['title']) ? $content['items'][0]['snippet']['title'] : '';
            $this->video_title = $title;
        }

        $this->entity = $entity;
        $this->entity_id = $entity_id;
        $this->original_url = $url;
        return $this;
    }

    public function updateTitle()
    {
        $key = Yii::$app->params['youtubeApiKey'];
        $content = file_get_contents("https://www.googleapis.com/youtube/v3/videos?id=" . $this->entity_id . "&key=" . $key . "&fields=items(id,snippet(title),statistics)&part=snippet,statistics");
        $content = json_decode($content, true);
        $title = isset($content['items'][0]['snippet']['title']) ? $content['items'][0]['snippet']['title'] : '';
        $this->video_title = $title;
    }
}
