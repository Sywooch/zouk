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
 * VkAccessToken model
 *
 * @property integer $id         Идентификатор
 * @property integer $user_id    Пользователь загрузивший картинку
 * @property string  $group_id
 * @property string  $access_token
 * @property integer $expires_in
 * @property integer $created_at
 * @property integer $updated_at
 */
class VkAccessToken extends ActiveRecord
{

    const THIS_ENTITY = 'vk_access_token';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vk_access_tokens';
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
            [['user_id', 'expires_in', 'date_update', 'date_create'], 'integer'],
            [['group_id', 'access_token'], 'string'],
        ];
    }
}
