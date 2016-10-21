<?php

namespace app\controllers;

use Yii;
use Exception;
use yii\filters\AccessControl;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

use app\models\CustomerEntity;
use app\models\CustomerEntitystatus;
use app\models\CronStatuslog;
use app\models\CustomerStatuslog;
use app\models\Register;



//use app\lib\Stripe;
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

// Include AWS SES Email Library
use app\lib\ses\SimpleEmailService;
use app\lib\ses\SimpleEmailServiceMessage;
use app\lib\ses\SimpleEmailServiceRequest;

use app\models\CustomerSubscription;

class PaymentController extends \app\components\AController
{
	/**
	 *
	 */
	public function actionCharge(){
		$stripeSecret_key = Yii::$app->params['stripe']['stripesecretkey'];
		Stripe::setApiKey($stripeSecret_key);
		
		$created_from = $created_to = strtotime(date('Y-m-d'));
		
		if(isset($_GET['from']) && isset($_GET['to']) && $_GET['from']!=null && $_GET['to']!=null){
			$created_from  = $_GET['from'];
			$created_to = $_GET['to'];
		}
		$limit = 50;
		$offset = 0;
		if(isset($_GET['p'])){
			$offset = $limit * $_GET['p'];
		}
		if($created_from == $created_to){
			$charges = Stripe_Charge::all(array("created" =>array('gte'=>$created_from,
																),
												"limit" => 2,				
												'offset'=> $offset
												)
										);
		}else{
			$charges = Stripe_Charge::all(array("created" =>array('gte'=>$created_from,
															      'lte'=>$created_to
																  ),
																"limit" => 2,
																'offset'=> $offset
												)
										);
														
		}
		
		$count = 0;
		$status = "paid";
		if(isset($_GET['status']) && $_GET['status']!=null){
			$status = 'paid';
		}
		foreach($charges->data as $charge){
			if($status == $charge->status){
				++$count;
				echo '<br>'.$count.')'.$charge->source->customer.' | '.$charge->status .' | '.date('Y-m-d',$charge->created);
			}
		}
			echo "<br>yes";die;
	}

}   