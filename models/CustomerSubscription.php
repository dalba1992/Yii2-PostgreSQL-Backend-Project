<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customer_subscription".
 *
 * @property integer        $id
 * @property integer        $customer_id
 * @property string         $gateway_id
 * @property string         $cc_number
 * @property string         $cc_type
 * @property string         $exp_date
 * @property string         $referred_by
 * @property string         $created_at
 * @property string         $stripe_customer_id
 * @property string         $stripe_subscription_id
 * @property string         $stripe_plan_id
 *
 * @property CustomerEntity                $customer
 * @property CustomerSubscriptionState     $customerSubscriptionState
 * @property CustomerSubscriptionState     $activeStateChange
 * @property CustomerSubscriptionLog[]     $customerSubscriptionLog
 */
class CustomerSubscription extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_subscription';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id'], 'integer'],
            [['stripe_customer_id', 'stripe_subscription_id', 'stripe_plan_id'], 'string'],
            [['gateway_id', 'cc_number', 'cc_type', 'exp_date', 'referred_by', 'created_at'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'gateway_id' => 'Gateway ID',
            'cc_number' => 'Cc Number',
            'cc_type' => 'Cc Type',
            'exp_date' => 'Exp Date',
            'referred_by' => 'Referred By',
            'created_at' => 'Created At',
            'stripe_customer_id' => 'Stripe Customer ID',
            'stripe_subscription_id' => 'Stripe Subscription ID',
            'stripe_plan_id' => 'Stripe Plan ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(CustomerEntity::className(), ['id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerSubscriptionState()
    {
        return $this->hasOne(CustomerSubscriptionState::className(), ['subscription_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerSubscriptionLog()
    {
        return $this->hasMany(CustomerSubscriptionLog::className(), ['subscription_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActiveStateChange()
    {
        if($this->customerSubscriptionState && $this->customerSubscriptionState->update_time && $this->customerSubscriptionState->update_status)
            return $this->customerSubscriptionState;
        else
            return null;
    }

    /**
     * This function can clear subscription data from stripe
     * @param bool $removeCustomerId is a flag that prevents the removal of customer id so that we may resume it's stripe subscription in the future
     * @return bool
     */
    public function removeStripeData($removeCustomerId = false)
    {
        if($removeCustomerId)
            $this->stripe_customer_id = null;

        $this->stripe_subscription_id = null;
        $this->stripe_plan_id = null;
        return $this->save();
    }
}
