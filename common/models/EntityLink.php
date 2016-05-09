<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * EntityLink model
 *
 * @property integer $id
 * @property string  $entity_1
 * @property integer $entity_1_id
 * @property string  $entity_2
 * @property integer $entity_2_id
 * @property integer $sort
 * @property integer $date_update
 * @property integer $date_create
 */
class EntityLink extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'entity_link';
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
            'entity_1'    => 'Сущность 1',
            'entity_1_id' => 'ID сущности 1',
            'entity_2'    => 'Сущность 2',
            'entity_2_id' => 'ID сущности 2',
            'sort'        => 'Сортировка',
            'date_update' => 'Date Update',
            'date_create' => 'Date Create',
        ];
    }

}
