<?php

use yii\db\Migration;

/**
 * Class m180119_190237_add_column_bot_name_to_telegram_messages
 */
class m180119_190237_add_column_bot_name_to_telegram_messages extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->alterColumn('telegram_messages', 'status', $this->string());
        $this->addColumn('telegram_messages', 'bot_name', $this->string());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('telegram_messages', 'bot_name');
        $this->alterColumn('telegram_messages', 'status', $this->integer());
    }
}
