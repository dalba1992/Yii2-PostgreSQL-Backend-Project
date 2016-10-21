<?php

use yii\db\Schema;
use yii\db\Migration;

class m150704_074821_create_productorderlog_table extends Migration
{
    public function up()
    {
        $this->createTable('productorderlog', [
            'id' => Schema::TYPE_PK,
            'customerId' => Schema::TYPE_INTEGER,
            'orderId' => Schema::TYPE_INTEGER,
            'productId' => Schema::TYPE_INTEGER,
            'createdAt' => Schema::TYPE_DATETIME,
            'updatedAt' => Schema::TYPE_DATETIME
        ]);
    }

    public function down()
    {
        $this->dropTable('productorderlog');
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
