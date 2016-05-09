<?php
namespace frontend\models;

use common\models\User;
use yii\base\InvalidParamException;
use yii\base\Model;
use Yii;

/**
 * Password change form
 */
class ChangePasswordForm extends Model
{
    public $password;

    public $newPassword;

    /**
     * @var \common\models\User
     */
    private $_user;


    /**
     * Creates a form model given a token.
     *
     * @param  array                           $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($config = [])
    {
        $this->_user = User::thisUser();
        if (!$this->_user) {
            throw new InvalidParamException('Wrong password reset token.');
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password', 'newPassword'], 'required'],
            [['password', 'newPassword'], 'string', 'min' => 6],
        ];
    }

    /**
     * Resets password.
     *
     * @return boolean if password was reset.
     */
    public function changePassword()
    {
        $user = $this->_user;
        if ($user->validatePassword($this->password)) {
            $user->setPassword($this->newPassword);
            return $user->save(false);
        }
        $this->addError('password', Lang::t('page/accountProfile', 'changePasswordIncorrect'));
        return false;
    }
}
