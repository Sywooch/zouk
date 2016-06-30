<?php

use yii\db\Schema;
use yii\db\Migration;

class m160629_042259_createSchoolsList extends Migration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%school}}', [
            'id'          => Schema::TYPE_PK,
            'user_id'     => Schema::TYPE_INTEGER . ' NOT NULL',
            'title'       => Schema::TYPE_STRING . '(255) NOT NULL',
            'description' => Schema::TYPE_TEXT . ' NOT NULL',
            'date'        => Schema::TYPE_INTEGER,
            'country'     => Schema::TYPE_INTEGER . '(11)',
            'city'        => Schema::TYPE_STRING . '(60)',
            'site'        => Schema::TYPE_STRING . '(120)',
            'alias'       => Schema::TYPE_STRING . ' NOT NULL',
            'like_count'  => Schema::TYPE_INTEGER . ' NOT NULL',
            'show_count'  => Schema::TYPE_INTEGER . ' NOT NULL',
            'deleted'     => Schema::TYPE_BOOLEAN . ' DEFAULT 0 NOT NULL',
            'date_update' => Schema::TYPE_INTEGER . ' NOT NULL',
            'date_create' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%school}}');
    }
}
