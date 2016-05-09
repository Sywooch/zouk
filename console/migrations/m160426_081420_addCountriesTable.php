<?php

use yii\db\Schema;
use yii\db\Migration;

class m160426_081420_addCountriesTable extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%countries}}', [
            'id'       => Schema::TYPE_PK,
            'title_ru' => Schema::TYPE_STRING . '(60) NOT NULL',
            'title_ua' => Schema::TYPE_STRING . '(60) NOT NULL',
            'title_be' => Schema::TYPE_STRING . '(60) NOT NULL',
            'title_en' => Schema::TYPE_STRING . '(60) NOT NULL',
            'title_es' => Schema::TYPE_STRING . '(60) NOT NULL',
            'title_pt' => Schema::TYPE_STRING . '(60) NOT NULL',
            'title_de' => Schema::TYPE_STRING . '(60) NOT NULL',
            'title_fr' => Schema::TYPE_STRING . '(60) NOT NULL',
            'title_it' => Schema::TYPE_STRING . '(60) NOT NULL',
            'title_pl' => Schema::TYPE_STRING . '(60) NOT NULL',
            'title_ja' => Schema::TYPE_STRING . '(60) NOT NULL',
            'title_lt' => Schema::TYPE_STRING . '(60) NOT NULL',
            'title_lv' => Schema::TYPE_STRING . '(60) NOT NULL',
            'title_cz' => Schema::TYPE_STRING . '(60) NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%countries}}');
    }

}
