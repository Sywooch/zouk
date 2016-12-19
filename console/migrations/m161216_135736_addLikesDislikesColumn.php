<?php

use yii\db\Schema;
use yii\db\Migration;

class m161216_135736_addLikesDislikesColumn extends Migration
{
    public function up()
    {
        $this->addColumn('{{%item}}', 'likes', Schema::TYPE_INTEGER . ' DEFAULT 0 NOT NULL');
        $this->addColumn('{{%item}}', 'dislikes', Schema::TYPE_INTEGER . ' DEFAULT 0 NOT NULL');
        $this->execute('UPDATE {{%item}} SET likes=like_count WHERE like_count>0');
        $this->execute('UPDATE {{%item}} SET dislikes=abs(like_count) WHERE like_count<0');

        $this->addColumn('{{%school}}', 'likes', Schema::TYPE_INTEGER . ' DEFAULT 0 NOT NULL');
        $this->addColumn('{{%school}}', 'dislikes', Schema::TYPE_INTEGER . ' DEFAULT 0 NOT NULL');
        $this->execute('UPDATE {{%school}} SET likes=like_count WHERE like_count>0');
        $this->execute('UPDATE {{%school}} SET dislikes=abs(like_count) WHERE like_count<0');

        $this->addColumn('{{%event}}', 'likes', Schema::TYPE_INTEGER . ' DEFAULT 0 NOT NULL');
        $this->addColumn('{{%event}}', 'dislikes', Schema::TYPE_INTEGER . ' DEFAULT 0 NOT NULL');
        $this->execute('UPDATE {{%event}} SET likes=like_count WHERE like_count>0');
        $this->execute('UPDATE {{%event}} SET dislikes=abs(like_count) WHERE like_count<0');

        $this->addColumn('{{%comment}}', 'likes', Schema::TYPE_INTEGER . ' DEFAULT 0 NOT NULL');
        $this->addColumn('{{%comment}}', 'dislikes', Schema::TYPE_INTEGER . ' DEFAULT 0 NOT NULL');
        $this->execute('UPDATE {{%comment}} SET likes=like_count WHERE like_count>0');
        $this->execute('UPDATE {{%comment}} SET dislikes=abs(like_count) WHERE like_count<0');


    }

    public function down()
    {
        $this->dropColumn('{{%item}}', 'likes');
        $this->dropColumn('{{%item}}', 'dislikes');

        $this->dropColumn('{{%school}}', 'likes');
        $this->dropColumn('{{%school}}', 'dislikes');

        $this->dropColumn('{{%event}}', 'likes');
        $this->dropColumn('{{%event}}', 'dislikes');

        $this->dropColumn('{{%comment}}', 'likes');
        $this->dropColumn('{{%comment}}', 'dislikes');
    }

}
