<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "tradegecko_order_line_item".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $order_id
 * @property integer $variant_id
 * @property integer $tax_type_id
 * @property double $discount
 * @property string $freeform
 * @property string $image_url
 * @property string $label
 * @property string $line_type
 * @property integer $position
 * @property double $price
 * @property string $quantity
 * @property double $tax_rate_override
 * @property double $tax_rate
 * @property string $fulfillment_line_item_ids
 * @property string $fulfillment_return_line_item_ids
 * @property string $invoice_line_item_ids
 *
 */
class TradegeckoOrderLineItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_order_line_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'variant_id', 'tax_type_id', 'position'], 'integer'],
            [['quantity', 'freeform', 'image_url', 'label', 'line_type', 'fulfillment_line_item_ids', 'fulfillment_return_line_item_ids', 'invoice_line_item_ids'], 'string'],
            [['created_at', 'updated_at'], 'string'],
            [['tax_rate_override', 'tax_rate', 'discount', 'price'], 'double']
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTradegeckoVariant() {
        return $this->hasOne(TradegeckoVariant::className(), ['id' => 'variant_id']);
    }
}
