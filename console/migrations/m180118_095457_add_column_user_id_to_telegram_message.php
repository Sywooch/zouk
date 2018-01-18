<?php

use yii\db\Migration;

/**
 * Class m180118_095457_add_column_user_id_to_telegram_message
 */
class m180118_095457_add_column_user_id_to_telegram_message extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('telegram_messages', 'user_id', $this->string());
        $this->addColumn('telegram_messages', 'username', $this->string());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('telegram_messages', 'user_id');
        $this->dropColumn('telegram_messages', 'username');
    }
}
