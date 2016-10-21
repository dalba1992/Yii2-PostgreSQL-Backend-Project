<?php

use yii\db\Schema;
use yii\db\Migration;

class m150828_064400_create_stripe_customer_table extends Migration
{
    public function up()
    {
        $this->createTable('stripe_customer', [
            'id' => Schema::TYPE_PK,
            'customerId' => Schema::TYPE_STRING,
            'created' => Schema::TYPE_INTEGER,
            'email' => Schema::TYPE_STRING,
            'account_balance' => Schema::TYPE_FLOAT,
            'currency' => Schema::TYPE_STRING,
            'livemode' => Schema::TYPE_STRING,
            'default_card' => Schema::TYPE_STRING,
            'default_source' => Schema::TYPE_STRING,
            'description' => Schema::TYPE_TEXT,
            'regDate' => Schema::TYPE_STRING,
            'updateDate' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('stripe_customer');
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