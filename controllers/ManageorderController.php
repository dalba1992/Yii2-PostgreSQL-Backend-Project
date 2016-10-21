<?php

namespace app\controllers;
use Yii;

use app\models\CustomerSubscription;
use app\models\CustomerEntityInfo;
use app\models\CustomerEntityAddress;
use app\models\TbAttributeDetail;
use app\models\CustomerEntityAttribute;
use app\models\TradegeckoOrderLog;
use app\models\TradegeckoOrderLogSearch;
use app\models\TradegeckoInfo;
use app\models\TradegeckoOderItemsLog;


/***************** Stripe Library Start *****************/
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
/***************** Stripe Library End *****************/
class ManageorderController extends \app\components\AController
{
    public function actionIndex()
    {
		if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        return $this->render('index');
    }
	public function actionSubmitdate()
    {
		if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
		$stripeSecret_key = Yii::$app->params['stripe']['stripesecretkey'];
		Stripe::setApiKey($stripeSecret_key);
		if(isset($_GET['offset']) && $_GET['offset']>=0 && $_GET['total_charge']>0 && $_GET['from'] && $_GET['total_charge'] >0 && $_GET['total_charge'] && $_GET['total_charge'] >0){
			$from = $_GET['from'];
			$offset = $_GET['offset'];
			if(isset($_GET['prev']) && $_GET['prev']==1 && $offset>0){
				$offset = $offset-1;
			}
			$charges = Stripe_Charge::all(array("created" =>array('gte'=>$from,
																),
												"paid"=>true,
												"limit"=>1,
												'offset'=>$offset,
												)
											);
											
			Yii::$app->session->set('chargeduser',$charges->data);								
			$total_charge = $_GET['total_charge'];
			$this->redirect(array('index','offset'=>$offset,'from'=>$from,'total_charge'=>$total_charge));								
		}else if(isset($_POST) && isset($_POST['from']) && $_POST['from']!=null){
				$from = strtotime($_POST['from']);
				$charges = Stripe_Charge::all(array("created" =>array('gte'=>$from,
																	),
													"paid"=>true,
													"limit"=>'100',
													)
												);
				Yii::$app->session->set('chargeduser',$charges->data);								
				$total_charge = sizeof($charges->data);
				$this->redirect(array('index','from'=>$from,'total_charge'=>$total_charge));
		}else{
			return $this->redirect(['index']);
		}
    }
	public function actionTest(){
		$stripeSecret_key = Yii::$app->params['stripe']['stripesecretkey'];
		Stripe::setApiKey($stripeSecret_key);
		if(isset($_GET['from']) && $_GET['from'] != null){
			$from = $_GET['from'];
			
			
			$charges = Stripe_Charge::all(array("created" =>array('gte'=>$from,
																),
												"paid"=>true,
												"limit"=>$_GET['limit'],
												'offset'=>$_GET['offset'],
												)
											);
			foreach($charges->data as $charge)
			{
				$custid = $charge->source->customer;
				echo "<br>customer->".$charge->source->customer;	
				$CustomerSubscription = CustomerSubscription::find()->where(['stripe_customer_id'=>$custid])->one();
				if(sizeof($CustomerSubscription)>0)
				{
					$customer_id = $CustomerSubscription->customer_id;
					$custinfo = CustomerEntityInfo::find()->where(['customer_id'=>$customer_id])->one();
					echo  '->email:'.$custinfo->email;
				}
			}	
		}
	}
	public function actionCreatemanualorder(){
		$backurl = $_SERVER['HTTP_REFERER'];
		
		if(isset($_POST['manualorder']) && $_POST['manualorder']['customer_id']>0){
			$product = $_POST['manualorder'];
			$customer_id = $product['customer_id'];
			$order_line_items = array();
			$error = array();
			
			$customer_status = CustomerSubscription::find()->where(['customer_id' => $customer_id])->orderBy(['id'=>SORT_DESC])->one();
			$issued_date = date('M d Y',strtotime($customer_status->created_at));
		
			$ship_date = strtotime($issued_date);
			$ship_date = strtotime("+7 day", $ship_date);
			$ship_date = date('M d Y', $ship_date);
		
			$accesstoken = "Bearer ".Yii::$app->tradeGeckoHelper->Randomtradegeckoapi();
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
				Yii::$app->session->setFlash('error','No company(company code) on tradegecko exist for customer id - '.$customer_id);
				return $this->redirect($backurl);
			}
			
			$company_id = $ResponseCompany['companies'][0]['id'];
		
			if($company_id == 0) {
				Yii::$app->session->setFlash('error','No company ID exist for customer - '.$customer_id);
				return $this->redirect($backurl);				
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
			//print_r($product);die;
			if($_SERVER['REMOTE_ADDR'] == '122.160.154.228'){
				//print_r($product);die;
			}
			$productsTradegecko = array();
			for($i=1;$i<=6;$i++){
				if($product['product'.$i]['productid']>0){
					if(($product['product'.$i]['sku']!=''  || $product['product'.$i]['sku']!=null) && ($product['product'.$i]['variation_id']!='') &&
						($product['product'.$i]['retail_price']!='' && $product['product'.$i]['retail_price']!='null')
					){
						if($ResponseAddress['addresses'][0]['state']=='CA')
						{
							$order_line_items[] = array('quantity'=>1,'price'=>$product['product'.$i]['retail_price'], 'variant_id'=>$product['product'.$i]['variation_id'], "tax_rate_override"=> "9.0");
						}
						else
						{
							$order_line_items[] = array('quantity'=>1,'price'=>$product['product'.$i]['retail_price'], 'variant_id'=>$product['product'.$i]['variation_id']);
						}
						$productsTradegecko[]= $product['product'.$i]['sku'];
					}else{
						$error[] = "SKU Or Variant Or Retail Price is missing for your Selected Product ".$product['product'.$i]['productid'];
					}
					
				}else{
					unset($product['product'.$i]);					
				}
			}
			
			if(sizeof($error)>0){
				Yii::$app->session->setFlash('error',$error[0]);
				return $this->redirect($backurl);	
			}
			if(sizeof($order_line_items) == 0){
				Yii::$app->session->setFlash('error','Please Select the Prodcut and Variation for Customer Order!');
				return $this->redirect($backurl);	
			}
			
			if($ResponseAddress['addresses'][0]['state']=='CA' || $ResponseAddress['addresses'][0]['state']=='AZ' || $ResponseAddress['addresses'][0]['state']=='NV' || $ResponseAddress['addresses'][0]['state']=='OR')
			{
				$order_line_items[] = array('quantity'=>1,'price'=>8.5, 'label'=>'Shipping', 'freeform'=>true);
			}
			else
			{
				$order_line_items[] = array('quantity'=>1,'price'=>13.5, 'label'=>'Shipping', 'freeform'=>true);
			}
		
			$req = array('order' => array(
										'company_id' => $company_id,
										'billing_address_id' => $billing_address_id,
										'shipping_address_id' => $shipping_address_id,
										'email'=> $email_id,
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
				$date = date('Y-m');
				$orderLog = new TradegeckoOrderLog();
				$orderLog->customer_id = $customer_id;
				$orderLog->created_at = $date;
				$orderLog->import_csv_title = 'manual order';
				$orderLog->tradegecko_order_number = $Response->order->order_number;
				$orderLog->tradegecko_order_id = $Response->order->id;
				$orderLog->save();
				if(count($productsTradegecko)>0) {
					foreach($productsTradegecko as $item_sku) {
						$orderItemLog = new TradegeckoOderItemsLog();
						$orderItemLog->log_order_id = $orderLog->id;
						$orderItemLog->customer_id = $customer_id;
						$orderItemLog->item_id = $item_sku;
						$orderItemLog->save();
					}
				}
				Yii::$app->session->setFlash('success','Order Has been Created Successfully!.Tradegecko Order id is '.$Response->order->id);
				return $this->redirect($backurl);			
			}else{
				Yii::$app->session->setFlash('error','Fail To Create The Order!');
				return $this->redirect($backurl);
			}
		}
	}
	
}
