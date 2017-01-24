<?php

use yii\db\Schema;
use yii\db\Migration;

class m170124_094541_addLogColumnReferrerUseragent extends Migration
{
    public function up()
    {
        $this->addColumn('{{%log}}', 'referrer', $this->string());
        $this->addColumn('{{%log}}', 'user_agent', $this->string());

    }

    public function down()
    {
        $this->dropColumn('{{%log}}', 'user_agent');
        $this->dropColumn('{{%log}}', 'referrer');
    }
}
