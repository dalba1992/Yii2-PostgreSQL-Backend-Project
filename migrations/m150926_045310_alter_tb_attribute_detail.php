<?php

use yii\db\Schema;
use yii\db\Migration;

class m150926_045310_alter_tb_attribute_detail extends Migration
{
    public function safeUp()
    {
        $this->addColumn( 'tb_attribute_detail', 'is_active', 'boolean DEFAULT TRUE' );
    }

    public function safeDown()
    {
        $this->dropColumn('tb_attribute_detail','is_active');
    }
}