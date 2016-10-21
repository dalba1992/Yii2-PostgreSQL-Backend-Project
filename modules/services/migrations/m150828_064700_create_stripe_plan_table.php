<?php

use yii\db\Schema;
use yii\db\Migration;

class m150828_064700_create_stripe_plan_table extends Migration
{
    public function up()
    {
        $this->createTable('stripe_plan', [
            'id' => Schema::TYPE_PK,
            'planId' => Schema::TYPE_STRING,
            'interval' => Schema::TYPE_STRING,
            'name' => Schema::TYPE_STRING,
            'created' => Schema::TYPE_INTEGER,
            'amount' => Schema::TYPE_FLOAT,
            'currency' => Schema::TYPE_STRING,
            'livemode' => Schema::TYPE_STRING,
            'interval_count' => Schema::TYPE_INTEGER,
            'trial_period_days' => Schema::TYPE_INTEGER,
            'statement_descriptor' => Schema::TYPE_STRING,
            'statement_description' => Schema::TYPE_STRING,
            'regDate' => Schema::TYPE_STRING,
            'updateDate' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('stripe_plan');
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