<?php

use yii\db\Schema;
use yii\db\Migration;

class m151030_183300_create_tradegecko_purchase_order_line_item_table extends Migration
{
    public function up()
    {
        $this->createTable('tradegecko_purchase_order_line_item', [
            'id' => Schema::TYPE_PK,
            'purchase_order_id' => Schema::TYPE_INTEGER,
            'variant_id' => Schema::TYPE_INTEGER,
            'procurement_id' => Schema::TYPE_INTEGER,
            'quantity' => Schema::TYPE_STRING,
            'position' => Schema::TYPE_INTEGER,
            'tax_type_id' => Schema::TYPE_INTEGER,
            'tax_rate_override' => Schema::TYPE_STRING,
            'price' => Schema::TYPE_STRING,
            'label' => Schema::TYPE_STRING,
            'freeform' => Schema::TYPE_STRING,
            'base_price' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('tradegecko_purchase_order_line_item');
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