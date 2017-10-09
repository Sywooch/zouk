<?php

use yii\db\Migration;

/**
 * Handles the creation of table `vk_tasks`.
 */
class m170923_140606_create_vk_tasks_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('vk_tasks', [
            'id'          => $this->primaryKey(),
            'type'        => $this->string(),
            'user_id'     => $this->integer(),
            'group_id'    => $this->string(),
            'period'      => $this->integer(),
            'time_start'  => $this->integer(),
            'time_end'    => $this->integer(),
            'date_update' => $this->integer(),
            'date_create' => $this->integer(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('vk_tasks');
    }
}
