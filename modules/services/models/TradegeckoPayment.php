<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "tradegecko_payment".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $invoice_id
 * @property double $amount
 * @property double $exchange_rate
 * @property string $reference
 * @property string $paid_at
 * @property string $payment_status
 *
 */
class TradegeckoPayment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_payment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'invoice_id'], 'integer'],
            [['created_at', 'updated_at', 'reference', 'paid_at', 'payment_status'], 'string'],
            [['amount', 'exchange_rate'], 'double']
        ];
    }
}
