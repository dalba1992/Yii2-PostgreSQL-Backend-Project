<?php

use yii\db\Schema;
use yii\db\Migration;

class m151028_181200_create_tradegecko_company_table extends Migration
{
    public function up()
    {
        $this->createTable('tradegecko_company', [
            'id' => Schema::TYPE_PK,
            'created_at' => Schema::TYPE_STRING,
            'updated_at' => Schema::TYPE_STRING,
            'assignee_id' => Schema::TYPE_INTEGER,
            'default_ledger_account_id' => Schema::TYPE_INTEGER,
            'default_payment_term_id' => Schema::TYPE_INTEGER,
            'default_stock_location_id' => Schema::TYPE_INTEGER,
            'default_tax_type_id' => Schema::TYPE_INTEGER,
            'company_code' => Schema::TYPE_STRING,
            'company_type' => Schema::TYPE_STRING,
            'default_discount_rate' => Schema::TYPE_FLOAT,
            'default_price_list_id' => Schema::TYPE_STRING,
            'default_tax_rate' => Schema::TYPE_FLOAT,
            'default_document_theme_id' => Schema::TYPE_STRING,
            'description' => Schema::TYPE_TEXT,
            'email' => Schema::TYPE_STRING,            
            'fax' => Schema::TYPE_STRING,
            'company_name' => Schema::TYPE_STRING,
            'phone_number' => Schema::TYPE_STRING,
            'company_status' => Schema::TYPE_STRING,
            'tax_number' => Schema::TYPE_STRING,
            'website' => Schema::TYPE_STRING,
            'address_ids' => Schema::TYPE_TEXT,
            'contact_ids' => Schema::TYPE_TEXT,
            'note_ids' => Schema::TYPE_TEXT,
            'default_price_type_id' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('tradegecko_company');
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