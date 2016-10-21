<?php

use yii\db\Schema;
use yii\db\Migration;

class m151102_153500_create_tradegecko_tax_component_table extends Migration
{
    public function up()
    {
        $this->createTable('tradegecko_tax_component', [
            'id' => Schema::TYPE_PK,
            'tax_type_id' => Schema::TYPE_INTEGER,
            'position' => Schema::TYPE_INTEGER,
            'label' => Schema::TYPE_STRING,
            'rate' => Schema::TYPE_STRING,
            'compound' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('tradegecko_tax_component');
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