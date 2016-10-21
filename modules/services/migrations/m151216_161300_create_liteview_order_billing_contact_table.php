<?php

use yii\db\Schema;
use yii\db\Migration;

class m151216_161300_create_liteview_order_billing_contact_table extends Migration
{
    public function up()
    {
        $this->createTable('liteview_order_billing_contact', [
            'id' => Schema::TYPE_PK,
            'liteview_order_id' => Schema::TYPE_INTEGER,
            'billto_prefix' => Schema::TYPE_STRING,
            'billto_first_name' => Schema::TYPE_STRING,
            'billto_last_name' => Schema::TYPE_STRING,
            'billto_suffix' => Schema::TYPE_STRING,
            'billto_company_name' => Schema::TYPE_STRING,
            'billto_address1' => Schema::TYPE_STRING,
            'billto_address2' => Schema::TYPE_STRING,
            'billto_address3' => Schema::TYPE_STRING,
            'billto_city' => Schema::TYPE_STRING,
            'billto_state' => Schema::TYPE_STRING,
            'billto_postal_code' => Schema::TYPE_STRING,
            'billto_country' => Schema::TYPE_STRING,
            'billto_telephone_no' => Schema::TYPE_STRING,
            'billto_email' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('liteview_order_billing_contact');
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