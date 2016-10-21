<?php

use yii\db\Schema;
use yii\db\Migration;

class m151216_155800_create_liteview_order_details_table extends Migration
{
    public function up()
    {
        $this->createTable('liteview_order_details', [
            'id' => Schema::TYPE_PK,
            'liteview_order_id' => Schema::TYPE_INTEGER,
            'order_status' => Schema::TYPE_STRING,
            'order_date' => Schema::TYPE_STRING,
            'order_number' => Schema::TYPE_STRING,
            'order_source' => Schema::TYPE_STRING,
            'order_type' => Schema::TYPE_STRING,
            'catalog_name' => Schema::TYPE_STRING,
            'gift_order' => Schema::TYPE_STRING,
            'po_number' => Schema::TYPE_STRING,
            'passthrough_field_01' => Schema::TYPE_STRING,
            'passthrough_field_02' => Schema::TYPE_STRING,
            'future_date_release' => Schema::TYPE_STRING,
            'allocate_inventory_now' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('liteview_order_details');
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