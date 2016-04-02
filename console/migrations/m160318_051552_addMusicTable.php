<?php

use yii\db\Schema;
use yii\db\Migration;

class m160318_051552_addMusicTable extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%music}}', [
            'id'          => Schema::TYPE_PK,
            'user_id'     => Schema::TYPE_INTEGER . ' NOT NULL',
            'key'         => Schema::TYPE_STRING . ' NOT NULL',
            'entity_key'  => Schema::TYPE_STRING . ' NOT NULL',
            'url'         => Schema::TYPE_STRING . ' NOT NULL',
            'short_url'   => Schema::TYPE_STRING . ' NOT NULL',
            'artist'      => Schema::TYPE_STRING . ' NOT NULL',
            'title'       => Schema::TYPE_STRING . ' NOT NULL',
            'playtime'    => Schema::TYPE_FLOAT . ' DEFAULT 0 NOT NULL',
            'deleted'     => Schema::TYPE_BOOLEAN . ' DEFAULT 0 NOT NULL',
            'date_update' => Schema::TYPE_INTEGER . ' NOT NULL',
            'date_create' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%music}}');
    }

}
