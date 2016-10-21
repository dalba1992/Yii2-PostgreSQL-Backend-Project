<?php

namespace app\modules\services\controllers;

use app\models\CustomerEntity;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

use Stripe\Stripe;
use Stripe\StripeObject as Stripe_Object;
use Stripe\Customer as Stripe_Customer;

use app\modules\services\models\StripeCustomer;
use app\modules\services\models\StripeCustomerSearch;
use app\modules\services\models\StripeSubscription;
use app\modules\services\models\StripeCharge;
use app\modules\services\models\StripeChargeSearch;
use app\modules\services\models\StripeCoupon;
use app\modules\services\models\StripeCouponSearch;
use app\modules\services\models\StripeCard;
use app\modules\services\models\StripeSource;
use app\modules\services\models\StripePlan;
use app\modules\services\models\StripePlanSearch;
use app\modules\services\models\StripeRefund;
use app\modules\services\models\StripeInvoice;
use app\modules\services\models\StripeTransfer;
use app\modules\services\models\StripeTransferSearch;
use app\modules\services\models\StripeBalanceTransaction;
use app\modules\services\models\StripeBalanceTransactionSearch;

use app\models\AuthUrls;
use app\models\AuthUrlsAssignment;
use app\models\CustomerSubscription;
use app\models\CustomerEntityInfo;

class StripeController extends \app\modules\services\components\AController
{
    /**
     * This function loads index page.
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * This function loads customer data from a database and displays on customers page
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCustomers()
    {
        $searchModel = new StripeCustomerSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams)->dataProvider;

        return $this->render('customers', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * This function loads charge data from a database and displays on payments page
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPayments()
    {
        $searchModel = new StripeChargeSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams)->dataProvider;

        return $this->render('payments', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * This function loads coupon data from a database and displays on coupons page
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCoupons()
    {
        $searchModel = new StripeCouponSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams)->dataProvider;

        return $this->render('coupons', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * This function loads plan data from a database and displays on plans page
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPlans()
    {
        $searchModel = new StripePlanSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams)->dataProvider;

        return $this->render('plans', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * This function loads transfer data from a database and displays on transfers page
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionTransfers()
    {
        $searchModel = new StripeTransferSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams)->dataProvider;

        return $this->render('transfers', [
            'dataProvider' => $dataProvider,
            'searchModel' =>$searchModel,
        ]);
    }

    /**
     * This function loads balance data from a database and displays on balancetransactions page
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionBalanceTransactions()
    {
        $searchModel = new StripeBalanceTransactionSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams)->dataProvider;

        return $this->render('balancetransactions', [
            'dataProvider' => $dataProvider,
            'searchModel' =>$searchModel,
        ]);
    }

    /**
     * This function displays customer detail data
     *
     * @param string $customerId the ID of a customer to be shown
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSync()
    {
        if (isset($_REQUEST ['customerId']))
        {
            $customerId = $_REQUEST['customerId'];

            Stripe::setApiKey(Yii::$app->params['stripe']['stripesecretkey']);

            $customer = Stripe_Customer::retrieve($customerId);

            //CUSTOMER
            $item = [];
            $item['created'] = $customer->created;
            $item['email'] = $customer->email;
            $item['account_balance'] = $customer->account_balance;
            $item['currency'] = $customer->currency;
            $item['livemode'] = $customer->livemode ? 'true' : 'false';
            $item['default_card'] = $customer->default_card;
            $item['default_source'] = $customer->default_source;
            $item['description'] = $customer->description;
            $item['updateDate'] = date('Y-m-d H:i:s');

            $model = StripeCustomer::find()->where(['customerId' => $customer->id])->one();
            if ($model == null)
                $model = new StripeCustomer();

            $model->attributes = $item;
            $model->save();

            //SUBSCRIPTIONS
            $subscriptionModels = StripeSubscription::find()->where(['customerId' => $customer->id])->all();
            foreach($subscriptionModels as $subscriptionModel)
                $subscriptionModel->delete();

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

                $subscriptionModel = new StripeSubscription();

                $subscriptionModel->attributes = $itemSub;
                $subscriptionModel->save();
            }

            //CARDS
            $cardModels = StripeCard::find()->where(['customerId' => $customer->id])->all();
            foreach($cardModels as $cardModel)
                $cardModel->delete();
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

                $cardModel = new StripeCard();

                $cardModel->attributes = $itemCard;
                $cardModel->save();
            }

            //SOURCES
            $sourceModels = StripeSource::find()->where(['customerId' => $customer->id])->all();
            foreach($sourceModels as $sourceModel)
                $sourceModel->delete();
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

                $sourceModel = new StripeSource();

                $sourceModel->attributes = $itemSource;
                $sourceModel->save();
            }

            /**
             * Add data to its local subscription as well
             * @todo look into the scenario of multiple subscriptions
             */
            $customerSubscription = CustomerSubscription::find()->where(['stripe_customer_id' => $customerId])->one();
            if(!$customerSubscription)
                $customerSubscription = new CustomerSubscription();

            $conciergeMemberInfo = CustomerEntityInfo::findOne(['email'=>$customer->email]);
            if($conciergeMemberInfo)
                $customerSubscription->customer_id = $conciergeMemberInfo->customer_id;

            $customerSubscription->gateway_id = '';
            $customerSubscription->cc_number = $card->last4;
            $customerSubscription->cc_type = $card->brand;
            $customerSubscription->exp_date = $source->exp_month.'/'.$source->exp_year;
            $customerSubscription->referred_by = '';
            $customerSubscription->stripe_customer_id = $customerId;
            $customerSubscription->stripe_subscription_id = $subscription->id;
            $customerSubscription->stripe_plan_id = $subscription->plan->id;

            $customerSubscription->save();

            if(!Yii::$app->request->isAjax && Yii::$app->request->isGet){
                $this->redirect(Yii::$app->request->referrer);
            }
            else{
                $jsonString = json_encode($item);
                echo $jsonString;
            }
        }

        Yii::$app->end();

    }

    /**
    * This function displays customer detail data
    *
    * @param string $customerId the ID of a customer to be shown
    *
    * @return string
    * @throws NotFoundHttpException
    */
    public function actionView()
    {
        $customerId = $_GET['customerId'];

        $customer = StripeCustomer::find()->where(['customerId' => $customerId])->one();
        if ($customer != null)
        {
            $subscriptions = StripeSubscription::find()->where(['customerId' => $customer['customerId']])->all();

            $plans = [];
            foreach($subscriptions as $subscription)
            {
                $res = StripePlan::find()->where(['planId' => $subscription['planId']])->all();
                foreach($res as $key=>$item)
                    $plans[] = $item;
            }

            $cards = StripeCard::find()->where(['customerId' => $customer['customerId']])->all();

            $charges = StripeCharge::find()->where(['customerId' => $customer['customerId']])->all();

            $invoices = StripeInvoice::find()->where(['customerId' => $customer['customerId']])->all();

            $refunds = [];
            foreach($charges as $charge)
            {
                $res = StripeRefund::find()->where(['chargeId' => $charge['chargeId']])->all();
                foreach($res as $item)
                    $refunds[] = $item;
            }

            return $this->render('view', [
                'customer' => $customer,
                'subscriptions' => $subscriptions,
                'plans' => $plans,
                'cards' => $cards,
                'charges' => $charges,
                'invoices' => $invoices,
                'refunds' => $refunds,
                /*'coupons' => $coupons,
                'transfers' => $transfers,
                'balances' => $balances,*/
            ]);
        }
        else
            throw new NotFoundHttpException('The requested member not exist.'); 
    }
}
