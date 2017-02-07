<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\IdentityInterface;

/**
 * Class EntryModel
 *
 * @property integer     $id
 * @property integer     $user_id
 * @property string      $title
 * @property string      $description
 * @property int         $like_count
 * @property int         $show_count
 * @property int         $likes
 * @property int         $dislikes
 * @property string      $alias
 * @property int         $deleted
 * @property integer     $date_update
 * @property integer     $date_create
 *
 * @property User        $user
 */
class EntryModel extends VoteModel
{
    const THIS_ENTITY = 'item';

    public function getThisEntity()
    {
        return self::THIS_ENTITY;
    }

    public function getTitle()
    {
        return htmlspecialchars($this->title);
    }

    public function getTitle2()
    {
        return strip_tags($this->title);
    }

    public function isStopWord($text = '')
    {
        return parent::isStopWord($this->title) !== false || parent::isStopWord($this->description) !== false;
    }

    public function getShortDescription($length = 500, $end = '...')
    {
        $charset = 'UTF-8';
        $token = '~';
        $description = $this->description;
        $description = preg_replace("'<blockquote[^>]*?>.*?</blockquote>'si", " ", $description);
        $str = $description;
        $str = strip_tags($str);
        $str = preg_replace('/\s+/', ' ', $str);
        if (mb_strlen($str, $charset) >= $length) {
            $wrap = wordwrap($str, $length, $token);
            $str_cut = mb_substr($wrap, 0, mb_strpos($wrap, $token, 0, $charset), $charset);
            $str_cut .= $end;
            $str_cut = nl2br($str_cut);
            return $str_cut;
        } else {
            $str = nl2br($str);
            return $str;
        }
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

    public function rules()
    {
        return [
            [['description'], 'default', 'value' => ''],
            [['title'], 'required'],
            [['user_id', 'like_count', 'show_count', 'likes', 'dislikes', 'date_update', 'date_create'], 'integer'],
            [['title', 'alias'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 40000],
            [['like_count', 'show_count', 'likes', 'dislikes'], 'default', 'value' => 0],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTagEntity()
    {
        return $this->hasMany(TagEntity::className(), ['entity_id' => 'id'])->andOnCondition([TagEntity::tableName() . '.entity' => $this->getThisEntity()]);
    }

    public function getUrl($scheme = false, $addParams = [])
    {
        $str = get_class($this);
        $className = end(explode('\\', $str));
        if ($this->alias) {
            $params = [$className . '/view', 'alias' => $this->alias];
            $params = array_merge($params, $addParams);
            return Url::to($params, $scheme);
        } else {
            $params = [$className . '/view', 'index' => $this->id];
            $params = array_merge($params, $addParams);
            return Url::to($params, $scheme);
        }
    }

    /**
     * @return Img[]
     */
    public function getImgsSort()
    {
        $query = Img::find()
            ->innerJoin(EntityLink::tableName(), Img::tableName() . '.id = `' . EntityLink::tableName() . '`.entity_2_id')
            ->andWhere(['entity_1' => $this->getThisEntity(), 'entity_2' => Img::THIS_ENTITY, 'entity_1_id' => $this->id])
            ->orderBy(['sort' => SORT_ASC]);
        return $query->all();
    }
}