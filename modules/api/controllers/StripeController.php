<?php

namespace app\modules\api\controllers;

use app\models\CustomerSubscription;
use app\models\Order;
use app\modules\api\models\WebhookLog;
use Yii;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

use Stripe\Stripe;

class StripeController extends Controller {

    /**
     * @throws BadRequestHttpException when the request method is not POST
     */
    public function actionCustomerCharge() {
        if(Yii::$app->request->isPost) {
            //Set your secret key: remember to change this to your live secret key in production
            //See your keys here https://dashboard.stripe.com/account/apikeys
            Stripe::setApiKey(Yii::$app->params['stripe']['stripesecretkey']);

            //Retrieve the request's body and parse it as JSON
            $input = @file_get_contents("php://input");

            // dump stripe event data in log file
            Yii::info($input, 'stripe_dump');

            $event_json = json_decode($input, true);
            if($event_json['type'] == 'charge.succeeded') {
                $object = $event_json['data']['object'];
                $data['event_id'] = $object['id'];
                $data['type'] = $event_json['type'];
                $data['created'] = date('Y-m-d H:i:s', (int)$object['created']);
                $data['paid'] = $object['paid'];
                $data['status'] = $object['status'];
                $data['source_id'] = $object['source']['id'];
                $data['customer_id'] = $object['customer'];
                $data['balance_transaction'] = $object['balance_transaction'];
                $data['invoice_id'] = $object['invoice'];

                Yii::info(json_encode($data), 'stripe');

                if($orderId = $this->createOrder($data['customer_id'])) {
                    $data['orderId'] = $orderId;
                    $log = new WebhookLog();
                    $log->stripeCustomerId = $data['customer_id'];
                    $log->data = json_encode($data);
                    $log->save(false);
                    Yii::info('Order created', 'stripe');
                    http_response_code(200);
                } else {
                    Yii::error('Cannot create order', 'stripe');
                    http_response_code(500);
                }
            } else {
                Yii::error('Customer was not charged', 'stripe');
            }

        } else {
            throw new BadRequestHttpException('The request is not valid!');
        }
    }

    /**
     * Creates a new order queue after the subscription is created in Stripe
     * @param $stripeCustomerId Customer id from Stripe
     * @return int | string Returns string when the customer subscription not found
     * @throws NotFoundHttpException when the customer was not found
     */
    private function createOrder($stripeCustomerId) {
        $customerSubscription = CustomerSubscription::find()->where(['stripe_customer_id' => $stripeCustomerId])->one();
        if($customerSubscription) {
            $order = new Order();
            $order->setAttributes([
                'userId' => 100,
                'customerId' => $customerSubscription->customer_id,
                'status' => 'active',
                'date' => date('Y-m-d')
            ]);
            if($order->save()) {
                return $order->id;
            } else {
                return false;
            }
        } else {
            return 'Customer not found!';
        }
    }

}