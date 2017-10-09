<?php

use yii\db\Migration;

/**
 * Handles the creation of table `vk_tasks_completed`.
 */
class m170923_141332_create_vk_tasks_completed_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('vk_tasks_completed', [
            'id'          => $this->primaryKey(),
            'user_id'     => $this->integer(),
            'vk_task_id'  => $this->integer(),
            'date_update' => $this->integer(),
            'date_create' => $this->integer(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('vk_tasks_completed');
    }
}
