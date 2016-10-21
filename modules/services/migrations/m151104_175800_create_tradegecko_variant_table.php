<?php

use yii\db\Schema;
use yii\db\Migration;

class m151104_175800_create_tradegecko_variant_table extends Migration
{
    public function up()
    {
        $this->createTable('tradegecko_variant', [
            'id' => Schema::TYPE_PK,
            'created_at' => Schema::TYPE_STRING,
            'updated_at' => Schema::TYPE_STRING,
            'product_id' => Schema::TYPE_INTEGER,
            'default_ledger_account_id' => Schema::TYPE_INTEGER,
            'buy_price' => Schema::TYPE_STRING,
            'committed_stock' => Schema::TYPE_STRING,
            'incoming_stock' => Schema::TYPE_STRING,
            'composite' => Schema::TYPE_STRING,
            'description' => Schema::TYPE_TEXT,
            'is_online' => Schema::TYPE_STRING,
            'keep_selling' => Schema::TYPE_STRING,
            'last_cost_price' => Schema::TYPE_STRING,
            'manage_stock' => Schema::TYPE_STRING,
            'max_online' => Schema::TYPE_INTEGER,
            'moving_average_cost' => Schema::TYPE_STRING,
            'variant_name' => Schema::TYPE_STRING,
            'online_ordering' => Schema::TYPE_STRING,
            'opt1' => Schema::TYPE_STRING,
            'opt2' => Schema::TYPE_STRING,
            'opt3' => Schema::TYPE_STRING,
            'position' => Schema::TYPE_INTEGER,
            'product_name' => Schema::TYPE_STRING,
            'product_status' => Schema::TYPE_STRING,
            'product_type' => Schema::TYPE_STRING,
            'retail_price' => Schema::TYPE_STRING,
            'sellable' => Schema::TYPE_STRING,
            'sku' => Schema::TYPE_STRING,
            'variant_status' => Schema::TYPE_STRING,
            'stock_on_hand' => Schema::TYPE_STRING,
            'supplier_code' => Schema::TYPE_STRING,
            'taxable' => Schema::TYPE_STRING,
            'upc' => Schema::TYPE_STRING,
            'weight' => Schema::TYPE_STRING,
            'wholesale_price' => Schema::TYPE_STRING,
            'image_ids' => Schema::TYPE_TEXT,
        ]);
    }

    public function down()
    {
        $this->dropTable('tradegecko_variant');
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