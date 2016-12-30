<?php
namespace frontend\controllers;

use common\models\Alarm;
use common\models\School;
use common\models\TagEntity;
use common\models\Tags;
use common\models\User;
use common\models\Vote;
use frontend\models\Lang;
use frontend\widgets\SchoolList;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\helpers\Url;

/**
 * Site controller
 */
class SchoolController extends Controller
{

    public $thisPage = 'school';

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
        if (!Yii::$app->user->can(User::PERMISSION_CREATE_SCHOOLS)) {
            return Yii::$app->getResponse()->redirect(Url::home());
        }
        $school = new School();
        if (Yii::$app->request->isPost && $school->load(Yii::$app->request->post())) {
            $gRecaptchaResponse = Yii::$app->request->post('g-recaptcha-response');
            if (!empty($gRecaptchaResponse)
                && Yii::$app->google->testCaptcha($gRecaptchaResponse, Yii::$app->request->getUserIP())
                || !Yii::$app->params['gRecaptchaResponse']
            ) {
                $schoolPost = Yii::$app->request->post('School');
                $school->country = $schoolPost['country'];
                $school->description = \yii\helpers\HtmlPurifier::process($school->description, []);
                $school->user_id = Yii::$app->user->identity->getId();
                $school->date = strtotime(date('Y-m-d'));
                $school->like_count = 0;
                $school->likes = 0;
                $school->dislikes = 0;
                $school->show_count = 0;
                if ($school->save()) {
                    // Добавляем теги
                    $tagsArr = explode(',', Yii::$app->request->post('tags'));
                    $school->saveTags($tagsArr);
                    // Добавляем картинки к записи
                    $imgs = Yii::$app->request->post('imgs');
                    if (!empty($imgs) && is_array($imgs)) {
                        $school->saveImgs($imgs);
                    } else {
                        $school->saveImgs([]);
                    }

                    $school->saveLocations(Yii::$app->request->post('location'));

                    return Yii::$app->getResponse()->redirect($school->getUrl());
                }
            }
        }
        Yii::$app->params['jsZoukVar']['tagsAll'] = Tags::getTags(Tags::TAG_GROUP_ALL);

        return $this->render(
            'add',
            ['school' => $school]
        );
    }

    public function actionView()
    {
        $school = null;
        if ($id = Yii::$app->request->get('index', null)) {
            $school = School::findOne((int)$id);
        } elseif ($id = Yii::$app->request->get('id', null)) {
            $school = School::findOne((int)$id);
        } else {
            $alias = Yii::$app->request->get('alias', null);
            $school = School::findOne(['alias' => $alias]);
        }
        if (empty($school)) {
            return;
        }

        if ($school->deleted) {
            return $this->render('viewDeleted');
        }

        if ($anchor = Yii::$app->request->get('comment', null)) {
            Yii::$app->params['jsZoukVar']['anchor'] = $anchor;
        }

        if (!User::isBot()) {
            $school->addShowCount();
        }
        $thisUser = User::thisUser();
        $vote = !empty($thisUser) ? $thisUser->getVoteByEntity(Vote::ENTITY_SCHOOL, $id) : null;

        return $this->render(
            'view',
            [
                'school' => $school,
                'vote'   => $vote,
            ]
        );
    }

    public function actionEdit($id)
    {
        /** @var School $school */
        $school = School::findOne($id);
        if (!Yii::$app->user->can(User::PERMISSION_EDIT_SCHOOLS, ['object' => $school])) {
            return Yii::$app->getResponse()->redirect($school->getUrl());
        }
        if ($school && $school->load(Yii::$app->request->post())) {
            $schoolPost = Yii::$app->request->post('School');
            $school->country = $schoolPost['country'];
            $school->description = \yii\helpers\HtmlPurifier::process($school->description, []);
            if ($school->save()) {
                // Добавляем картинки к записи
                $imgs = Yii::$app->request->post('imgs');
                if (!empty($imgs) && is_array($imgs)) {
                    $school->saveImgs($imgs);
                } else {
                    $school->saveImgs([]);
                }

                TagEntity::deleteAll(['entity' => TagEntity::ENTITY_SCHOOL, 'entity_id' => $school->id]);
                $tagsArr = explode(',', Yii::$app->request->post('tags'));
                $school->saveTags($tagsArr);

                $school->saveLocations(Yii::$app->request->post('location'));

                return Yii::$app->getResponse()->redirect($school->getUrl());
            }
        }
        Yii::$app->params['jsZoukVar']['tagsAll'] = Tags::getTags(Tags::TAG_GROUP_ALL);

        return $this->render(
            'edit',
            ['school' => $school]
        );
    }

    public function actionDelete($id)
    {
        /** @var School $school */
        $school = School::findOne($id);
        if (Yii::$app->user->can(User::PERMISSION_DELETE_SCHOOLS, ['object' => $school])) {
            $school->deleted = 1;
            if ($school->save()) {
                return Yii::$app->getResponse()->redirect(['schools/all']);
            };
        }

        return Yii::$app->getResponse()->redirect($school->getUrl());
    }

    public function actionSchools()
    {
        $lastDate = Yii::$app->request->post('lastDate', 0);
        $lastIds = Yii::$app->request->post('loadSchoolId', []);
        $schoolIds = Yii::$app->request->post('lastIds', []);
        $order = Yii::$app->request->post('order', SchoolList::ORDER_BY_LIKE_SHOW);
        $dateCreateType = Yii::$app->request->post('dateCreateType', SchoolList::DATE_CREATE_ALL);
        return SchoolList::widget([
            'lastIds'        => $schoolIds,
            'lastDate'       => $lastDate,
            'onlySchool'     => true,
            'orderBy'        => $order,
            'dateCreateType' => $dateCreateType,
        ]);
    }

    public function actionData()
    {
        $schools = School::find()->from(["t" => School::tableName()])
            ->andWhere('t.deleted = 0')
            ->addSelect('*')
            ->addSelect(['(like_count * 15 + show_count) as like_show_count'])
            ->orderBy('like_show_count DESC')
            ->with(['locations'])
            ->all();

        $data = [];
        foreach ($schools as $school) {
            $locations = $school->locations;
            foreach ($locations as $location) {
                $value = [
                    'lat'          => $location->lat,
                    'lng'          => $location->lng,
                    'zoom'         => $location->zoom,
                    'title'        => $location->title,
                    'title-url'    => $school->getUrl(),
                    'site-url'     => $school->site,
                    'type'         => $location->getTypeLocal(),
                    'description'  => $location->getDescription(),
                    'school-title' => $school->getTitle(),
                ];

                $data[] = $value;
            }
        }

        return json_encode($data);
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
        $school = School::findOne($id);
        if ($school && !empty($msg)) {
            if (Alarm::addAlarm(Alarm::ENTITY_SCHOOL, $school->id, $msg)) {
                $resultMsg = Lang::t('main/dialogs', 'modalAlarm_msgAlarmResultTrue');
                Yii::$app->session->setFlash('success', Lang::t('main/dialogs', 'modalAlarm_msgAlarmResultTrue'));
            } else {
                $resultMsg = Lang::t('main/dialogs', 'modalAlarm_msgAlarmResultFalse');
                Yii::$app->session->setFlash('success', Lang::t('main/dialogs', 'modalAlarm_msgAlarmResultFalse'));
            }
            return json_encode(['msg' => $resultMsg]);
        }
    }

}