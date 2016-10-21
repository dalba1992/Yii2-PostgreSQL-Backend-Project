<?php

use yii\db\Schema;
use yii\db\Migration;

class m151030_164400_create_tradegecko_price_list_table extends Migration
{
    public function up()
    {
        $this->createTable('tradegecko_price_list', [
            'id' => Schema::TYPE_STRING,
            'price_list_code' => Schema::TYPE_STRING,
            'price_list_name' => Schema::TYPE_STRING,
            'is_cost' => Schema::TYPE_STRING,
            'currency_id' => Schema::TYPE_INTEGER,
            'currency_symbol' => Schema::TYPE_STRING,
            'currency_iso' => Schema::TYPE_STRING,
            'is_default' => Schema::TYPE_INTEGER,
        ]);

        $this->addPrimaryKey('PK1', 'tradegecko_price_list', 'id');
    }

    public function down()
    {
        $this->dropTable('tradegecko_price_list');
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