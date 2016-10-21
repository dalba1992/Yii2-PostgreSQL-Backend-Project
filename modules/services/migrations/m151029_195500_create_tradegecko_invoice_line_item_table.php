<?php

use yii\db\Schema;
use yii\db\Migration;

class m151029_195500_create_tradegecko_invoice_line_item_table extends Migration
{
    public function up()
    {
        $this->createTable('tradegecko_invoice_line_item', [
            'id' => Schema::TYPE_PK,
            'created_at' => Schema::TYPE_STRING,
            'updated_at' => Schema::TYPE_STRING,
            'invoice_id' => Schema::TYPE_INTEGER,
            'order_line_item_id' => Schema::TYPE_INTEGER, 
            'ledger_account_id' => Schema::TYPE_INTEGER, 
            'quantity' => Schema::TYPE_STRING,
            'position' => Schema::TYPE_INTEGER,
        ]);
    }

    public function down()
    {
        $this->dropTable('tradegecko_invoice_line_item');
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