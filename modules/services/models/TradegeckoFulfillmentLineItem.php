<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "tradegecko_fulfillment_line_item".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $fulfillment_id
 * @property integer $order_line_item_id
 * @property string $base_price
 * @property integer $position
 * @property string $quantity
 *
 */
class TradegeckoFulfillmentLineItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_fulfillment_line_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'fulfillment_id', 'order_line_item_id', 'position'], 'integer'],
            [['base_price', 'quantity'], 'string'],
            [['created_at', 'updated_at'], 'string']
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTradegeckoOrderLineItem() {
        return $this->hasOne(TradegeckoOrderLineItem::className(), ['id' => 'order_line_item_id']);
    }
}
