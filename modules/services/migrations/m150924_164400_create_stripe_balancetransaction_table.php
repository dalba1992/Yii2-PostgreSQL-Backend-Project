<?php

use yii\db\Schema;
use yii\db\Migration;

class m150924_164400_create_stripe_balancetransaction_table extends Migration
{
    public function up()
    {
        $this->createTable('stripe_balancetransaction', [
            'id' => Schema::TYPE_PK,
            'balanceId' => Schema::TYPE_STRING,
            'amount' => Schema::TYPE_FLOAT,
            'currency' => Schema::TYPE_STRING,
            'net' => Schema::TYPE_FLOAT,
            'type' => Schema::TYPE_STRING,
            'created' => Schema::TYPE_INTEGER,
            'available_on' => Schema::TYPE_INTEGER,
            'status' => Schema::TYPE_STRING,
            'fee' => Schema::TYPE_FLOAT,
            'source' => Schema::TYPE_STRING,
            'description' => Schema::TYPE_TEXT,
            'regDate' => Schema::TYPE_STRING,
            'updateDate' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('stripe_balancetransaction');
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