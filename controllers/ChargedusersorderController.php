<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\models\TbAttribute;
use app\components\AController;

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

use app\models\TradegeckoOrderLog;
use app\models\TradegeckoOrderLogSearch;
use app\models\CustomerSubscription;
use app\models\TradegeckoInfo;
use app\models\TradegeckoOderItemsLog;

/**
 * PostController implements the CRUD actions for Post model.
 */
class ChargedusersorderController extends AController
{

	/**
	 * @return string
	 */
	public function actionStripe()
	{
		$stripeSecret_key = Yii::$app->params['stripe']['stripesecretkey'];
		Stripe::setApiKey($stripeSecret_key);
	
		$created_from = $created_to = strtotime(date('Y-m-d'));
		
		if(isset($_GET['from']) && isset($_GET['to']) && $_GET['from']!=null && $_GET['to']!=null)
		{
			$created_from  = $_GET['from'];
			$created_to = $_GET['to'];
		}
		$limit = 2;
		$offset = 0;
		if(isset($_GET['p'])){
			$offset = $limit * $_GET['p'];
		}
		$fromdate  = '2015-02-01';
		$todate = '2015-02-28';
		$created_from = strtotime($fromdate);
		$created_to = strtotime($todate);
		if($created_from == $created_to){
			$charges = Stripe_Charge::all(array("created" =>array('gte'=>$created_from,
																),
												"limit" => 10,				
												'offset'=> $offset
												)
										);
		}else{
			$charges = Stripe_Charge::all(array("created" =>array('gte'=>$created_from,
															      'lte'=>$created_to
																  ),
																"limit" => 10,
																'offset'=> $offset
												)
										);
														
		}
		$TbAttribute = 	TbAttribute::find()->orderBy(['id' => SORT_ASC,])->all();
		return $this->render('stripe', ['charges' => $charges,'TbAttribute'=>$TbAttribute]);
	}

	/**
	 *
	 */
	public function actionOrdersubmission()
	{
		$post = Yii::$app->request->post();
		if(!isset($post['customer_id']) || $post['customer_id']=='') {
			echo 'Customer Id not passed to create order.';
			return;
		}
		
		if($post['sku1']=='' && $post['sku2']=='') {
			echo 'Enter atleast one product to create order.';
			return;
		}
		
		$customer_id = $post['customer_id'];
		$date = date('Y-m');
		$customer_status=TradegeckoOrderLog::find()->where(['customer_id' => $customer_id])->andWhere(['like', 'created_at', $date])->one();
		if(sizeof($customer_status)==0)
		{
			$products = array();
			if($post['sku1']!='') {
				$products[] = array('sku_id'=>$post['sku1']);
			}
			if($post['sku2']!='') {
				$products[] = array('sku_id'=>$post['sku2']);
			}
			$orderCreate = Yii::$app->tradeGeckoHelper->tradegeckoordercreate($customer_id, $customer_id, $products);

			if($orderCreate['error']==true) {
				echo $orderCreate['errmsg'];
				return;
			}
			
			try {
				$orderLog = new TradegeckoOrderLog();
				$orderLog->customer_id = $customer_id;
				$orderLog->created_at = $date;
				$orderLog->import_csv_title = 'manual order';
				$orderLog->tradegecko_order_number = $orderCreate['order_number'];
				$orderLog->tradegecko_order_id = $orderCreate['order_id'];
				$orderLog->save();
			} catch(Exception $e) {
				echo $e->getMessage;
				return;
			}
			
			if(!$orderLog->id) {
				echo 'Order not created successfully.';
			}
			$productsTradegecko = array();
			$productsTradegecko = $orderCreate['productsTradegecko'];
			if(count($productsTradegecko)>0) {
				foreach($productsTradegecko as $keyPr => $valPr) {
					$orderItemLog = new TradegeckoOderItemsLog();
					$orderItemLog->log_order_id = $orderLog->id;
					$orderItemLog->customer_id = $customer_id;
					$orderItemLog->item_id = $valPr['sku_id'];
					$orderItemLog->save();
				}
			}
		} else {
			echo 'Customer already subscribed for this month. Customer Id - '.$customer_id;
			return;
		}
		echo 'OK';		
	}


	/**
	 * @param       $row_id
	 * @param int   $customer_id
	 * @param array $products
	 *
	 * @return array
	 */
	public function tradegeckoordercreate($row_id, $customer_id=0, $products=array()) {
		$productsTradegecko = array();
		if($customer_id==0) {
			return array('error'=>true,'errmsg'=>'No customer ID provided for this customer. Customer ID - '.$customer_id);
		}
		$company_id = 0;
		/*$TradegeckoInfo = TradegeckoInfo::find()->where(['customer_id'=>$customer_id])
            ->orderBy(['id'=>SORT_DESC])
            ->one();
		if(sizeof($TradegeckoInfo) > 0){
			$company_id = $TradegeckoInfo->company_id;
		}*/
		$customer_status=CustomerSubscription::find()->where(['customer_id' => $customer_id])->orderBy(['id'=>SORT_DESC])->one();
		$issued_date = date('M d Y',strtotime($customer_status->created_at));
		$ship_date = strtotime($issued_date);
		$ship_date = strtotime("+7 day", $ship_date);
		$ship_date = date('M d Y', $ship_date);
		
		$data = '';
		$accesstoken = "Bearer ".Yii::$app->params['tradegecko']['token'];
		$header = array();
		$header[] = 'Content-type: application/json';
		$header[] = 'Authorization: '.$accesstoken;
		
		$url = "https://api.tradegecko.com/companies?company_code=".$customer_id;
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$ResponseCompany = curl_exec($ch);
		curl_close($ch);
		$ResponseCompany = json_decode($ResponseCompany, TRUE);
		if(!isset($ResponseCompany['companies']) || !isset($ResponseCompany['companies'][0])) {
			return array('error'=>true,'errmsg'=>'No company(company code) on tradegecko exist for customer id - '.$customer_id);
		}
		
		$company_id = $ResponseCompany['companies'][0]['id'];
		
		if($company_id==0) {
			return array('error'=>true,'errmsg'=>'No company ID exist for customer - '.$customer_id);
		}
		
		
		
		$url = "https://api.tradegecko.com/addresses?company_id=".$company_id;
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$ResponseAddress = curl_exec($ch);
		curl_close($ch);
		$ResponseAddress = json_decode($ResponseAddress, TRUE);
		if(!isset($ResponseAddress['addresses']) || !isset($ResponseAddress['addresses'][0])) {
			return array('error'=>true,'errmsg'=>'No address exist for customer - '.$customer_id);
		}
		$shipping_address_id = $ResponseAddress['addresses'][0]['id'];
		$billing_address_id = $ResponseAddress['addresses'][0]['id'];
		$email_id = $ResponseAddress['addresses'][0]['email'];
		
		foreach($ResponseAddress['addresses'] as $key => $address) {
			if($address['label']=='Shipping') {
				$shipping_address_id = $address['id'];
			}
			if($address['label']=='Billing') {
				$billing_address_id = $address['id'];
			}
		}
		
		if(count($products)<=0) {
			return array('error'=>true,'errmsg'=>'No products exist to place order for customer - '.$customer_id);
		}
		
		
		$order_line_items = array();
		$variant_ids = array();
		foreach($products as $keyprod => $valproduct) {
			
			
			
			$sku_id = $valproduct['sku_id'];
			
			
			$url = "https://api.tradegecko.com/variants?sku=".$sku_id;
			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			$ResponseVariant = curl_exec($ch);
			curl_close($ch);
			$ResponseVariant = json_decode($ResponseVariant, TRUE);
			if(!$ResponseVariant  || !is_array($ResponseVariant) || !isset($ResponseVariant['variants'])) {
				continue;
			}
			$variant = $ResponseVariant['variants'][0];		
			$variant_id = $variant['id'];
			if(!in_array($variant_id,$variant_ids)) {
				$variant_ids[] = $variant_id;
			} else {
				continue;
			}
			$productsTradegecko[] = array('sku_id'=>$sku_id);
			if($ResponseAddress['addresses'][0]['state']=='CA')
			{
				$order_line_items[] = array('quantity'=>1,'price'=>$variant['retail_price'], 'variant_id'=>$variant['id'], "tax_rate_override"=> "9.0");
			}
			else
			{
				$order_line_items[] = array('quantity'=>1,'price'=>$variant['retail_price'], 'variant_id'=>$variant['id']);
			}
		}
		
		if($ResponseAddress['addresses'][0]['state']=='CA' || $ResponseAddress['addresses'][0]['state']=='AZ' || $ResponseAddress['addresses'][0]['state']=='NV' || $ResponseAddress['addresses'][0]['state']=='OR')
		{
			$order_line_items[] = array('quantity'=>1,'price'=>8.5, 'label'=>'Shipping', 'freeform'=>true);
		}
		else
		{
			$order_line_items[] = array('quantity'=>1,'price'=>15.5, 'label'=>'Shipping', 'freeform'=>true);
		}
		
		$req = array('order' => array(
										'company_id' => $company_id,
										'billing_address_id' => $billing_address_id,
										'shipping_address_id' => $shipping_address_id,
										'email'=> $email_id,
									//	'issued_at' => gmdate('D, d M Y H:i:s \G\M\T',time()),
										'issued_at' => $issued_date,
										'ship_at' => $ship_date,
										'tax_type' => 'exclusive',
										'tax_label'  => 'SALES',
         								'payment_status' => 'unpaid',
										'status'=> 'active',
										'order_line_items' => $order_line_items,
									  ),
					);
					
		$req = json_encode($req);

		$url = "https://api.tradegecko.com/orders/";
		$ch = curl_init($url);
			
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$req);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
		$Response = curl_exec($ch);
		curl_close($ch);
	
		$Response = json_decode($Response);
		if(isset($Response->order->id)) {
			return array('error'=>false,'order_id'=>$Response->order->id,'order_number'=>$Response->order->order_number,'productsTradegecko'=>$productsTradegecko);
		}
		return array('error'=>true,'errmsg'=>'Order not created for the customer - '.$customer_id);
	}
	
}
