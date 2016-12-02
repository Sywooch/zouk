<?php

namespace common\components;


use common\models\Event;
use common\models\Item;
use common\models\School;
use common\models\User as ModelUser;
use common\models\VoteModel;

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
}