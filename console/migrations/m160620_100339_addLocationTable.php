<?php

use yii\db\Schema;
use yii\db\Migration;

class m160620_100339_addLocationTable extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%location}}', [
            'id'          => Schema::TYPE_PK,
            'user_id'     => Schema::TYPE_INTEGER . ' NOT NULL',
            'lat'         => Schema::TYPE_DOUBLE . ' NOT NULL',
            'lng'         => Schema::TYPE_DOUBLE . ' NOT NULL',
            'zoom'        => Schema::TYPE_INTEGER . ' NOT NULL',
            'title'       => Schema::TYPE_STRING . ' NOT NULL',
            'description' => Schema::TYPE_STRING . ' NOT NULL',
            'type'        => Schema::TYPE_STRING . ' NOT NULL',
            'entity'      => Schema::TYPE_STRING . ' NOT NULL',
            'entity_id'   => Schema::TYPE_INTEGER . ' NOT NULL',
            'deleted'     => Schema::TYPE_BOOLEAN . ' DEFAULT 0 NOT NULL',
            'date_update' => Schema::TYPE_INTEGER . ' NOT NULL',
            'date_create' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%location}}');
    }

}
