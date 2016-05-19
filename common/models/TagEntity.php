<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Class TagEntity
 *
 * @property int     $id
 * @property int     $tag_id
 * @property string  $entity
 * @property int     $entity_id
 * @property integer $date_update
 * @property integer $date_create
 *
 * @property Tags $tags
 */
class TagEntity extends ActiveRecord
{

    const ENTITY_ITEM = 'item';
    const ENTITY_EVENT = 'event';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tag_entity';
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
            [['tag_id', 'entity', 'entity_id'], 'unique', 'targetAttribute' => ['tag_id', 'entity', 'entity_id']],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'tag_id'      => 'ID tag',
            'entity'      => 'Сущность',
            'entity_id'   => 'ID сущности',
            'date_update' => 'Date Update',
            'date_create' => 'Date Create',
        ];
    }

    /**
     * @param string $name
     * @param string $tagGroup
     * @param string $entity
     * @param int    $entityId
     *
     * @return bool
     */
    public static function addTag($name, $tagGroup, $entity, $entityId)
    {
        if (!($tag = Tags::findOne(['name' => $name, 'tag_group' => $tagGroup]))) {
            $tag = new Tags();
            $tag->name = $name;
            $tag->tag_group = $tagGroup;
            $tag->save();
        }

        if ($tag) {
            if (!($tagEntity = TagEntity::findOne(['tag_id' => $tag->id, 'entity' => $entity, 'entity_id' => $entityId]))) {
                $tagEntity = new TagEntity();
                $tagEntity->tag_id = $tag->id;
                $tagEntity->entity = $entity;
                $tagEntity->entity_id = $entityId;
                $tagEntity->save();
            }

            if ($tagEntity) {
                return true;
            }
        }
        return false;
    }

    public function getTags()
    {
        return $this->hasOne(Tags::className(), ['id' => 'tag_id']);
    }
}
