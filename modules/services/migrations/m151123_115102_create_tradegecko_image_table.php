<?php

use yii\db\Schema;
use yii\db\Migration;

class m151123_115102_create_tradegecko_image_table extends Migration
{
    public function up()
    {
        $this->createTable('tradegecko_image', [
            'id' => Schema::TYPE_PK,
            'variant_id' => Schema::TYPE_INTEGER,
            'uploader_id' => Schema::TYPE_INTEGER,
            'image_name' => Schema::TYPE_STRING,
            'position' => Schema::TYPE_INTEGER,
            'base_path' => Schema::TYPE_STRING,
            'file_name' => Schema::TYPE_STRING,
            'versions' => Schema::TYPE_TEXT,
            'image_processing' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('tradegecko_image');
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