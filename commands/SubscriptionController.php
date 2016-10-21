<?php
namespace app\commands;

use Stripe\Stripe as Stripe;
use Stripe\Customer as Stripe_Customer;
use app\models\CustomerSubscription;
use app\models\Notification;
use Yii;
use yii\console\Controller;
use yii\base\ErrorException;

use app\components;

use app\components\StripeHelper;
use app\models\CustomerEntity;
use app\models\CustomerSubscriptionState;
use app\models\CustomerSubscriptionLog;
use yii\helpers\VarDumper;

class SubscriptionController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($message = 'hello world')
    {
        Yii::$app->utils->logToConsole($message);
    }

    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionCheckStatus($test = 'ok')
    {
        Yii::$app->utils->logToConsole('checking...');
    }

    /**
     * Check all the customers for status update requests
     * @var string $interval is the range of dates to use when looking for status updates. Ex: 01.01.2015,01.04.2015 [the format is dd.mm.yyyy]
     */
    public function actionCheckAll($interval = null)
    {
        if(!$interval){
            $date1 = date('Y-m-d 00:00:00');
            $date = \DateTime::createFromFormat('Y-m-d H:i:s', $date1);
            $date1 = $date->getTimestamp();

            $date2 = date('Y-m-d 00:00:00', (strtotime('+1 day')));
            $date = \DateTime::createFromFormat('Y-m-d H:i:s', $date2);
            $date2 = $date->getTimestamp();

            $interval = date('d.m.Y') . ',' . date('d.m.Y', (strtotime('+1 day')));
        }
        else{
            $arr = explode(',', $interval);
            $date = \DateTime::createFromFormat('d.m.Y', $arr[0]);
            $date->setTime(0,0,0);
            $date1 = $date->getTimestamp();
            $date = \DateTime::createFromFormat('d.m.Y', $arr[1]);
            $date->setTime(0,0,0);
            $date2 = $date->getTimestamp();
        }

        $condition = 'start_date > :date1 AND start_date < :date2';

        Yii::$app->utils->logToConsole('Checking interval (date): '.$interval);
        Yii::$app->utils->logToConsole('Checking interval (timestamp): '.$date1.' - '.$date2);

        $alias = CustomerEntity::tableName();
        $customers = CustomerEntity::find()
            ->leftJoin(CustomerSubscription::tableName().' cs', 'cs.customer_id = '.$alias.'.id')
            ->leftJoin(CustomerSubscriptionState::tableName().' css', 'css.subscription_id = cs.id')
            ->where('css.update_status IS NOT NULL AND css.update_time >= :date1 AND css.update_time < :date2', [':date1'=>$date1, ':date2'=>$date2])
            ->all();


        foreach($customers as $customer){
            Yii::$app->utils->logToConsole('Customer: '.$customer->id);
            //$this->actionCheckOne($customer->id);
            $this->checkSubscriptionUpdateRequest($customer, [$date1, $date2]);
        }
    }

    /**
     * Check & process a status update
     * @param CustomerEntity $customer
     */
    public function actionCheckOne($customerId)
    {
        $this->checkSubscriptionUpdateRequest($customerId);
    }

    /**
     * Check customers subscription update request
     * @param $customer
     */
    protected function checkSubscriptionUpdateRequest($customer)
    {
        if(!is_object($customer))
            $customer = CustomerEntity::findOne($customer);

        if(!$customer)
            die("Error: Customer not found!\n");

        $activeState = $customer->customerSubscription->customerSubscriptionState;
        $initialState = clone $activeState;

        if(!$activeState){
            Yii::$app->utils->logToConsole("Error: Customer has no active status update requested!");
            return false;
        }

        /**
         * Cancel subscription
         */
        try {
            if($activeState->update_status == CustomerSubscriptionState::STATUS_CANCEL){
                $action = Yii::$app->stripe->cancelSubscription($customer);
                if($action && !empty($action)){
                    $initialState->update_status = $activeState->status = CustomerSubscriptionState::STATUS_CANCEL;

                    $activeState->resetState();
                    if($activeState->save())
                        CustomerSubscriptionLog::logStateChange($initialState, null, CustomerSubscriptionLog::TYPE_SET);

                    Yii::$app->utils->logToConsole("Info: Subscription has been canceled!");
                    $activeState->sendNotificationEmail('cancel');
                }
                else {
                    Yii::$app->utils->logToConsole("Error: Subscription cancel failed!");
                }

                return;
            }
        } catch (\Stripe\Error\ApiConnection $e) {
            Yii::$app->utils->logToConsole("Network problem, perhaps try again.");
        } catch (\Stripe\Error\InvalidRequest $e) {
            Yii::$app->utils->logToConsole("Invalid request.");
            echo $e->getMessage()."\n";
        } catch (\Stripe\Error\Api $e) {
            Yii::$app->utils->logToConsole("Api error.");
            echo $e->getMessage()."\n";
        } catch (\Stripe\Error\Card $e) {
            echo "Your card was declined!\n";
            Yii::$app->utils->logToConsole($e->getMessage());
        } catch (ErrorException $e) {
            Yii::$app->utils->logToConsole($e->getMessage());
        }

        /**
         * Pause subscription
         */
        try {
            if($activeState->update_status == CustomerSubscriptionState::STATUS_PAUSE){
                $action = Yii::$app->stripe->pauseSubscription($customer, $activeState);
                if($action && !empty($action)){
                    $initialState->update_status = $activeState->status = CustomerSubscriptionState::STATUS_PAUSE;

                    if(!$initialState->pause_duration){
                        $notification = new Notification();
                        $notification->customer_id = $customer->id;
                        $notification->type = Notification::TYPE_RESUME;
                        $notification->notify_next = date('Y-m-d H:i:s', strtotime('+3 months', $initialState->update_time));
                        $notification->save();
                    }

                    $activeState->resetState();
                    if($activeState->save())
                        CustomerSubscriptionLog::logStateChange($initialState, null, CustomerSubscriptionLog::TYPE_SET);

                    Yii::$app->utils->logToConsole("Info: Subscription has been paused!");
                    $activeState->sendNotificationEmail('pause');
                }
                else {
                    Yii::$app->utils->logToConsole("Error: Subscription pause failed!");
                }

                return;
            }
        } catch (\Stripe\Error\ApiConnection $e) {
            Yii::$app->utils->logToConsole("Network problem, perhaps try again.");
        } catch (\Stripe\Error\InvalidRequest $e) {
            Yii::$app->utils->logToConsole("Invalid request.");
            echo $e->getMessage()."\n";
        } catch (\Stripe\Error\Api $e) {
            Yii::$app->utils->logToConsole("Api error.");
            echo $e->getMessage()."\n";
        } catch (\Stripe\Error\Card $e) {
            echo "Your card was declined!\n";
            Yii::$app->utils->logToConsole($e->getMessage());
        } catch (ErrorException $e) {
            Yii::$app->utils->logToConsole("Error: 123");
            Yii::$app->utils->logToConsole($e->getMessage() . " in file: " . $e->getFile() . " on line: " . $e->getLine());
        }

        /**
         * Resume subscription
         */
        try {
            if($activeState->update_status == CustomerSubscriptionState::STATUS_RESUME){
                $action = Yii::$app->stripe->resumeSubscription($customer, $activeState->update_time);
                if($action && !empty($action)){
                    $initialState->update_status = $activeState->status = CustomerSubscriptionState::STATUS_RESUME;

                    $activeState->resetState();
                    if($activeState->save())
                        CustomerSubscriptionLog::logStateChange($initialState, null, CustomerSubscriptionLog::TYPE_SET);

                    Yii::$app->utils->logToConsole("Info: Subscription has been resumed!");
                    $activeState->sendNotificationEmail('resume');
                }
                else
                    Yii::$app->utils->logToConsole("Error: Subscription resume failed!");

                return;
            }
        } catch (\Stripe\Error\ApiConnection $e) {
            Yii::$app->utils->logToConsole("Network problem, perhaps try again.");
        } catch (\Stripe\Error\InvalidRequest $e) {
            Yii::$app->utils->logToConsole("Invalid request.");
            echo $e->getMessage()."\n";
        } catch (\Stripe\Error\Api $e) {
            Yii::$app->utils->logToConsole("Api error.");
            echo $e->getMessage()."\n";
        } catch (\Stripe\Error\Card $e) {
            echo "Your card was declined!\n";
            Yii::$app->utils->logToConsole($e->getMessage());
        } catch (ErrorException $e) {
            Yii::$app->utils->logToConsole($e->getMessage());
        }
    }

    public function actionRetrieveCustomer() {
        Stripe::setApiKey(Yii::$app->params['stripe']['stripesecretkey']);
        echo VarDumper::dumpAsString(Stripe_Customer::retrieve('cus_5uWCLc6mve5CSF'));
    }
}
