<?php

use yii\db\Schema;
use yii\db\Migration;

class m160328_050600_changeItemVideoToLinkEntity extends Migration
{
    public function up()
    {
        $this->renameTable('{{%item_video}}', '{{%entity_link}}');
        $this->addColumn('{{%entity_link}}', 'entity_1', Schema::TYPE_STRING . ' NOT NULL AFTER id');
        $this->renameColumn('{{%entity_link}}', 'item_id', 'entity_1_id');
        $this->addColumn('{{%entity_link}}', 'entity_2', Schema::TYPE_STRING . ' NOT NULL AFTER entity_1_id');
        $this->renameColumn('{{%entity_link}}', 'video_id', 'entity_2_id');

        $this->update('{{%entity_link}}', ['entity_1' => 'item', 'entity_2' => 'video']);
    }

    public function down()
    {
        $this->renameTable('entity_link', 'item_video');
        $this->dropColumn('{{%item_video}}', 'entity_1');
        $this->renameColumn('{{%item_video}}', 'entity_1_id', 'item_id');
        $this->dropColumn('{{%item_video}}', 'entity_2');
        $this->renameColumn('{{%item_video}}', 'entity_2_id', 'video_id');

    }

}
