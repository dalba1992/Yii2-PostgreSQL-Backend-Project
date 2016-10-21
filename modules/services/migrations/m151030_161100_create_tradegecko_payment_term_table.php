<?php

use yii\db\Schema;
use yii\db\Migration;

class m151030_161100_create_tradegecko_payment_term_table extends Migration
{
    public function up()
    {
        $this->createTable('tradegecko_payment_term', [
            'id' => Schema::TYPE_PK,
            'payment_term_name' => Schema::TYPE_STRING,
            'due_in_days' => Schema::TYPE_INTEGER,
            'payment_term_status' => Schema::TYPE_STRING,
            'payment_term_from' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable('tradegecko_payment_term');
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