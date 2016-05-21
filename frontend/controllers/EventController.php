<?php
namespace frontend\controllers;

use common\models\Alarm;
use common\models\Event;
use common\models\TagEntity;
use common\models\Tags;
use common\models\User;
use common\models\Vote;
use frontend\models\Lang;
use frontend\widgets\EventList;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\helpers\Url;

/**
 * Site controller
 */
class EventController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['add', 'edit', 'delete', 'alarm'],
                'rules' => [
                    [
                        'actions' => ['add', 'edit', 'delete', 'alarm'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }


    public function actionAdd()
    {
        if (User::thisUser()->reputation < Event::MIN_REPUTATION_Event_CREATE) {
            return Yii::$app->getResponse()->redirect(Url::home());
        }
        $event = new Event();
        if ($event->load(Yii::$app->request->post())) {
            $event->description = \yii\helpers\HtmlPurifier::process($event->description, []);
            $event->user_id = Yii::$app->user->identity->getId();
            $event->like_count = 0;
            $event->show_count = 0;
            if ($event->save()) {
                // Добавляем теги
                $tags = explode(',', Yii::$app->request->post('tags'));
                $event->saveTags($tags);
                // Добавляем картинки к записи
                $imgs = Yii::$app->request->post('imgs');
                if (!empty($imgs) && is_array($imgs)) {
                    $event->saveImgs($imgs);
                } else {
                    $event->saveImgs([]);
                }


                return Yii::$app->getResponse()->redirect($event->getUrl());
            }
        }
        Yii::$app->params['jsZoukVar']['tagsAll'] = Tags::getTags(Tags::TAG_GROUP_ALL);

        return $this->render(
            'add',
            ['event' => $event]
        );
    }

    public function actionView()
    {
        $event = null;
        if ($id = Yii::$app->request->get('index', null)) {
            $event = Event::findOne((int)$id);
        } elseif ($id = Yii::$app->request->get('id', null)) {
            $event = Event::findOne((int)$id);
        } else {
            $alias = Yii::$app->request->get('alias', null);
            $event = Event::findOne(['alias' => $alias]);
        }
        if (empty($event)) {
            return;
        }

        if ($event->deleted) {
            return $this->render('viewDeleted');
        }

        if ($anchor = Yii::$app->request->get('comment', null)) {
            Yii::$app->params['jsZoukVar']['anchor'] = $anchor;
        }

        if (!User::isBot()) {
            $event->addShowCount();
        }
        $thisUser = User::thisUser();
        $vote = !empty($thisUser) ? $thisUser->getVoteByEntity(Vote::ENTITY_EVENT, $id) : null;

        return $this->render(
            'view',
            [
                'event' => $event,
                'vote'  => $vote,
            ]
        );
    }

    public function actionEdit($id)
    {
        /** @var Event $event */
        $event = Event::findOne($id);
        if ($event->user_id != User::thisUser()->id || $event->deleted) {
            return Yii::$app->getResponse()->redirect($event->getUrl());
        }
        if ($event && $event->load(Yii::$app->request->post())) {
            $event->description = \yii\helpers\HtmlPurifier::process($event->description, []);
            if ($event->save()) {
                // Добавляем картинки к записи
                $imgs = Yii::$app->request->post('imgs');
                if (!empty($imgs) && is_array($imgs)) {
                    $event->saveImgs($imgs);
                } else {
                    $event->saveImgs([]);
                }

                TagEntity::deleteAll(['entity' => TagEntity::ENTITY_EVENT, 'entity_id' => $event->id]);
                $tags = explode(',', Yii::$app->request->post('tags'));
                if (is_array($tags)) {
                    $event->saveTags($tags);
                }


                return Yii::$app->getResponse()->redirect($event->getUrl());
            }
        }
        Yii::$app->params['jsZoukVar']['tagsAll'] = Tags::getTags(Tags::TAG_GROUP_ALL);

        return $this->render(
            'edit',
            ['event' => $event]
        );
    }

    public function actionDelete($id)
    {
        /** @var Event $event */
        $event = Event::findOne($id);
        if ($event && $event->user_id == User::thisUser()->id) {
            $event->deleted = 1;
            if ($event->save()) {
                return Yii::$app->getResponse()->redirect(Url::home());
            };
        }

        return Yii::$app->getResponse()->redirect($event->getUrl());
    }

    public function actionEvents()
    {
        $lastId = Yii::$app->request->post('lastId', 0);
        $order = Yii::$app->request->post('order', EventList::ORDER_BY_DATE);
        $searchTag = Yii::$app->request->post('tag', '');
        return EventList::widget(['lastId' => $lastId, 'onlyEvent' => true, 'orderBy' => $order, 'searchTag' => $searchTag]);
    }

    public function actionAll()
    {
        $searchTag = Yii::$app->request->get('tag', '');
        return $this->render('listAll', ['searchTag' => $searchTag]);
    }

    public function actionBefore()
    {
        $searchTag = Yii::$app->request->get('tag', '');
        return $this->render('listBefore', ['searchTag' => $searchTag]);
    }

    public function actionAfter()
    {
        $searchTag = Yii::$app->request->get('tag', '');
        return $this->render('listAfter', ['searchTag' => $searchTag]);
    }

    public function actionAlarm()
    {
        $id = Yii::$app->request->post('id');
        $msg = Yii::$app->request->post('msg');
        $event = Event::findOne($id);
        if ($event && !empty($msg)) {
            if (Alarm::addAlarm(Alarm::ENTITY_EVENT, $event->id, $msg)) {
                $resultMsg = Lang::t('page/listView', 'msgAlarmResultTrue');
                Yii::$app->session->setFlash('success', Lang::t('page/listView', 'msgAlarmResultTrue'));
            } else {
                $resultMsg = Lang::t('page/listView', 'msgAlarmResultFalse');
                Yii::$app->session->setFlash('success', Lang::t('page/listView', 'msgAlarmResultFalse'));
            }
            return json_encode(['msg' => $resultMsg]);
        }
    }
    
}