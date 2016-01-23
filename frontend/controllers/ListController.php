<?php
namespace frontend\controllers;

use common\models\Item;
use Yii;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Url;

/**
 * Site controller
 */
class ListController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['add'],
                'rules' => [
                    [
                        'actions' => ['add'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }


    public function actionAdd()
    {

        $item = new Item();
        if ($item->load(Yii::$app->request->post())) {
            $item->user_id = Yii::$app->user->identity->getId();
            $item->like_count = 0;
            $item->show_count = 0;
            if ($item = $item->save()) {
                return Yii::$app->getResponse()->redirect(Url::toRoute('/'));
            }
        }

        return $this->render(
            'add',
            ['item' => $item]
        );
    }
}