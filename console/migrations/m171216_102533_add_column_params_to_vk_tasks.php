<?php

use yii\db\Migration;

class m171216_102533_add_column_params_to_vk_tasks extends Migration
{
    public function up()
    {
        $this->addColumn('vk_tasks', 'params', $this->text());
    }

    public function down()
    {
        $this->dropColumn('vk_tasks', 'params');
    }

}
