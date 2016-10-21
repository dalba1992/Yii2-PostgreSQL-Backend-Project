<?php

use yii\db\Schema;
use yii\db\Migration;

class m151028_181400_create_tradegecko_address_table extends Migration
{
    public function up()
    {
        $this->createTable('tradegecko_address', [
            'id' => Schema::TYPE_PK,
            'created_at' => Schema::TYPE_STRING,
            'updated_at' => Schema::TYPE_STRING,
            'company_id' => Schema::TYPE_INTEGER,
            'address1' => Schema::TYPE_STRING,
            'address2' => Schema::TYPE_STRING,
            'city' => Schema::TYPE_STRING,
            'country' => Schema::TYPE_STRING,
            'company_name' => Schema::TYPE_STRING,
            'email' => Schema::TYPE_STRING,
            'first_name' => Schema::TYPE_STRING,
            'last_name' => Schema::TYPE_STRING,
            'label' => Schema::TYPE_STRING,
            'phone_number' => Schema::TYPE_STRING,
            'state' => Schema::TYPE_STRING,
            'address_status' => Schema::TYPE_STRING,
            'suburb' => Schema::TYPE_STRING,
            'zip_code' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('tradegecko_address');
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