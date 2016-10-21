<?php

use yii\db\Schema;
use yii\db\Migration;

class m160112_052500_add_cancelled_at extends Migration
{
    public function up()
    {
        $this->addColumn('liteview_order', 'cancelled_at', 'string(255)');
    }

    public function down()
    {
        $this->dropColumn('liteview_order', 'cancelled_at');
    }
}
