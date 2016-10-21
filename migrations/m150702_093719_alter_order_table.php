<?php

use yii\db\Schema;
use yii\db\Migration;

class m150702_093719_alter_order_table extends Migration
{
    public function up()
    {
        $this->dropColumn('order', 'tradegeckoOrderId');
        $this->dropColumn('order', 'liteviewOrderNo');
        $this->addColumn('orderlog', 'tradegeckoOrderId', Schema::TYPE_INTEGER);
        $this->addColumn('orderlog', 'liteviewOrderNo', Schema::TYPE_STRING);
    }

    public function down()
    {
        $this->dropColumn('orderlog', 'tradegeckoOrderId');
        $this->dropColumn('orderlog', 'liteviewOrderNo');
        $this->addColumn('order', 'tradegeckoOrderId', Schema::TYPE_INTEGER);
        $this->addColumn('order', 'liteviewOrderNo', Schema::TYPE_STRING);
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
