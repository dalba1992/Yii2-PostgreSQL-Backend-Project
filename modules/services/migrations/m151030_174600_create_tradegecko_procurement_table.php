<?php

use yii\db\Schema;
use yii\db\Migration;

class m151030_174600_create_tradegecko_procurement_table extends Migration
{
    public function up()
    {
        $this->createTable('tradegecko_procurement', [
            'id' => Schema::TYPE_PK,
            'created_at' => Schema::TYPE_STRING,
            'updated_at' => Schema::TYPE_STRING,
            'received_at' => Schema::TYPE_STRING,
            'purchase_order_id' => Schema::TYPE_INTEGER,
            'purchase_order_line_item_ids' => Schema::TYPE_TEXT,
        ]);
    }

    public function down()
    {
        $this->dropTable('tradegecko_procurement');
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