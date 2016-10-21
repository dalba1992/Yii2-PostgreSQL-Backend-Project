<?php
use yii\db\Schema;
use yii\db\Migration;
class m150903_172200_create_user_entity_table extends Migration
{
    public function up()
    {
        $this->createTable('user_entity', [
            'id' => Schema::TYPE_PK,
            'username' => Schema::TYPE_STRING,
            'email' => Schema::TYPE_STRING,
            'firstName' => Schema::TYPE_STRING,
            'lastName' => Schema::TYPE_STRING,
            'password' => Schema::TYPE_STRING,
            'authKey' => Schema::TYPE_STRING,
            'accessToken' => Schema::TYPE_STRING,
            'regDate' => Schema::TYPE_INTEGER,
            'updateDate' => Schema::TYPE_INTEGER,
        ]);
    }
    public function down()
    {
        $this->dropTable('user_entity');
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