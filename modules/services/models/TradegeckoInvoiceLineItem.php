<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "tradegecko_invoice_line_item".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $invoice_id
 * @property integer $order_line_item_id
 * @property integer $ledger_account_id
 * @property integer $position
 * @property string $quantity
 *
 */
class TradegeckoInvoiceLineItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_invoice_line_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'invoice_id', 'order_line_item_id', 'position', 'ledger_account_id'], 'integer'],
            [['quantity'], 'string'],
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
