<?php
namespace common\models\helpers;

use frontend\models\Lang;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * RandomTextValue
 *
 * @property integer $id
 * @property string $entity_type
 * @property string $value
 */
class RandomTextValue extends RandomValue
{

    const ENTITY_TYPE_BDAY = 'bday';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'random_text_values';
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
