<?php

use yii\db\Schema;
use yii\db\Migration;

class m151028_184700_create_tradegecko_fulfillment_table extends Migration
{
    public function up()
    {
        $this->createTable('tradegecko_fulfillment', [
            'id' => Schema::TYPE_PK,
            'created_at' => Schema::TYPE_STRING,
            'updated_at' => Schema::TYPE_STRING,
            'order_id' => Schema::TYPE_INTEGER,
            'shipping_address_id' => Schema::TYPE_INTEGER,            
            'billing_address_id' => Schema::TYPE_INTEGER,
            'stock_location_id' => Schema::TYPE_INTEGER,
            'delivery_type' => Schema::TYPE_STRING,
            'exchange_rate' => Schema::TYPE_STRING,
            'notes' => Schema::TYPE_STRING,
            'packed_at' => Schema::TYPE_STRING,
            'received_at' => Schema::TYPE_STRING,
            'service' => Schema::TYPE_STRING,
            'shipped_at' => Schema::TYPE_STRING,
            'fulfillment_status' => Schema::TYPE_STRING,
            'tracking_company' => Schema::TYPE_STRING,
            'tracking_number' => Schema::TYPE_STRING,
            'tracking_url' => Schema::TYPE_STRING,
            'order_number' => Schema::TYPE_STRING,
            'company_id' => Schema::TYPE_INTEGER,
            'fulfillment_line_item_ids' => Schema::TYPE_TEXT,
        ]);
    }

    public function down()
    {
        $this->dropTable('tradegecko_fulfillment');
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