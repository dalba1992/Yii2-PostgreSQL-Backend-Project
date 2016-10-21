<?php

use yii\db\Schema;
use yii\db\Migration;

class m151112_112500_add_shop extends Migration
{
    public function up()
    {
        $this->addColumn('tradegecko_product', 'shop', 'string(1) DEFAULT 0');
    }

    public function down()
    {
        $this->dropColumn('tradegecko_product', 'shop');
    }
}
