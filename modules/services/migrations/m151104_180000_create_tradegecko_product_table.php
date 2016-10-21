<?php

use yii\db\Schema;
use yii\db\Migration;

class m151104_180000_create_tradegecko_product_table extends Migration
{
    public function up()
    {
        $this->createTable('tradegecko_product', [
            'id' => Schema::TYPE_PK,
            'created_at' => Schema::TYPE_STRING,
            'updated_at' => Schema::TYPE_STRING,
            'brand' => Schema::TYPE_STRING,
            'description' => Schema::TYPE_TEXT,
            'image_url' => Schema::TYPE_STRING,
            'product_name' => Schema::TYPE_STRING,
            'opt1' => Schema::TYPE_STRING,
            'opt2' => Schema::TYPE_STRING,
            'opt3' => Schema::TYPE_STRING,
            'product_type' => Schema::TYPE_STRING,
            'quantity' => Schema::TYPE_FLOAT,
            'search_cache' => Schema::TYPE_STRING,
            'product_status' => Schema::TYPE_STRING,
            'supplier' => Schema::TYPE_STRING,
            'supplier_ids' => Schema::TYPE_TEXT,
            'tags' => Schema::TYPE_STRING,
            'variant_ids' => Schema::TYPE_TEXT,
            'vendor' => Schema::TYPE_STRING,
            'variant_num' => Schema::TYPE_INTEGER,
        ]);
    }

    public function down()
    {
        $this->dropTable('tradegecko_product');
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