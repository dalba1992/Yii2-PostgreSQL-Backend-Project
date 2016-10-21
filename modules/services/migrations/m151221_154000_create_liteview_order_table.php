<?php

use yii\db\Schema;
use yii\db\Migration;

class m151221_154000_create_liteview_order_table extends Migration
{
    public function up()
    {
        $this->createTable('liteview_order', [
            'id' => Schema::TYPE_PK,
            'ifs_order_number' => Schema::TYPE_STRING,
            'created_at' => Schema::TYPE_STRING,
            'updated_at' => Schema::TYPE_STRING,
            'submitted_at' => Schema::TYPE_STRING,
            'tradegecko_order_ids' => Schema::TYPE_TEXT,
            'liteview_order_status' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('liteview_order');
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