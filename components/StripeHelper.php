<?php

namespace app\components;

use Yii;
use yii\base\Component;

use app\models\CustomerEntity;
use app\models\CustomerSubscription;
use app\models\CustomerSubscriptionState;
use app\models\CustomerSubscriptionLog;

use Stripe\Stripe;
use Stripe\Util\Util as Stripe_Util;
use Stripe\Util\Set as Stripe_Util_Set;
use Stripe\Error\Api as Stripe_ApiError;
use Stripe\Error\ApiConnection as Stripe_ApiConnectionError;
use Stripe\Error\Authentication as Stripe_AuthenticationError;
use Stripe\Error\Card as Stripe_CardError;
use Stripe\Error\InvalidRequest as Stripe_InvalidRequestError;
use Stripe\Error\RateLimit as Stripe_RateLimitError;

// Plumbing
use Stripe\StripeObject as Stripe_Object;
use Stripe\ApiRequestor as Stripe_ApiRequestor;
use Stripe\ApiResource as Stripe_ApiResource;
use Stripe\SingletonApiResource as Stripe_SingletonApiResource;
use Stripe\AttachedObject as Stripe_AttachedObject;
//use Stripe\Stripe_List;

// Stripe API Resources
use Stripe\Account as Stripe_Account;
use Stripe\Card as Stripe_Card;
use Stripe\Balance as Stripe_Balance;
use Stripe\BalanceTransaction as Stripe_BalanceTransaction;
use Stripe\Charge as Stripe_Charge;
use Stripe\Customer as Stripe_Customer;
use Stripe\Invoice as Stripe_Invoice;
use Stripe\InvoiceItem as Stripe_InvoiceItem;
use Stripe\Plan as Stripe_Plan;
use Stripe\Subscription as Stripe_Subscription;
use Stripe\Token as Stripe_Token;
use Stripe\Coupon as Stripe_Coupon;
use Stripe\Event as Stripe_Event;
use Stripe\Transfer as Stripe_Transfer;
use Stripe\Recipient as Stripe_Recipient;
use Stripe\Refund as Stripe_Refund;
use Stripe\ApplicationFee as Stripe_ApplicationFee;
use Stripe\ApplicationFeeRefund as Stripe_ApplicationFeeRefund;
use yii\helpers\VarDumper;

class StripeHelper extends Component
{
    public $stripeSecretKey = null;

    private $stripeCustomer = null;
    private $stripeSubscription = null;

    public function __construct($config = [])
    {
        $this->stripeSecretKey = Yii::$app->params['stripe']['stripesecretkey'];
        return parent::__construct($config);
    }

    public function getCustomer($customer)
    {
        if(!$this->stripeCustomer){
            $subscription = $customer->customerSubscription;
            Stripe::setApiKey($this->stripeSecretKey);

            $this->stripeCustomer = Stripe_Customer::retrieve($subscription->stripe_customer_id);
        }
        return $this->stripeCustomer;
    }

    public function getSubscription($customer)
    {
        return $this->stripeSubscription = $this->getCustomer($customer)->subscriptions->retrieve($customer->customerSubscription->stripe_subscription_id);
    }

    public function getLastInvoice($customer)
    {
        return Stripe_InvoiceItem::all(['limit'=>100, 'customer'=>$this->getCustomer($customer)->id]);
    }

    public function getInvoice($id)
    {
        Stripe::setApiKey($this->stripeSecretKey);
        return Stripe_InvoiceItem::retrieve($id);
    }

    /**
     * @param CustomerEntity $customer
     * @param array $attributes is data we want to update on a customer entity on Stripe
     * @return mixed
     */
    public function updateCustomer($customer, $attributes = [])
    {
        $subscription = $customer->customerSubscription;

        $stripeCustomer = $this->getCustomer($customer);
        foreach($attributes as $key=>$value){
            $stripeCustomer->{$key} = $value;
        }

        return $stripeCustomer->save();
    }

    /**
     * @param CustomerEntity $customer
     * @return mixed
     */
    public function cancelSubscription($customer)
    {
        return $this->removeSubscription($customer);
    }

    /**
     * @param CustomerEntity $customer
     * @param integer $plan is the stripe plan id
     * @return mixed
     */
    public function pauseSubscription($customer, $activeState)
    {
        /**
         * remove the old subscription from stripe
         */
        $result = $this->removeSubscription($customer);
        /**
         * set a resume action for some time in the future
         */
        if($activeState->pause_duration && is_numeric($activeState->pause_duration)){
            $months = (int) $activeState->pause_duration;

            // new state request - resume
            $futureState = $customer->customerSubscription->customerSubscriptionState;
            $futureState->status = $activeState->update_status;


            $futureState->update_time = strtotime(date('Y-m-d H:i:s', strtotime('+'.$months.' month', ($activeState->update_time != null ? (int)$activeState->update_time : time()))));
            $futureState->update_status = CustomerSubscriptionState::STATUS_RESUME;
            $futureState->update_reason = null;
            $futureState->update_note = null;
            $futureState->pause_duration = null;

            $futureState->save();
            // log the resume request
            CustomerSubscriptionLog::logStateChange($futureState, null, CustomerSubscriptionLog::TYPE_REQUEST);
        }

        return $result;
    }

    /**
     * @param CustomerEntity $customer
     * @param integer $plan is the stripe plan id
     * @return mixed
     */
    protected function removeSubscription($customer, $plan = null)
    {
        /**
         * @var CustomerSubscription $subscription
         */
        $subscription = $customer->customerSubscription;
        if(!$subscription->stripe_subscription_id){
            $subscription->removeStripeData();
            return [];
        }
        else
            $stripeSubscription = $this->getSubscription($customer);

        $result = $stripeSubscription->cancel();
        if($result){
            //if the subscription got canceled we need to remove customers stripe_subscription_id
            $subscription = $customer->customerSubscription;
            $subscription->stripe_subscription_id = null;
            $subscription->stripe_plan_id = ($plan ? $plan : $stripeSubscription->plan->id); // make sure we always have the current/old plan in the db. we need it in order to resume
            $subscription->save();
        }

        return $result;
    }

    /**
     * @param CustomerEntity $customer
     * @param integer $startTime is a timestamp for when to resume
     * @param integer $plan is the stripe plan id
     * @return mixed
     */
    public function resumeSubscription($customer, $startTime, $plan = null)
    {
        $subscription = $customer->customerSubscription;
        if(!$plan)
            $plan = $subscription->stripe_plan_id;

        if(!$plan) return false;

        $stripeCustomer = $this->getCustomer($customer);
        $stripeSubscription = $stripeCustomer->subscriptions->create(["plan" => $plan]);
        $stripeSubscription->trial_end = $startTime;

        $result = $stripeSubscription->save();
        if($result){
            $subscription->stripe_subscription_id = $stripeSubscription->id;
            $subscription->stripe_plan_id = ($plan ? $plan : $stripeSubscription->plan->id); // make sure we always have the current/old plan in the db. we need it in order to resume
            $subscription->save();
        }

        return $result;
    }
}