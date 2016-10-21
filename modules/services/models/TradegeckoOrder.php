<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "tradegecko_order".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $document_url
 * @property integer $assignee_id
 * @property integer $billing_address_id
 * @property integer $company_id
 * @property integer $contact_id
 * @property integer $currency_id
 * @property integer $shipping_address_id
 * @property integer $stock_location_id
 * @property integer $user_id
 * @property string $default_price_list_id
 * @property string $email
 * @property string $fulfillment_status
 * @property string $invoice_status
 * @property string $issued_at
 * @property string $notes
 * @property string $order_number
 * @property string $packed_status
 * @property string $payment_status
 * @property string $phone_number
 * @property string $reference_number
 * @property string $return_status
 * @property string $returning_status
 * @property string $ship_at
 * @property string $source_url
 * @property string $order_status
 * @property string $tax_label
 * @property string $tax_override
 * @property string $tax_treatment
 * @property double $total
 * @property string $tags
 * @property string $fulfillment_ids
 * @property string $fulfillment_return_ids
 * @property string $invoice_ids
 * @property string $payment_ids
 * @property string $refund_ids
 * @property double $cached_total
 * @property string $default_price_type_id
 * @property string $source 
 * @property string $tax_type
 * @property string $tracking_number
 * @property string $url
 * @property string $source_id
 * @property string $invoice_numbers
 * @property string $order_line_item_ids
 *
 */
class TradegeckoOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'company_id', 'shipping_address_id', 'billing_address_id', 'user_id', 'assignee_id', 'stock_location_id', 'currency_id', 'contact_id'], 'integer'],
            [['document_url', 'fulfillment_ids', 'invoice_ids', 'order_number', 'phone_number', 'email', 'notes', 'reference_number', 'order_status', 'payment_status', 'invoice_status', 'packed_status', 'fulfillment_status', 'tax_type', 'default_price_list_id', 'issued_at', 'ship_at', 'tax_override', 'tax_label', 'tracking_number', 'source_id', 'source_url', 'order_line_item_ids', 'return_status', 'returning_status', 'tax_treatment', 'tags', 'fulfillment_return_ids', 'payment_ids', 'default_price_type_id', 'source', 'url', 'invoice_numbers', 'refund_ids'], 'string'],
            [['total', 'cached_total'], 'double'],
            [['created_at', 'updated_at'], 'string']
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTradegeckoCompany() {
        return $this->hasOne(TradegeckoCompany::className(), ['id' => 'company_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTradegeckoCurrency() {
        return $this->hasOne(TradegeckoCurrency::className(), ['id' => 'currency_id']);
    }
}
