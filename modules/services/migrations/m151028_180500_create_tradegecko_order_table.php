<?php

use yii\db\Schema;
use yii\db\Migration;

class m151028_180500_create_tradegecko_order_table extends Migration
{
    public function up()
    {
        $this->createTable('tradegecko_order', [
            'id' => Schema::TYPE_PK,
            'created_at' => Schema::TYPE_STRING,
            'updated_at' => Schema::TYPE_STRING,
            'document_url' => Schema::TYPE_STRING,
            'assignee_id' => Schema::TYPE_INTEGER,
            'billing_address_id' => Schema::TYPE_INTEGER,
            'company_id' => Schema::TYPE_INTEGER,
            'contact_id' => Schema::TYPE_INTEGER,
            'currency_id' => Schema::TYPE_INTEGER,
            'shipping_address_id' => Schema::TYPE_INTEGER,
            'stock_location_id' => Schema::TYPE_INTEGER,
            'user_id' => Schema::TYPE_INTEGER,
            'default_price_list_id' => Schema::TYPE_STRING,
            'email' => Schema::TYPE_STRING,
            'fulfillment_status' => Schema::TYPE_STRING,
            'invoice_status' => Schema::TYPE_STRING,
            'issued_at' => Schema::TYPE_STRING,
            'notes' => Schema::TYPE_STRING,
            'order_number' => Schema::TYPE_STRING,
            'packed_status' => Schema::TYPE_STRING,
            'payment_status' => Schema::TYPE_STRING,
            'phone_number' => Schema::TYPE_STRING,
            'reference_number' => Schema::TYPE_STRING,
            'return_status' => Schema::TYPE_STRING,
            'returning_status' => Schema::TYPE_STRING,
            'ship_at' => Schema::TYPE_STRING,
            'source_url' => Schema::TYPE_STRING,
            'order_status' => Schema::TYPE_STRING,
            'tax_label' => Schema::TYPE_STRING,
            'tax_override' => Schema::TYPE_STRING,
            'tax_treatment' => Schema::TYPE_STRING,
            'total' => Schema::TYPE_FLOAT,
            'tags' => Schema::TYPE_STRING,
            'fulfillment_ids' => Schema::TYPE_TEXT,
            'fulfillment_return_ids' => Schema::TYPE_TEXT,
            'invoice_ids' => Schema::TYPE_TEXT,
            'payment_ids' => Schema::TYPE_TEXT,
            'refund_ids' => Schema::TYPE_TEXT,
            'cached_total' => Schema::TYPE_FLOAT,
            'default_price_type_id' => Schema::TYPE_STRING,
            'source' => Schema::TYPE_STRING,
            'tax_type' => Schema::TYPE_STRING,
            'tracking_number' => Schema::TYPE_STRING,
            'url' => Schema::TYPE_STRING,
            'source_id' => Schema::TYPE_STRING,
            'invoice_numbers' => Schema::TYPE_TEXT,
            'order_line_item_ids' => Schema::TYPE_TEXT,
        ]);
    }

    public function down()
    {
        $this->dropTable('tradegecko_order');
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