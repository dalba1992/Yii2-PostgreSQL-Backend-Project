<?php

namespace app\modules\services\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "stripe_transfer".
 *
 * @property integer $id
 * @property string $transferId
 * @property integer $created
 * @property integer $scheduledDate
 * @property string $livemode
 * @property double $amount
 * @property string $currency
 * @property string $reversed
 * @property string $status
 * @property string $type
 * @property string $balance_transaction
 * @property string $bank_account_id
 * @property string $bank_account_last4
 * @property string $bank_account_country
 * @property string $bank_account_status
 * @property string $bank_account_fingerprint
 * @property string $bank_account_routing_number
 * @property string $bank_account_bank_name
 * @property string $destination
 * @property string $description
 * @property string $failure_message
 * @property string $failure_code
 * @property double $amount_reversed
 * @property string $statement_descriptor
 * @property string $recipient
 * @property string $source_transaction
 * @property string $application_fee
 * @property string $statement_description
 * @property string $regDate
 * @property string $updateDate
 *
 */
class StripeTransfer extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stripe_transfer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['amount', 'amount_reversed'], 'double'],
            [['created', 'scheduledDate'], 'integer'],
            [['transferId', 'reversed', 'currency', 'livemode', 'status', 'type', 'balance_transaction', 'bank_account_id', 'bank_account_last4', 'bank_account_country', 'bank_account_status', 'bank_account_fingerprint', 'bank_account_routing_number', 'bank_account_bank_name', 'destination', 'description', 'failure_message', 'failure_code', 'statement_descriptor', 'recipient', 'source_transaction', 'application_fee', 'statement_description'], 'string', 'max' => 255],
            [['regDate', 'updateDate'], 'string', 'max' => 255]
        ];
    }
}
