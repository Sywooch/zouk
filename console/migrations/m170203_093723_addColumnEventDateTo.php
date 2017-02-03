<?php

use yii\db\Migration;

class m170203_093723_addColumnEventDateTo extends Migration
{
    public function up()
    {
        $this->addColumn('{{%event}}', 'date_to', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('{{%event}}', 'date_to');
    }

}
