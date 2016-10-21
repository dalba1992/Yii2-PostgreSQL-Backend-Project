<?php

use yii\db\Schema;
use yii\db\Migration;

class m150430_161324_add_subscription_status extends Migration
{
    public function up()
    {
        $this->createTable(
            'customer_subscription_status',
            [
                'id'=>'bigpk',
                'subscription_id'=>'integer(15)',
                'start_date'=>'timestamp',
                'status'=>'string(50)',
                'reason'=>'string(150)',
                'notes'=>'text'
            ]
        );
    }

    public function down()
    {
        $this->dropTable('customer_subscription_status');
    }
}
