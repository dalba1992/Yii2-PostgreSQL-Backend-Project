<?php

use yii\db\Schema;
use yii\db\Migration;

class m151109_163700_create_tradegecko_payment_table extends Migration
{
    public function up()
    {
        $this->createTable('tradegecko_payment', [
            'id' => Schema::TYPE_PK,
            'created_at' => Schema::TYPE_STRING,
            'updated_at' => Schema::TYPE_STRING,
            'invoice_id' => Schema::TYPE_INTEGER,
            'amount' => Schema::TYPE_FLOAT,
            'reference' => Schema::TYPE_STRING,
            'paid_at' => Schema::TYPE_STRING,
            'payment_status' => Schema::TYPE_STRING,
            'exchange_rate' => Schema::TYPE_FLOAT,
        ]);
    }

    public function down()
    {
        $this->dropTable('tradegecko_payment');
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