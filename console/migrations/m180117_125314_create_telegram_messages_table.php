<?php

use yii\db\Migration;

/**
 * Handles the creation of table `telegram_messages`.
 */
class m180117_125314_create_telegram_messages_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('telegram_messages', [
            'id'          => $this->primaryKey(),
            'chat_id'     => $this->integer(),
            'message_id'  => $this->integer(),
            'text'        => $this->text(),
            'status'      => $this->integer(),
            'date_update' => $this->integer(),
            'date_create' => $this->integer(),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('telegram_messages');
    }
}
