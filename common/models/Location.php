<?php
namespace common\models;

use frontend\models\Lang;
use Yii;
use yii\db\ActiveRecord;

/**
 * Location model
 *
 * @property integer $id
 * @property integer $user_id
 * @property float $lat
 * @property float $lng
 * @property integer $zoom
 * @property string $title
 * @property string $description
 * @property string $type
 * @property string $entity
 * @property string $entity_id
 * @property integer $deleted
 * @property integer $date_update
 * @property integer $date_create
 *
 * @property User user
 */
class Location extends ActiveRecord
{

    const LOCATION_TYPE_OTHER = 'other';
    const LOCATION_TYPE_SCHOOL = 'school';
    const LOCATION_TYPE_LOCATION = 'location';
    const LOCATION_TYPE_PARTY = 'party';

    public static function getLocationType()
    {
        return [
            self::LOCATION_TYPE_OTHER,
            self::LOCATION_TYPE_SCHOOL,
            self::LOCATION_TYPE_PARTY,
            self::LOCATION_TYPE_LOCATION,
        ];
    }

    public static function getLocationTypeLocal()
    {
        $locationType = array_flip(self::getLocationType());
        foreach ($locationType as $key => $value) {
            $locationType[$key] = Lang::t('main/location', $key);
        }
        return $locationType;
    }

    public function getTypeLocal()
    {
        return Lang::t('main/location', $this->type);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'location';
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
        ];
    }

    public function getTitle()
    {
        return htmlspecialchars($this->title);
    }

    public function getDescription()
    {
        return htmlspecialchars($this->description);
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'user_id'     => 'Пользователь текущий',
            'lat'         => 'lat',
            'lng'         => 'lng',
            'zoom'        => 'zoom',
            'title'       => 'Заголовок',
            'description' => 'Описание',
            'type'        => 'Тип',
            'entity'      => 'Сущность',
            'entity_id'   => 'Id',
            'deleted'     => 'Удален',
            'date_update' => 'Date Update',
            'date_create' => 'Date Create',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'id']);
    }

}
