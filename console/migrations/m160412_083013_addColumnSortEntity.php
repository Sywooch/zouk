<?php

use yii\db\Schema;
use yii\db\Migration;

class m160412_083013_addColumnSortEntity extends Migration
{
    public function up()
    {
        $this->addColumn('{{%entity_link}}', 'sort', Schema::TYPE_INTEGER . ' DEFAULT 0 NOT NULL');
    }

    public function down()
    {
        $this->dropColumn('{{%entity_link}}', 'sort');
    }
}
