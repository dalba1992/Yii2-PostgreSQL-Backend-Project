<?php

use yii\db\Schema;
use yii\db\Migration;

class m151216_161600_create_liteview_order_shipping_contact_table extends Migration
{
    public function up()
    {
        $this->createTable('liteview_order_shipping_contact', [
            'id' => Schema::TYPE_PK,
            'liteview_order_id' => Schema::TYPE_INTEGER,
            'shipto_prefix' => Schema::TYPE_STRING,
            'shipto_first_name' => Schema::TYPE_STRING,
            'shipto_last_name' => Schema::TYPE_STRING,
            'shipto_suffix' => Schema::TYPE_STRING,
            'shipto_company_name' => Schema::TYPE_STRING,
            'shipto_address1' => Schema::TYPE_STRING,
            'shipto_address2' => Schema::TYPE_STRING,
            'shipto_address3' => Schema::TYPE_STRING,
            'shipto_city' => Schema::TYPE_STRING,
            'shipto_state' => Schema::TYPE_STRING,
            'shipto_postal_code' => Schema::TYPE_STRING,
            'shipto_country' => Schema::TYPE_STRING,
            'shipto_telephone_no' => Schema::TYPE_STRING,
            'shipto_email' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('liteview_order_shipping_contact');
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