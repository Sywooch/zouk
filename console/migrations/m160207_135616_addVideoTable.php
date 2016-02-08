<?php

use yii\db\Schema;
use yii\db\Migration;

class m160207_135616_addVideoTable extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%video}}', [
            'id'          => Schema::TYPE_PK,
            'user_id'     => Schema::TYPE_INTEGER . ' NOT NULL',
            'entity'      => Schema::TYPE_STRING . ' NOT NULL',
            'entity_id'   => Schema::TYPE_STRING . ' NOT NULL',
            'originalUrl' => Schema::TYPE_STRING . ' NOT NULL',
            'date_update' => Schema::TYPE_INTEGER . ' NOT NULL',
            'date_create' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);

        $this->createTable('{{%item_video}}', [
            'id'          => Schema::TYPE_PK,
            'item_id'     => Schema::TYPE_INTEGER . ' NOT NULL',
            'video_id'    => Schema::TYPE_INTEGER . ' NOT NULL',
            'video_title' => Schema::TYPE_STRING . ' NOT NULL',
            'date_update' => Schema::TYPE_INTEGER . ' NOT NULL',
            'date_create' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%video}}');
        $this->dropTable('{{%item_video}}');
    }

}
