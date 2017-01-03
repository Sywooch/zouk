<?php

use yii\db\Migration;

class m161230_081711_createSearchEntry extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%searchEntry}}', [
            'id'            => $this->primaryKey(),
            'user_id'       => $this->integer(),
            'title'         => $this->string(255),
            'search_text'   => $this->text(),
            'search_entity' => $this->string(64),
            'show_count'    => $this->integer(),
            'date_update'   => $this->integer(),
            'date_create'   => $this->integer(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%searchEntry}}');
    }

}
