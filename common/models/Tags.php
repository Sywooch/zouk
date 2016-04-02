<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Class Tags
 *
 * @property int     $id
 * @property string  $name
 * @property string  $tag_group
 * @property integer $date_update
 * @property integer $date_create
 */
class Tags extends ActiveRecord
{

    const TAG_GROUP_ALL = 'all';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tags';
    }

    public function getName()
    {
        return strip_tags($this->name);
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
            [['name', 'tag_group'], 'unique', 'targetAttribute' => ['name', 'tag_group']],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'name'        => 'Название',
            'tag_group'   => 'Группировка',
            'date_update' => 'Date Update',
            'date_create' => 'Date Create',
        ];
    }

    public static function getTags($tagGroup)
    {
        $tags = Tags::findAll(['tag_group' => $tagGroup]);
        $result = [];
        foreach ($tags as $tag) {
            $result[] = $tag->getName();
        }
        return $result;
    }

}
