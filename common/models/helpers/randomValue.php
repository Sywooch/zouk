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
 * RandomValue
 */
class RandomValue extends ActiveRecord
{

    /**
     * @param $entityType
     * @return RandomStringValue
     */
    public static function getRandomValue($entityType)
    {
        return self::find()
            ->where(['entity_type' => $entityType])
            ->orderBy(new Expression('rand()'))
            ->one();
    }
}
