<?php

namespace app\modules\services\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "stripe_refund".
 *
 * @property integer $id
 * @property string $refundId
 * @property string $chargeId
 * @property integer $created
 * @property double $amount
 * @property string $currency
 * @property string $balance_transaction
 * @property string $receipt_number
 * @property string $reason
 * @property string $regDate
 * @property string $updateDate
 *
 */
class StripeRefund extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stripe_refund';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['amount'], 'double'],
            [['created'], 'integer'],
            [['refundId', 'chargeId', 'currency', 'balance_transaction', 'receipt_number', 'reason'], 'string', 'max' => 255],
            [['regDate', 'updateDate'], 'string', 'max' => 255]
        ];
    }
}
