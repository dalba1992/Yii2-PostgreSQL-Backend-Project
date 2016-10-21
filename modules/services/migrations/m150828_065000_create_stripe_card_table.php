<?php

use yii\db\Schema;
use yii\db\Migration;

class m150828_065000_create_stripe_card_table extends Migration
{
    public function up()
    {
        $this->createTable('stripe_card', [
            'id' => Schema::TYPE_PK,
            'customerId' => Schema::TYPE_STRING,
            'cardId' => Schema::TYPE_STRING,
            'last4' => Schema::TYPE_STRING,
            'brand' => Schema::TYPE_STRING,
            'funding' => Schema::TYPE_STRING,
            'exp_month' => Schema::TYPE_INTEGER,
            'exp_year' => Schema::TYPE_INTEGER,
            'fingerprint' => Schema::TYPE_STRING,
            'country' => Schema::TYPE_STRING,
            'name' => Schema::TYPE_STRING,
            'address_line1' => Schema::TYPE_STRING,
            'address_line2' => Schema::TYPE_STRING,
            'address_city' => Schema::TYPE_STRING,
            'address_state' => Schema::TYPE_STRING,
            'address_zip' => Schema::TYPE_STRING,
            'address_country' => Schema::TYPE_STRING,
            'cvc_check' => Schema::TYPE_STRING,
            'address_line1_check' => Schema::TYPE_STRING,
            'address_zip_check' => Schema::TYPE_STRING,
            'tokenization_method' => Schema::TYPE_STRING,
            'dynamic_last4' => Schema::TYPE_STRING,
            'regDate' => Schema::TYPE_STRING,
            'updateDate' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('stripe_card');
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