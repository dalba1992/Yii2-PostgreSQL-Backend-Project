<?php

use yii\db\Schema;
use yii\db\Migration;

class m151029_200700_create_tradegecko_order_line_item_table extends Migration
{
    public function up()
    {
        $this->createTable('tradegecko_order_line_item', [
            'id' => Schema::TYPE_PK,
            'created_at' => Schema::TYPE_STRING,
            'updated_at' => Schema::TYPE_STRING,
            'order_id' => Schema::TYPE_INTEGER,
            'variant_id' => Schema::TYPE_INTEGER, 
            'tax_type_id' => Schema::TYPE_INTEGER, 
            'discount' => Schema::TYPE_FLOAT,
            'freeform' => Schema::TYPE_STRING,
            'image_url' => Schema::TYPE_STRING,
            'label' => Schema::TYPE_STRING,
            'line_type' => Schema::TYPE_STRING,
            'position' => Schema::TYPE_INTEGER,
            'price' => Schema::TYPE_FLOAT,
            'quantity' => Schema::TYPE_STRING,
            'tax_rate_override' => Schema::TYPE_FLOAT,
            'tax_rate' => Schema::TYPE_FLOAT,
            'fulfillment_line_item_ids' => Schema::TYPE_TEXT,
            'fulfillment_return_line_item_ids' => Schema::TYPE_TEXT,
            'invoice_line_item_ids' => Schema::TYPE_TEXT,
        ]);
    }

    public function down()
    {
        $this->dropTable('tradegecko_order_line_item');
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