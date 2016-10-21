<?php

use yii\db\Schema;
use yii\db\Migration;

class m150615_080137_create_products_table extends Migration
{
    public function up()
    {
        $this->createTable('product', [
            'id' => 'pk',
            'tradegeckoId' => 'bigint(11) NOT NULL DEFAULT 0',
            'name' => 'string(200) NOT NULL',
            'description' => 'text',
            'brand' => 'string(200) NULL',
            'productType' => 'string NOT NULL',
            'supplier' => 'string(200) NOT NULL',
            'quantity' => 'decimal(10,1) NOT NULL',
            'status' => 'string(30) NOT NULL',
            'opt1' => 'string',
            'opt2' => 'string',
            'opt3' => 'string',
            'imageUrl' => 'string(255)',
            'tradegeckoCreatedAt' => 'datetime',
            'tradegeckoUpdatedAt' => 'datetime',
            'createdAt' => 'datetime',
            'updatedAt' => 'datetime'
        ]);
        $this->createIndex('tradegeckoId', 'product', 'tradegeckoId');

        $this->createTable('producttag', [
            'id' => 'pk',
            'frequency' => 'integer',
            'name' => 'string'
        ]);
        $this->createTable('producttagassign', [
            'productId' => 'integer',
            'tagId' => 'integer'
        ]);

        $this->createTable('productvariant', [
            'id' => 'pk',
            'tradegeckoId' => 'bigint NOT NULL',
            'tradegeckoProductId' => 'bigint NOT NULL',
            'name' => 'string NOT NULL',
            'sku' => 'string',
            'description' => 'text',
            'supplierCode' => 'string',
            'upc' => 'string',
            'buyPrice' => 'decimal(10,2)',
            'wholesalePrice' => 'decimal(10,2)',
            'retailPrice' => 'decimal(10,2)',
            'lastCostPrice' => 'decimal(10,2)',
            'movingAverageCost' => 'decimal(10,2)',
            'availableStock' => 'decimal(10,1)',
            'stockOnHand' => 'integer',
            'committedStock' => 'integer',
            'incomingStock' => 'integer',
            'opt1' => 'string',
            'opt2' => 'string',
            'opt3' => 'string',
            'status' => 'string(30)',
            'taxable' => 'integer(1) NOT NULL DEFAULT 0',
            'sellable' => 'integer(1) NOT NULL DEFAULT 0',
            'tradegeckoCreatedAt' => 'datetime',
            'tradegeckoUpdatedAt' => 'datetime',
            'createdAt' => 'datetime',
            'updatedAt' => 'datetime'
        ]);
        $this->createIndex('tradegeckoVariantId', 'productvariant', 'tradegeckoId');
        $this->createIndex('tradegeckoProductId', 'productvariant', 'tradegeckoProductId');
    }

    public function down()
    {
        $this->dropIndex('tradegeckoId', 'product');
        $this->dropTable('product');

        $this->dropTable('producttag');
        $this->dropTable('producttagassign');

        $this->dropIndex('tradegeckoVariantId', 'productvariant');
        $this->dropIndex('tradegeckoProductId', 'productvariant');
        $this->dropTable('productvariant');
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
