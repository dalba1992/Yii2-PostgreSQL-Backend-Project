<?php

use yii\db\Schema;
use yii\db\Migration;

class m150926_081020_style_attributes_relation extends Migration
{
    public function up()
    {
        $this->addForeignKey ( 'attribute_id_fkey', 'tb_attribute_detail', 'tb_attribute_id', 'tb_attribute', 'id','CASCADE', 'NO ACTION' );
        $this->addForeignKey ( 'attribute_detail_fkey', 'tb_attribute_detail_image', 'attribute_detail_id', 'tb_attribute_detail', 'id', 'CASCADE', 'NO ACTION' );
    }

    public function down()
    {
       /* $this->dropForeignKey('attribute_id_fkey', 'tb_attribute_detail');
        $this->dropForeignKey('attribute_detail_fkey', 'tb_attribute_detail_image');*/
        echo "m150926_081020_style_attributes_relation cannot be reverted.\n";

        return false;
    }
}
