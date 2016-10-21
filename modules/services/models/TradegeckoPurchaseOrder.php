<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "tradegecko_purchase_order".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $billing_address_id
 * @property integer $company_id
 * @property string $due_at
 * @property string $email
 * @property string $notes
 * @property string $order_number
 * @property string $procurement_ids
 * @property string $procurement_status
 * @property string $purchase_order_line_item_ids
 * @property string $reference_number
 * @property string $purchase_order_status
 * @property integer $stock_location_id
 * @property string $tax_type
 * @property integer $vendor_address_id
 * @property string $xero
 *
 */
class TradegeckoPurchaseOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_purchase_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'billing_address_id', 'company_id', 'stock_location_id', 'vendor_address_id'], 'integer'],
            [['created_at', 'updated_at', 'due_at', 'email', 'notes', 'order_number', 'procurement_ids', 'procurement_status', 'purchase_order_line_item_ids', 'reference_number', 'purchase_order_status', 'tax_type', 'xero'], 'string'],
        ];
    }
}
