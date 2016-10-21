<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

use app\models\CustomerEntity;
use app\models\CustomerSubscription;
use app\models\CustomerSubscriptionState;
use app\models\CustomerSubscriptionLog;

use Stripe\Stripe;
use Stripe\Customer as Stripe_Customer;
use app\models\Notification;
use yii\base\ErrorException;


class CustomerSubscriptionStateController extends \app\components\AController
{
    public function actionIndex()
    {
        $customer = CustomerEntity::findOne(122);
        \yii\helpers\VarDumper::dump( Yii::$app->stripe->updateCustomer($customer, []), 10, true );
    }

    /**
     * Updates an existing CustomerSubscriptionStatus model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        /**
         * @var CustomerSubscriptionState $model
         */
        $model = $this->findModel($id);
        $data = Yii::$app->request->post();
        if(isset($data['CustomerSubscriptionState'])){
            $model->attributes = $data['CustomerSubscriptionState'];

            if(isset($data['CustomerSubscriptionState']['now']) && $data['CustomerSubscriptionState']['now'] == 1) {
                $startDate = date('F j, Y', strtotime('+1 day'));
            } else {
                $startDate = $data['CustomerSubscriptionState']['start_date'];
            }

            $date = \DateTime::createFromFormat('F j, Y', $startDate);
            $model->update_time = $date->getTimestamp();

            // we can have a pause duration only if we are going to pause the subscription
            if($model->update_status != CustomerSubscriptionState::STATUS_PAUSE)
                $model->pause_duration = null;

            $model->userIdentity = Yii::$app->user->identity;
            if($model->save()) {
                if(isset($data['CustomerSubscriptionState']['now']) && $data['CustomerSubscriptionState']['now'] == 1) {
                    $this->checkSubscriptionUpdateRequest($model->customerSubscription->customer);
                }
            }
        }
    }

    /**
     * @param CustomerEntity|integer $customer
     * @throws HttpException when the user not found
     * @return boolean|null
     */
    private function checkSubscriptionUpdateRequest($customer) {
        if(!is_object($customer)) {
            $customer = CustomerEntity::findOne($customer);
        }

        if(!$customer)
            throw new HttpException(404, 'Customer not found');

        $activeState = $customer->customerSubscription->customerSubscriptionState;
        $initialState = clone $activeState;

        if(!$activeState) {
            Yii::$app->utils->logToFile("Error: Customer has no active status update requested!");
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

                    Yii::$app->utils->logToFile("Info: Subscription has been canceled!");
                    $activeState->sendNotificationEmail('cancel');
                }
                else {
                    Yii::$app->utils->logToFile("Error: Subscription cancel failed!");
                }

                return;
            }
        } catch (\Stripe\Error\ApiConnection $e) {
            Yii::$app->utils->logToFile("Network problem, perhaps try again.");
        } catch (\Stripe\Error\InvalidRequest $e) {
            Yii::$app->utils->logToFile("Invalid request.");
            echo $e->getMessage()."\n";
        } catch (\Stripe\Error\Api $e) {
            Yii::$app->utils->logToFile("Api error.");
            echo $e->getMessage()."\n";
        } catch (\Stripe\Error\Card $e) {
            echo "Your card was declined!\n";
            Yii::$app->utils->logToFile($e->getMessage());
        } catch (ErrorException $e) {
            Yii::$app->utils->logToFile($e->getMessage());
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

                    Yii::$app->utils->logToFile("Info: Subscription has been paused!");
                    $activeState->sendNotificationEmail('pause');
                }
                else {
                    Yii::$app->utils->logToFile("Error: Subscription pause failed!");
                }

                return;
            }
        } catch (\Stripe\Error\ApiConnection $e) {
            Yii::$app->utils->logToFile("Network problem, perhaps try again.");
        } catch (\Stripe\Error\InvalidRequest $e) {
            Yii::$app->utils->logToFile("Invalid request.");
            echo $e->getMessage()."\n";
        } catch (\Stripe\Error\Api $e) {
            Yii::$app->utils->logToFile("Api error.");
            echo $e->getMessage()."\n";
        } catch (\Stripe\Error\Card $e) {
            echo "Your card was declined!\n";
            Yii::$app->utils->logToFile($e->getMessage());
        } catch (ErrorException $e) {
            Yii::$app->utils->logToFile($e->getMessage() . " in file: " . $e->getFile() . " on line: " . $e->getLine());
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

                    Yii::$app->utils->logToFile("Info: Subscription has been resumed!");
                    $activeState->sendNotificationEmail('resume');
                }
                else
                    Yii::$app->utils->logToFile("Error: Subscription resume failed!");

                return;
            }
        } catch (\Stripe\Error\ApiConnection $e) {
            Yii::$app->utils->logToFile("Network problem, perhaps try again.");
        } catch (\Stripe\Error\InvalidRequest $e) {
            Yii::$app->utils->logToFile("Invalid request.");
            echo $e->getMessage()."\n";
        } catch (\Stripe\Error\Api $e) {
            Yii::$app->utils->logToFile("Api error.");
            echo $e->getMessage()."\n";
        } catch (\Stripe\Error\Card $e) {
            echo "Your card was declined!\n";
            Yii::$app->utils->logToFile($e->getMessage());
        } catch (ErrorException $e) {
            Yii::$app->utils->logToFile($e->getMessage());
        }

    }

    /**
     * Load a subscription and reset its state
     * @throws NotFoundHttpException
     */
    public function actionCancelUpdate()
    {
        /**
         * @var CustomerSubscriptionState $model
         */
        $data = Yii::$app->request->post();
        if (isset($data['id'])) {
            $model = $this->findModel($data['id']);
            if($model->validStateUpdate()){
                CustomerSubscriptionLog::logStateChange($model, Yii::$app->user->identity->id, CustomerSubscriptionLog::TYPE_CANCEL);
                $model->resetState();
                $model->save();
            }
        }
        else
            throw new NotFoundHttpException('The requested subscription does not exist.');
    }

    /**
     * Finds the CustomerSubscriptionStatus model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CustomerSubscriptionStatus the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CustomerSubscriptionState::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionTestEmail()
    {
        $customer = CustomerEntity::findOne(97);
        if($customer->customerSubscription->customerSubscriptionState->sendNotificationEmail('resume'))
            echo 'sent';
        else
            echo 'not sent';
    }

    public function actionTestInvoice()
    {
        $customer = CustomerEntity::findOne(97);
        //$invoice = Yii::$app->stripe->getLastInvoice($customer);
        //$invoice = Yii::$app->stripe->getInvoice('in_162WbCIskqa68wvdnuk79VAD');
        $subscription = Yii::$app->stripe->getSubscription($customer);
        \yii\helpers\VarDumper::dump($subscription, 10, true);
    }
}
