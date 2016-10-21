<?php

use yii\db\Schema;
use yii\db\Migration;

class m151029_202700_create_tradegecko_location_table extends Migration
{
    public function up()
    {
        $this->createTable('tradegecko_location', [
            'id' => Schema::TYPE_PK,
            'created_at' => Schema::TYPE_STRING,
            'updated_at' => Schema::TYPE_STRING,
            'address1' => Schema::TYPE_STRING,
            'address2' => Schema::TYPE_STRING,
            'city' => Schema::TYPE_STRING,
            'country' => Schema::TYPE_STRING,
            'holds_stock' => Schema::TYPE_INTEGER,
            'label' => Schema::TYPE_STRING,
            'state' => Schema::TYPE_STRING,
            'location_status' => Schema::TYPE_STRING,
            'suburb' => Schema::TYPE_STRING,
            'zip_code' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('tradegecko_location');
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