<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "searchEntry".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $title
 * @property string $search_text
 * @property string $search_entity
 * @property integer $show_count
 * @property integer $date_update
 * @property integer $date_create
 */
class SearchEntry extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'searchEntry';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'show_count', 'date_update', 'date_create'], 'integer'],
            [['search_text'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['search_entity'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'user_id'       => 'User ID',
            'title'         => 'Title',
            'search_text'   => 'Search Text',
            'search_entity' => 'Search Entity',
            'show_count'    => 'Show Count',
            'date_update'   => 'Date Update',
            'date_create'   => 'Date Create',
        ];
    }
}
