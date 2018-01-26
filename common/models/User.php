<?php
namespace common\models;

use frontend\models\Lang;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;

/**
 * User model
 *
 * @property integer      $id
 * @property string       $username
 * @property string       $firstname
 * @property string       $lastname
 * @property string       $display_name
 * @property UploadedFile $imageFile
 * @property string       $avatar_pic
 * @property string       $password_hash
 * @property string       $password_reset_token
 * @property string       $email
 * @property string       $auth_key
 * @property integer      $status
 * @property integer      $reputation
 * @property integer      $created_at
 * @property integer      $updated_at
 * @property integer      $date_blocked
 * @property string       $password  write-only password
 * @property string       $new_password
 *
 * @property VkTask       $vkTasks
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE  = 10;

    const PERMISSION_CREATE_ITEMS = 'create_items';
    const PERMISSION_EDIT_ITEMS = 'edit_items';
    const PERMISSION_DELETE_ITEMS = 'delete_items';

    const PERMISSION_CREATE_EVENTS = 'create_events';
    const PERMISSION_EDIT_EVENTS = 'edit_events';
    const PERMISSION_DELETE_EVENTS = 'delete_events';
    const PERMISSION_APPROVED_EVENTS = 'approved_events';

    const PERMISSION_CREATE_SCHOOLS = 'create_schools';
    const PERMISSION_EDIT_SCHOOLS = 'edit_schools';
    const PERMISSION_DELETE_SCHOOLS = 'delete_schools';

    const PERMISSION_MOCK_USER = 'mock_user';

    const ROLE_ADMIN = 'admin';
    const ROLE_MODERATOR = 'moderator';
    const ROLE_USER = 'user';
    const ROLE_MOCK_USER = 'mock';

    /**
     * @var UploadedFile
     */
    public $imageFile;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['display_name', 'unique', 'message' => Lang::t('page/accountProfile', 'display_name_error')],
            ['display_name', 'required', 'message' => Lang::t('page/accountProfile', 'display_name_error2')],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            [['date_blocked'], 'integer'],
            [
                ['imageFile'],
                'file',
                'skipOnEmpty' => true,
                'extensions'  => 'jpg, png, jpeg',
                'maxSize'     => 5 * 1024 * 1024,
                'tooBig'      => Lang::t('page/accountProfile', 'limitSize'),
            ],
        ];
    }

    public function getFirstname()
    {
        return htmlspecialchars($this->firstname);
    }

    public function getLastname()
    {
        return htmlspecialchars($this->lastname);
    }


    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     *
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     *
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status'               => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     *
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     *
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @param $entity
     * @param $entity_id
     *
     * @return null|Vote
     */
    public function getVoteByEntity($entity, $entity_id)
    {
        return Vote::findOne(['user_id' => $this->id, 'entity' => $entity, 'entity_id' => $entity_id]);
    }

    /**
     * @param $entity
     * @param $entityIds
     *
     * @return Vote[]
     */
    public function getVotesByEntity($entity, $entityIds)
    {
        return Vote::findAll(['user_id' => $this->id, 'entity' => $entity, 'entity_id' => $entityIds]);
    }

    /**
     * @return null|User
     */
    public static function thisUser()
    {
        return Yii::$app->user->isGuest ? null : Yii::$app->user->identity;
    }

    public function getAvatarPic()
    {
        $imgUrl = $this->avatar_pic;
        if (empty($imgUrl)) {
            $imgUrl = Yii::$app->UrlManager->to('img/no_avatar.png');
        }

        return $imgUrl;
    }

    public function getDisplayName()
    {
        $auth = \Yii::$app->authManager;
        if ($auth->checkAccess($this->id, User::PERMISSION_MOCK_USER)) {
            return 'Аноним';
        }

        if (empty($this->display_name)) {
            return htmlspecialchars($this->username);
        }
        return htmlspecialchars($this->display_name);
    }

    public function isMock()
    {
        $auth = \Yii::$app->authManager;
        return $auth->checkAccess($this->id, User::PERMISSION_MOCK_USER);
    }

    public function getAUserLink()
    {
        $auth = \Yii::$app->authManager;
        if ($auth->checkAccess($this->id, User::PERMISSION_MOCK_USER)) {
            return 'Аноним (<b>' . $this->reputation . '</b>)';
        } else {
            return Html::a($this->getDisplayName() . ' (<b>' . $this->reputation . '</b>)', ['user/' . $this->display_name]);
        }
    }

    public function getLastAudio()
    {
        return Music::getMusic(['userId' => $this->id]);
    }

    public function getUserImgs()
    {
        return Img::getImgs(['userId' => $this->id]);
    }

    public static function isBot()
    {
        $bots = [
            'rambler', 'googlebot', 'aport', 'yahoo', 'msnbot', 'turtle', 'mail.ru', 'omsktele',
            'yetibot', 'picsearch', 'sape.bot', 'sape_context', 'gigabot', 'snapbot', 'alexa.com',
            'megadownload.net', 'askpeter.info', 'igde.ru', 'ask.com', 'qwartabot', 'yanga.co.uk',
            'scoutjet', 'similarpages', 'oozbot', 'shrinktheweb.com', 'aboutusbot', 'followsite.com',
            'dataparksearch', 'google-sitemaps', 'appEngine-google', 'feedfetcher-google',
            'liveinternet.ru', 'xml-sitemaps.com', 'agama', 'metadatalabs.com', 'h1.hrn.ru',
            'googlealert.com', 'seo-rus.com', 'yaDirectBot', 'yandeG', 'yandex',
            'yandexSomething', 'Copyscape.com', 'AdsBot-Google', 'domaintools.com',
            'Nigma.ru', 'bing.com', 'dotnetdotcom', 'tweetmemebot', 'twitterbot',
        ];
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'none';
        foreach ($bots as $bot)
            if (stripos($userAgent, $bot) !== false) {
                return true;
            }
        return false;
    }

    /**
     * @return Userinfo
     */
    public function getUerinfo()
    {
        $userinfo = Userinfo::findOne(['user_id' => $this->id]);
        if (empty($userinfo)) {
            $userinfo = new Userinfo();
            $userinfo->user_id = $this->id;
            $userinfo->save();
        }
        return $userinfo;
    }

   public function getVkAccessTokens()
   {
       return $this->hasMany(VkAccessToken::class, ['user_id' => 'id']);
   }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVkTasks()
    {
        return $this->hasMany(VkTask::class, ['user_id' => 'id']);
    }
}
