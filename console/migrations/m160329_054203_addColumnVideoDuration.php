<?php

use yii\db\Migration;
use yii\db\Schema;

class m160329_054203_addColumnVideoDuration extends Migration
{
    public function up()
    {
        $this->addColumn('{{%video}}', 'duration', Schema::TYPE_INTEGER . ' DEFAULT 0 NOT NULL');
    }

    public function down()
    {
        $this->dropColumn('{{%video}}', 'duration');
    }

}
