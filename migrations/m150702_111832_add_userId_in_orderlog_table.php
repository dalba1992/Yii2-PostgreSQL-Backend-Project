<?php

use yii\db\Schema;
use yii\db\Migration;

class m150702_111832_add_userId_in_orderlog_table extends Migration
{
    public function up()
    {
        $this->addColumn('orderlog', 'userId', Schema::TYPE_INTEGER);
    }

    public function down()
    {
        $this->dropColumn('orderlog', 'userId');
    }
    
    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }
    
    public function safeDown()
    {
    }
    */
}
