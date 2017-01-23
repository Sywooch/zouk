<?php

use yii\db\Schema;
use yii\db\Migration;

class m170123_100429_addLogTable extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%log}}', [
            'id'          => $this->primaryKey(),
            'user_id'     => $this->integer(),
            'ip'          => $this->string(30),
            'url'         => $this->string(),
            'post'        => $this->text(),
            'date_create' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%log}}');
    }
}
