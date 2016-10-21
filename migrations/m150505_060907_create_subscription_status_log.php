<?php

use yii\db\Schema;
use yii\db\Migration;

class m150505_060907_create_subscription_status_log extends Migration
{
    public function up()
    {
        $this->createTable(
            'customer_subscription_status_log',
            [
                'id'=>'bigpk',
                'subscription_id'=>'integer(15)',
                'user_id'=>'integer(15)',
                'is_active'=>'integer(1) default 0',
                'start_date'=>'timestamp',
                'status'=>'string(50)',
                'reason'=>'string(150)',
                'created_at'=>'timestamp',
                'updated_at'=>'timestamp',
            ]
        );
    }

    public function down()
    {
        $this->dropTable('customer_subscription_status_log');
    }
}
