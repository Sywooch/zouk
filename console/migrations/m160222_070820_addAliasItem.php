<?php

use yii\db\Schema;
use yii\db\Migration;

class m160222_070820_addAliasItem extends Migration
{
    public function up()
    {
        $this->addColumn('{{%item}}', 'alias', 'string NOT NULL');
    }

    public function down()
    {
        $this->dropColumn('{{%item}}', 'alias');
    }

}
