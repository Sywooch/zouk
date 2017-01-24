<?php

namespace common\components;


use common\models\Log;
use common\models\Event;
use common\models\Item;
use common\models\School;
use common\models\User as ModelUser;
use common\models\VoteModel;
use Faker\Provider\DateTime;
use frontend\models\SignupForm;
use Yii;

class User extends \yii\web\User
{

    private $_user = null;

    public function init()
    {
        parent::init();

        $user = $this->getUserModel();
        if (!empty($user)) {
            if (!empty($user->date_blocked) && $user->date_blocked >= (new \DateTime())->getTimestamp()) {
                return Yii::$app->controller->redirect(['site/blocked']);
            }
            $date = (new \DateTime())->sub(new \DateInterval('PT3M'));
            $countClick = Log::find()
                ->andWhere(['user_id' => $user->id])
                ->andWhere(['>=', 'date_create', $date->getTimestamp()])
                ->count();
            if ($countClick > 100) {
                $newDateBlocked = (new \DateTime())->add(new \DateInterval('P1D'))->getTimestamp();
                if (empty($user->date_blocked) || $newDateBlocked > $user->date_blocked) {
                    $user->date_blocked = $newDateBlocked;
                    $user->save();
                }
            }
        }

    }
    
    public function can($permissionName, $params = [], $allowCaching = true)
    {
        $auth = \Yii::$app->authManager;
        if (!$this->isGuest && empty($auth->getRolesByUser($this->id))) {
            $userRole = $auth->getRole(\common\models\User::ROLE_USER);
            $auth->assign($userRole, $this->id);
        }

        $can = parent::can($permissionName, $params, $allowCaching);
        if (isset($params['object'])) {
            $object = $params['object'];
            if (!$this->isGuest) {
                /** @var ModelUser $thisUser */
                $thisUser = $this->identity;

                if ($object instanceof VoteModel) {
                    /** @var VoteModel $object */
                    switch ($permissionName) {
                        case ModelUser::PERMISSION_EDIT_ITEMS:
                        case ModelUser::PERMISSION_DELETE_ITEMS:
                        case ModelUser::PERMISSION_EDIT_EVENTS:
                        case ModelUser::PERMISSION_DELETE_EVENTS:
                        case ModelUser::PERMISSION_EDIT_SCHOOLS:
                        case ModelUser::PERMISSION_DELETE_SCHOOLS:
                            $can = $can || $object->user_id === $this->id;
                            break;
                    }
                }

                if ($object instanceof Item) {
                    /** @var Item $object */
                    if ($permissionName == ModelUser::PERMISSION_DELETE_ITEMS) {
                        $can = $can || ($thisUser->reputation > Item::MIN_REPUTAION_BAD_ITEM_DELETE && $object->like_count < 0);
                    } elseif ($permissionName == ModelUser::PERMISSION_EDIT_ITEMS) {
                        $can = $can && !$object->deleted;
                    } elseif ($permissionName == ModelUser::PERMISSION_CREATE_ITEMS) {
                        $can = $can && $thisUser->reputation >= Item::MIN_REPUTATION_ITEM_CREATE;
                    }
                } else if ($object instanceof School) {
                    /** @var School $object */
                    if ($permissionName == ModelUser::PERMISSION_DELETE_SCHOOLS) {

                    } elseif ($permissionName == ModelUser::PERMISSION_EDIT_SCHOOLS) {
                        $can = $can && !$object->deleted;
                    } elseif ($permissionName == ModelUser::PERMISSION_CREATE_SCHOOLS) {
                        $can = $can && $thisUser->reputation >= School::MIN_REPUTATION_SCHOOL_CREATE;
                    }
                } else if ($object instanceof Event) {
                    /** @var School $object */
                    if ($permissionName == ModelUser::PERMISSION_DELETE_EVENTS) {

                    } elseif ($permissionName == ModelUser::PERMISSION_EDIT_EVENTS) {
                        $can = $can && !$object->deleted;
                    } elseif ($permissionName == ModelUser::PERMISSION_CREATE_EVENTS) {
                        $can = $can && $thisUser->reputation >= Event::MIN_REPUTATION_EVENT_CREATE;
                    }
                }
            }
            unset($params['object']);
        }

        return $can;
    }


    private $_addedToLog = false;
    public function addToLog($user_id)
    {
        if (!$this->_addedToLog) {
            $request = Yii::$app->request;
            $log = new Log();
            $log->ip = $request->getUserIP();
            $log->user_id = $user_id;
            $log->url = mb_substr($request->getAbsoluteUrl(), 0, 255);
            $log->date_create = (new \DateTime())->getTimestamp();
            $log->post = json_encode($request->post());
            $log->user_agent = $request->getUserAgent();
            $log->referrer = $request->getReferrer();
            $log->save();
            $this->_addedToLog = true;
        }
    }

    public function getIsGuest()
    {
        $isGuest = $this->getIdentity() === null;
        $auth = \Yii::$app->authManager;
        if ($isGuest) {
            $userIpName = 'mock_' . substr(md5('mock'), 0, 8) . '_' . Yii::$app->request->getUserIP();
            $user = \common\models\User::findOne(['username' => $userIpName]);
            if (empty($user)) {
                $model = new SignupForm();
                $model->username = $userIpName;
                $model->displayName = Yii::$app->request->getUserIP();
                $model->password = md5($userIpName);
                $model->email = $userIpName . '@prozouk.ru';
                $user = $model->signup(false);
            }
            if (!empty($user)) {
                $role = $auth->getRole(\common\models\User::ROLE_MOCK_USER);
                if (is_null($auth->getAssignment(\common\models\User::ROLE_MOCK_USER, $user->id))) {
                    $auth->assign($role, $user->id);
                }
                if (!is_numeric($user->lastname)) {
                    $user->lastname = 0;
                }
                $user->lastname++;
                $user->save();

                $this->login($user);
                $this->_user = $user;
            }
            $this->addToLog(empty($user) ? null : $user->id);
        } else {
//            $isGuest = !is_null($auth->getAssignment(\common\models\User::ROLE_MOCK_USER, $this->id));
            $this->addToLog($this->id);
        }
        return $isGuest;
    }

    public function getUserModel()
    {
        if (!$this->getIsGuest()) {
            if (empty($this->_user)) {
                $this->_user = \common\models\User::findOne($this->id);
            }
            return $this->_user;
        }
        return null;
    }
}