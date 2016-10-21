<?php

use yii\db\Schema;
use yii\db\Migration;

class m150828_065700_create_stripe_charge_table extends Migration
{
    public function up()
    {
        $this->createTable('stripe_charge', [
            'id' => Schema::TYPE_PK,
            'chargeId' => Schema::TYPE_STRING,
            'created' => Schema::TYPE_INTEGER,
            'livemode' => Schema::TYPE_STRING,
            'paid' => Schema::TYPE_STRING,
            'status' => Schema::TYPE_STRING,
            'amount' => Schema::TYPE_FLOAT,
            'currency' => Schema::TYPE_STRING,
            'refunded' => Schema::TYPE_STRING,
            'sourceId' => Schema::TYPE_STRING,
            'cardId' => Schema::TYPE_STRING,
            'balance_transaction' => Schema::TYPE_STRING,
            'failure_message' => Schema::TYPE_STRING,
            'failure_code' => Schema::TYPE_STRING,
            'amount_refunded' => Schema::TYPE_FLOAT,
            'customerId' => Schema::TYPE_STRING,
            'invoice' => Schema::TYPE_STRING,
            'description' => Schema::TYPE_STRING,
            'statement_descriptor' => Schema::TYPE_STRING,
            'receipt_email' => Schema::TYPE_STRING,
            'receipt_number' => Schema::TYPE_STRING,
            'destination' => Schema::TYPE_STRING,
            'application_fee' => Schema::TYPE_STRING,
            'statement_description' => Schema::TYPE_STRING,
            'regDate' => Schema::TYPE_STRING,
            'updateDate' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('stripe_charge');
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