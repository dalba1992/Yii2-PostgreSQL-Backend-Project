<?php

use yii\db\Schema;
use yii\db\Migration;

class m151102_163700_create_tradegecko_user_table extends Migration
{
    public function up()
    {
        $this->createTable('tradegecko_user', [
            'id' => Schema::TYPE_PK,
            'created_at' => Schema::TYPE_STRING,
            'updated_at' => Schema::TYPE_STRING,
            'action_items_email' => Schema::TYPE_STRING,
            'avatar_url' => Schema::TYPE_STRING,
            'billing_contact' => Schema::TYPE_INTEGER,
            'email' => Schema::TYPE_STRING,
            'first_name' => Schema::TYPE_STRING,
            'last_name' => Schema::TYPE_STRING,
            'last_sign_in_at' => Schema::TYPE_STRING,
            'location' => Schema::TYPE_STRING,
            'login_id' => Schema::TYPE_INTEGER,
            'mobile' => Schema::TYPE_STRING,
            'notification_email' => Schema::TYPE_INTEGER,
            'permissions' => Schema::TYPE_TEXT,
            'phone_number' => Schema::TYPE_STRING,
            'position' => Schema::TYPE_INTEGER,
            'sales_report_email' => Schema::TYPE_INTEGER,
            'user_status' => Schema::TYPE_STRING,
            'account_id' => Schema::TYPE_INTEGER,
        ]);
    }

    public function down()
    {
        $this->dropTable('tradegecko_user');
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