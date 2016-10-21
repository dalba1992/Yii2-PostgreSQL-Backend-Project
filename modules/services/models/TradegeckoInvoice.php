<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "tradegecko_invoice".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $document_url
 * @property integer $order_id
 * @property integer $shipping_address_id
 * @property integer $billing_address_id
 * @property integer $payment_term_id
 * @property string $due_at
 * @property double $exchange_rate
 * @property string $invoice_number
 * @property string $invoiced_at
 * @property string $notes
 * @property double $cached_total
 * @property string $payment_status
 * @property string $order_number
 * @property integer $company_id
 * @property integer $currency_id
 * @property string $invoice_line_item_ids
 * @property string $payment_ids
 * @property string $refund_ids
 *
 */
class TradegeckoInvoice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_invoice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'shipping_address_id', 'billing_address_id', 'payment_term_id', 'company_id', 'currency_id'], 'integer'],
            [['exchange_rate', 'cached_total'], 'double'],
            [['document_url', 'due_at', 'invoice_number', 'invoiced_at', 'notes', 'payment_status', 'order_number', 'invoice_line_item_ids', 'payment_ids', 'refund_ids'], 'string'],
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
    public function getTradegeckoOrder() {
        return $this->hasOne(TradegeckoOrder::className(), ['id' => 'order_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTradegeckoCurrency() {
        return $this->hasOne(TradegeckoCurrency::className(), ['id' => 'currency_id']);
    }
}
