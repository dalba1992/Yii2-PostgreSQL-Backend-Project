<?php

namespace app\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

use app\models\CustomerSubscription;
use app\models\CustomerSubscriptionState;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "customer_subscription_status_log".
 *
 * NOTE
 * - we are checking the status log data and look for active requests so that we don't have to go through all the customers and their
 * future months statuses and check via Stripe if we need to do any kind of update.
 *
 * @property integer    $id
 * @property integer    $subscription_id
 * @property integer    $user_id
 * @property string     $type
 *
 * @property string     $date
 * @property string     $status
 * @property string     $reason
 *
 * @property string     $created_at
 * @property string     $updated_at
 *
 *
 * @property CustomerSubscription   $customerSubscription
 */
class CustomerSubscriptionLog extends \yii\db\ActiveRecord
{
    const INACTIVE = 0;
    const ACTIVE   = 1;

    const TYPE_REQUEST = 'request';
    const TYPE_CANCEL = 'cancel';
    const TYPE_SET = 'set';

    /**
     * @var array these are the status labels
     */
    static public $actionTypeLabels = [
        self::TYPE_REQUEST => 'requested',
        self::TYPE_CANCEL => 'canceled',
        self::TYPE_SET => 'set',
    ];

    /**
     * @var array these are the status labels
     */
    static public $actionSetLabels = [
        CustomerSubscriptionState::STATUS_PAUSE => 'paused',
        CustomerSubscriptionState::STATUS_CANCEL => 'canceled',
        CustomerSubscriptionState::STATUS_RESUME => 'resumed',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_subscription_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subscription_id', 'user_id'], 'integer'],
            [['type', 'date', 'status', 'reason', 'note'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'subscription_id' => 'Subscription ID',
            'type' => 'Type',
            'date' => 'Start Date',
            'reason' => 'Reason',
            'note' => 'Notes',
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => function(){ return date("Y-m-d H:i:s");},
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerSubscription()
    {
        return $this->hasOne(CustomerSubscription::className(), ['id' => 'subscription_id']);
    }

    /**
     * Log an update for a subscription state. Can be: pause, resume, cancel or a state update request.
     * @param CustomerSubscriptionState $state
     * @param $userId
     * @return mixed
     */
    public static function logStateChange(CustomerSubscriptionState $state, $userId, $type)
    {
        /**
         * Save a new log entry
         * @var CustomerSubscriptionStatusLog $log
         */
        $log = new CustomerSubscriptionLog();
        $log->subscription_id = $state->subscription_id;
        $log->user_id = $userId;
        $log->type = $type;

        $log->date = date('Y-m-d H:i:s', $state->update_time);
        $log->status = $state->update_status;
        $log->reason = $state->update_reason;
        $log->note = $state->update_note;
        $return = $log->save();

        return $return;
    }
}
