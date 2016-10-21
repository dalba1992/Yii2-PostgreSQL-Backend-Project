<?php

use yii\db\Schema;
use yii\db\Migration;

class m150702_062949_create_tradegecko_customer_address extends Migration
{
    public function up()
    {
        $this->createTable('tradegeckocustomeraddress', [
            'id' => Schema::TYPE_PK,
            'customerId' => Schema::TYPE_INTEGER,
            'tradegeckoCustomerId' => Schema::TYPE_INTEGER,
            'tradegeckoAddressId' => Schema::TYPE_INTEGER,
            'createdAt' => Schema::TYPE_DATETIME,
            'updatedAt' => Schema::TYPE_DATETIME
        ]);
    }

    public function down()
    {
        $this->dropTable('tradegeckocustomeraddress');
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
