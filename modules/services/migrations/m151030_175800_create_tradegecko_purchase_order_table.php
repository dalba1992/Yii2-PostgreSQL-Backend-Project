<?php

use yii\db\Schema;
use yii\db\Migration;

class m151030_175800_create_tradegecko_purchase_order_table extends Migration
{
    public function up()
    {
        $this->createTable('tradegecko_purchase_order', [
            'id' => Schema::TYPE_PK,
            'created_at' => Schema::TYPE_STRING,
            'updated_at' => Schema::TYPE_STRING,
            'billing_address_id' => Schema::TYPE_INTEGER,
            'company_id' => Schema::TYPE_INTEGER,
            'due_at' => Schema::TYPE_STRING,
            'email' => Schema::TYPE_STRING,
            'notes' => Schema::TYPE_STRING,
            'order_number' => Schema::TYPE_STRING,
            'procurement_ids' => Schema::TYPE_TEXT,
            'procurement_status' => Schema::TYPE_STRING,
            'purchase_order_line_item_ids' => Schema::TYPE_TEXT,
            'reference_number' => Schema::TYPE_STRING,
            'purchase_order_status' => Schema::TYPE_STRING,
            'stock_location_id' => Schema::TYPE_INTEGER,
            'tax_type' => Schema::TYPE_STRING,
            'vendor_address_id' => Schema::TYPE_INTEGER,
            'xero' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('tradegecko_purchase_order');
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