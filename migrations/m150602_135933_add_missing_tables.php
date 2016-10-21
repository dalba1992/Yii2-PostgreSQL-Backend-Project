<?php


use yii\db\Schema;
use yii\db\Migration;

class m150602_135933_add_missing_tables extends Migration
{
    public function up()
    {
        // make sure we have the table customer_status_log
        if (Yii::$app->db->schema->getTableSchema('customer_status_log',true)===null) {
            $this->createTable(
                'customer_status_log',
                [
                    'id'          => 'bigpk',
                    'customer_id' => 'integer(15)',
                    'status'      => 'string(50)',
                    'created_at'  => 'timestamp',
                    'updated_at'  => 'timestamp',
                ]
            );
        }

        // make sure we have the table tb_product_variants
        if (Yii::$app->db->schema->getTableSchema('tb_product_variants',true)===null) {
            $this->createTable(
                'tb_product_variants',
                [
                    'id'                     => 'bigpk',
                    'product_entity_info_id' => 'integer(15)',
                    'variant_id'             => 'integer(15)',
                    'sku'                    => 'string(50)',
                    'wholesale_price'        => 'double',
                    'retail_price'           => 'double',
                    'buy_price'              => 'double',
                    'qty'                    => 'integer(10)',
                    'created_at'             => 'timestamp',
                    'updated_at'             => 'timestamp',
                ]
            );
        }

        // make sure we have the table tb_product_variants_image
        if (Yii::$app->db->schema->getTableSchema('tb_product_variants_image',true)===null) {
            $this->createTable(
                'tb_product_variants_image',
                [
                    'id'         => 'bigpk',
                    'variant_id' => 'integer(15)',
                    'img_url'    => 'text',
                    'created_at' => 'timestamp',
                    'updated_at' => 'timestamp',
                ]
            );
        }

        // make sure we have the table tb_product_entity_info
        if (Yii::$app->db->schema->getTableSchema('tb_product_entity_info',true)===null) {
            $this->createTable(
                'tb_product_entity_info',
                [
                    'id'                    => 'bigpk',
                    'tradegecko_product_id' => 'integer(15)',
                    'name'                  => 'string(50)',
                    'category_id'           => 'integer(15)',
                    'brand_id'              => 'integer(15)',
                    'created_at'            => 'timestamp',
                    'updated_at'            => 'timestamp',
                ]
            );
        }

    }

    public function down()
    {
        echo "m150602_135933_add_missing_tables cannot be reverted.\nThere is nothing to undo here, this migration just makes sure everything is there.\n";
        return true;
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
