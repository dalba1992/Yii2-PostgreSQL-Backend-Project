<?php
use yii\db\Schema;
use yii\db\Migration;
class m151111_144800_create_customer_cart_table extends Migration
{
    public function up()
    {
        $this->createTable('customer_cart', [
            'id' => Schema::TYPE_PK,
            'customerId' => Schema::TYPE_INTEGER,
            'data' => Schema::TYPE_TEXT,
            'cartStatus' => Schema::TYPE_STRING,
            'createdAt' => Schema::TYPE_TIMESTAMP,
            'updatedAt' => Schema::TYPE_TIMESTAMP,
        ]);
    }
    public function down()
    {
        $this->dropTable('customer_cart');
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