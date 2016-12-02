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
            $result = Yii::$app->google->testCaptcha($this->gRecaptchaResponse, Yii::$app->request->getUserIP());
        }
        return $result;
    }

    /**
     * Signs user up.
     *
     * @param bool $testCaptcha
     * @return User|null the saved model or null if saving fails
     */
    public function signup($testCaptcha = true)
    {
        if (($this->testCaptcha() || !$testCaptcha) && $this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $displayName = !empty($this->displayName) ? $this->displayName : $this->username;
            if (User::findOne(['display_name' => $displayName])) {
                $i = 1;
                $newDisplayName = $displayName . $i;
                while (User::findOne(['display_name' => $newDisplayName])) {
                    $i++;
                    $newDisplayName = $displayName . $i;
                }
                $displayName = $newDisplayName;
            }
            $user->display_name = $displayName;

            $user->email = $this->email;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            if ($user->save()) {
                return $user;
            }
            var_dump($user->display_name);
        }

        return null;
    }
}
