<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "tradegecko_purchase_order_line_item".
 *
 * @property integer $id
 * @property integer $purchase_order_id
 * @property integer $variant_id
 * @property integer $procurement_id
 * @property string $quantity
 * @property integer $position
 * @property integer $tax_type_id
 * @property string $tax_rate_override
 * @property string $price
 * @property string $label
 * @property string $freeform
 * @property string $base_price
 *
 */
class TradegeckoPurchaseOrderLineItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_purchase_order_line_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'purchase_order_id', 'variant_id', 'procurement_id', 'position', 'tax_type_id'], 'integer'],
            [['quantity', 'tax_rate_override', 'price', 'label', 'freeform', 'base_price'], 'string'],
        ];
    }
}
