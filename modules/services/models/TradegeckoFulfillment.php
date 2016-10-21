<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "tradegecko_fulfillment".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $order_id
 * @property integer $shipping_address_id
 * @property integer $billing_address_id
 * @property integer $stock_location_id
 * @property string $delivery_type
 * @property string $exchange_rate
 * @property string $notes
 * @property string $packed_at
 * @property string $received_at
 * @property string $service
 * @property string $shipped_at
 * @property string $fulfillment_status
 * @property string $tracking_company
 * @property string $tracking_number
 * @property string $tracking_url
 * @property string $order_number
 * @property integer $company_id
 * @property string $fulfillment_line_item_ids
 *
 */
class TradegeckoFulfillment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_fulfillment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'shipping_address_id', 'billing_address_id', 'stock_location_id', 'company_id'], 'integer'],
            [['delivery_type', 'exchange_rate', 'notes', 'packed_at', 'received_at', 'service', 'shipped_at', 'fulfillment_status', 'tracking_company', 'tracking_number', 'tracking_url', 'order_number', 'fulfillment_line_item_ids'], 'string'],
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
    public function getTradegeckoLocation() {
        return $this->hasOne(TradegeckoLocation::className(), ['id' => 'stock_location_id']);
    }
}
