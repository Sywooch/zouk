<?php

namespace console\controllers;

use common\models\User;
use yii\console\Controller;

class RbacController extends Controller
{
    public $defaultAction = 'init';

    // php yii migrate --migrationPath=@yii/rbac/migrations/
    public function actionInit()
    {
        $auth = \Yii::$app->authManager;

        $auth->removeAll();

        $editItems = $auth->createPermission(User::PERMISSION_EDIT_ITEMS);
        $editItems->description = 'Редактирование записей';
        $auth->add($editItems);

        $deleteItems = $auth->createPermission(User::PERMISSION_DELETE_ITEMS);
        $deleteItems->description = 'Удаление записей';
        $auth->add($deleteItems);

        $editEvents = $auth->createPermission(User::PERMISSION_EDIT_EVENTS);
        $editEvents->description = 'Редактирование событий';
        $auth->add($editEvents);

        $deleteEvents = $auth->createPermission(User::PERMISSION_DELETE_EVENTS);
        $deleteEvents->description = 'Удаление событий';
        $auth->add($deleteEvents);

        $editSchools = $auth->createPermission(User::PERMISSION_EDIT_SCHOOLS);
        $editSchools->description = 'Редактирование школ';
        $auth->add($editSchools);

        $deleteSchools = $auth->createPermission(User::PERMISSION_DELETE_SCHOOLS);
        $deleteSchools->description = 'Удаление школ';
        $auth->add($deleteSchools);

        $user = $auth->createRole(User::ROLE_USER);
        $auth->add($user);

        $moderator = $auth->createRole(User::ROLE_MODERATOR);
        $auth->add($moderator);
        $auth->addChild($moderator, $user);
        $auth->addChild($moderator, $editItems);
        $auth->addChild($moderator, $deleteItems);
        $auth->addChild($moderator, $editEvents);
        $auth->addChild($moderator, $deleteEvents);
        $auth->addChild($moderator, $editSchools);
        $auth->addChild($moderator, $deleteSchools);

        $admin = $auth->createRole(User::ROLE_ADMIN);
        $auth->add($admin);
        $auth->addChild($admin, $moderator);

        $auth->assign($admin, 1);
    }
}