<?php

use yii\db\Schema;
use yii\db\Migration;

class m160223_144546_addUserReputation extends Migration
{
    public function up()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%reputation}}', [
            'id'          => Schema::TYPE_PK,
            'user_id'     => Schema::TYPE_INTEGER . ' NOT NULL',
            'entity'      => Schema::TYPE_STRING . ' NOT NULL',
            'msg'         => Schema::TYPE_STRING . ' NOT NULL',
            'value'       => Schema::TYPE_INTEGER . ' NOT NULL',
            'date_update' => Schema::TYPE_INTEGER . ' NOT NULL',
            'date_create' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);

        $this->addColumn('{{%user}}', 'reputation', Schema::TYPE_INTEGER . ' NOT NULL');
    }

    public function down()
    {
        $this->dropTable('{{%reputation}}');
        $this->dropColumn('{{%user}}', 'reputation');
    }

}
