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
 * Img model
 *
 * @property integer $id         Идентификатор
 * @property integer $user_id    Пользователь загрузивший картинку
 * @property string  $key        Ключ для авторизации, чтобы узнать пользователя и пароль от хранилища
 * @property string  $entity_key Хранилище, например yandex
 * @property string  $url        Ссылка на файл, не прямая
 * @property string  $short_url  Короткая ссылка
 * @property integer $created_at
 * @property integer $updated_at
 */
class Img extends ActiveRecord
{

    const THIS_ENTITY = 'img';

    /**
     * @var UploadedFile
     */
    public $imgFile;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%img}}';
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
                ['imgFile'],
                'file',
                'skipOnEmpty'              => true,
                'extensions'               => 'jpg, png, jpeg',
                'maxSize'                  => 5 * 1024 * 1024,
                'tooBig'                   => '', //Lang::t('main/img', 'limitSize'),
            ],
            [['date_update', 'date_create'], 'integer'],
        ];
    }


    public static function getImgs($data)
    {
        $query = Img::find()
            ->limit(!empty($data['limit']) ? $data['limit'] : 40)
            ->orderBy('id DESC');

        if (!empty($data['userId'])) {
            $query->andWhere('user_id = :user_id', [':user_id' => $data['userId']]);
        }

        return $query->all();
    }
}
