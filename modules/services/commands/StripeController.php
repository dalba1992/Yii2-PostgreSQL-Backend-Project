<?php
namespace app\modules\services\commands;

use Yii;
use yii\base\Exception;
use yii\base\ExitException;
use yii\base\InvalidParamException;
use yii\console\Controller;
use yii\base\ErrorException;

use app\components;
use yii\base\Component;
use yii\log\Logger;

use Stripe\Stripe as Stripe;
use Stripe\StripeObject as Stripe_Object;
use Stripe\Plan as Stripe_Plan;
use Stripe\Customer as Stripe_Customer;
use Stripe\Charge as Stripe_Charge;
use Stripe\Coupon as Stripe_Coupon;
use Stripe\Invoice as Stripe_Invoice;
use Stripe\Refund as Stripe_Refund;
use Stripe\BalanceTransaction as Stripe_BalanceTransaction;
use Stripe\Transfer as Stripe_Transfer;

use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

use app\modules\services\models\StripeCustomer;
use app\modules\services\models\StripeSubscription;
use app\modules\services\models\StripeCard;
use app\modules\services\models\StripeSource;
use app\modules\services\models\StripeCharge;
use app\modules\services\models\StripeCoupon;
use app\modules\services\models\StripePlan;
use app\modules\services\models\StripeRefund;
use app\modules\services\models\StripeInvoice;
use app\modules\services\models\StripeTransfer;
use app\modules\services\models\StripeBalanceTransaction;

class StripeController extends Controller
{
    /**
     * This command echoes the list of functions.
     *
     * @return 0
     */
    public function actionIndex()
    {
        echo "\n\n";
        echo "-------------------List of commands--------------------\n\n";
        echo "1. index\n\tLists all available commands\n";
        echo "3. clear\n\tClears a stripe-db\n";
        echo "4. sync-customer\n\tPulling customer data from Stripe including subscription, card, source\n";
        echo "5. sync-charge\n\tPulling charge data from Stripe\n";
        echo "6. sync-coupon\n\tPulling coupon data from Stripe\n";
        echo "7. sync-plan\n\tPulling plan data from Stripe\n";
        echo "8. sync-invoice\n\tPulling invoice data from Stripe\n";
        echo "9. sync-refund\n\tPulling refund data from Stripe\n";
        echo "10. sync-transfer\n\tPulling transfer data from Stripe\n";
        echo "11. sync-balance-transaction\n\tPulling balance transaction data from Stripe\n";
        echo "12. sync\n\tRunning sync commands one by one\n";

        return 0;
    }

    /**
     * This command clears stripe-related tables in a database
     *
     * @return 0
     */
    public function actionClear()
    {
        Yii::$app->db->createCommand('truncate stripe_customer')->query();
        Yii::$app->db->createCommand('truncate stripe_card')->query();
        Yii::$app->db->createCommand('truncate stripe_source')->query();
        Yii::$app->db->createCommand('truncate stripe_subscription')->query();
        Yii::$app->db->createCommand('truncate stripe_charge')->query();
        Yii::$app->db->createCommand('truncate stripe_coupon')->query();
        Yii::$app->db->createCommand('truncate stripe_plan')->query();
        Yii::$app->db->createCommand('truncate stripe_refund')->query();
        Yii::$app->db->createCommand('truncate stripe_transfer')->query();
        Yii::$app->db->createCommand('truncate stripe_balancetransaction')->query();

        Yii::$app->db->createCommand('ALTER SEQUENCE stripe_customer_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE stripe_card_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE stripe_source_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE stripe_subscription_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE stripe_plan_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE stripe_charge_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE stripe_coupon_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE stripe_refund_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE stripe_transfer_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE stripe_balancetransaction_id_seq RESTART WITH 1')->query();

        echo PHP_EOL."CLEARED Stripe-realted tables successfully.". PHP_EOL;

        return 0;
    }

    /**
     * This command updates customer data from Stripe, including subscriptions, cards and sources
     *
     * @return 0
     */
    public function actionSyncCustomer()
    {

        self::logMessage('Syncing Customers...');

        $realArr = [];
        $realArr['customer'] = 0;
        $realArr['subscription'] = 0;
        $realArr['source'] = 0;
        $realArr['card'] = 0;

        $storedArr = [];
        $storedArr['customer'] = 0;
        $storedArr['subscription'] = 0;
        $storedArr['source'] = 0;
        $storedArr['card'] = 0;

        $startTime = microtime(true);

        Stripe::setApiKey(Yii::$app->params['stripe']['stripesecretkey']);

        //---------------------------UPDATING CUSTOMERS---------------------------------
        $limitDate = 0;
        $latestCustomer = StripeCustomer::find()->orderBy(['created'=>SORT_DESC])->one();
        if ($latestCustomer != null)
            $limitDate = $latestCustomer['created'];

        $start = 0;
        while (true)
        {
            $stopFlag = '0';
            try{
                $customers = Stripe_Customer::all(array("count" => 100, 'offset' => $start, 'include[]' => 'total_count'));
            }
            catch(\Exception $ex)
            {
                echo 'Syncing Customers Failed. API Request Error occured';
                self::logMessage('Syncing Customers Failed. API Request Error occured');
                return;
            }

            foreach($customers->data as $customer)
            {
                if (intval($limitDate) < intval($customer->created))
                {
                    //CUSTOMER UPDATE-------------------------------------------------
                    $item = [];
                    $item['customerId'] = $customer->id;
                    $item['created'] = $customer->created;
                    $item['email'] = $customer->email;
                    $item['account_balance'] = $customer->account_balance;
                    $item['currency'] = $customer->currency;
                    $item['livemode'] = $customer->livemode ? 'true' : 'false';
                    $item['default_card'] = $customer->default_card;
                    $item['default_source'] = $customer->default_source;
                    $item['description'] = $customer->description;
                    $item['regDate'] = date('Y-m-d H:i:s');
                    $item['updateDate'] = date('Y-m-d H:i:s');

                    $model = StripeCustomer::find()->where(['customerId' => $customer->id])->one();
                    if ($model == null)
                        $model = new StripeCustomer();

                    $model->attributes = $item;
                    if ($model->save()) {
                        $storedArr['customer'] ++;
                        //echo "Customer sync: ".$customer->id."\n";
                    }
                    $realArr['customer'] ++;

                    //SUBSCRIPTION UPDATE-------------------------------------------------
                    foreach($customer->subscriptions->data as $subscription)
                    {
                        $itemSub = [];
                        $itemSub['customerId'] = $customer->id;
                        $itemSub['subscriptionId'] = $subscription->id;
                        $itemSub['start'] = $subscription->start;
                        $itemSub['status'] = $subscription->status;
                        $itemSub['cancel_at_period_end'] = $subscription->cancel_at_period_end ? 'true' : 'false';
                        $itemSub['current_period_start'] = $subscription->current_period_start;
                        $itemSub['current_period_end'] = $subscription->current_period_end;
                        $itemSub['ended_at'] = $subscription->ended_at;
                        $itemSub['trial_start'] = $subscription->trial_start;
                        $itemSub['trial_end'] = $subscription->trial_end;
                        $itemSub['canceled_at'] = $subscription->canceled_at;
                        $itemSub['quantity'] = $subscription->quantity;
                        $itemSub['application_fee_percent'] = $subscription->application_fee_percent;
                        $itemSub['tax_percent'] = $subscription->tax_percent;
                        $itemSub['regDate'] = date('Y-m-d H:i:s');
                        $itemSub['updateDate'] = date('Y-m-d H:i:s');
                        $itemSub['planId'] = $subscription->plan->id;

                        $subscriptionModel = StripeSubscription::find()->where(['customerId' => $customer->id, 'subscriptionId' => $subscription->id])->one();
                        if ($subscriptionModel == null)
                            $subscriptionModel = new StripeSubscription();

                        $subscriptionModel->attributes = $itemSub;
                        if ($subscriptionModel->save()) {
                            $storedArr['subscription'] ++;
                            //echo "Subscription sync: ".$subscription->id."\n";
                        }

                        $realArr['subscription'] ++;
                    }

                    //CARD UPDATE
                    foreach($customer->cards->data as $card)
                    {
                        $itemCard = [];
                        $itemCard['customerId'] = $customer->id;
                        $itemCard['cardId'] = $card->id;
                        $itemCard['last4'] = $card->last4;
                        $itemCard['brand'] = $card->brand;
                        $itemCard['funding'] = $card->funding;
                        $itemCard['exp_month'] = $card->exp_month;
                        $itemCard['exp_year'] = $card->exp_year;
                        $itemCard['fingerprint'] = $card->fingerprint;
                        $itemCard['country'] = $card->country;
                        $itemCard['name'] = $card->name;
                        $itemCard['address_line1'] = $card->address_line1;
                        $itemCard['address_line2'] = $card->address_line2;
                        $itemCard['address_city'] = $card->address_city;
                        $itemCard['address_state'] = $card->address_state;
                        $itemCard['address_zip'] = $card->address_zip;
                        $itemCard['address_country'] = $card->address_country;
                        $itemCard['cvc_check'] = $card->cvc_check;
                        $itemCard['address_line1_check'] = $card->address_line1_check;
                        $itemCard['address_zip_check'] = $card->address_zip_check;
                        $itemCard['tokenization_method'] = $card->tokenization_method;
                        $itemCard['dynamic_last4'] = $card->dynamic_last4;
                        $itemCard['regDate'] = date('Y-m-d H:i:s');
                        $itemCard['updateDate'] = date('Y-m-d H:i:s');

                        $cardModel = StripeCard::find()->where(['customerId' => $customer->id, 'cardId' => $card->id])->one();
                        if ($cardModel == null)
                            $cardModel = new StripeCard();

                        $cardModel->attributes = $itemCard;
                        if ($cardModel->save()) {
                            $storedArr['card'] ++;
                            //echo "Card sync: ".$card->id."\n";
                        }
                        $realArr['card'] ++;
                    }

                    //SOURCE UPDATE
                    foreach($customer->sources->data as $source)
                    {
                        $itemSource = [];
                        $itemSource['customerId'] = $customer->id;
                        $itemSource['sourceId'] = $source->id;
                        $itemSource['last4'] = $source->last4;
                        $itemSource['brand'] = $source->brand;
                        $itemSource['funding'] = $source->funding;
                        $itemSource['exp_month'] = $source->exp_month;
                        $itemSource['exp_year'] = $source->exp_year;
                        $itemSource['fingerprint'] = $source->fingerprint;
                        $itemSource['country'] = $source->country;
                        $itemSource['name'] = $source->name;
                        $itemSource['address_line1'] = $source->address_line1;
                        $itemSource['address_line2'] = $source->address_line2;
                        $itemSource['address_city'] = $source->address_city;
                        $itemSource['address_state'] = $source->address_state;
                        $itemSource['address_zip'] = $source->address_zip;
                        $itemSource['address_country'] = $source->address_country;
                        $itemSource['cvc_check'] = $source->cvc_check;
                        $itemSource['address_line1_check'] = $source->address_line1_check;
                        $itemSource['address_zip_check'] = $source->address_zip_check;
                        $itemSource['tokenization_method'] = $source->tokenization_method;
                        $itemSource['dynamic_last4'] = $source->dynamic_last4;
                        $itemSource['regDate'] = date('Y-m-d H:i:s');
                        $itemSource['updateDate'] = date('Y-m-d H:i:s');

                        $sourceModel = StripeSource::find()->where(['customerId' => $customer->id, 'sourceId' => $source->id])->one();
                        if ($sourceModel == null)
                            $sourceModel = new StripeSource();

                        $sourceModel->attributes = $itemSource;
                        if ($sourceModel->save()) {
                            $storedArr['source'] ++;
                            //echo "Source sync: ".$source->id."\n";
                        }
                        $realArr['source'] ++;
                    }
                }
                else
                {
                    $stopFlag = '1';
                    break;
                }
            }

            if ($stopFlag == '1')
                break;
            else if ($customers->has_more != 'true')
                break;

            $start += 100;
        }

        self::logMessage($realArr['customer'].' Customer Updates Found');
        self::logMessage($storedArr['customer'].' Customer Updates were Applied to DB correctly');
        self::logMessage($realArr['subscription'].' Subscription Updates Found');
        self::logMessage($storedArr['subscription'].' Subscription Updates were Applied to DB correctly');
        self::logMessage($realArr['card'].' Card Updates Found');
        self::logMessage($storedArr['card'].' Card Updates were Applied to DB correctly');
        self::logMessage($realArr['source'].' Source Updates Found');
        self::logMessage($storedArr['source'].' Source Updates were Applied to DB correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Customer\t".$realArr['customer']."\t".$storedArr['customer'].PHP_EOL;
        echo "Subscription\t".$realArr['subscription']."\t".$storedArr['subscription'].PHP_EOL;
        echo "Card\t\t".$realArr['card']."\t".$storedArr['card'].PHP_EOL;
        echo "Source\t\t".$realArr['source']."\t".$storedArr['source'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Customers Done');
    }

    /**
     * This command updates charge data from Stripe
     *
     * @return 0
     */
    public function actionSyncCharge()
    {

        self::logMessage('Syncing Charges...');

        $realArr = [];
        $realArr['charge'] = 0;

        $storedArr = [];
        $storedArr['charge'] = 0;

        $startTime = microtime(true);

        Stripe::setApiKey(Yii::$app->params['stripe']['stripesecretkey']);

        //---------------------------UPDATING CHARGES and REFUNDS--------------------------------- !!!
        $limitDate = 0;
        $latestCharge = StripeCharge::find()->orderBy(['created'=>SORT_DESC])->one();
        if ($latestCharge != null)
            $limitDate = $latestCharge['created'];

        $start = 0;
        while (true)
        {
            $stopFlag = '0';
            try{
                $charges = Stripe_Charge::all(array("count" => 100, 'offset' => $start, 'include[]' => 'total_count'));
            }
            catch(\Exception $ex)
            {
                echo 'Syncing Charges Failed. API Request Error occured';
                self::logMessage('Syncing Charges Failed. API Request Error occured');
                return;
            }

            foreach($charges->data as $charge)
            {
                if (intval($limitDate) < intval($charge->created))
                {
                    $itemCharge = [];
                    $itemCharge['chargeId'] = $charge->id;
                    $itemCharge['created'] = $charge->created;
                    $itemCharge['livemode'] = $charge->livemode ? 'true' : 'false';
                    $itemCharge['paid'] = $charge->paid ? 'true' : 'false';
                    $itemCharge['status'] = $charge->status;
                    $itemCharge['amount'] = $charge->amount;
                    $itemCharge['currency'] = $charge->currency;                    
                    $itemCharge['refunded'] = $charge->refunded ? 'true' : 'false';
                    $itemCharge['sourceId'] = $charge->source->id;
                    $itemCharge['cardId'] = $charge->card->id;
                    $itemCharge['balance_transaction'] = $charge->balance_transaction;
                    $itemCharge['failure_message'] = $charge->failure_message;
                    $itemCharge['failure_code'] = $charge->failure_code;
                    $itemCharge['amount_refunded'] = $charge->amount_refunded;
                    $itemCharge['customerId'] = $charge->customer;
                    $itemCharge['invoice'] = $charge->invoice;
                    $itemCharge['description'] = $charge->description;
                    $itemCharge['statement_descriptor'] = $charge->statement_descriptor;
                    $itemCharge['receipt_email'] = $charge->receipt_email;
                    $itemCharge['receipt_number'] = $charge->receipt_number;
                    $itemCharge['destination'] = $charge->destination;
                    $itemCharge['application_fee'] = $charge->application_fee;
                    $itemCharge['statement_description'] = $charge->statement_description;
                    $itemCharge['regDate'] = date('Y-m-d H:i:s');
                    $itemCharge['updateDate'] = date('Y-m-d H:i:s');

                    $modelCharge = StripeCharge::find()->where(['chargeId' => $charge->id])->one();
                    if ($modelCharge == null)
                        $modelCharge = new StripeCharge();

                    $modelCharge->attributes = $itemCharge;
                    if ($modelCharge->save()) {
                        $storedArr['charge'] ++;
                    }
                    $realArr['charge'] ++;
                }
                else
                {
                    $stopFlag = '1';
                    break;
                }
            }

            if ($stopFlag == '1')
                break;
            else if ($charges->has_more != 'true')
                break;

            $start += 100;
        }

        self::logMessage($realArr['charge'].' Updates Found');
        self::logMessage($storedArr['charge'].' Updates were Applied to DB correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Charge\t\t".$realArr['charge']."\t".$storedArr['charge'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Charges Done');

        return 0;
    }

    /**
     * This command updates coupon data from Stripe
     *
     * @return 0
     */
    public function actionSyncCoupon()
    {
        self::logMessage('Syncing Coupons...');

        $realArr = [];
        $realArr['coupon'] = 0;

        $storedArr = [];
        $storedArr['coupon'] = 0;

        $startTime = microtime(true);

        Stripe::setApiKey(Yii::$app->params['stripe']['stripesecretkey']);

        //---------------------------UPDATING COUPONS--------------------------------- !!!
        $start = 0;
        $couponData = [];
        while (true)
        {
            try{
                $coupons = Stripe_Coupon::all(array("count" => 100, 'offset' => $start, 'include[]' => 'total_count'));
            }
            catch(\Exception $ex)
            {
                echo 'Syncing Coupons Failed. API Request Error occured';
                self::logMessage('Syncing Coupons Failed. API Request Error occured');
                return;
            }

            $couponData[] = $coupons;

            if ($coupons->has_more != 'true')
                break;

            $start += 100;
        }

        // sync the coupon data
        if(count($couponData)) {
            foreach($couponData as $coupons) {
                foreach($coupons->data as $coupon) {
                    $itemCoupon = [];
                    $itemCoupon['couponId'] = $coupon->id;
                    $itemCoupon['created'] = $coupon->created;
                    $itemCoupon['livemode'] = $coupon->livemode ? 'true' : 'false';
                    $itemCoupon['percent_off'] = $coupon->percent_off;
                    $itemCoupon['amount_off'] = $coupon->amount_off;
                    $itemCoupon['currency'] = $coupon->currency;
                    $itemCoupon['duration'] = $coupon->duration;
                    $itemCoupon['redeem_by'] = $coupon->redeem_by;
                    $itemCoupon['max_redemptions'] = $coupon->max_redemptions;
                    $itemCoupon['times_redeemed'] = $coupon->times_redeemed;
                    $itemCoupon['duration_in_months'] = $coupon->duration_in_months;
                    $itemCoupon['valid'] = $coupon->valid ? 'true' : 'false';
                    $itemCoupon['regDate'] = date('Y-m-d H:i:s');
                    $itemCoupon['updateDate'] = date('Y-m-d H:i:s');

                    $modelCoupon = StripeCoupon::find()->where(['couponId' => $coupon->id])->one();
                    if ($modelCoupon == null)
                        $modelCoupon = new StripeCoupon();

                    $modelCoupon->attributes = $itemCoupon;
                    if ($modelCoupon->save()) {
                        $storedArr['coupon'] ++;
                    }
                    $realArr['coupon'] ++;
                }
            }
        } else {
            //echo "No coupons were found"."\n";
        }

        self::logMessage($storedArr['coupon'].' Rows were updated to DB correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Coupon\t\t".$realArr['coupon']."\t".$storedArr['coupon'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Coupons Done');

        return 0;
    }

    /**
     * This command updates plan data from Stripe
     *
     * @return 0
     */
    public function actionSyncPlan() {

        self::logMessage('Syncing Plans...');

        $realArr = [];
        $realArr['plan'] = 0;

        $storedArr = [];
        $storedArr['plan'] = 0;

        $startTime = microtime(true);

        Stripe::setApiKey(Yii::$app->params['stripe']['stripesecretkey']);

        //---------------------------UPDATING PLANS--------------------------------- !!!
        $start = 0;
        $planData = [];
        while (true)
        {
            try{
                $plans = Stripe_Plan::all(array("count" => 100, 'offset' => $start, 'include[]' => 'total_count'));
            }
            catch (\Exception $ex)
            {
                echo 'Syncing Plans Failed. API Request Error occured';
                self::logMessage('Syncing Plans Failed. API Request Error occured');
                return;
            }

            $planData[] = $plans;

            if ($plans->has_more != 'true')
                break;

            $start += 100;
        }

        // sync the plan data
        if(count($planData)) {
            foreach($planData as $plans){
                foreach($plans->data as $plan){
                    $itemPlan = [];
                    $itemPlan['planId'] = $plan->id;
                    $itemPlan['interval'] = $plan->interval;
                    $itemPlan['livemode'] = $plan->livemode ? 'true' : 'false';
                    $itemPlan['name'] = $plan->name;
                    $itemPlan['created'] = $plan->created;
                    $itemPlan['amount'] = $plan->amount;
                    $itemPlan['currency'] = $plan->currency;
                    $itemPlan['interval_count'] = $plan->interval_count;
                    $itemPlan['trial_period_days'] = $plan->trial_period_days;
                    $itemPlan['statement_descriptor'] = $plan->statement_descriptor;
                    $itemPlan['statement_description'] = $plan->statement_description;
                    $itemPlan['regDate'] = date('Y-m-d H:i:s');
                    $itemPlan['updateDate'] = date('Y-m-d H:i:s');

                    $modelPlan = StripePlan::find()->where(['planId' => $plan->id])->one();
                    if ($modelPlan == null)
                        $modelPlan = new StripePlan();

                    $modelPlan->attributes = $itemPlan;
                    if ($modelPlan->save()) {
                        $storedArr['plan'] ++;
                    }
                    $realArr['plan'] ++;
                }
            }
        } else {
            //echo "No plans were found"."\n";
        }

        self::logMessage($storedArr['plan'].' Rows were updated to DB correctly.');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Plan\t\t".$realArr['plan']."\t".$storedArr['plan'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Plans Done');

        return 0;
    }

    /**
     * This command updates invoice data from Stripe
     *
     * @return 0
     */
    public function actionSyncInvoice() {

        self::logMessage('Syncing Invoices...');

        $realArr = [];
        $realArr['invoice'] = 0;

        $storedArr = [];
        $storedArr['invoice'] = 0;

        $startTime = microtime(true);

        Stripe::setApiKey(Yii::$app->params['stripe']['stripesecretkey']);

        //---------------------------UPDATING INVOICES--------------------------------- !!!
        $limitDate = 0;
        $latestInvoice = StripeInvoice::find()->orderBy(['created'=>SORT_DESC])->one();
        if ($latestInvoice != null)
            $limitDate = $latestInvoice['created'];

        $start = 0;
        $invoiceData = [];
        while (true)
        {
            $stopFlag = '0';
            try
            {
                $invoices = Stripe_Invoice::all(array("count" => 100, 'offset' => $start, 'include[]' => 'total_count'));
            }
            catch(\Exception $ex)
            {
                echo 'Syncing Invoices Failed. API Request Error occured';
                self::logMessage('Syncing Invoices Failed. API Request Error occured');
                return;
            }

            foreach($invoices->data as $invoice){
                if (intval($limitDate) < intval($invoice->date))
                {
                    $itemInvoice = [];
                    $itemInvoice['invoiceId'] = $invoice->id;
                    $itemInvoice['created'] = $invoice->date;
                    $itemInvoice['period_start'] = $invoice->period_start;
                    $itemInvoice['period_end'] = $invoice->period_end;
                    $itemInvoice['subtotal'] = $invoice->subtotal;
                    $itemInvoice['total'] = $invoice->total;
                    $itemInvoice['customerId'] = $invoice->customer;
                    $itemInvoice['attempted'] = $invoice->attempted ? 'true' : 'false';
                    $itemInvoice['closed'] = $invoice->closed ? 'true' : 'false';
                    $itemInvoice['forgiven'] = $invoice->forgiven ? 'true' : 'false';
                    $itemInvoice['paid'] = $invoice->paid ? 'true' : 'false';
                    $itemInvoice['livemode'] = $invoice->livemode ? 'true' : 'false';
                    $itemInvoice['attempt_count'] = $invoice->attempt_count;
                    $itemInvoice['amount_due'] = $invoice->amount_due;
                    $itemInvoice['currency'] = $invoice->currency;
                    $itemInvoice['starting_balance'] = $invoice->starting_balance;
                    $itemInvoice['ending_balance'] = $invoice->ending_balance;                    
                    $itemInvoice['next_payment_attempt'] = $invoice->next_payment_attempt;
                    $itemInvoice['webhooks_delivered_at'] = $invoice->webhooks_delivered_at;
                    $itemInvoice['chargeId'] = $invoice->charge;
                    $itemInvoice['subscriptionId'] = $invoice->subscription;
                    $itemInvoice['application_fee'] = $invoice->application_fee;
                    $itemInvoice['tax_percent'] = $invoice->tax_percent;
                    $itemInvoice['tax'] = $invoice->tax;
                    $itemInvoice['statement_descriptor'] = $invoice->statement_descriptor;
                    $itemInvoice['statement_description'] = $invoice->statement_description;
                    $itemInvoice['receipt_number'] = $invoice->receipt_number;
                    $itemInvoice['description'] = $invoice->description;
                    $itemInvoice['regDate'] = date('Y-m-d H:i:s');
                    $itemInvoice['updateDate'] = date('Y-m-d H:i:s');

                    $modelInvoice = StripeInvoice::find()->where(['invoiceId' => $invoice->id])->one();
                    if ($modelInvoice == null)
                        $modelInvoice = new StripeInvoice();

                    $modelInvoice->attributes = $itemInvoice;
                    if ($modelInvoice->save()) {
                        $storedArr['invoice'] ++;
                    }
                    $realArr['invoice'] ++;
                }
                else
                {
                    $stopFlag = '1';
                    break;
                }
            }

            if ($stopFlag == '1')
                break;
            else if ($invoices->has_more != 'true')
                break;

            $start += 100;
        }

        self::logMessage($realArr['invoice'].' Updates Found');
        self::logMessage($storedArr['invoice'].' Updates were Applied to DB correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Invoice\t\t".$realArr['invoice']."\t".$storedArr['invoice'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Invoices Done');

        return 0;
    }

    /**
     * This command updates refund data from Stripe
     *
     * @return 0
     */
    public function actionSyncRefund() {

        self::logMessage('Syncing Refunds...');

        $realArr = [];
        $realArr['refund'] = 0;

        $storedArr = [];
        $storedArr['refund'] = 0;

        $startTime = microtime(true);

        Stripe::setApiKey(Yii::$app->params['stripe']['stripesecretkey']);

        //---------------------------UPDATING REFUNDS--------------------------------- !!!
        $start = 0;
        $refundData = [];
        while (true)
        {
            try{
               $refunds = Stripe_Refund::all(array("count" => 100, 'offset' => $start, 'include[]' => 'total_count'));
            }
            catch(\Exception $ex)
            {
                echo 'Syncing Refunds Failed. API Request Error occured';
                self::logMessage('Syncing Refunds Failed. API Request Error occured');
                return;
            }

            $refundData[] = $refunds;

            if ($refunds->has_more != 'true')
                break;

            $start += 100;
        }

        // sync the invoice data
        if(count($refundData)) {
            foreach($refundData as $refunds){
                foreach($refunds->data as $refund){
                    $itemRefund = [];
                    $itemRefund['refundId'] = $refund->id;
                    $itemRefund['chargeId'] = $refund->charge;
                    $itemRefund['amount'] = $refund->amount;
                    $itemRefund['currency'] = $refund->currency;
                    $itemRefund['created'] = $refund->created;
                    $itemRefund['balance_transaction'] = $refund->balance_transaction;
                    $itemRefund['receipt_number'] = $refund->receipt_number;
                    $itemRefund['reason'] = $refund->reason;
                    $itemRefund['regDate'] = date('Y-m-d H:i:s');
                    $itemRefund['updateDate'] = date('Y-m-d H:i:s');

                    $modelRefund = StripeRefund::find()->where(['refundId' => $refund->id])->one();
                    if ($modelRefund == null)
                        $modelRefund = new StripeRefund();

                    $modelRefund->attributes = $itemRefund;
                    if ($modelRefund->save()) {
                        $storedArr['refund'] ++;
                    }
                    $realArr['refund'] ++;
                }
            }
        } else {
            //echo "No refunds were found"."\n";
        }

        self::logMessage($storedArr['refund'].' Rows were updated to DB correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Refund\t\t".$realArr['refund']."\t".$storedArr['refund'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Refunds Done');

        return 0;
    }

    /**
     * This command updates transfer data from Stripe
     *
     * @return 0
     */
    public function actionSyncTransfer()
    {
        self::logMessage('Syncing Transfers...');

        $realArr = [];
        $realArr['transfer'] = 0;

        $storedArr = [];
        $storedArr['transfer'] = 0;

        $startTime = microtime(true);

        Stripe::setApiKey(Yii::$app->params['stripe']['stripesecretkey']);

        //---------------------------UPDATING TRANSFERS---------------------------------
        $limitDate = 0;
        $latestTransfer = StripeTransfer::find()->orderBy(['created'=>SORT_DESC])->one();
        if ($latestTransfer != null)
            $limitDate = $latestTransfer['created'];

        $start = 0;
        while(true)
        {
            $stopFlag = '0';
            try
            {
                $transfers = Stripe_Transfer::all(array("count" => 100, 'offset' => $start, 'include[]' => 'total_count'));
            }
            catch(\Exception $ex)
            {
                echo 'Syncing Transfers Failed. API Request Error occured';
                self::logMessage('Syncing Transfers Failed. API Request Error occured');
                return;
            }

            foreach($transfers->data as $transfer) {
                if (intval($limitDate) < intval($transfer->created))
                {
                    //TRANSFER UPDATE
                    $itemTransfer = [];
                    $itemTransfer['transferId'] = $transfer->id;
                    $itemTransfer['created'] = $transfer->created;
                    $itemTransfer['scheduledDate'] = $transfer->date;
                    $itemTransfer['livemode'] = $transfer->livemode ? 'true' : 'false';
                    $itemTransfer['amount'] = $transfer->amount;
                    $itemTransfer['currency'] = $transfer->currency;
                    $itemTransfer['reversed'] = $transfer->reversed ? 'true' : 'false';
                    $itemTransfer['status'] = $transfer->status;
                    $itemTransfer['type'] = $transfer->type;
                    $itemTransfer['balance_transaction'] = $transfer->balance_transaction;
                    $itemTransfer['bank_account_id'] = $transfer->bank_account->id;
                    $itemTransfer['bank_account_last4'] = $transfer->bank_account->last4;
                    $itemTransfer['bank_account_country'] = $transfer->bank_account->country;
                    $itemTransfer['bank_account_currency'] = $transfer->bank_account->currency;
                    $itemTransfer['bank_account_status'] = $transfer->bank_account->status;
                    $itemTransfer['bank_account_fingerprint'] = $transfer->bank_account->fingerprint;
                    $itemTransfer['bank_account_routing_number'] = $transfer->bank_account->routing_number;
                    $itemTransfer['bank_account_bank_name'] = $transfer->bank_account->bank_name;
                    $itemTransfer['destination'] = $transfer->destination;
                    $itemTransfer['description'] = $transfer->description;
                    $itemTransfer['failure_message'] = $transfer->failure_message;
                    $itemTransfer['failure_code'] = $transfer->failure_code;
                    $itemTransfer['amount_reversed'] = $transfer->amount_reversed;
                    $itemTransfer['statement_descriptor'] = $transfer->statement_descriptor;
                    $itemTransfer['recipient'] = $transfer->recipient;
                    $itemTransfer['source_transaction'] = $transfer->source_transaction;
                    $itemTransfer['application_fee'] = $transfer->application_fee;
                    $itemTransfer['statement_description'] = $transfer->statement_description;
                    $itemTransfer['regDate'] = date('Y-m-d H:i:s');
                    $itemTransfer['updateDate'] = date('Y-m-d H:i:s');

                    $modelTransfer = StripeTransfer::find()->where(['transferId' => $transfer->id])->one();
                    if ($modelTransfer == null)
                        $modelTransfer = new StripeTransfer();

                    $modelTransfer->attributes = $itemTransfer;
                    if ($modelTransfer->save()) {
                        $storedArr['transfer'] ++;
                    }
                    $realArr['transfer'] ++;
                }
                else
                {
                    $stopFlag = '1';
                    break;
                }
            }

            if ($stopFlag == '1')
                break;
            else if ($transfers->has_more != 'true')
                break;

            $start += 100;
        }

        self::logMessage($realArr['transfer'].' Updates Found');
        self::logMessage($storedArr['transfer'].' Updates were Applied to DB correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Transfer\t".$realArr['transfer']."\t".$storedArr['transfer'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Transfers Done');

        return 0;
    }

    /**
     * This command updates balance transaction data from Stripe
     *
     * @return 0
     */
    public function actionSyncBalanceTransaction()
    {
        self::logMessage('Syncing Balance Transactions...');

        $realArr = [];
        $realArr['balance_transaction'] = 0;

        $storedArr = [];
        $storedArr['balance_transaction'] = 0;

        $startTime = microtime(true);

        Stripe::setApiKey(Yii::$app->params['stripe']['stripesecretkey']);

        //---------------------------UPDATING BALANCE TRANSACTIONS---------------------------------
        $limitDate = 0;
        $latestBalanceTransaction = StripeBalanceTransaction::find()->orderBy(['created'=>SORT_DESC])->one();
        if ($latestBalanceTransaction != null)
            $limitDate = $latestBalanceTransaction['created'];

        $start = 0;
        while(true)
        {
            $stopFlag = '0';
            try
            {
                $balanceTransactions = Stripe_BalanceTransaction::all(array("count" => 100, 'offset' => $start, 'include[]' => 'total_count'));
            }
            catch(\Exception $ex)
            {
                echo 'Syncing Balance Transactions Failed. API Request Error occured';
                self::logMessage('Syncing Balance Transactions Failed. API Request Error occured');
                return;
            }

            foreach($balanceTransactions->data as $balance_transaction) {
                if (intval($limitDate) < intval($balance_transaction->created))
                {
                    //TRANSFER UPDATE
                    $itemBalanceTransaction = [];
                    $itemBalanceTransaction['balanceId'] = $balance_transaction->id;
                    $itemBalanceTransaction['amount'] = $balance_transaction->amount;
                    $itemBalanceTransaction['currency'] = $balance_transaction->currency;
                    $itemBalanceTransaction['net'] = $balance_transaction->net;
                    $itemBalanceTransaction['type'] = $balance_transaction->type;
                    $itemBalanceTransaction['created'] = $balance_transaction->created;
                    $itemBalanceTransaction['available_on'] = $balance_transaction->available_on;
                    $itemBalanceTransaction['status'] = $balance_transaction->status;
                    $itemBalanceTransaction['fee'] = $balance_transaction->fee;
                    $itemBalanceTransaction['source'] = $balance_transaction->source;
                    $itemBalanceTransaction['description'] = $balance_transaction->description;
                    $itemBalanceTransaction['regDate'] = date('Y-m-d H:i:s');
                    $itemBalanceTransaction['updateDate'] = date('Y-m-d H:i:s');

                    $modelBalanceTransaction = StripeBalanceTransaction::find()->where(['balanceId' => $balance_transaction->id])->one();
                    if ($modelBalanceTransaction == null)
                        $modelBalanceTransaction = new StripeBalanceTransaction();

                    $modelBalanceTransaction->attributes = $itemBalanceTransaction;
                    if ($modelBalanceTransaction->save()) {
                        $storedArr['balance_transaction'] ++;
                    }
                    $realArr['balance_transaction'] ++;
                }
                else
                {
                    $stopFlag = '1';
                    break;
                }
            }

            if ($stopFlag == '1')
                break;
            else if ($balanceTransactions->has_more != 'true')
                break;

            $start += 100;
        }

        self::logMessage($realArr['balance_transaction'].' Updates Found');
        self::logMessage($storedArr['balance_transaction'].' Updates were Applied to DB correctly');

        echo PHP_EOL."Type\t\t\tReal\tStored".PHP_EOL;
        echo "--------------------------------------".PHP_EOL;
        echo "Balance Transactions\t".$realArr['balance_transaction']."\t".$storedArr['balance_transaction'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Balance Transactions Done');

        return 0;
    }

    /**
     * This command logs a message to a stripe.log file
     *
     * @param string $msg the message to be logged
     *
     */
    public function logMessage($msg)
    {
        Yii::info($msg, 'stripe');
    }

    /**
     * This command updates all stripe-related data, by calling all sync actions
     *
     * @return 0
     */
    public function actionSync()
    {
        \Yii::$app->runAction('services/stripe/sync-customer');
        \Yii::$app->runAction('services/stripe/sync-charge');
        \Yii::$app->runAction('services/stripe/sync-coupon');
        \Yii::$app->runAction('services/stripe/sync-plan');
        \Yii::$app->runAction('services/stripe/sync-invoice');
        \Yii::$app->runAction('services/stripe/sync-refund');
        \Yii::$app->runAction('services/stripe/sync-transfer');
        \Yii::$app->runAction('services/stripe/sync-balance-transaction');

        return 0;
    }

}
