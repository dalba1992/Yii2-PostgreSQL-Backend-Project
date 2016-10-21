<?php

use yii\db\Schema;
use yii\db\Migration;

class m150506_142637_add_subscription_plan extends Migration
{
    public function up()
    {
        $this->addColumn('customer_subscription', 'stripe_plan_id', 'string(15) DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('customer_subscription', 'stripe_plan_id');
    }
}
