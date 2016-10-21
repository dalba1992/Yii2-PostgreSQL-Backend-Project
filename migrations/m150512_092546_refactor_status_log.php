<?php

use yii\db\Schema;
use yii\db\Migration;

class m150512_092546_refactor_status_log extends Migration
{
    public function up()
    {
        /**
         * Drop old tables
         */
        $this->dropTable('customer_subscription_status');
        $this->dropTable('customer_subscription_status_log');

        /**
         * Create new tables
         */
        $this->createTable(
            'customer_subscription_state',
            [
                'id'=>'bigpk',
                'subscription_id'=>'integer(15)',
                'status'=>'string(25)',

                'update_time'=>'integer(15) DEFAULT NULL',
                'update_status'=>'string(25)',
                'update_reason'=>'string(150)',
                'update_note'=>'text',
                'pause_duration'=>'integer(2) DEFAULT NULL', // this is the pause duration until auto-resume, is expressed in months. NULL means do not resume.

                'created_at'=>'timestamp',
                'updated_at'=>'timestamp',
            ]
        );

        $this->createTable(
            'customer_subscription_log',
            [
                'id'=>'bigpk',
                'subscription_id'=>'integer(15)',
                'user_id'=>'integer(15)',

                'date'=>'timestamp',
                'status'=>'string(50)',
                'reason'=>'string(150)',

                'created_at'=>'timestamp',
                'updated_at'=>'timestamp',
            ]
        );
    }

    public function down()
    {
        /**
         * Drop new tables
         */
        $this->dropTable('customer_subscription_state');
        $this->dropTable('customer_subscription_log');

        /**
         * Re-create old tables
         */
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
}
