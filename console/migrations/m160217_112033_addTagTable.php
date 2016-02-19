<?php

use yii\db\Schema;
use yii\db\Migration;

class m160217_112033_addTagTable extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tags}}', [
            'id'          => Schema::TYPE_PK,
            'name'        => Schema::TYPE_STRING . '(255) NOT NULL',
            'tag_group'   => Schema::TYPE_STRING . '(255) NOT NULL',
            'date_update' => Schema::TYPE_INTEGER . ' NOT NULL',
            'date_create' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);

        $this->createTable('{{%tag_entity}}', [
            'id'          => Schema::TYPE_PK,
            'tag_id'      => Schema::TYPE_INTEGER . ' NOT NULL',
            'entity'      => Schema::TYPE_STRING . '(255) NOT NULL',
            'entity_id'   => Schema::TYPE_INTEGER . ' NOT NULL',
            'date_update' => Schema::TYPE_INTEGER . ' NOT NULL',
            'date_create' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%tags}}');
        $this->dropTable('{{%tag_entity}}');
    }

}
