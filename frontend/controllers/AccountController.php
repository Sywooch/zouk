<?php
namespace frontend\controllers;

use common\components\vk\VkontakteComponent;
use common\models\Ulogin;
use common\models\User;
use common\models\VkAccessToken;
use frontend\models\ChangePasswordForm;
use frontend\models\Lang;
use Yii;
use yii\web\Controller;
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
                'rules' => [
                    [
                        'actions' => ['profile', 'edit', 'settings', 'view', 'editavatar', 'get-access-token', 'set-access-token'],
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
        return $this->render('profile', ['user' => $user, 'isThisUser' => true]);
    }

    public function actionEdit()
    {
        $user = User::thisUser();
        $userinfo = $user->getUerinfo();
        if (Yii::$app->request->isPost) {
            $userPost = Yii::$app->request->post('User');
            $user->display_name = isset($userPost['display_name']) ? $userPost['display_name'] : $user->display_name;
            $user->display_name = str_replace('/', '', strip_tags($user->display_name));
            $user->firstname = isset($userPost['firstname']) ? $userPost['firstname'] : $user->firstname;
            $user->lastname = isset($userPost['lastname']) ? $userPost['lastname'] : $user->lastname;

            $userinfoPost = Yii::$app->request->post('Userinfo');
            $userinfo->country = $userinfoPost['country'];
            $userinfo->city = $userinfoPost['city'];
            $userinfo->birthday = strtotime($userinfoPost['birthday']);
            $userinfo->about_me = $userinfoPost['about_me'];
            $userinfo->telephone = $userinfoPost['telephone'];
            $userinfo->skype = $userinfoPost['skype'];
            $userinfo->vk = $userinfoPost['vk'];
            $userinfo->fb = $userinfoPost['fb'];

            if ($user->save() && $userinfo->save()) {
                return Yii::$app->getResponse()->redirect(Url::to(['account/profile']));
            }
        }

        return $this->render(
            'edit',
            [
                'user'     => $user,
                'userinfo' => $userinfo,
            ]
        );
    }

    public function actionSettings()
    {
        $user = User::thisUser();
        $changePasswordModel = new ChangePasswordForm();
        if ($changePasswordModel->load(Yii::$app->request->post()) && $changePasswordModel->validate() && $changePasswordModel->changePassword()) {
            Yii::$app->session->setFlash('success', Lang::t('page/accountProfile', 'changePasswordSuccess'));

            return $this->redirect(['account/settings']);
        }

        $ulogins = Ulogin::findAll(['user_id' => $user->id]);

        return $this->render(
            'settings',
            [
                'user'                => $user,
                'changePasswordModel' => $changePasswordModel,
                'ulogins'             => $ulogins,
            ]
        );
    }

    public function actionEditavatar()
    {
        $user = User::thisUser();
        if (Yii::$app->request->isPost) {
            $user->imageFile = UploadedFile::getInstance($user, 'imageFile');
            if ($user->imageFile instanceof UploadedFile && $user->validate('imageFile')) {
                $uploadInfo = Yii::$app->cloudinary->uploadFromFile(
                    $user->imageFile->tempName,
                    'a' . md5("avatar_" . $user->id),
                    ["avatar"]
                );
                $user->avatar_pic = $uploadInfo['secure_url'];

                if ($user->save()) {
                    return Yii::$app->getResponse()->redirect(Url::to(['account/profile']));
                }
            }
        }
        return json_encode(['msg' => '']);
    }

    public function actionView($account)
    {
        if (empty($account)) {
            return $this->redirect(['account/profile']);
        }
        $user = User::findOne(['display_name' => $account]);
        if (empty($user)) {
            return $this->redirect(['account/profile']);
        }
        return $this->render('profile', ['user' => $user, 'isThisUser' => false]);
    }

}