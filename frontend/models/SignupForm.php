<?php
namespace frontend\models;

use common\models\User;
use yii\base\Model;
use Yii;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $displayName;
    public $email;
    public $password;
    public $gRecaptchaResponse;

    public function load($data, $formName = null)
    {
        $this->gRecaptchaResponse = isset($data['g-recaptcha-response']) ? $data['g-recaptcha-response'] : '';
        return parent::load($data, $formName);
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    public function testCaptcha()
    {
        $result = !empty($this->gRecaptchaResponse);
        if ($result) {
            $url = 'https://www.google.com/recaptcha/api/siteverify';

            $data = [
                'secret' => Yii::$app->google->googleRecaptchaPrivate,
                'response' => $this->gRecaptchaResponse,
                'remoteip' => Yii::$app->request->getUserIP(),
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            $answer = json_decode(curl_exec($ch), true);
            curl_close($ch);

            $result = isset($answer['success']) && $answer['success'];
        }
        return $result;
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if ($this->testCaptcha() && $this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->display_name = !empty($this->displayName) ? $this->displayName : $this->username;
            $user->email = $this->email;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            if ($user->save()) {
                return $user;
            }
        }

        return null;
    }
}
