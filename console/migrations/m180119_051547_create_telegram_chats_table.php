<?php

use yii\db\Migration;

/**
 * Handles the creation of table `telegram_chats`.
 */
class m180119_051547_create_telegram_chats_table extends Migration
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

        $this->createTable('telegram_chats', [
            'id'          => $this->primaryKey(),
            'chat_id'     => $this->integer(),
            'lang_id'     => $this->integer(),
            'params'      => $this->text(),
            'date_update' => $this->integer(),
            'date_create' => $this->integer(),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('telegram_chats');
    }
}
