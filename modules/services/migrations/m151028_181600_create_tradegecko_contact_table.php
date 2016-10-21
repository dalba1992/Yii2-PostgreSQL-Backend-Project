<?php

use yii\db\Schema;
use yii\db\Migration;

class m151028_181600_create_tradegecko_contact_table extends Migration
{
    public function up()
    {
        $this->createTable('tradegecko_contact', [
            'id' => Schema::TYPE_PK,
            'created_at' => Schema::TYPE_STRING,
            'updated_at' => Schema::TYPE_STRING,
            'company_id' => Schema::TYPE_INTEGER,
            'email' => Schema::TYPE_STRING,            
            'fax' => Schema::TYPE_STRING,
            'first_name' => Schema::TYPE_STRING,
            'last_name' => Schema::TYPE_STRING,
            'location' => Schema::TYPE_STRING,
            'mobile' => Schema::TYPE_STRING,
            'notes' => Schema::TYPE_STRING,
            'phone_number' => Schema::TYPE_STRING,
            'position' => Schema::TYPE_STRING,
            'contact_status' => Schema::TYPE_STRING,
            'iguana_admin' => Schema::TYPE_STRING,
            'online_ordering' => Schema::TYPE_STRING,
            'invitation_accepted_at' => Schema::TYPE_STRING,
            'phone' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('tradegecko_contact');
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