<?php
namespace frontend\controllers;

use common\models\Alarm;
use common\models\form\SearchEntryForm;
use common\models\Item;
use common\models\EntityLink;
use common\models\TagEntity;
use common\models\Tags;
use common\models\User;
use common\models\Video;
use common\models\Vote;
use frontend\models\Lang;
use frontend\widgets\ItemList;
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

    public $thisPage = 'list';

    public $searchPath = 'list/index';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['add', 'edit', 'delete', 'alarm', 'share-to-instagram'],
                'rules' => [
                    [
                        'actions' => ['add', 'edit', 'delete', 'alarm', 'share-to-instagram'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }


    public function actionAdd()
    {
        $thisUser = User::thisUser();
        if (!Yii::$app->user->can(User::PERMISSION_CREATE_ITEMS)) {
            return Yii::$app->getResponse()->redirect(Url::home());
        }
        $item = new Item();
        if (Yii::$app->request->isPost && $item->load(Yii::$app->request->post())) {
            $gRecaptchaResponse = Yii::$app->request->post('g-recaptcha-response');
            if (!empty($gRecaptchaResponse)
                && Yii::$app->google->testCaptcha($gRecaptchaResponse, Yii::$app->request->getUserIP())
                || !Yii::$app->params['gRecaptchaResponse']
            ) {
                $item->description = \yii\helpers\HtmlPurifier::process($item->description, []);
//                if ($thisUser->reputation < Item::MIN_REPUTATION_ITEM_CREATE_NO_STOP_WORD) {
//                    if ($item->isStopWord()) {
//                        Yii::$app->session->setFlash('error', Lang::t('main', 'stopWord'));
//                        return Yii::$app->getResponse()->redirect(Url::home());
//                    }
//                }

                $item->user_id = $thisUser->id;
                $item->like_count = 0;
                $item->likes = 0;
                $item->dislikes = 0;
                $item->show_count = 0;
                if ($item->save()) {
                    // Добавление видео к записи
                    $videosUrl = Yii::$app->request->post('videos');
                    if (!empty($videosUrl) && is_array($videosUrl)) {
                        $item->saveVideos($videosUrl, $item->user_id);
                    }
                    // Добавляем аудиозаписи к записи
                    $sounds = Yii::$app->request->post('sounds');
                    if (!empty($sounds) && is_array($sounds)) {
                        $item->saveSounds($sounds);
                    } else {
                        $item->saveSounds([]);
                    }
                    // Добавляем теги
                    $tags = explode(',', Yii::$app->request->post('tags'));
                    if (is_array($tags)) {
                        $item->saveTags($tags);
                    }
                    // Добавляем картинки к записи
                    $imgs = Yii::$app->request->post('imgs');
                    if (!empty($imgs) && is_array($imgs)) {
                        $item->saveImgs($imgs);
                    } else {
                        $item->saveImgs([]);
                    }


                    return Yii::$app->getResponse()->redirect($item->getUrl());
                }
            }
        }
        Yii::$app->params['jsZoukVar']['tagsAll'] = Tags::getTags(Tags::TAG_GROUP_ALL);

        return $this->render(
            'add',
            ['item' => $item]
        );
    }

    public function actionView()
    {
        $item = null;
        if ($id = Yii::$app->request->get('index', null)) {
            $item = Item::findOne((int)$id);
        } elseif ($id = Yii::$app->request->get('id', null)) {
            $item = Item::findOne((int)$id);
        } else {
            $alias = Yii::$app->request->get('alias', null);
            $item = Item::findOne(['alias' => $alias]);
        }
        if (empty($item)) {
            return;
        }

        if ($item->deleted) {
            return $this->render('viewDeleted');
        }

        if ($anchor = Yii::$app->request->get('comment', null)) {
            Yii::$app->params['jsZoukVar']['anchor'] = $anchor;
        }

        if (!User::isBot()) {
            $item->addShowCount();
        }
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
        if (!Yii::$app->user->can(User::PERMISSION_EDIT_ITEMS, ['object' => $item])) {
            return Yii::$app->getResponse()->redirect($item->getUrl());
        }
        if ($item && $item->load(Yii::$app->request->post())) {
            $item->description = \yii\helpers\HtmlPurifier::process($item->description, []);
            if ($item->save()) {
                EntityLink::deleteAll(['entity_1' => Item::THIS_ENTITY, 'entity_1_id' => $item->id, 'entity_2' => Video::THIS_ENTITY]);
                // Добавление видео к записи
                $videosUrl = Yii::$app->request->post('videos');
                if (!empty($videosUrl) && is_array($videosUrl)) {
                    $item->saveVideos($videosUrl, $item->user_id);
                }
                // Добавляем аудиозаписи к записи
                $sounds = Yii::$app->request->post('sounds');
                if (!empty($sounds) && is_array($sounds)) {
                    $item->saveSounds($sounds);
                } else {
                    $item->saveSounds([]);
                }
                // Добавляем картинки к записи
                $imgs = Yii::$app->request->post('imgs');
                if (!empty($imgs) && is_array($imgs)) {
                    $item->saveImgs($imgs);
                } else {
                    $item->saveImgs([]);
                }

                TagEntity::deleteAll(['entity' => TagEntity::ENTITY_ITEM, 'entity_id' => $item->id]);
                $tags = explode(',', Yii::$app->request->post('tags'));
                if (is_array($tags)) {
                    $item->saveTags($tags);
                }


                return Yii::$app->getResponse()->redirect($item->getUrl());
            }
        }
        Yii::$app->params['jsZoukVar']['tagsAll'] = Tags::getTags(Tags::TAG_GROUP_ALL);

        return $this->render(
            'edit',
            ['item' => $item]
        );
    }

    public function actionDelete($id)
    {
        /** @var Item $item */
        $item = Item::findOne($id);
        if ($item && Yii::$app->user->can(User::PERMISSION_DELETE_ITEMS, ['object' => $item])) {
            $item->deleted = 1;
            if ($item->save()) {
                return Yii::$app->getResponse()->redirect(Url::home());
            };
        }

        return Yii::$app->getResponse()->redirect($item->getUrl());
    }

    public function actionShareToInstagram($id)
    {
        /** @var Item $item */
        $item = Item::findOne($id);
        if ($item && (Yii::$app->user->can(User::ROLE_ADMIN) || Yii::$app->user->can(User::ROLE_MODERATOR))) {
            $k = 0;
            foreach ($item->videos as $video) {
                $imgUrl = $video->getThumbnailUrl();
                $comment = $item->title . "\n" . $video->video_title . "\n";

                $tags = $item->tagEntity;

                $tagValues = ['#prozouk', '#zouk'];
                foreach ($tags as $tag) {
                    $tagItem = $tag->tags;
                    $tagName = $tagItem->getName();
                    $tagName = str_replace(' ', '', $tagName);
                    if (!empty($tagName)) {
                        $tagValues[] = '#' . $tagName;
                    }
                }
                $comment .= join(' ', $tagValues) . "\n";
                $comment .= Url::to(['list/view', 'id' => $item->alias], true);

                /** @var \common\components\InstagramComponent $x */
                $instagram = Yii::$app->Instagram;
                $result = $instagram->sendInstagramm(
                    $imgUrl,
                    $comment
                );
                if ($result !== false) {
                    $k++;
                }
            }
            if ($k > 0) {
                $item->shared_instagram = true;
                $item->save();
            }
        }
        return $this->redirect(['list/view', 'id' => $id]);
    }

    public function actionItems()
    {
        $lastId = Yii::$app->request->post('lastId', 0);
        $order = Yii::$app->request->post('order', ItemList::ORDER_BY_ID);
        $searchTag = Yii::$app->request->post('tag', '');
        return ItemList::widget(['lastId' => $lastId, 'onlyItem' => true, 'orderBy' => $order, 'searchTag' => $searchTag]);
    }

    public function actionIndex()
    {
        $searchEntryForm = SearchEntryForm::loadFromPost();
        $request = Yii::$app->request;
        $page = $request->get('page', $request->post('page', 0));
        if ($request->isAjax) {
            return $this->renderPartial('index', ['searchEntryForm' => $searchEntryForm, 'page' => $page]);
        }
        return $this->render('index', ['searchEntryForm' => $searchEntryForm, 'page' => $page]);
    }

    public function actionWeek()
    {
        $searchTag = Yii::$app->request->get('tag', '');
        return $this->render('listWeek', ['searchTag' => $searchTag]);
    }

    public function actionMonth()
    {
        $searchTag = Yii::$app->request->get('tag', '');
        return $this->render('listMonth', ['searchTag' => $searchTag]);
    }

    public function actionPopular()
    {
        $this->searchPath = 'list/popular';
        $searchEntryForm = SearchEntryForm::loadFromPost();
        $request = Yii::$app->request;
        $page = $request->get('page', $request->post('page', 0));
        if ($request->isAjax) {
            return $this->renderPartial('listPopular', ['searchEntryForm' => $searchEntryForm, 'page' => $page]);
        }
        return $this->render('listPopular', ['searchEntryForm' => $searchEntryForm, 'page' => $page]);
    }

    public function actionAlarm()
    {
        $id = Yii::$app->request->post('id');
        $msg = Yii::$app->request->post('msg');
        $item = Item::findOne($id);
        if ($item && !empty($msg)) {
            if (Alarm::addAlarm(Alarm::ENTITY_ITEM, $item->id, $msg)) {
                $resultMsg = Lang::t('main/dialogs', 'modalAlarm_msgAlarmResultTrue');
                Yii::$app->session->setFlash('success', $resultMsg);
            } else {
                $resultMsg = Lang::t('main/dialogs', 'modalAlarm_msgAlarmResultFalse');
                Yii::$app->session->setFlash('success', $resultMsg);
            }
            return json_encode(['msg' => $resultMsg]);
        }
    }
    
}