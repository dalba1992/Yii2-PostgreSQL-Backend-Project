<?php

use yii\db\Schema;
use yii\db\Migration;

class m151029_184700_create_tradegecko_fulfillment_line_item_table extends Migration
{
    public function up()
    {
        $this->createTable('tradegecko_fulfillment_line_item', [
            'id' => Schema::TYPE_PK,
            'created_at' => Schema::TYPE_STRING,
            'updated_at' => Schema::TYPE_STRING,
            'fulfillment_id' => Schema::TYPE_INTEGER,
            'order_line_item_id' => Schema::TYPE_INTEGER,            
            'base_price' => Schema::TYPE_STRING,
            'quantity' => Schema::TYPE_STRING,
            'position' => Schema::TYPE_INTEGER,
        ]);
    }

    public function down()
    {
        $this->dropTable('tradegecko_fulfillment_line_item');
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