<?php

use yii\db\Migration;

/**
 * Class m180126_193654_add_column_status_to_event
 */
class m180126_193654_add_column_status_to_event extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('event', 'status', $this->string()->defaultValue(\common\models\EntryModel::STATUS_APPROVED));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('event', 'status');
    }
}
