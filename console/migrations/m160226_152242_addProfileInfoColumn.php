<?php

use yii\db\Schema;
use yii\db\Migration;

class m160226_152242_addProfileInfoColumn extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'firstname', Schema::TYPE_STRING . ' NOT NULL');
        $this->addColumn('{{%user}}', 'lastname', Schema::TYPE_STRING . ' NOT NULL');
        $this->addColumn('{{%user}}', 'display_name', Schema::TYPE_STRING . ' NOT NULL');
        $this->addColumn('{{%user}}', 'avatar_pic', Schema::TYPE_STRING . ' NOT NULL');
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'firstname');
        $this->dropColumn('{{%user}}', 'lastname');
        $this->dropColumn('{{%user}}', 'display_name');
        $this->dropColumn('{{%user}}', 'avatar_pic');
    }

}
