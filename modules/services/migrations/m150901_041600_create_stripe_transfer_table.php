<?php

use yii\db\Schema;
use yii\db\Migration;

class m150901_041600_create_stripe_transfer_table extends Migration
{
    public function up()
    {
        $this->createTable('stripe_transfer', [
            'id' => Schema::TYPE_PK,
            'transferId' => Schema::TYPE_STRING,
            'created' => Schema::TYPE_INTEGER,
            'scheduledDate' => Schema::TYPE_INTEGER,
            'livemode' => Schema::TYPE_STRING,
            'amount' => Schema::TYPE_FLOAT,
            'currency' => Schema::TYPE_STRING,
            'reversed' => Schema::TYPE_STRING,
            'status' => Schema::TYPE_STRING,
            'type' => Schema::TYPE_STRING,
            'balance_transaction' => Schema::TYPE_STRING,
            'bank_account_id' => Schema::TYPE_STRING,
            'bank_account_last4' => Schema::TYPE_STRING,
            'bank_account_country' => Schema::TYPE_STRING,
            'bank_account_status' => Schema::TYPE_STRING,
            'bank_account_fingerprint' => Schema::TYPE_STRING,
            'bank_account_routing_number' => Schema::TYPE_STRING,
            'bank_account_bank_name' => Schema::TYPE_STRING,
            'destination' => Schema::TYPE_STRING,
            'description' => Schema::TYPE_STRING,
            'failure_message' => Schema::TYPE_STRING,
            'failure_code' => Schema::TYPE_STRING,
            'amount_reversed' => Schema::TYPE_FLOAT,
            'statement_descriptor' => Schema::TYPE_STRING,
            'recipient' => Schema::TYPE_STRING,
            'source_transaction' => Schema::TYPE_STRING,
            'application_fee' => Schema::TYPE_STRING,
            'statement_description' => Schema::TYPE_STRING,
            'regDate' => Schema::TYPE_STRING,
            'updateDate' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('stripe_transfer');
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