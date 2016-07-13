<?php

use yii\db\Schema;
use yii\db\Migration;

class m160713_060301_addSchoolOfficialEditor extends Migration
{
    public function up()
    {
        $this->addColumn('{{%school}}', 'official_editor', Schema::TYPE_INTEGER . ' DEFAULT 1 NOT NULL');
    }

    public function down()
    {
        $this->dropColumn('{{%school}}', 'official_editor');
    }

}
