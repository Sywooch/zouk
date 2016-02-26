<?php

use yii\db\Schema;
use yii\db\Migration;

class m160226_095035_addColumnDeletedItem extends Migration
{
    public function up()
    {
        $this->addColumn('{{%item}}', 'deleted', Schema::TYPE_BOOLEAN . ' DEFAULT 0 NOT NULL');
    }

    public function down()
    {
        $this->dropColumn('{{%item}}', 'deleted');
    }

}
