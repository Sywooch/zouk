<?php
namespace frontend\controllers;

use common\models\User;
use Yii;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\bootstrap\Html;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * Site controller
 */
class AccountController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['profile'],
                'rules' => [
                    [
                        'actions' => ['profile'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionProfile()
    {
        $user = User::thisUser();
        return $this->render('profile', ['user' => $user]);
    }

    public function actionEdit()
    {
        $user = User::thisUser();
        if (Yii::$app->request->isPost) {
            $userPost = Yii::$app->request->post('User');

            $user->imageFile = UploadedFile::getInstance($user, 'imageFile');
            if ($user->imageFile instanceof UploadedFile && $user->validate('imageFile')) {
                $uploadInfo = Yii::$app->cloudinary->uploadFromFile(
                    $user->imageFile->tempName,
                    'a' . md5("avatar_" . $user->id),
                    ["avatar"]
                );
                $user->avatar_pic = $uploadInfo['url'];
            }

            $user->display_name = isset($userPost['display_name']) ? $userPost['display_name'] : $user->display_name;
            $user->firstname = isset($userPost['firstname']) ? $userPost['firstname'] : $user->firstname;
            $user->lastname = isset($userPost['lastname']) ? $userPost['lastname'] : $user->lastname;
            $user->save();
            return Yii::$app->getResponse()->redirect(Url::to(['account/profile']));
        }

        return $this->render('edit', ['user' => $user]);
    }

}