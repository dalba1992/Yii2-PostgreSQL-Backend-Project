<?php

namespace app\modules\services\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "stripe_subscription".
 *
 * @property integer $id
 * @property string $customerId
 * @property string $subscriptionId
 * @property integer $start
 * @property string $status
 * @property string $cancel_at_period_end
 * @property integer $current_period_start
 * @property integer $current_period_end
 * @property integer $ended_at
 * @property integer $trial_start
 * @property integer $trial_end
 * @property integer $canceled_at
 * @property integer $quantity
 * @property double $tax_percent
 * @property double $application_fee_percent
 * @property string $planId
 * @property string $regDate
 * @property string $updateDate
 *
 */
class StripeSubscription extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stripe_subscription';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['application_fee_percent', 'tax_percent'], 'double'],
            [['quantity', 'start', 'current_period_start', 'current_period_end', 'ended_at', 'trial_start', 'trial_end', 'canceled_at'], 'integer'],
            [['subscriptionId', 'customerId', 'status', 'cancel_at_period_end', 'planId'], 'string', 'max' => 255],
            [['regDate', 'updateDate'], 'string', 'max' => 255]
        ];
    }
}
