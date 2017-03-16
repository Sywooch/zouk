<?php

use yii\db\Migration;

class m170315_112153_addColumnInstagramItem extends Migration
{
    public function up()
    {
        $this->addColumn('{{%item}}', 'shared_instagram', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('{{%item}}', 'shared_instagram');
    }

}
