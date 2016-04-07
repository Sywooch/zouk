<?php
namespace frontend\controllers;

use common\models\Item;
use common\models\Ulogin;
use frontend\models\Lang;
use Yii;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
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
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['logout', 'signup', 'uloginbind', 'uloginunbind'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow'   => true,
                        'roles'   => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'uloginbind', 'uloginunbind'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
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
        $searchTag = Yii::$app->request->get('tag', '');
        return $this->render('index', ['searchTag' => $searchTag]);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', Lang::t('page/contact', 'sendSuccess'));
            } else {
                Yii::$app->session->setFlash('error', Lang::t('page/contact', 'sendError'));
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('aboutLang/about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', Lang::t('page/siteLogin', 'reset1Success'));

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', Lang::t('page/siteLogin', 'reset1Error'));
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     *
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', Lang::t('page/siteLogin', 'reset2Success'));

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionSitemap()
    {
        // проверяем есть ли закэшированная версия sitemap
        $urls = array();

        $items = Item::find()->where(['deleted' => 0])->all();

        foreach ($items as $item) {
            /** @var Item $item */
            $urls[] = $item->getUrl(true);
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_XML;
        echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($urls as $url) {
            echo '<url>';
            echo '<loc>' . $url . '</loc>';
            echo '<changefreq>daily</changefreq>';
            echo '<priority>0.5</priority>';
            echo '</url>';
        }
        echo '</urlset>';
    }


    public function actionUloginunbind()
    {
        $thisUser = \common\models\User::thisUser();
        $socialId = Yii::$app->request->post('social');
        $ulogin = Ulogin::findOne(['id' => $socialId, 'user_id' => $thisUser->id]);
        if ($ulogin) {
            $ulogin->user_id = 0;
            $ulogin->save();
            $this->redirect(Url::to(['account/profile']));
        }
    }

    public function actionUloginbind()
    {
        $thisUser = \common\models\User::thisUser();
        $loginUlogin = Yii::$app->request->post('login_ulogin');
        if (!empty($loginUlogin)) {
            $ulogin = new Ulogin();
            if ($ulogin->loadAuthData($loginUlogin)) {
                $modelInBase = Ulogin::findUlogin($ulogin->identity, $ulogin->network);
                if (!empty($modelInBase)) {
                    $modelInBase->user_id = $thisUser->id;
                    $modelInBase->save();
                } else {
                    $ulogin->user_id = $thisUser->id;
                    $ulogin->save();
                }
                $this->redirect(Url::to(['account/profile']));
            }
        }
    }

    public function actionUlogin()
    {
        $loginUlogin = Yii::$app->request->post('login_ulogin');
        if (!empty($loginUlogin)) {
            $ulogin = new Ulogin();
            if ($ulogin->loadAuthData($loginUlogin)) {
                $modelInBase = Ulogin::findUlogin($ulogin->identity, $ulogin->network);
                if (!empty($modelInBase)) {
                    // войти под пользователем $modelInBase
                    $user = \common\models\User::findOne($modelInBase->user_id);
                    if (!empty($user)) {
                        if (Yii::$app->getUser()->login($user)) {
                            return $this->goHome();
                        }
                    }
                } else {
                    if ($user = \common\models\User::findOne(['email' => $ulogin->email])) {
                        // Прикрепляем к существующему
                        if (empty($user->firstname) || empty($user->lastname)) {
                            $user->firstname = empty($user->firstname) ? $user->firstname : $ulogin->firstname;
                            $user->lastname = empty($user->lastname) ? $user->lastname : $ulogin->lastname;
                            $user->save();
                        }
                    } elseif ($user = \common\models\User::findOne(['username' => $ulogin->network . '_' . md5($ulogin->identity)])) {

                    } else {
                        // зарегистрировать нового пользователя
                        $signupForm = new SignupForm();
                        $signupForm->username = $ulogin->network . '_' . md5($ulogin->identity);
                        $signupForm->displayName = $ulogin->nickname;
                        $signupForm->password = $ulogin->randomPassword();
                        $signupForm->email = $ulogin->email;
                        $user = $signupForm->signup();
                        if (!empty($user)) {
                            $user->firstname = $ulogin->firstname;
                            $user->lastname = $ulogin->lastname;
                            $user->save();
                        }
                    }

                    if (!empty($user)) {
                        $ulogin->user_id = $user->id;
                        $ulogin->user_start_id = $user->id;
                        $ulogin->save();
                        if (Yii::$app->getUser()->login($user)) {
                            return $this->goHome();
                        }
                    }
                }
            };
        }
    }

}
