<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "tradegecko_variant".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $product_id
 * @property integer $default_ledger_account_id
 * @property string $buy_price
 * @property string $committed_stock
 * @property string $incoming_stock
 * @property string $composite
 * @property string $description
 * @property string $is_online
 * @property string $keep_selling
 * @property string $last_cost_price
 * @property string $manage_stock
 * @property integer $max_online
 * @property string $moving_average_cost
 * @property string $variant_name
 * @property string $online_ordering
 * @property string $opt1
 * @property string $opt2
 * @property string $opt3
 * @property integer $position
 * @property string $product_name
 * @property string $product_status
 * @property string $product_type
 * @property string $retail_price
 * @property string $sellable
 * @property string $sku
 * @property string $variant_status
 * @property string $stock_on_hand
 * @property string $supplier_code
 * @property string $taxable
 * @property string $upc
 * @property string $weight
 * @property string $wholesale_price
 * @property string $image_ids
 *
 */
class TradegeckoVariant extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_variant';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'product_id', 'default_ledger_account_id', 'max_online', 'position'], 'integer'],
            [['created_at', 'updated_at', 'buy_price', 'committed_stock', 'incoming_stock', 'stock_on_hand', 'composite', 'description', 'is_online', 'keep_selling', 'last_cost_price', 'manage_stock', 'moving_average_cost', 'variant_name', 'online_ordering', 'opt1', 'opt2', 'opt3', 'product_name', 'product_status', 'product_type', 'retail_price', 'sellable', 'sku', 'variant_status', 'stock_on_hand', 'supplier_code', 'taxable', 'upc', 'weight', 'wholesale_price', 'image_ids'], 'string'],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTradegeckoImage() {
        return $this->hasMany(TradegeckoImage::className(), ['variant_id' => 'id']);
    }
}
