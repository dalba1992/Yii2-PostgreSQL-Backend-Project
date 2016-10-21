<?php

namespace app\modules\services\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "stripe_charge".
 *
 * @property integer $id
 * @property string $chargeId
 * @property integer $created
 * @property string $livemode
 * @property string $paid
 * @property string $status
 * @property double $amount
 * @property string $currency
 * @property string $refunded
 * @property string $sourceId
 * @property string $cardId
 * @property string $balance_transaction
 * @property string $failure_message
 * @property string $failure_code
 * @property double $amount_refunded
 * @property string $customerId
 * @property string $invoice
 * @property string $description
 * @property string $statement_descriptor
 * @property string $receipt_email
 * @property string $receipt_number
 * @property string $destination
 * @property string $application_fee
 * @property string $statement_description
 * @property string $regDate
 * @property string $updateDate
 *
 */
class StripeCharge extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stripe_charge';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['amount_refunded', 'amount'], 'double'],
            [['created'], 'integer'],
            [['chargeId', 'paid', 'status', 'livemode', 'currency', 'refunded', 'sourceId', 'cardId', 'balance_transaction', 'failure_message', 'failure_code', 'customerId', 'invoice', 'statement_descriptor', 'receipt_email', 'receipt_number', 'destination', 'application_fee', 'statement_description'], 'string', 'max' => 255],
            [['regDate', 'updateDate'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 255]
        ];
    }
}
