<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "productvariant".
 *
 * @property integer $id
 * @property integer $tradegeckoId
 * @property integer $productId
 * @property integer $tradegeckoProductId
 * @property string $name
 * @property string $sku
 * @property string $description
 * @property string $supplierCode
 * @property string $upc
 * @property string $buyPrice
 * @property string $wholesalePrice
 * @property string $retailPrice
 * @property string $lastCostPrice
 * @property string $movingAverageCost
 * @property string $availableStock
 * @property integer $stockOnHand
 * @property integer $committedStock
 * @property integer $incomingStock
 * @property string $opt1
 * @property string $opt2
 * @property string $opt3
 * @property string $status
 * @property integer $taxable
 * @property integer $sellable
 * @property string $tradegeckoCreatedAt
 * @property string $tradegeckoUpdatedAt
 * @property string $createdAt
 * @property string $updatedAt
 */
class ProductVariants extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'productvariant';
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'updatedAt',
                'value' => new Expression('NOW()')
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['productId', 'tradegeckoId', 'tradegeckoProductId', 'name'], 'required'],
            [['tradegeckoId', 'tradegeckoProductId', 'taxable', 'sellable'], 'integer'],
            [['description'], 'string'],
            [['buyPrice', 'wholesalePrice', 'retailPrice', 'lastCostPrice', 'movingAverageCost', 'availableStock', 'stockOnHand', 'committedStock', 'incomingStock'], 'number'],
            [['tradegeckoCreatedAt', 'tradegeckoUpdatedAt', 'createdAt', 'updatedAt'], 'safe'],
            [['name', 'sku', 'supplierCode', 'upc', 'opt1', 'opt2', 'opt3'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 30]
        ];
    }

    /**
     * Get product details
     * @return \yii\db\ActiveQuery
     */
    public function getProduct() {
        return $this->hasOne(Products::className(), ['id' => 'productId']);
    }

    public function getImages() {
        return $this->hasMany(ProductImage::className(), ['tradegeckoVariantId' => 'tradegeckoId']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tradegeckoId' => 'Tradegecko ID',
            'productId' => 'Product ID',
            'tradegeckoProductId' => 'Tradegecko Product ID',
            'name' => 'Name',
            'sku' => 'Sku',
            'description' => 'Description',
            'supplierCode' => 'Supplier Code',
            'upc' => 'Barcode',
            'buyPrice' => 'Buy Price',
            'wholesalePrice' => 'Wholesale Price',
            'retailPrice' => 'Retail Price',
            'lastCostPrice' => 'Last Cost Price',
            'movingAverageCost' => 'Moving Average Cost',
            'availableStock' => 'Available Stock',
            'stockOnHand' => 'Stock On Hand',
            'committedStock' => 'Committed Stock',
            'incomingStock' => 'Incoming Stock',
            'opt1' => 'Opt1',
            'opt2' => 'Opt2',
            'opt3' => 'Opt3',
            'status' => 'Status',
            'taxable' => 'Taxable',
            'sellable' => 'Sellable',
            'tradegeckoCreatedAt' => 'Tradegecko Created At',
            'tradegeckoUpdatedAt' => 'Tradegecko Updated At',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
        ];
    }
}