<?php

use yii\db\Schema;
use yii\db\Migration;

class m150727_151607_fix extends Migration
{
    public function up()
    {
        $this->dropColumns();
        $this->addColumn('productvariant', 'buyPrice', "decimal(10,2) DEFAULT NULL");
        $this->addColumn('productvariant', 'wholesalePrice', "decimal(10,2) DEFAULT NULL");
        $this->addColumn('productvariant', 'retailPrice', "decimal(10,2) DEFAULT NULL");
        $this->addColumn('productvariant', 'lastCostPrice', "decimal(10,2) DEFAULT NULL");
        $this->addColumn('productvariant', 'movingAverageCost', "decimal(10,2) DEFAULT NULL");
        $this->addColumn('productvariant', 'availableStock', "decimal(10,1) DEFAULT NULL");
    }

    public function down()
    {
        $this->dropColumns();
        $this->addColumn('productvariant', 'buyPrice', "decimal(10,2)");
        $this->addColumn('productvariant', 'wholesalePrice', "decimal(10,2)");
        $this->addColumn('productvariant', 'retailPrice', "decimal(10,2)");
        $this->addColumn('productvariant', 'lastCostPrice', "decimal(10,2)");
        $this->addColumn('productvariant', 'movingAverageCost', "decimal(10,2)");
        $this->addColumn('productvariant', 'availableStock', "decimal(10,1)");
    }

    protected function dropColumns(){
        $table = Yii::$app->db->getSchema()->getTableSchema('productvariant');
        $columnNames = $table->getColumnNames();
        if(in_array('buyPrice', $columnNames))
            $this->dropColumn('productvariant', 'buyPrice');

        if(in_array('wholesalePrice', $columnNames))
            $this->dropColumn('productvariant', 'wholesalePrice');

        if(in_array('retailPrice', $columnNames))
            $this->dropColumn('productvariant', 'retailPrice');

        if(in_array('lastCostPrice', $columnNames))
            $this->dropColumn('productvariant', 'lastCostPrice');

        if(in_array('movingAverageCost', $columnNames))
            $this->dropColumn('productvariant', 'movingAverageCost');

        if(in_array('availableStock', $columnNames))
            $this->dropColumn('productvariant', 'availableStock');
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
