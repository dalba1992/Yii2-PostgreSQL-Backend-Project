<?php

use yii\db\Schema;
use yii\db\Migration;

class m150828_065800_create_stripe_refund_table extends Migration
{
    public function up()
    {
        $this->createTable('stripe_refund', [
            'id' => Schema::TYPE_PK,
            'refundId' => Schema::TYPE_STRING,
            'chargeId' => Schema::TYPE_STRING,
            'amount' => Schema::TYPE_FLOAT,
            'currency' => Schema::TYPE_STRING,
            'created' => Schema::TYPE_INTEGER,
            'balance_transaction' => Schema::TYPE_STRING,
            'receipt_number' => Schema::TYPE_STRING,
            'reason' => Schema::TYPE_STRING,
            'regDate' => Schema::TYPE_STRING,
            'updateDate' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('stripe_refund');
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