<?php

namespace app\controllers;
use app\models\Order;
use app\models\OrderLog;
use app\models\OrderSearch;
use app\models\ProductVariants;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;

use app\models\CustomerSubscription;
use app\models\CustomerEntityInfo;
use app\models\CustomerEntityAddress;
use app\models\TbAttributeDetail;
use app\models\CustomerEntityAttribute;
use app\models\TradegeckoOrderLog;
use app\models\TradegeckoOrderLogSearch;
use app\models\TradegeckoInfo;
use app\models\TradegeckoOderItemsLog;
use app\models\Statuslog;

use app\models\Product;
use app\models\Brand;
use app\models\Category;
use app\models\Productattribute;
use app\models\Productattributeoption;
use app\models\Productattributevalue;
use app\models\Variants;
use app\models\Variantsimage;

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
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\Response;

/***************** Stripe Library End *****************/

class ManualorderController extends \app\components\AController
{
    public function actionIndex($order = 1) {
        $orderPage = $order - 1;

        $filters = Yii::$app->request->get('Filter');
        if($filters) {
            Yii::$app->session['Filter'] = $filters;
            $this->redirect(['index', 'order' => $order]);
        }

        $params = isset(Yii::$app->session['Filter']) ? Yii::$app->session['Filter'] : [];
        $params['orderPage'] = $orderPage;

        $orderSearch = new OrderSearch();
        $dataProvider = $orderSearch->search(['OrderSearch' => $params]);

        $model = null;
        $customer = null;
        $customerAddress = null;
        $customerInfo = null;
        $customerPreviouslySentProducts = null;

        $latestOrders = Order::find();
        $latestOrders->joinWith(['orderLog']);
        $latestOrders->where(['order.userId' => Yii::$app->user->getId()])
            ->andWhere(['order.status' => Order::STATUS_SENT])
            ->orderBy('orderlog.id desc')
            ->limit(2);
        $latestOrders = $latestOrders->all();

        $attributes = null;
        $items = [];

        /* @var $model Order */
        if(count($dataProvider->getModels()) > 0) {
            $model = $dataProvider->getModels()[$orderPage];
            $customer = $model->customer;
            $customerInfo = $customer->customerEntityInfo;
            $customerAddress = $customer->customerEntityAddress;
            $customerAttributes = $customer->customerEntityAttributes;
            $customerPreviouslySentProducts = count($customer->getPreviouslySentProducts()) > 0 ? $customer->getPreviouslySentProducts() : null;
            $attributes = [];
            foreach($customerAttributes as $customerAttribute) {
                $attributes[] = [
                    'name' => $customerAttribute->tbAttribute->tooltip_bar_title,
                    'value' => $customerAttribute->tbAttributeDetail->title,
                    'attribute_id' => $customerAttribute->tbAttributeDetail->tb_attribute_id,
                ];
            }
            if(count($model->items) > 0 && count($model->items) < 7) {
                foreach($model->items as $item) {
                    $product = ProductVariants::find()->where(['sku' => $item])->one();
                    $items[] = $product;
                }
            }
        } elseif($order > $dataProvider->getTotalCount()) {
            $this->redirect(['index', 'order' => $dataProvider->getTotalCount()]);
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'model' => $model,
            'customerInfo' => $customerInfo,
            'customerAddress' => $customerAddress,
            'customerAttributes' => $attributes,
            'previouslySentProducts' => $customerPreviouslySentProducts,
            'latestOrders' => $latestOrders,
            'order' => $order,
            'items' => $items
        ]);
    }

    public function actionResetFilters() {
        unset(Yii::$app->session['Filter']);
        $this->redirect('index');
    }

    public function actionSubmitOrder() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if(Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $errors = [];
            $items = [];
            $data = Yii::$app->request->post();
            $orderId = $data['orderId'];
            $order = Order::find()->where(['id' => $orderId])->one();
            if(isset($data['items']) && count($data['items']) > 0) {
                foreach($data['items'] as $item) {
                    $variant = ProductVariants::find()->where(['sku' => $item])->one();
                    if($variant) {
                        if((int)$variant->availableStock > 0) {
                            $items[] = $item;
                        } else {
                            $errors[] = "You don't have any items left for " . $variant->product->name;
                        }
                    } else {
                        $errors[] = "The product (". $item .") doesn't exist";
                    }
                }
            } else {
                $errors[] = "No items found";
            }

            if(count($items) > 0 && empty($errors)) {
                // export customers to tradegecko
                $exportResponse = $order->customer->exportToTradegecko();
                $response = $order->send($items, $exportResponse);

                return [
                    'success' => true
                ];
            } else {
                Yii::$app->getSession()->setFlash('fail', $errors);
            }
        } else {
            throw new HttpException(403, "The request is not valid");
        }
    }

    public function actionIndexold()
    {
		if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
		$current_day = date('d');
		
		if($current_day<14){
			$charge_start_date = strtotime( date('Y-m-14') . " -1 month " );
			$charge_start_date = date('Y-m-d',$charge_start_date);
		}else{
			$charge_start_date = date('Y-m-14');
		}
		
		$nextmonthDate = strtotime( $charge_start_date . " +1 month " );
		$charge_end_date = date("Y-m-d", $nextmonthDate);
		
		$limit=1;
		
		if(isset($_GET['p'])){
			$offset = $_GET['p']-1;
			$search = array();
			if(isset($_GET['style']) && $_GET['style']!=''){
				$search[] = $_GET['style'];
				$Filterattribute['1'] = $_GET['style'];
			}
			if(isset($_GET['tsize']) && $_GET['tsize']!=''){
				$search[] = $_GET['tsize'];
				$Filterattribute['4'] = $_GET['tsize'];
			}
			if(isset($_GET['bsize']) && $_GET['bsize']!=''){
				$search[] = $_GET['bsize'];
				$Filterattribute['5'] =$_GET['bsize'];
			}
			if(isset($_GET['bfit']) && $_GET['bfit']!=''){
				$search[] = $_GET['bfit'];
				$Filterattribute['6'] = $_GET['bfit'];
			}
			if(sizeof($search)== 1){
				if(isset($_GET['state']) && $_GET['state']!='' && isset($_GET['mailid']) && $_GET['mailid']!=''){
					$query = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN(select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."'))) limit '".$limit."'  offset '".$offset."'";
					$countuser = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN(select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."')))";
					
					$countuser =  sizeof(Statuslog::findBySql($countuser)->all());
					$ChargedUser = Statuslog::findBySql($query)->all();
					
				}else if(isset($_GET['state']) && $_GET['state']!=''){
					$query = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."')) limit '".$limit."'  offset '".$offset."'";
					$countuser = "select count(customer_id) from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."')) ";
					
					$countuser =  sizeof(Statuslog::findBySql($countuser)->all());
					$ChargedUser = Statuslog::findBySql($query)->all();
				}else if(isset($_GET['mailid']) && $_GET['mailid']!=''){
					$query = "select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."')) limit '".$limit."'  offset '".$offset."'";
					$countuser = "select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."')) ";
					
					$countuser =  sizeof(Statuslog::findBySql($countuser)->all());
					$ChargedUser = Statuslog::findBySql($query)->all();
				}else{
					
					$query = "select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."') limit '".$limit."'  offset '".$offset."'";
					$countuser = "select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."')";
					
					$countuser =  sizeof(Statuslog::findBySql($countuser)->all());
					$ChargedUser = Statuslog::findBySql($query)->all();
					
					
				}
			}else if(sizeof($search)== 2){
				if(isset($_GET['state']) && $_GET['state']!='' && isset($_GET['mailid']) && $_GET['mailid']!=''){
					$query = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN(select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."')))) limit '".$limit."'  offset '".$offset."'";
					$ChargedUser = Statuslog::findBySql($query)->all();
					
					$countuser = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN(select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."')))) ";
					$countuser =  sizeof(Statuslog::findBySql($countuser)->all());
				}else if(isset($_GET['state']) && $_GET['state']!=''){
					$query = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."'))) limit '".$limit."'  offset '".$offset."'";
					$ChargedUser = Statuslog::findBySql($query)->all(); 
				
					$countuser = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."'))) ";
					$countuser =  sizeof(Statuslog::findBySql($countuser)->all());
				}else if(isset($_GET['mailid']) && $_GET['mailid']!=''){
					$query = "select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."'))) limit '".$limit."'  offset '".$offset."'";
					$ChargedUser = Statuslog::findBySql($query)->all();  
				
					$countuser = "select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."'))) ";
					$countuser =  sizeof(Statuslog::findBySql($countuser)->all());
				}else{
					$query = "select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."')) limit '".$limit."'  offset '".$offset."'";
					$ChargedUser = Statuslog::findBySql($query)->all();
				
					$countuser = "select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."')) ";
					$countuser =  sizeof(Statuslog::findBySql($countuser)->all());
				}	
				
			}else if(sizeof($search)== 3){
				if(isset($_GET['state']) && $_GET['state']!='' && isset($_GET['mailid']) && $_GET['mailid']!=''){
					$query = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN (select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."'))))) limit '".$limit."'  offset '".$offset."'";
					$ChargedUser = Statuslog::findBySql($query)->all();
				
					$countuser = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN (select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."'))))) ";
					$countuser =  sizeof(Statuslog::findBySql($countuser)->all());
				}else if(isset($_GET['state']) && $_GET['state']!=''){
					$query = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN (select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."')))) limit '".$limit."'  offset '".$offset."'";
					$ChargedUser = Statuslog::findBySql($query)->all(); 
					
					$countuser = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN (select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."')))) ";
					$countuser =  sizeof(Statuslog::findBySql($countuser)->all());
				}else if(isset($_GET['mailid']) && $_GET['mailid']!=''){
					$query = "select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN (select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."')))) limit '".$limit."'  offset '".$offset."'";
					$ChargedUser = Statuslog::findBySql($query)->all(); 
					
					$countuser = "select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN (select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."')))) ";
					$countuser =  sizeof(Statuslog::findBySql($countuser)->all());
				}else{
					$query = "select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."'))) limit '".$limit."'  offset '".$offset."'";
					$ChargedUser = Statuslog::findBySql($query)->all(); 
					
					$countuser = "select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."'))) ";
					$countuser =  sizeof(Statuslog::findBySql($countuser)->all());
				}
				
			}else if(sizeof($search)== 4){
				if(isset($_GET['state']) && $_GET['state']!='' && isset($_GET['mailid']) && $_GET['mailid']!=''){
					$query = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN (select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."' AND customer_id IN (select customer_id from customer_entity_attribute where value ='".$search[3]."')))))) limit '".$limit."'  offset '".$offset."'";
					$ChargedUser = Statuslog::findBySql($query)->all();
					
					$countuser = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN (select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."' AND customer_id IN (select customer_id from customer_entity_attribute where value ='".$search[3]."')))))) ";
					$countuser =  sizeof(Statuslog::findBySql($countuser)->all());
				}else if(isset($_GET['state']) && $_GET['state']!=''){
					$query = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN (select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."' AND customer_id IN (select customer_id from customer_entity_attribute where value ='".$search[3]."'))))) limit '".$limit."'  offset '".$offset."'";
					$ChargedUser = Statuslog::findBySql($query)->all(); 
					
					$countuser = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN (select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."' AND customer_id IN (select customer_id from customer_entity_attribute where value ='".$search[3]."'))))) ";
					$countuser =  sizeof(Statuslog::findBySql($countuser)->all());
				}else if(isset($_GET['mailid']) && $_GET['mailid']!=''){
					$query = "select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN (select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."' AND customer_id IN (select customer_id from customer_entity_attribute where value ='".$search[3]."'))))) limit '".$limit."'  offset '".$offset."'";
					$ChargedUser = Statuslog::findBySql($query)->all();  
					
					$countuser = "select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN (select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."' AND customer_id IN (select customer_id from customer_entity_attribute where value ='".$search[3]."'))))) ";
					$countuser =  sizeof(Statuslog::findBySql($countuser)->all());
				}else{
					$query = "select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."' AND customer_id IN (select customer_id from customer_entity_attribute where value ='".$search[3]."')))) limit '".$limit."'  offset '".$offset."'";
					$ChargedUser = Statuslog::findBySql($query)->all();	 
					
					$countuser = "select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."' AND customer_id IN (select customer_id from customer_entity_attribute where value ='".$search[3]."')))) ";
					$countuser =  sizeof(Statuslog::findBySql($countuser)->all());
				}
			}else if(sizeof($search) == 0 && isset($_GET['state']) && $_GET['state']!='' && isset($_GET['mailid']) && $_GET['mailid']!=''){
				$query = "select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN (select customer_id from customer_entity_info where email = '".$_GET['mailid']."')) limit '".$limit."'  offset '".$offset."'";
				$ChargedUser = Statuslog::findBySql($query)->all();
				
				$countuser = "select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN (select customer_id from customer_entity_info where email = '".$_GET['mailid']."')) ";
				$countuser =  sizeof(Statuslog::findBySql($countuser)->all());
				
			}else if(sizeof($search) == 0 && isset($_GET['state']) && $_GET['state']!=''){
				$query = "select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_address where state = '".$_GET['state']."') limit '".$limit."'  offset '".$offset."'";
				$ChargedUser = Statuslog::findBySql($query)->all(); 
				
				$countuser = "select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_address where state = '".$_GET['state']."')";
				$countuser =  sizeof(Statuslog::findBySql($countuser)->all());
				
			}else if(sizeof($search) == 0 && isset($_GET['mailid']) && $_GET['mailid']!=''){
				$query = "select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_info where email = '".$_GET['mailid']."') limit '".$limit."'  offset '".$offset."'";
				$ChargedUser = Statuslog::findBySql($query)->all();
				
				$countuser = "select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_info where email = '".$_GET['mailid']."')";
				$countuser =  sizeof(Statuslog::findBySql($countuser)->all());
			}else{
				$ChargedUser = Statuslog::find()->where(['status'=>'active'])
										->andWhere(['between', 'created_at', $charge_start_date, $charge_end_date])
										->limit($limit)
										->offset($offset)
										->all();
				
				return $this->render('indexold',['chargeduser' => $ChargedUser]);
			}
			return $this->render('indexold',['chargeduser' => $ChargedUser,'search'=>$search,'countuser'=>$countuser]);
			
		}else{
			$limit=1;
			$offset=0;
			
			$Filterattribute = array();
			$search =array();
				
			if(isset($_GET) && sizeof($_GET)>0){
				if($_GET['style']!=''){
					$search[] = $_GET['style'];
					$Filterattribute['1'] = $_GET['style'];
				}
				if($_GET['tsize']!=''){
					$search[] = $_GET['tsize'];
					$Filterattribute['4'] = $_GET['tsize'];
				}
				if($_GET['bsize']!=''){
					$search[] = $_GET['bsize'];
					$Filterattribute['5'] =$_GET['bsize'];
				}
				if($_GET['bfit']!=''){
					$search[] = $_GET['bfit'];
					$Filterattribute['6'] = $_GET['bfit'];
				}
				
				if(sizeof($search)== 1){
					if(isset($_GET['state']) && $_GET['state']!='' && isset($_GET['mailid']) && $_GET['mailid']!=''){
						$query = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN(select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."')))";
						$ChargedUser = Statuslog::findBySql($query)->all();
					}else if(isset($_GET['state']) && $_GET['state']!=''){
						$query = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."'))";
						$ChargedUser = Statuslog::findBySql($query)->all();
					}else if(isset($_GET['mailid']) && $_GET['mailid']!=''){
						$query = "select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."'))";
						$ChargedUser = Statuslog::findBySql($query)->all();
					}else{
						$query = "select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."')";
						$ChargedUser = Statuslog::findBySql($query)->all();  
					}
				}else if(sizeof($search)== 2){
					if(isset($_GET['state']) && $_GET['state']!='' && isset($_GET['mailid']) && $_GET['mailid']!=''){
						$query = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN(select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."'))))";
						$ChargedUser = Statuslog::findBySql($query)->all();  
					}else if(isset($_GET['state']) && $_GET['state']!=''){
						$query = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."')))";
						$ChargedUser = Statuslog::findBySql($query)->all();  
					}else if(isset($_GET['mailid']) && $_GET['mailid']!=''){
						$query = "select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."')))";
						$ChargedUser = Statuslog::findBySql($query)->all();  
					}else{
						$query = "select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."'))";
						$ChargedUser = Statuslog::findBySql($query)->all();  
					}	
					
				}else if(sizeof($search)== 3){
					if(isset($_GET['state']) && $_GET['state']!='' && isset($_GET['mailid']) && $_GET['mailid']!=''){
						$query = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN (select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."')))))";
						$ChargedUser = Statuslog::findBySql($query)->all();  
					}else if(isset($_GET['state']) && $_GET['state']!=''){
						$query = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN (select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."'))))";
						$ChargedUser = Statuslog::findBySql($query)->all();  
					}else if(isset($_GET['mailid']) && $_GET['mailid']!=''){
						$query = "select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN (select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."'))))";
						$ChargedUser = Statuslog::findBySql($query)->all();  
					}else{
						$query = "select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."')))";
						$ChargedUser = Statuslog::findBySql($query)->all();  
					}
					
				}else if(sizeof($search)== 4){
					if(isset($_GET['state']) && $_GET['state']!='' && isset($_GET['mailid']) && $_GET['mailid']!=''){
						$query = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN (select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."' AND customer_id IN (select customer_id from customer_entity_attribute where value ='".$search[3]."'))))))";
						$ChargedUser = Statuslog::findBySql($query)->all();  
					}else if(isset($_GET['state']) && $_GET['state']!=''){
						$query = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN (select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."' AND customer_id IN (select customer_id from customer_entity_attribute where value ='".$search[3]."')))))";
						$ChargedUser = Statuslog::findBySql($query)->all();  
					}else if(isset($_GET['mailid']) && $_GET['mailid']!=''){
						$query = "select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN (select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."' AND customer_id IN (select customer_id from customer_entity_attribute where value ='".$search[3]."')))))";
						$ChargedUser = Statuslog::findBySql($query)->all();  
					}else{
						$query = "select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."' AND customer_id IN (select customer_id from customer_entity_attribute where value ='".$search[3]."'))))";
						$ChargedUser = Statuslog::findBySql($query)->all();  
					}
				}else if(sizeof($search) == 0 && isset($_GET['state']) && $_GET['state']!='' && isset($_GET['mailid']) && $_GET['mailid']!=''){
					$query = "select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN (select customer_id from customer_entity_info where email = '".$_GET['mailid']."'))";
					$ChargedUser = Statuslog::findBySql($query)->all();  
					
				}else if(sizeof($search) == 0 && isset($_GET['state']) && $_GET['state']!=''){
					$query = "select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_address where state = '".$_GET['state']."')";
					$ChargedUser = Statuslog::findBySql($query)->all();  
					
				}else if(sizeof($search) == 0 && isset($_GET['mailid']) && $_GET['mailid']!=''){
					$query = "select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_info where email = '".$_GET['mailid']."')";
					$ChargedUser = Statuslog::findBySql($query)->all();  
				}
				
				return $this->render('indexold',['chargeduser' => $ChargedUser,'search'=>$search]);
			}else{
				$ChargedUser = Statuslog::find()->where(['status'=>'active'])
												->andWhere(['between', 'created_at', $charge_start_date, $charge_end_date])
												->limit($limit)
												->offset($offset)
												->all();
				return $this->render('indexold',['chargeduser' => $ChargedUser]);
			}
			
			
		}
		
    }
	public function selectcustomer($customer_id,$tb_attr_id,$tb_attr_detail_value){
		$CustomerEntityAttribute = CustomerEntityAttribute::find()->where(['customer_id'=>$customer_id,
																			'attribute_id' => $tb_attr_id,
																			'value' => $tb_attr_detail_value ])->one();
		if(sizeof($CustomerEntityAttribute)>0){
			return 1;
		}else{
			return 0;
		}																	
	}
	#---------------------------------------------------------------------------------#
							# All Tradegecko Product Variants #
	#---------------------------------------------------------------------------------#
	public function actionGetvariation(){
		if(isset($_POST['product_id']) && $_POST['product_id']>0){
			$Variations = Productattributevalue::find()->where(['product_entity_info_id' => $_POST['product_id']])
														->all();
			if(sizeof($Variations)){
				foreach($Variations as $variants){
					$Options = Productattributeoption::findOne($variants->attribute_option_id);
					if(sizeof(Variants::find()->where('variant_id ='.$variants->variant_id.' and qty>0')->one())>0)
					{
						$allVariation[$variants->attribute_id][$Options->attribute_value][] = $variants->variant_id;
					}
				}
				/* print_r($allVariation);die; */
				return json_encode($allVariation);
			}else{
				echo 'no variation found!';
			}													
		}
	}
	public function actionChangeprice(){
		$price = 0;
		$price = number_format((float)$price, 2, '.', '');
		$variantDetail = array('price'=>00.00,
								'img_url'=>Yii::$app->homeUrl.'demo/noimage.gif');
		if(isset($_POST['id']) && $_POST['id'] >0){
			$Variants = Variants::find()->where(['variant_id' => $_POST['id']])->one();
			if(sizeof($Variants)>0){
				$variantDetail['price'] =  number_format((float)$Variants['retail_price'], 2, '.', '');
				$Variants_image = Variantsimage::find()->where(['variant_id' => $Variants['id']])->one();
				if(sizeof($Variants_image)>0){
					$variantDetail['img_url'] = $Variants_image['img_url'];
				}
			}	
		}
		echo json_encode($variantDetail);
	}
	
	public function actionSubmitorderOld(){
		$backurl = $_SERVER['HTTP_REFERER'];
		$error = array();
		if(isset($_POST['manualorder']) && sizeof($_POST['manualorder'])>0){
			if(isset($_POST['manualorder']['customer_id']) && $_POST['manualorder']['customer_id']!= ''){
				$customer_id = $_POST['manualorder']['customer_id'];
				if(isset($_POST['manualorder']['variation_id']) && sizeof($_POST['manualorder']['variation_id'])>0){
					$order_line_items = array();
					$Productvariants = $_POST['manualorder']['variation_id'];
					$customer_status = CustomerSubscription::find()->where(['customer_id' => $customer_id])->orderBy(['id'=>SORT_DESC])->one();
					//print_r($customer_status);die;
					$issued_date = date('M d Y',strtotime($customer_status->created_at));
		
					$ship_date = strtotime($issued_date);
					$ship_date = strtotime("+7 day", $ship_date);
					$ship_date = date('M d Y', $ship_date);
					
				/************************ Check Company On Tradegecko ******************************/	
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
				/************************* Check Company Id At Tradegecko End ********************************/	
					
				/****************************** Get Company Address Detail ***********************************/
					$url = "https://api.tradegecko.com/addresses?company_id=".$company_id;
					$ch = curl_init();
					
					curl_setopt($ch, CURLOPT_URL,$url);
					curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
					$ResponseAddress = curl_exec($ch);
					curl_close($ch);
					$ResponseAddress = json_decode($ResponseAddress, TRUE);
					if(!isset($ResponseAddress['addresses']) || !isset($ResponseAddress['addresses'][0])) {
						Yii::$app->session->setFlash('error','No address exist for customer - '.$customer_id);
						return $this->redirect($backurl);
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
					/****************************** Get Company Address Detail End *******************************/
					/****************************** Prepare Order Line Items *************************************/
					$productSku = array();
					foreach($Productvariants as $variant_id){
						$Variant_detail = Variants::find()->where(['variant_id'=>$variant_id])->One();
						if($Variant_detail->retail_price>0 && $Variant_detail->sku!=null){
							if($ResponseAddress['addresses'][0]['state']=='CA')
							{
								$order_line_items[] = array('quantity'=>1,'price'=>$Variant_detail->retail_price, 'variant_id'=>$variant_id, "tax_rate_override"=> "9.0");
							}
							else
							{
								$order_line_items[] = array('quantity'=>1,'price'=>$Variant_detail->retail_price, 'variant_id'=>$variant_id);
							}
							$productSku[]= $Variant_detail->sku;
						}else{
							$error[] = "SKU Or Retail Price is missing for your Selected Product Variant ".$variant_id;
						}	
						
					}
					/*********************** Order Line Item Prepare End ***********************/
					if(sizeof($error)>0){
						Yii::$app->session->setFlash('fail',$error[0]);
						return $this->redirect($backurl);	
					}
					
					if(sizeof($order_line_items) == 0){
						Yii::$app->session->setFlash('fail','Please Select the Product and Variation for Customer Order!');
						return $this->redirect($backurl);	
					}
					/********************* Add Shipment Charge As Per Country ***********************/
					if($ResponseAddress['addresses'][0]['state']=='CA' || $ResponseAddress['addresses'][0]['state']=='AZ' || $ResponseAddress['addresses'][0]['state']=='NV' || $ResponseAddress['addresses'][0]['state']=='OR')
					{
						$order_line_items[] = array('quantity'=>1,'price'=>8.5, 'label'=>'Shipping', 'freeform'=>true);
					}
					else
					{
						$order_line_items[] = array('quantity'=>1,'price'=>13.5, 'label'=>'Shipping', 'freeform'=>true);
					}
					/*************************** Shipment Charge End ******************************/
					
					/**************** Create An Order At Tradegecko *********************************/
					
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
						$date = date('Y-m-d');
						$orderLog = new TradegeckoOrderLog();
						$orderLog->customer_id = $customer_id;
						$orderLog->created_at = $date;
						$orderLog->import_csv_title = 'manual order';
						$orderLog->tradegecko_order_number = $Response->order->order_number;
						$orderLog->tradegecko_order_id = $Response->order->id;
						$orderLog->save();
						$connection = Yii::$app->db;
						if(count($productSku)>0) {
							foreach($productSku as $item_sku) {
								$orderItemLog = new TradegeckoOderItemsLog();
								$orderItemLog->log_order_id = $orderLog->id;
								$orderItemLog->customer_id = $customer_id;
								$orderItemLog->item_id = $item_sku;
								$orderItemLog->save();
								
								$command = $connection->createCommand ( "update tb_product_variants set qty=(qty-1) where sku='".$item_sku."'" );
								$command->execute();//Decrease the quantity of products in our database
							}
						}
						Yii::$app->session->setFlash('success','Order Has been Created Successfully!.Tradegecko Order id is '.$Response->order->id);
						return $this->redirect($backurl);
						
					// -------------- Order Creation End --------------- //	
					}else{
						Yii::$app->session->setFlash('error','Fail To Create The Order!');
						return $this->redirect($backurl);
					}	
							
				}else{
					Yii::$app->session->setFlash('fail'," Fail to Create the Order!.Product Variations is Missing!.");
					return $this->redirect($backurl);
				}
			}else{
				//echo "not found";die;
				Yii::$app->session->setFlash('fail'," Fail to Create the Order!.Customer Id is Missing!.");
				return $this->redirect($backurl);	
			} 
		}
	}
}
