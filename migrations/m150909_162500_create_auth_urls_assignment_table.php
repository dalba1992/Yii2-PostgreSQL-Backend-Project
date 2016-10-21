<?php

use yii\db\Schema;
use yii\db\Migration;

class m150909_162500_create_auth_urls_assignment_table extends Migration
{
    public function up()
    {
        $this->createTable('auth_urls_assignment', [
            'id' => Schema::TYPE_PK,
            'role' => Schema::TYPE_STRING,
            'auth_urls_id' => Schema::TYPE_INTEGER,
        ]);
    }

    public function down()
    {
        $this->dropTable('auth_urls_assignment');
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