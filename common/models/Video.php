<?php
namespace common\models;

use DateInterval;
use DateTime;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;

/**
 * Video model
 *
 * @property integer $id
 * @property integer $user_id
 * @property string  $entity
 * @property string  $entity_id
 * @property string  $original_url
 * @property string  $video_title
 * @property integer $duration
 * @property integer $date_update
 * @property integer $date_create
 */
class Video extends ActiveRecord
{

    const THIS_ENTITY = 'video';

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
            'id'           => 'ID',
            'userId'       => 'Пользователь',
            'entity'       => 'Сущность',
            'entity_id'    => 'ID сущности',
            'original_url' => 'url',
            'video_title'  => 'Заголовок видео',
            'duration'     => 'Продолжительность',
            'date_update'  => 'Date Update',
            'date_create'  => 'Date Create',
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

    public function getThumbnailUrl($param = 1)
    {
        $url = '';

        if ($this->entity == self::ENTITY_YOUTUBE) {
            $name = "1.jpg";
            if ($param == 2) {
                $name = "hqdefault.jpg";
            }

            $url = 'http://img.youtube.com/vi/' . $this->entity_id . '/' . $name;
        }

        return $url;
    }

    public function getVideoUrl($autoplay = true)
    {
        $url = '';
        if ($this->entity == self::ENTITY_YOUTUBE) {
            $url = 'http://www.youtube.com/embed/' . $this->entity_id . ($autoplay ? '?autoplay=1' : '');
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

            $properties = Yii::$app->google->getVideoProperties($entity_id);
            $this->video_title = $properties['title'];
            $this->duration = $properties['duration'];
        }

        $this->entity = $entity;
        $this->entity_id = $entity_id;
        $this->original_url = $url;
        return $this;
    }

    public function updateProperties()
    {
        $title = "";
        $duration = 0;
        if ($this->entity == self::ENTITY_YOUTUBE) {
            $properties = Yii::$app->google->getVideoProperties($this->entity_id);
            $title = $properties['title'];
            $duration = $properties['duration'];
        }
        $this->video_title = $title;
        $this->duration = $duration;
    }


    public function getDuration()
    {
        if (empty($this->duration)) {
            $duration = "";
        } else {
            $durationS = ($this->duration % 60);
            $durationM = ($this->duration - $durationS) / 60;
            if ($durationS < 10) {
                $durationS = '0' . $durationS;
            }
            $duration = $durationM . ':' . $durationS;
        }
        return $duration;
    }

    /**
     * @return Video
     */
    public static function getRandomVideo($exclude = [])
    {
        $find = Video::find()->orderBy(new Expression('rand()'));
        if (!empty($exclude)) {
            $find = $find->andWhere(['not in', 'entity_id', $exclude]);
        }
        return $find->one();
    }
}
