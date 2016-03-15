<?php

use yii\db\Schema;
use yii\db\Migration;

class m160313_092553_addTableComment extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%comment}}', [
            'id'          => Schema::TYPE_PK,
            'user_id'     => Schema::TYPE_INTEGER . ' NOT NULL',
            'entity'      => Schema::TYPE_STRING . ' NOT NULL',
            'entity_id'   => Schema::TYPE_INTEGER . ' NOT NULL',
            'parent_id'   => Schema::TYPE_INTEGER . ' DEFAULT 0 NOT NULL',
            'description' => Schema::TYPE_TEXT . ' NOT NULL',
            'like_count'  => Schema::TYPE_INTEGER . ' NOT NULL',
            'deleted'     => Schema::TYPE_BOOLEAN . ' DEFAULT 0 NOT NULL',
            'date_update' => Schema::TYPE_INTEGER . ' NOT NULL',
            'date_create' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%comment}}');
    }
}
