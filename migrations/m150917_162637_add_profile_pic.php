<?php

use yii\db\Schema;
use yii\db\Migration;

class m150917_162637_add_profile_pic extends Migration
{
    public function up()
    {
        $this->addColumn('user_entity', 'profilePic', 'string(255) DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('user_entity', 'profilePic');
    }
}
