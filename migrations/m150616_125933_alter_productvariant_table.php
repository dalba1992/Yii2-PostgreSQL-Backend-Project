<?php

use yii\db\Schema;
use yii\db\Migration;

class m150616_125933_alter_productvariant_table extends Migration
{
    public function up()
    {
        $this->addColumn('productvariant', 'productId', 'integer NOT NULL');
    }

    public function down()
    {
        $this->dropColumn('productvariant', 'productId');
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
