<?php

use yii\db\Schema;
use yii\db\Migration;

class m151216_163400_create_liteview_order_shipping_details_table extends Migration
{
    public function up()
    {
        $this->createTable('liteview_order_shipping_details', [
            'id' => Schema::TYPE_PK,
            'liteview_order_id' => Schema::TYPE_INTEGER,
            'ship_method' => Schema::TYPE_STRING,
            'signature_requested' => Schema::TYPE_STRING,
            'insurance_requested' => Schema::TYPE_STRING,
            'insurance_value' => Schema::TYPE_FLOAT,
            'saturday_delivery_requested' => Schema::TYPE_STRING,
            'third_party_billing_requested' => Schema::TYPE_STRING,
            'third_party_billing_account_no' => Schema::TYPE_STRING,
            'third_party_billing_zip' => Schema::TYPE_STRING,
            'third_party_country' => Schema::TYPE_STRING,
            'general_description' => Schema::TYPE_TEXT,
            'content_description' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('liteview_order_shipping_details');
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