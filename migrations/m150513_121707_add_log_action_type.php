<?php

use yii\db\Schema;
use yii\db\Migration;

class m150513_121707_add_log_action_type extends Migration
{
    public function up()
    {
        $this->addColumn('customer_subscription_log', 'type', 'string(15) DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('customer_subscription_log', 'type');
    }
}
