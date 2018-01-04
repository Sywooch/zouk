<?php
namespace common\models\helpers;

use frontend\models\Lang;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;

/**
 * RandomStringValue
 *
 * @property integer $id
 * @property string $entity_type
 * @property string $value
 */
class RandomStringValue extends RandomValue
{

    const ENTITY_TYPE_BDAY = 'bday_photo';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'random_string_values';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['entity_type', 'value'], 'string'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'entity_type' => 'Сущность',
            'value'       => 'Значение',
        ];
    }
}
