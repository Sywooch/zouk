<?php

namespace common\components;


use common\models\Event;
use common\models\Item;
use common\models\School;
use common\models\User as ModelUser;
use common\models\VoteModel;
use frontend\models\SignupForm;
use Yii;

class User extends \yii\web\User
{
    
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
            }
        } else {
//            $isGuest = !is_null($auth->getAssignment(\common\models\User::ROLE_MOCK_USER, $this->id));
        }
        return $isGuest;
    }
}