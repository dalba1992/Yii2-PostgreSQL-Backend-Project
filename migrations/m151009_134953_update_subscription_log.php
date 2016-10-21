<?php

use yii\db\Schema;
use yii\db\Migration;

class m151009_134953_update_subscription_log extends Migration
{
    public function up()
    {
        $this->addColumn('customer_subscription_log', 'note', Schema::TYPE_STRING);
    }

    public function down()
    {
        $this->dropColumn('customer_subscription_log', 'note');
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
