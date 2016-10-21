<?php

use yii\db\Schema;
use yii\db\Migration;

class m150518_121710_add_notification extends Migration
{
    public function up()
    {
        $this->createTable(
            'notification',
            [
                'id'          => 'bigpk',
                'customer_id' => 'integer(15)',
                'type'        => 'string(50)',
                'notify_next' => 'timestamp',
                'created_at'  => 'timestamp',
                'updated_at'  => 'timestamp',
            ]
        );
    }

    public function down()
    {
        $this->dropTable('notification');
    }
}
