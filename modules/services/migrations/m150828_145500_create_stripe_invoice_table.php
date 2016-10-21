<?php

use yii\db\Schema;
use yii\db\Migration;

class m150828_145500_create_stripe_invoice_table extends Migration
{
    public function up()
    {
        $this->createTable('stripe_invoice', [
            'id' => Schema::TYPE_PK,
            'invoiceId' => Schema::TYPE_STRING,
            'created' => Schema::TYPE_INTEGER,
            'period_start' => Schema::TYPE_INTEGER,
            'period_end' => Schema::TYPE_INTEGER,
            'subtotal' => Schema::TYPE_FLOAT,
            'total' => Schema::TYPE_FLOAT,
            'customerId' => Schema::TYPE_STRING,
            'currency' => Schema::TYPE_STRING,
            'accepted' => Schema::TYPE_STRING,
            'closed' => Schema::TYPE_STRING,
            'forgiven' => Schema::TYPE_STRING,
            'paid' => Schema::TYPE_STRING,
            'livemode' => Schema::TYPE_STRING,
            'attempt_count' => Schema::TYPE_INTEGER,
            'amount_due' => Schema::TYPE_FLOAT,
            'starting_balance' => Schema::TYPE_FLOAT,
            'ending_balance' => Schema::TYPE_FLOAT,
            'next_payment_attempt' => Schema::TYPE_INTEGER,
            'webhooks_delivered_at' => Schema::TYPE_INTEGER,
            'chargeId' => Schema::TYPE_STRING,
            'subscriptionId' => Schema::TYPE_STRING,
            'application_fee' => Schema::TYPE_INTEGER,
            'tax_percent' => Schema::TYPE_FLOAT,
            'tax' => Schema::TYPE_INTEGER,
            'statement_descriptor' => Schema::TYPE_STRING,
            'statement_description' => Schema::TYPE_STRING,
            'description' => Schema::TYPE_STRING,
            'receipt_number' => Schema::TYPE_STRING,
            'regDate' => Schema::TYPE_STRING,
            'updateDate' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('stripe_invoice');
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