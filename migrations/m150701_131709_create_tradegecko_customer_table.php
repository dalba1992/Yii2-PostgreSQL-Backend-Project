<?php

use yii\db\Schema;
use yii\db\Migration;

class m150701_131709_create_tradegecko_customer_table extends Migration
{
    public function up()
    {
        $this->createTable('tradegeckocustomer', [
            'id' => Schema::TYPE_PK,
            'customerId' => Schema::TYPE_INTEGER,
            'tradegeckoId' => Schema::TYPE_INTEGER,
            'createdAt' => Schema::TYPE_DATETIME,
            'updatedAt' => Schema::TYPE_DATETIME
        ]);
    }

    public function down()
    {
        $this->dropTable('tradegeckocustomer');
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
