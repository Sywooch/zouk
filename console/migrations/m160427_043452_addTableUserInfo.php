<?php

use yii\db\Schema;
use yii\db\Migration;

class m160427_043452_addTableUserInfo extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%userinfo}}', [
            'user_id'     => Schema::TYPE_PK,
            'country'     => Schema::TYPE_INTEGER . '(11)',
            'city'        => Schema::TYPE_STRING . '(60)',
            'birthday'    => Schema::TYPE_INTEGER,
            'about_me'    => Schema::TYPE_STRING . '(1024)',
            'telephone'   => Schema::TYPE_STRING . '(25)',
            'skype'       => Schema::TYPE_STRING . '(40)',
            'vk'          => Schema::TYPE_STRING . '(60)',
            'fb'          => Schema::TYPE_STRING . '(60)',
            'date_update' => Schema::TYPE_INTEGER . ' NOT NULL',
            'date_create' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%userinfo}}');
    }

}
