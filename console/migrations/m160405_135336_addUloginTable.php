<?php

use yii\db\Schema;
use yii\db\Migration;

class m160405_135336_addUloginTable extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%ulogin}}', [
            'id'            => Schema::TYPE_PK,
            'user_id'       => Schema::TYPE_INTEGER . ' NOT NULL',
            'user_start_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'identity'      => Schema::TYPE_STRING . ' NOT NULL',
            'network'       => Schema::TYPE_STRING . ' NOT NULL',
            'email'         => Schema::TYPE_STRING . ' NOT NULL',
            'firstname'     => Schema::TYPE_STRING . ' NOT NULL',
            'lastname'      => Schema::TYPE_STRING . ' NOT NULL',
            'nickname'      => Schema::TYPE_STRING . ' NOT NULL',
            'date_update'   => Schema::TYPE_INTEGER . ' NOT NULL',
            'date_create'   => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%ulogin}}');
    }

}
