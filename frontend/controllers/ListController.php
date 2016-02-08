<?php
namespace frontend\controllers;

use common\models\Item;
use common\models\ItemVideo;
use common\models\User;
use common\models\Video;
use common\models\Vote;
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

    const MAX_VIDEO_ITEM = 5;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['add', 'edit'],
                'rules' => [
                    [
                        'actions' => ['add', 'edit'],
                        'allow'   => true,
                        'roles'   => ['@'],
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
            if ($item->save()) {
                $videosUrl = Yii::$app->request->post('videos');
                // Добавление видео к записи
                if (!empty($videosUrl) && is_array($videosUrl)) {
                    $itemVideoCount = 0;
                    foreach ($videosUrl as $url) {
                        if (!empty($url)) {
                            $itemVideoCount++;
                            if ($itemVideoCount > self::MAX_VIDEO_ITEM) {
                                break;
                            }
                            $video = new Video();
                            $video->parseUrl($url);
                            $video->user_id = $item->user_id;
                            if ($video->save()) {
                                $itemVideo = new ItemVideo();
                                $itemVideo->item_id = $item->id;
                                $itemVideo->video_id = $video->id;
                                $itemVideo->save();
                            }
                        }
                    }
                }
                return Yii::$app->getResponse()->redirect(Url::to(['list/view', 'id' => $item->id]));
            }
        }

        return $this->render(
            'add',
            ['item' => $item]
        );
    }

    public function actionView($id)
    {
        $item = Item::findOne((int)$id);
        $thisUser = User::thisUser();
        $vote = !empty($thisUser) ? $thisUser->getVoteByEntity(Vote::ENTITY_ITEM, $id) : null;

        return $this->render(
            'view',
            [
                'item' => $item,
                'vote' => $vote,
            ]
        );
    }

    public function actionEdit($id)
    {
        /** @var Item $item */
        $item = Item::findOne($id);
        if ($item && $item->load(Yii::$app->request->post())) {
            if ($item = $item->save()) {
                return Yii::$app->getResponse()->redirect(Url::to(['list/view', 'id' => $id]));
            }
        }

        return $this->render(
            'edit',
            ['item' => $item]
        );
    }
}