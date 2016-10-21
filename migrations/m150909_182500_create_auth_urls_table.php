<?php

use yii\db\Schema;
use yii\db\Migration;

class m150909_182500_create_auth_urls_table extends Migration
{
    public function up()
    {
        $this->createTable('auth_urls', [
            'id' => Schema::TYPE_PK,
            'controller' => Schema::TYPE_STRING,
            'action' => Schema::TYPE_STRING,
            'description' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('auth_urls');
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