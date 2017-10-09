<?php
namespace frontend\controllers;

use common\components\vk\VkontakteComponent;
use common\models\form\SearchEntryForm;
use common\models\form\VkTaskForm;
use common\models\Item;
use common\models\School;
use common\models\search\VkTaskSearch;
use common\models\Ulogin;
use common\models\Video;
use common\models\VkAccessToken;
use common\models\VkTask;
use frontend\models\Lang;
use Yii;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\Event;
use yii\base\InvalidParamException;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\User;

/**
 * Site controller
 */
class VkSettingsController extends Controller
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
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error'   => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class'           => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        /** @var \common\models\User $user */
        $user = Yii::$app->user->identity;
        $vkAccessToken = \common\models\VkAccessToken::findOne(['user_id' => $user->id]);

        $searchModel = new VkTaskSearch();
        $request = \Yii::$app->request;
        $dataProvider = $searchModel->search(null, $request->get());

        return $this->render('index', [
            'vkAccessToken' => $vkAccessToken,
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
        ]);
    }


    public function actionGetAccessToken()
    {
        $request = Yii::$app->request;
        $groupId = $request->post('group_id');
        /** @var VkontakteComponent $vkapi */
        $vkapi = Yii::$app->vkapi;
        $vkapi->setRedirectUri('https://oauth.vk.com/blank.html');
        $vkapi->setGroupIds([$groupId]);
        $url = $vkapi->getLoginUrl();
        $code = $request->post('code');
        if (!empty($code)) {
            $url = $vkapi->getAccessTokenUrl($code);

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL            => $url, // Полный адрес метода
                CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
                CURLOPT_POST           => false,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            $response = curl_exec($curl); // Выполненяем запрос

            if ($response !== false) {
                $response = json_decode($response, true); // Декодируем из JSON в массив
                $key = 'access_token';
                if (isset($response[$key])) {
                    /** @var User $user */
                    $user = Yii::$app->user->identity;
                    $token = VkAccessToken::findOne(['user_id' => $user->id]);
                    if (empty($token)) {
                        $token = new VkAccessToken();
                    }
                    $token->setAttributes([
                        'user_id'      => $user->id,
                        'access_token' => $response[$key],
                        'expires_in'   => $response['expires_in'],
                    ]);
                    $token->save();
                    return $this->redirect(['vk-settings/index']);
                }
            }
        }

        return $this->redirect($url);
    }


    public function actionAddTask()
    {
        $vkTaskForm = new VkTaskForm();

        $request = Yii::$app->request;
        if ($request->isPost && $vkTaskForm->load($request->post()) && $vkTaskForm->saveNewVkTask()) {
            return $this->redirect(['vk-settings/index']);
        }

        return $this->render('addTask', [
            'vkTaskForm' => $vkTaskForm,
        ]);
    }

    public function actionDelete($id)
    {
        $vkTask = VkTask::findOne([
            'user_id' => Yii::$app->user->id,
            'id' => $id,
        ]);
        if ($vkTask) {
            $vkTask->delete();
        }
        return $this->redirect(['vk-settings/index']);
    }
}
