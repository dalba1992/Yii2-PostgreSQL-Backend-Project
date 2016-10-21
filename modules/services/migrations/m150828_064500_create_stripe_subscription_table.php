<?php

use yii\db\Schema;
use yii\db\Migration;

class m150828_064500_create_stripe_subscription_table extends Migration
{
    public function up()
    {
        $this->createTable('stripe_subscription', [
            'id' => Schema::TYPE_PK,
            'customerId' => Schema::TYPE_STRING,
            'subscriptionId' => Schema::TYPE_STRING,
            'start' => Schema::TYPE_INTEGER,
            'status' => Schema::TYPE_STRING,
            'cancel_at_period_end' => Schema::TYPE_STRING,
            'current_period_start' => Schema::TYPE_INTEGER,
            'current_period_end' => Schema::TYPE_INTEGER,
            'ended_at' => Schema::TYPE_INTEGER,
            'trial_start' => Schema::TYPE_INTEGER,
            'trial_end' => Schema::TYPE_INTEGER,
            'canceled_at' => Schema::TYPE_INTEGER,
            'quantity' => Schema::TYPE_INTEGER,
            'application_fee_percent' => Schema::TYPE_FLOAT,
            'tax_percent' => Schema::TYPE_FLOAT,
            'planId' => Schema::TYPE_STRING,
            'regDate' => Schema::TYPE_STRING,
            'updateDate' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('stripe_subscription');
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