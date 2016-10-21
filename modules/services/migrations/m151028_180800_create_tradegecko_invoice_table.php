<?php

use yii\db\Schema;
use yii\db\Migration;

class m151028_180800_create_tradegecko_invoice_table extends Migration
{
    public function up()
    {
        $this->createTable('tradegecko_invoice', [
            'id' => Schema::TYPE_PK,
            'created_at' => Schema::TYPE_STRING,
            'updated_at' => Schema::TYPE_STRING,
            'document_url' => Schema::TYPE_STRING,
            'order_id' => Schema::TYPE_INTEGER,
            'shipping_address_id' => Schema::TYPE_INTEGER,
            'billing_address_id' => Schema::TYPE_INTEGER,
            'payment_term_id' => Schema::TYPE_INTEGER,
            'due_at' => Schema::TYPE_STRING,
            'exchange_rate' => Schema::TYPE_FLOAT,
            'invoice_number' => Schema::TYPE_STRING,
            'invoiced_at' => Schema::TYPE_STRING,
            'notes' => Schema::TYPE_STRING,
            'cached_total' => Schema::TYPE_FLOAT,
            'payment_status' => Schema::TYPE_STRING,
            'order_number' => Schema::TYPE_STRING,
            'company_id' => Schema::TYPE_INTEGER,
            'currency_id' => Schema::TYPE_INTEGER,
            'invoice_line_item_ids' => Schema::TYPE_TEXT,
            'payment_ids' => Schema::TYPE_TEXT,
            'refund_ids' => Schema::TYPE_TEXT,
        ]);
    }

    public function down()
    {
        $this->dropTable('tradegecko_invoice');
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