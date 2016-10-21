<?php

use yii\db\Schema;
use yii\db\Migration;

class m151115_190059_add_product_image_table extends Migration
{
    public function up()
    {
        $this->createTable('productimage', [
            'id' => Schema::TYPE_PK,
            'tradegeckoId' => Schema::TYPE_INTEGER,
            'tradegeckoVariantId' => Schema::TYPE_INTEGER,
            'uploaderId' => Schema::TYPE_STRING,
            'name' => Schema::TYPE_STRING,
            'position' => Schema::TYPE_STRING,
            'basePath' => Schema::TYPE_TEXT,
            'fileName' => Schema::TYPE_STRING,
            'versions' => Schema::TYPE_TEXT,
            'imageProcessing' => Schema::TYPE_INTEGER.'(1) DEFAULT 0',
            'createdAt' => Schema::TYPE_INTEGER,
            'updatedAt' => Schema::TYPE_INTEGER
        ]);
    }

    public function down()
    {
       $this->dropTable('productimage');
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
