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
 * Music model
 *
 * @property integer $id         Идентификатор
 * @property integer $user_id    Пользователь загрузивший аудиозапись
 * @property string  $key        Ключ для авторизации, чтобы узнать пользователя и пароль от хранилища
 * @property string  $entity_key Хранилище, например yandex
 * @property string  $url        Ссылка на файл, не прямая
 * @property string  $short_url  Короткая ссылка
 * @property string  $artist     Исполнитель
 * @property string  $title      Название
 * @property float   $playtime   Продолжительность аудиозаписи
 * @property integer $deleted    Удалена
 * @property integer $created_at
 * @property integer $updated_at
 * @property string  $password   write-only password
 */
class Music extends VoteModel
{

    const THIS_ENTITY = 'sound';

    /**
     * @var UploadedFile
     */
    public $musicFile;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%music}}';
    }

    public function getArtist()
    {
        return htmlspecialchars($this->artist);
    }

    public function getTitle()
    {
        return htmlspecialchars($this->title);
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
            [
                ['musicFile'],
                'file',
                'skipOnEmpty'              => true,
                'extensions'               => 'mp3, mpeg',
                'checkExtensionByMimeType' => false,
                'maxSize'                  => 15 * 1024 * 1024,
                'tooBig'                   => Lang::t('main/music', 'limitSize'),
            ],
            [['title', 'artist'], 'default', 'value' => ''],
            [['title', 'artist'], 'string', 'max' => 255],
            [['date_update', 'date_create'], 'integer'],
        ];
    }


    public static function getMusic($data)
    {
        $query = Music::find()
            ->andWhere('deleted = 0')
            ->limit(!empty($data['limit']) ? $data['limit'] : 20)
            ->orderBy('id DESC');

        if (!empty($data['userId'])) {
            $query->andWhere('user_id = :user_id', [':user_id' => $data['userId']]);
        }
        if (!empty($data['artistOrTitle'])) {
            $artistOrTitle = '%' . $data['artistOrTitle'] . '%';
            $query->andWhere('artist LIKE :value OR title LIKE :value', [':value' => $artistOrTitle]);
        }

        return $query->all();
    }
}
