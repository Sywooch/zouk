<?php

use yii\db\Schema;
use yii\db\Migration;

class m160226_102814_addAlarmTable extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%alarm}}', [
            'id'          => Schema::TYPE_PK,
            'user_id'     => Schema::TYPE_INTEGER . ' NOT NULL',
            'entity'      => Schema::TYPE_STRING . ' NOT NULL',
            'entity_id'   => Schema::TYPE_INTEGER . ' NOT NULL',
            'msg'         => Schema::TYPE_STRING . ' NOT NULL',
            'date_update' => Schema::TYPE_INTEGER . ' NOT NULL',
            'date_create' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%alarm}}');
    }

}
