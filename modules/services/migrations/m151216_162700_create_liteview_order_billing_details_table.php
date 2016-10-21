<?php

use yii\db\Schema;
use yii\db\Migration;

class m151216_162700_create_liteview_order_billing_details_table extends Migration
{
    public function up()
    {
        $this->createTable('liteview_order_billing_details', [
            'id' => Schema::TYPE_PK,
            'liteview_order_id' => Schema::TYPE_INTEGER,
            'sub_total' => Schema::TYPE_FLOAT,
            'shipping_handling' => Schema::TYPE_FLOAT,
            'sales_tax_total' => Schema::TYPE_FLOAT,
            'discount_total' => Schema::TYPE_FLOAT,
        ]);
    }

    public function down()
    {
        $this->dropTable('liteview_order_billing_details');
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