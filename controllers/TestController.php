<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\data\Pagination;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\models\TradegeckoInfo;
use app\models\TradegeckoOrderLog;
use app\models\TradegeckoOderItemsLog;

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

/**
 * PostController implements the CRUD actions for Post model.
 */
class TestController extends \app\components\AController
{
    /**
     *
     */
    public function actionStripe(){
        $stripeSecret_key = Yii::$app->params['stripe']['stripesecretkey'];
        Stripe::setApiKey($stripeSecret_key);
        //$all_customer = Stripe_Customer::all();
        $charges = Stripe_Charge::all();
        print_r($charges);
        echo "hello->".Yii::$app->params['stripe']['stripesecretkey'];
    }

    public function actionSesMail() {
        $send = Yii::$app->mailer->compose()
            ->setTo('florinp94@gmail.com')
            ->setFrom(Yii::$app->params['campaignEmail'])
            ->setSubject('Test ses email')
            ->setTextBody('Test ses email')
            ->setHtmlBody('<h1>Test ses email</h1>')
            ->send();

        VarDumper::dump(Yii::$app->mailer->transport, 10, true);

        echo VarDumper::dumpAsString($send, 10, true);
    }

   
    /**
     *
     */
    public function actionCheckorder(){

        # ------------------------------------------------------------------------------------------------
        # TradeGeko  API
        # ------------------------------------------------------------------------------------------------

        $accesstoken = "Bearer ".Yii::$app->params['tradegecko']['token'];
        # ------------------------------------------------------------------------------------------------
        # Create Request Header
        # ------------------------------------------------------------------------------------------------
        $headr = array();
        //$headr[] = 'Content-length: '.strlen($customer_parameter);
        $headr[] = 'Content-type: application/json';
        $headr[] = 'Authorization: '.$accesstoken;
        # ------------------------------------------------------------------------------------------------
        # Initiate cURL, Set Option for URL, Header, Return data and then close cUrl
        # ------------------------------------------------------------------------------------------------

        $url ="https://api.tradegecko.com/companies/3599958";
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
        //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_POSTFIELDS,$address);
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $customer = curl_exec($ch);
        curl_close($ch);
        $customer = json_decode($customer);
        $assigne_id = $customer->company->assignee_id;
        $company_code = $customer->company->company_code;
        $email = $customer->company->email;

        $url = "https://api.tradegecko.com/addresses?company_id=".$company_id;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $ResponseAddress = curl_exec($ch);
        curl_close($ch);
        $ResponseAddress = json_decode($ResponseAddress, TRUE);
        //print_r($company_code);
    }

    /**
     *
     */
    public function actionCustomerimport(){
        //echo "hello";die;
        $url ="https://api.tradegecko.com/companies?limit=10000&page=1";//limit=5000&page=1
        # ------------------------------------------------------------------------------------------------
        # TradeGeko  API
        # ------------------------------------------------------------------------------------------------

        $accesstoken = "Bearer ".Yii::$app->params['tradegecko']['token'];
        # ------------------------------------------------------------------------------------------------
        # Create Request Header
        # ------------------------------------------------------------------------------------------------
        $headr = array();
        //$headr[] = 'Content-length: '.strlen($customer_parameter);
        $headr[] = 'Content-type: application/json';
        $headr[] = 'Authorization: '.$accesstoken;
        # ------------------------------------------------------------------------------------------------
        # Initiate cURL, Set Option for URL, Header, Return data and then close cUrl
        # ------------------------------------------------------------------------------------------------
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headr);
        //curl_setopt($ch, CURLOPT_HEADER, TRUE);
        //curl_setopt($ch, CURLOPT_NOBODY, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($ch);
        curl_close($ch);
        # ------------------------------------------------------------------------------------------------
        # Decode jason so PHP understands
        # ------------------------------------------------------------------------------------------------
        $results = json_decode($head, TRUE);
        echo count($results['companies']);
        for($i=0; $i<=count($results['companies']); $i++) {
            if(isset($results['companies'][$i]["company_type"]) && $results['companies'][$i]["company_type"]!=null){
                $company_type = $results['companies'][$i]["company_type"];

                if ($company_type==="consumer"){
                    if($results['companies'][$i]["company_code"]>0){
                        $tradegecko = TradegeckoInfo::find()->where(['customer_id'=>$results['companies'][$i]["company_code"]])
                            ->orderBy(['id'=>SORT_DESC])->one();
                        if(sizeof($tradegecko)<=0){
                            echo "<br>".$i.")TG Company ID: ".$results['companies'][$i]["id"]." Trendy Butler User Id: ".$results['companies'][$i]["company_code"];
                            /*$created_at = date('Y-m-d');
                            $tradegecko = new TradegeckoInfo();
                            $tradegecko->customer_id = $results['companies'][$i]["company_code"];
                            $tradegecko->company_id = $results['companies'][$i]["id"];
                            $tradegecko->created_at = $created_at;
                            $tradegecko->save();*/
                        }
                    }else{
                        //echo '<br>'.$results['companies'][$i]["id"];
                    }
                    //die;
                }
            }
        }
        echo "<br>Done..";
    }

    /**
     *
     */
    public function actionImportorder(){
        //$accesstoken = "Bearer ".Yii::$app->params['tradegecko']['token'];
        $ranNumber=rand(1, 8);

        switch ($ranNumber) {
            case '1':
                $accesstoken = "Bearer 0e2aeba42311646706550a622ba8327dee6bf1d18b30743aa2a9c94664bb7fb5";
                break;
            case '2':
                $accesstoken = "Bearer c98c9e5098a57d4601a24fec718cd8967586b11f03e31fd0e7835876c0bdc737";
                break;
            case '3':
                $accesstoken = "Bearer 0e2aeba42311646706550a622ba8327dee6bf1d18b30743aa2a9c94664bb7fb5";
                break;
            case '4':
                $accesstoken = "Bearer 2d6d22d28589159055f5f53beeb325e336907f608f761e12d403534d9ba2db2a";
                break;
            case '5':
                $accesstoken = "Bearer a05b05acc15594df6e8c7764f285eff4af09c40d94fb3878dffb1d7d1af616f5";
                break;
            case '6':
                $accesstoken = "Bearer 6d89130532823ca8835782f977a2d3dcada3de47387cf31297ccf3947c848149";
                break;
            case '7':
                $accesstoken = "Bearer 4e1660749bf2055e015500f9fb933dce508bf4e62f6a246dc09fe1959cfe75b7";
                break;
            case '8':
                $accesstoken = "Bearer 9a38884332094b1ffcd7f7f88113bce33f824de7de17cfcac4c4a677732dff92";
                break;
            default:
                $accesstoken = "Bearer ".Yii::$app->params['tradegecko']['token'];
                break;
        }

        # ------------------------------------------------------------------------------------------------
        # TradeGeko  API
        # ------------------------------------------------------------------------------------------------
        $url ="https://api.tradegecko.com/orders?limit=".$_GET['limit']."&page=".$_GET['page'];//limit=5000&page=1

        # ------------------------------------------------------------------------------------------------
        # Create Request Header
        # ------------------------------------------------------------------------------------------------
        $headr = array();
        $headr[] = 'Content-length: 0';
        $headr[] = 'Content-type: application/json';
        $headr[] = 'Authorization: '.$accesstoken;
        # ------------------------------------------------------------------------------------------------
        # Initiate cURL, Set Option for URL, Header, Return data and then close cUrl
        # ------------------------------------------------------------------------------------------------
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headr);
        //curl_setopt($ch, CURLOPT_HEADER, TRUE);
        //curl_setopt($ch, CURLOPT_NOBODY, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($ch);
        curl_close($ch);
        # ------------------------------------------------------------------------------------------------
        # Decode jason so PHP understands
        # ------------------------------------------------------------------------------------------------
        $results = json_decode($head, TRUE);
        //print_r($results);die;



        if(isset($results['orders']) && sizeof($results['orders']) > 0){
            //echo 'Total Order->'.count($results['orders']);
            $count = 1;
            for($i=0; $i<count($results['orders']); $i++) {
                $url = "https://api.tradegecko.com/companies/".$results['orders'][$i]["company_id"];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$url);
                curl_setopt($ch, CURLOPT_HTTPHEADER,$headr);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                $headC = curl_exec($ch);
                curl_close($ch);

                $company = json_decode($headC, TRUE);
                if(isset($results['orders'][$i]["order_line_item_ids"]) && sizeof($results['orders'][$i]["order_line_item_ids"])>0){
                    if(sizeof($results['orders'][$i]["order_line_item_ids"])>0){
                        $item =array();
                        foreach($results['orders'][$i]["order_line_item_ids"] as $item_id){

                            if($item_id >0){
                                $url = "https://api.tradegecko.com/order_line_items/".$item_id;
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL,$url);
                                curl_setopt($ch, CURLOPT_HTTPHEADER,$headr);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                                $headC = curl_exec($ch);
                                curl_close($ch);

                                $variants = json_decode($headC, TRUE);
                                if(isset($variants['order_line_item']['variant_id']) && $variants['order_line_item']['variant_id']>0){

                                    $url = "https://api.tradegecko.com/variants/".$variants['order_line_item']['variant_id'];
                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL,$url);
                                    curl_setopt($ch, CURLOPT_HTTPHEADER,$headr);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                                    $headC = curl_exec($ch);
                                    curl_close($ch);
                                    $products = json_decode($headC, TRUE);
                                    $sku = '';
                                    if(isset($products['variant']['sku']) && $products['variant']['sku'] !=null){
                                        $item[] = $products['variant']['sku'];
                                        $sku = $products['variant']['sku'].','.$sku;
                                    }
                                }
                            }
                        }
                        if(isset($company['company']["company_code"]) && isset($results['orders'][$i]["id"]) && isset($results['orders'][$i]["order_number"]) && isset($results['orders'][$i]["issued_at"]))
                        {
                            echo " <br>".$count.")TG Company Code: ".$company['company']["company_code"].
                                " | TradeGeko order_Id: ".$results['orders'][$i]["id"].
                                " | TG Order Number: ".$results['orders'][$i]["order_number"];
                            echo " | SKU ->[".$sku."]' | TG Issued At: ".$results['orders'][$i]["issued_at"];
                            //print_r($item);
                            $count++;
                            $TradegeckoOrderLog = TradegeckoOrderLog::find()->where(['tradegecko_order_id'=>$results['orders'][$i]["id"]])->one();
                            if(sizeof($TradegeckoOrderLog)<=0){
                                $TradegeckoOrderLog = new TradegeckoOrderLog();
                                $TradegeckoOrderLog->customer_id = $company['company']["company_code"];
                                $TradegeckoOrderLog->created_at = $results['orders'][$i]["issued_at"];
                                $TradegeckoOrderLog->import_csv_title = 'manual order';
                                $TradegeckoOrderLog->tradegecko_order_number = $results['orders'][$i]["order_number"];
                                $TradegeckoOrderLog->tradegecko_order_id =  $results['orders'][$i]["id"];
                                $TradegeckoOrderLog->save();
                                if(sizeof($item)>0){
                                    foreach($item as $sku){
                                        $TradegeckoOderItemsLog = new TradegeckoOderItemsLog();
                                        $TradegeckoOderItemsLog->log_order_id = $TradegeckoOrderLog->id;
                                        $TradegeckoOderItemsLog->item_id = $sku;
                                        $TradegeckoOderItemsLog->customer_id = $company['company']["company_code"];
                                        $TradegeckoOderItemsLog->save();
                                    }
                                }
                            }
                        }
                    }
                }
                //die;
            }
        }else{
            print_r($results);
        }
    }
}
