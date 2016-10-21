<?php

use yii\db\Schema;
use yii\db\Migration;

class m150828_065300_create_stripe_coupon_table extends Migration
{
    public function up()
    {
        $this->createTable('stripe_coupon', [
            'id' => Schema::TYPE_PK,
            'couponId' => Schema::TYPE_STRING,
            'created' => Schema::TYPE_INTEGER,
            'percent_off' => Schema::TYPE_INTEGER,
            'amount_off' => Schema::TYPE_INTEGER,
            'currency' => Schema::TYPE_STRING,
            'livemode' => Schema::TYPE_STRING,
            'duration' => Schema::TYPE_STRING,
            'redeem_by' => Schema::TYPE_INTEGER,
            'max_redemptions' => Schema::TYPE_INTEGER,
            'times_redeemed' => Schema::TYPE_INTEGER,
            'duration_in_months' => Schema::TYPE_INTEGER,
            'valid' => Schema::TYPE_STRING,
            'regDate' => Schema::TYPE_STRING,
            'updateDate' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('stripe_coupon');
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