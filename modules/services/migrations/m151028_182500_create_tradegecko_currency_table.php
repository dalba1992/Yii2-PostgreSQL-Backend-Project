<?php

use yii\db\Schema;
use yii\db\Migration;

class m151028_182500_create_tradegecko_currency_table extends Migration
{
    public function up()
    {
        $this->createTable('tradegecko_currency', [
            'id' => Schema::TYPE_PK,
            'created_at' => Schema::TYPE_STRING,
            'updated_at' => Schema::TYPE_STRING,
            'delimiter' => Schema::TYPE_STRING,
            'currency_format' => Schema::TYPE_STRING,            
            'iso' => Schema::TYPE_STRING,
            'currency_name' => Schema::TYPE_STRING,
            'currency_precision' => Schema::TYPE_INTEGER,
            'rate' => Schema::TYPE_STRING,
            'currency_separator' => Schema::TYPE_STRING,
            'symbol' => Schema::TYPE_STRING,
            'currency_status' => Schema::TYPE_STRING,
            'subunit' => Schema::TYPE_INTEGER,
        ]);
    }

    public function down()
    {
        $this->dropTable('tradegecko_currency');
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