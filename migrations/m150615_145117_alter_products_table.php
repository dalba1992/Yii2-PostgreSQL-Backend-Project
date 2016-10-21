<?php

use yii\db\Schema;
use yii\db\Migration;

class m150615_145117_alter_products_table extends Migration
{
    public function up()
    {
        $this->addColumn('product', 'tradegeckoStatus', "string(10) NOT NULL DEFAULT 'normal'");
    }

    public function down()
    {
        $this->dropColumn('product', 'tradegeckoStatus');
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
