<?php

use yii\db\Schema;
use yii\db\Migration;

class m150624_123641_create_order_queue_table extends Migration
{
    public function up()
    {
        $this->createTable('order', [
            'id' => 'pk',
            'userId' => 'integer',
            'customerId' => 'integer',
            'tradegeckoOrderId' => 'integer',
            'liteviewOrderNo' => 'string',
            'items' => 'text',
            'status' => 'string',
            'date' => 'date',
            'csvFile' => 'string',
            'createdAt' => 'datetime',
            'updatedAt' => 'datetime',
        ]);
        $this->createTable('orderlog', [
            'id' => 'pk',
            'orderId' => 'integer',
            'status' => 'string',
            'createdAt' => 'datetime',
            'updatedAt' => 'datetime',
        ]);
    }

    public function down()
    {
        $this->dropTable('order');
        $this->dropTable('orderlog');
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
