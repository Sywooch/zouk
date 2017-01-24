<?php

use yii\db\Migration;

class m170124_101305_addColumnUserBlocked extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'date_blocked', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'date_blocked');
    }
}
