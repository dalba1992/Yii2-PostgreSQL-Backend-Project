<?php

use yii\db\Schema;
use yii\db\Migration;

class m151219_043500_create_liteview_order_items_table extends Migration
{
    public function up()
    {
        $this->createTable('liteview_order_items', [
            'id' => Schema::TYPE_PK,
            'liteview_order_id' => Schema::TYPE_INTEGER,
            'inventory_item' => Schema::TYPE_STRING,
            'inventory_item_sku' => Schema::TYPE_STRING,
            'inventory_item_description' => Schema::TYPE_TEXT,
            'inventory_item_price' => Schema::TYPE_FLOAT,
            'inventory_item_qty' => Schema::TYPE_FLOAT,
            'inventory_item_ext_price' => Schema::TYPE_FLOAT,
            'inventory_passthrough_01' => Schema::TYPE_STRING,
            'inventory_passthrough_02' => Schema::TYPE_STRING,
            'tradegecko_currency_id' => Schema::TYPE_INTEGER,
        ]);
    }

    public function down()
    {
        $this->dropTable('liteview_order_items');
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