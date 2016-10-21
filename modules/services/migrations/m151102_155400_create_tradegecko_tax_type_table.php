<?php

use yii\db\Schema;
use yii\db\Migration;

class m151102_155400_create_tradegecko_tax_type_table extends Migration
{
    public function up()
    {
        $this->createTable('tradegecko_tax_type', [
            'id' => Schema::TYPE_PK,
            'tax_type_code' => Schema::TYPE_STRING,
            'imported_from' => Schema::TYPE_STRING,
            'tax_type_name' => Schema::TYPE_STRING,
            'tax_type_status' => Schema::TYPE_STRING,
            'quickbooks_online_id' => Schema::TYPE_INTEGER,
            'xero_online_id' => Schema::TYPE_INTEGER,
            'effective_rate' => Schema::TYPE_STRING,
            'tax_component_ids' => Schema::TYPE_TEXT,
        ]);
    }

    public function down()
    {
        $this->dropTable('tradegecko_tax_type');
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