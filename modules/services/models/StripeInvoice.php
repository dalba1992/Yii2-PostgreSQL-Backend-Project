<?php

namespace app\modules\services\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "stripe_invoice".
 *
 * @property integer $id
 * @property string $invoiceId
 * @property integer $created
 * @property integer $period_start
 * @property integer $period_end
 * @property double $subtotal
 * @property double $total
 * @property string $customerId
 * @property string $currency
 * @property string $livemode
 * @property string $accepted
 * @property string $closed
 * @property string $forgiven
 * @property string $paid
 * @property integer $attempt_count
 * @property double $amount_due
 * @property double $starting_balance
 * @property double $ending_balance
 * @property integer $next_payment_attempt
 * @property integer $webhooks_delivered_at
 * @property string $chargeId
 * @property string $subscriptionId
 * @property integer $application_fee
 * @property integer $tax
 * @property double $tax_percent
 * @property string $statement_descriptor
 * @property string $statement_description
 * @property string $description
 * @property string $receipt_number
 * @property string $regDate
 * @property string $updateDate
 *
 */
class StripeInvoice extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stripe_invoice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subtotal', 'total', 'amount_due', 'starting_balance', 'ending_balance', 'tax_percent'], 'double'],
            [['created', 'period_start', 'period_end', 'attempt_count', 'next_payment_attempt', 'webhooks_delivered_at', 'application_fee', 'tax'], 'integer'],
            [['invoiceId', 'customerId', 'currency', 'livemode', 'accepted', 'closed', 'forgiven', 'paid', 'chargeId', 'subscriptionId', 'statement_descriptor', 'statement_description', 'receipt_number'], 'string', 'max' => 255],
            [['regDate', 'updateDate'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 255]
        ];
    }
}
