<?php

use yii\db\Schema;
use yii\db\Migration;

class m151216_164100_create_liteview_order_shipping_details_table extends Migration
{
    public function up()
    {
        $this->createTable('liteview_order_notes', [
            'id' => Schema::TYPE_PK,
            'liteview_order_id' => Schema::TYPE_INTEGER,
            'note_type' => Schema::TYPE_STRING,
            'note_description' => Schema::TYPE_TEXT,
            'show_on_ps' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('liteview_order_notes');
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