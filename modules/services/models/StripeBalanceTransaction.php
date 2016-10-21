<?php

namespace app\modules\services\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "stripe_balancetransaction".
 *
 * @property integer $id
 * @property string $balanceId
 * @property double $amount
 * @property string $currency
 * @property double $net
 * @property string $type
 * @property integer $created
 * @property integer $available_on
 * @property string $status
 * @property double $fee
 * @property string $source
 * @property string $description
 * @property string $regDate
 * @property string $updateDate
 *
 */
class StripeBalanceTransaction extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stripe_balancetransaction';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['amount', 'net', 'fee'], 'double'],
            [['created', 'available_on'], 'integer'],
            [['balanceId', 'type', 'currency', 'status', 'source'], 'string', 'max' => 255],
            [['regDate', 'updateDate'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 255]
        ];
    }
}
