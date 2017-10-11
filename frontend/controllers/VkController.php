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
 * Vk controller
 */
class VkController extends Controller
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
                        'actions' => ['post-random-video'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionPostRandomVideo()
    {
        return $this->redirect(Yii::$app->request->referrer);
        /** @var User $user */
        $user = Yii::$app->user->identity;
        /** @var VkAccessToken[] $accesses */
        $accesses = VkAccessToken::findAll(['user_id' => $user]);
        foreach ($accesses as $access) {
            /** @var VkontakteComponent $vkapi */
            $vkapi = \Yii::$app->vkapi;
            $vkapi->initAccessToken($access->access_token);
            $publishDate = strtotime('+7 min', time());

            $vkapi->postRandomVideo($access->group_id, $publishDate, []);
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

}