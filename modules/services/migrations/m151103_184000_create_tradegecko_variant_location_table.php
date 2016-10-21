<?php

use yii\db\Schema;
use yii\db\Migration;

class m151103_184000_create_tradegecko_variant_location_table extends Migration
{
    public function up()
    {
        $this->createTable('tradegecko_variant_location', [
            'id' => Schema::TYPE_PK,
            'variant_id' => Schema::TYPE_INTEGER,
            'location_id' => Schema::TYPE_INTEGER,
            'stock_on_hand' => Schema::TYPE_STRING,
            'committed' => Schema::TYPE_INTEGER,
            'bin_location' => Schema::TYPE_STRING,
            'reorder_point' => Schema::TYPE_INTEGER,
        ]);
    }

    public function down()
    {
        $this->dropTable('tradegecko_variant_location');
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