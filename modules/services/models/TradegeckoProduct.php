<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "tradegecko_product".
 *
 * @property integer $id
 * @property string $product_name
 * @property string $description
 * @property string $product_type
 * @property string $supplier
 * @property string $brand
 * @property string $product_status
 * @property string $tags
 * @property string $opt1
 * @property string $opt2
 * @property string $opt3
 * @property string $image_url
 * @property string $search_cache
 * @property string $variant_ids
 * @property string $created_at
 * @property string $updated_at
 * @property double $quantity
 * @property string $supplier_ids
 * @property string $vendor
 * @property integer $variant_num
 * @property string $shop
 *
 */
class TradegeckoProduct extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'variant_num'], 'integer'],
            [['quantity'], 'double'],
            [['product_name', 'shop', 'description', 'product_type', 'supplier', 'brand', 'product_status', 'tags', 'opt1', 'opt2', 'opt3', 'image_url', 'search_cache', 'variant_ids', 'vendor', 'supplier_ids'], 'string'],
            [['created_at', 'updated_at'], 'string']
        ];
    }
}
