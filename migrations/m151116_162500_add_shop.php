<?php

use yii\db\Schema;
use yii\db\Migration;

class m151116_162500_add_shop extends Migration
{
    public function up()
    {
        $this->addColumn('product', 'shop', 'string(1) DEFAULT 0');
    }

    public function down()
    {
        $this->dropColumn('product', 'shop');
    }
}
