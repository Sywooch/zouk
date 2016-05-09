<?php
namespace common\models;

use DateInterval;
use DateTime;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Ulogin model
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $user_start_id
 * @property string  $identity
 * @property string  $network
 * @property string  $email
 * @property string  $firstname
 * @property string  $lastname
 * @property string  $nickname
 * @property integer $date_update
 * @property integer $date_create
 *
 * @property User    user
 * @property User    startUser
 */
class Ulogin extends ActiveRecord
{

    const ULOGIN_AUTH_URL = 'http://ulogin.ru/token.php?token=';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ulogin';
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


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'user_id'       => 'Пользователь текущий',
            'user_start_id' => 'Пользователь изначальный',
            'identity'      => 'ID в соц сети',
            'network'       => 'Социальная сеть',
            'email'         => 'Почта',
            'firstname'     => 'Имя',
            'lastname'      => 'Фамилия',
            'full_name'     => 'Заголовок видео',
            'date_update'   => 'Date Update',
            'date_create'   => 'Date Create',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'id']);
    }

    public function getStartUser()
    {
        return $this->hasOne(User::className(), ['user_start_id' => 'id']);
    }

    public function loadAuthData($token)
    {
        $authData = json_decode(file_get_contents(self::ULOGIN_AUTH_URL . $token . '&host=' . $_SERVER['HTTP_HOST']), true);
        if (isset($authData['identity']) && isset($authData['network'])) {
            $this->identity = $authData['identity'];
            $this->network = $authData['network'];
            $this->email = $authData['email'];
            $this->firstname = $authData['first_name'];
            $this->lastname = $authData['last_name'];
            $this->nickname = empty($authData['nickname']) ? $authData['first_name'] : $authData['nickname'];
            return $this;
        } else {
            return false;
        }
    }

    public function randomPassword($len = 12)
    {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $len; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    /**
     * @param string $identity
     * @param string $network
     * @param bool   $loadNull
     *
     * @return Ulogin
     */
    public static function findUlogin($identity, $network, $loadNull = false)
    {
        $query = Ulogin::find()->andWhere(
            'identity = :identity AND network = :network',
            [
                ':identity' => $identity,
                ':network'  => $network,
            ]
        );
        if (!$loadNull) {
            $query->andWhere('user_id <> 0 AND user_id is not null');
        }
        return $query->one();
    }

}
