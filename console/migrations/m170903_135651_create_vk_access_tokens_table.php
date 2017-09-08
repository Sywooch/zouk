<?php

use yii\db\Migration;

/**
 * Handles the creation of table `vk_access_tokens`.
 */
class m170903_135651_create_vk_access_tokens_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('vk_access_tokens', [
            'id'           => $this->primaryKey(),
            'user_id'      => $this->integer(),
            'group_id'     => $this->string(),
            'access_token' => $this->string(),
            'expires_in'   => $this->integer(),
            'date_update'  => $this->integer(),
            'date_create'  => $this->integer(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('vk_access_tokens');
    }
}
