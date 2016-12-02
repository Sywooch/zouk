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

        $createItems = $auth->createPermission(User::PERMISSION_CREATE_ITEMS);
        $createItems->description = 'Создание записей';
        $auth->add($createItems);

        $editItems = $auth->createPermission(User::PERMISSION_EDIT_ITEMS);
        $editItems->description = 'Редактирование записей';
        $auth->add($editItems);

        $deleteItems = $auth->createPermission(User::PERMISSION_DELETE_ITEMS);
        $deleteItems->description = 'Удаление записей';
        $auth->add($deleteItems);

        $createEvents = $auth->createPermission(User::PERMISSION_CREATE_EVENTS);
        $createEvents->description = 'Создание событий';
        $auth->add($createEvents);

        $editEvents = $auth->createPermission(User::PERMISSION_EDIT_EVENTS);
        $editEvents->description = 'Редактирование событий';
        $auth->add($editEvents);

        $deleteEvents = $auth->createPermission(User::PERMISSION_DELETE_EVENTS);
        $deleteEvents->description = 'Удаление событий';
        $auth->add($deleteEvents);

        $createSchools = $auth->createPermission(User::PERMISSION_CREATE_SCHOOLS);
        $createSchools->description = 'Создание школ';
        $auth->add($createSchools);

        $editSchools = $auth->createPermission(User::PERMISSION_EDIT_SCHOOLS);
        $editSchools->description = 'Редактирование школ';
        $auth->add($editSchools);

        $deleteSchools = $auth->createPermission(User::PERMISSION_DELETE_SCHOOLS);
        $deleteSchools->description = 'Удаление школ';
        $auth->add($deleteSchools);

        $mockUsers = $auth->createPermission(User::PERMISSION_MOCK_USER);
        $mockUsers->description = 'Права фиктивного пользователя';
        $auth->add($mockUsers);

        $user = $auth->createRole(User::ROLE_USER);
        $auth->add($user);
        $auth->addChild($user, $createItems);
        $auth->addChild($user, $createEvents);
        $auth->addChild($user, $createSchools);

        $mock = $auth->createRole(User::ROLE_MOCK_USER);
        $auth->add($mock);
        $auth->addChild($mock, $user);

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