<?php

use yii\db\Schema;
use yii\db\Migration;

class m151018_123601_create_webhook_logs_table extends Migration
{
    public function up()
    {
        $this->createTable('webhooklog', [
            'id' => Schema::TYPE_PK,
            'stripeCustomerId' => Schema::TYPE_STRING . ' NOT NULL',
            'data' => Schema::TYPE_TEXT,
            'createdAt' => Schema::TYPE_INTEGER,
            'updatedAt' => Schema::TYPE_INTEGER
        ]);
    }

    public function down()
    {
        $this->dropTable('webhooklog');
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
