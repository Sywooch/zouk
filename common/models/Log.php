<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "log".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $ip
 * @property string $url
 * @property string $post
 * @property string referrer
 * @property string user_agent
 * @property integer $date_create
 */
class Log extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'date_create'], 'integer'],
            [['date_create'], 'required'],
            [['ip'], 'string', 'max' => 30],
            [['url', 'referrer', 'user_agent'], 'string', 'max' => 255],
            [['post'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'ip' => 'Ip',
            'url' => 'Url',
            'psot' => 'Post',
            'referrer' => 'Referrer',
            'user_agent' => 'Useragent',
            'date_create' => 'Date Create',
        ];
    }

}
