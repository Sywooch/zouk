<?php
namespace frontend\controllers;

use common\models\Alarm;
use common\models\EntryModel;
use common\models\Event;
use common\models\form\SearchEntryForm;
use common\models\TagEntity;
use common\models\Tags;
use common\models\User;
use common\models\Vote;
use console\controllers\RbacController;
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

    public $searchPath = 'event/all';

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
                    [
                        'actions' => ['approve'],
                        'allow' =>true,
                        'roles' => [User::PERMISSION_APPROVED_EVENTS],
                    ]
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
                $event->date_to = strtotime($eventPost['date_to']);
                if ($event->date_to < $event->date) {
                    $event->date_to = $event->date;
                }
                $event->like_count = 0;
                $event->likes = 0;
                $event->dislikes = 0;
                $event->show_count = 0;
                if ($event->save()) {
                    // Добавляем теги
                    $tagsArr = explode(',', Yii::$app->request->post('tags'));
                    $event->saveTags($tagsArr);
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
            $event->date_to = strtotime($eventPost['date_to']);
            if ($event->date_to < $event->date) {
                $event->date_to = $event->date;
            }
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
                $event->saveTags($tagsArr);

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
        $display = Yii::$app->request->post('display', EventList::EVENT_LIST_DISPLAY_MAIN);
        return EventList::widget([
            'lastIds'        => $lastIds,
            'lastDate'       => $lastDate,
            'onlyEvent'      => true,
            'orderBy'        => $order,
            'dateCreateType' => $dateCreateType,
            'display'        => $display,
        ]);
    }

    public function actionAll()
    {
        $searchEntryForm = SearchEntryForm::loadFromPost();
        $request = Yii::$app->request;
        $page = $request->get('page', $request->post('page', 0));
        $status = $request->get('status', EntryModel::STATUS_APPROVED);
        if ($request->isAjax) {
            return $this->renderPartial('listAll', [
                'searchEntryForm' => $searchEntryForm,
                'page'            => $page,
                'status'          => $status,
            ]);
        }
        return $this->render('listAll', [
            'searchEntryForm' => $searchEntryForm,
            'page'            => $page,
            'status'          => $status,
        ]);
    }

    public function actionBefore()
    {
        $this->searchPath = 'event/before';
        $searchEntryForm = SearchEntryForm::loadFromPost();
        $request = Yii::$app->request;
        $page = $request->get('page', $request->post('page', 0));
        $searchEntryForm->date_to = (new \DateTime())->getTimestamp();
        if ($request->isAjax) {
            return $this->renderPartial('listBefore', ['searchEntryForm' => $searchEntryForm, 'page' => $page]);
        }
        return $this->render('listBefore', ['searchEntryForm' => $searchEntryForm, 'page' => $page]);
    }

    public function actionAfter()
    {
        $this->searchPath = 'event/after';
        $searchEntryForm = SearchEntryForm::loadFromPost();
        $request = Yii::$app->request;
        $page = $request->get('page', $request->post('page', 0));
        $searchEntryForm->date_from = (new \DateTime())->getTimestamp();
        if ($request->isAjax) {
            return $this->renderPartial('listAfter', ['searchEntryForm' => $searchEntryForm, 'page' => $page]);
        }
        return $this->render('listAfter', ['searchEntryForm' => $searchEntryForm, 'page' => $page]);
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

    public function actionApprove($id)
    {
        /** @var Event $event */
        $event = Event::findOne($id);
        $event->status = EntryModel::STATUS_APPROVED;
        $event->save();
        return $this->goBack();
    }

}