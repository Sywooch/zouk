<?php
namespace frontend\controllers;

use common\models\Alarm;
use common\models\Event;
use common\models\form\SearchEntryForm;
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

    public $thisPage = 'event';

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
        if (!Yii::$app->user->can(User::PERMISSION_CREATE_EVENTS)) {
            return Yii::$app->getResponse()->redirect(Url::home());
        }
        $event = new Event();
        if (Yii::$app->request->isPost && $event->load(Yii::$app->request->post())) {
            $gRecaptchaResponse = Yii::$app->request->post('g-recaptcha-response');
            if (!empty($gRecaptchaResponse)
                && Yii::$app->google->testCaptcha($gRecaptchaResponse, Yii::$app->request->getUserIP())
                || !Yii::$app->params['gRecaptchaResponse']
            ) {
                $eventPost = Yii::$app->request->post('Event');
                $event->country = $eventPost['country'];
                $event->description = \yii\helpers\HtmlPurifier::process($event->description, []);
                $event->user_id = Yii::$app->user->identity->getId();
                $event->date = strtotime($eventPost['date']);
                $event->like_count = 0;
                $event->likes = 0;
                $event->dislikes = 0;
                $event->show_count = 0;
                if ($event->save()) {
                    // Добавляем теги
                    $tagsArr = explode(',', Yii::$app->request->post('tags'));
                    $tags = array_shift($tagsArr);
                    $event->saveTags($tags);
                    // Добавляем картинки к записи
                    $imgs = Yii::$app->request->post('imgs');
                    if (!empty($imgs) && is_array($imgs)) {
                        $event->saveImgs($imgs);
                    } else {
                        $event->saveImgs([]);
                    }

                    $event->saveLocations(Yii::$app->request->post('location'));

                    return Yii::$app->getResponse()->redirect($event->getUrl());
                }
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
        if (!Yii::$app->user->can(User::PERMISSION_EDIT_EVENTS, ['object' => $event])) {
            return Yii::$app->getResponse()->redirect($event->getUrl());
        }
        if ($event && $event->load(Yii::$app->request->post())) {
            $eventPost = Yii::$app->request->post('Event');
            $event->country = $eventPost['country'];
            $event->description = \yii\helpers\HtmlPurifier::process($event->description, []);
            $event->date = strtotime($eventPost['date']);
            if ($event->save()) {
                // Добавляем картинки к записи
                $imgs = Yii::$app->request->post('imgs');
                if (!empty($imgs) && is_array($imgs)) {
                    $event->saveImgs($imgs);
                } else {
                    $event->saveImgs([]);
                }

                TagEntity::deleteAll(['entity' => TagEntity::ENTITY_EVENT, 'entity_id' => $event->id]);
                $tagsArr = explode(',', Yii::$app->request->post('tags'));
                $tags = array_shift($tagsArr);
                $event->saveTags($tags);

                $event->saveLocations(Yii::$app->request->post('location'));

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
        if (Yii::$app->user->can(User::PERMISSION_DELETE_EVENTS, ['object' => $event])) {
            $event->deleted = 1;
            if ($event->save()) {
                return Yii::$app->getResponse()->redirect(['events/all']);
            };
        }

        return Yii::$app->getResponse()->redirect($event->getUrl());
    }

    public function actionEvents()
    {
        $searchEntryForm = SearchEntryForm::loadFromPost();
        $lastDate = Yii::$app->request->post('lastDate', 0);
        $lastIds = Yii::$app->request->post('loadEventId', []);
        $order = Yii::$app->request->post('order', EventList::ORDER_BY_DATE);
        $dateCreateType = Yii::$app->request->post('dateCreateType', EventList::DATE_CREATE_ALL);
        return EventList::widget([
            'lastIds'        => $lastIds,
            'lastDate'       => $lastDate,
            'onlyEvent'      => true,
            'orderBy'        => $order,
            'dateCreateType' => $dateCreateType,
        ]);
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
                $resultMsg = Lang::t('main/dialogs', 'modalAlarm_msgAlarmResultTrue');
                Yii::$app->session->setFlash('success', Lang::t('main/dialogs', 'modalAlarm_msgAlarmResultTrue'));
            } else {
                $resultMsg = Lang::t('main/dialogs', 'modalAlarm_msgAlarmResultFalse');
                Yii::$app->session->setFlash('success', Lang::t('main/dialogs', 'modalAlarm_msgAlarmResultFalse'));
            }
            return json_encode(['msg' => $resultMsg]);
        }
        return "";
    }

    public function actionYear($year)
    {
        $dateStart = strtotime($year . '-01-01');
        $dateEnd = strtotime($year . '-12-31');

        $events = Event::find()->andWhere(
            'date >= :dateStart AND date <= :dateEnd',
            [':dateStart' => $dateStart, ':dateEnd' => $dateEnd]
        )->orderBy('date')->all();

        return $this->render('year', ['events' => $events, 'year' => $year]);
    }

    public function actionMonth($year, $month)
    {
        $dateStart = strtotime($year . '-' . $month . '-01');
        $dateEnd = strtotime(date('Y-m-t', $dateStart));

        $events = Event::find()->andWhere(
            'date >= :dateStart AND date <= :dateEnd',
            [':dateStart' => $dateStart, ':dateEnd' => $dateEnd]
        )->orderBy('date')->all();

        return $this->render('month', ['events' => $events, 'year' => $year, 'month' => $month]);
    }

}