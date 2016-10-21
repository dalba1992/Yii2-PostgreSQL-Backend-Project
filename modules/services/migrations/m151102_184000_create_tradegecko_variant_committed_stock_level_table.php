<?php

use yii\db\Schema;
use yii\db\Migration;

class m151102_184000_create_tradegecko_variant_committed_stock_level_table extends Migration
{
    public function up()
    {
        $this->createTable('tradegecko_variant_committed_stock_level', [
            'id' => Schema::TYPE_PK,
            'variant_id' => Schema::TYPE_INTEGER,
            'committed_stock_level_id' => Schema::TYPE_INTEGER,
            'committed_stock_level_value' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('tradegecko_variant_committed_stock_level');
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