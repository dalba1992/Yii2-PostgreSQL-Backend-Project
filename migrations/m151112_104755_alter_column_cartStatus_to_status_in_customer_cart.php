<?php

use yii\db\Schema;
use yii\db\Migration;

class m151112_104755_alter_column_cartStatus_to_status_in_customer_cart extends Migration
{
    public function up()
    {
        $this->renameColumn('customer_cart', 'cartStatus', 'status');
    }

    public function down()
    {
        $this->renameColumn('customer_cart', 'status', 'cartStatus');
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
