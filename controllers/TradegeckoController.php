<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\models\CustomerEntity;
use app\models\CustomerEntitySearch;
use app\models\CustomerEntitystatus;
use app\models\CustomerEntityInfo;
use app\models\CustomerEntityAddress;
use app\models\CustomerEntityAttribute;
use app\models\TbAttribute;
use app\models\TbAttributeDetail;
use app\models\TradegeckoOrderLog;
use app\models\TradegeckoOderItemsLog;
use yii\helpers\Url;
/**
 * PostController implements the CRUD actions for Post model.
 */
class TradegeckoController extends Controller
{
    public function actionIndex(){
        if (Yii::$app->user->isGuest){
            $this->redirect(array('site/dashboard'));
        }else{

            $Response = array();
            $data = '';
            $accesstoken = "Bearer ".Yii::$app->params['tradegecko']['token'];


            $headr = array();
            $headr[] = 'Content-length: '.strlen($data);
            $headr[] = 'Content-type: application/json';
            $headr[] = 'Authorization: '.$accesstoken;

            $page = 1;
            $limit = 100;
            if(isset($_GET['limit']) && $_GET['limit']>0){
                $limit = $_GET['limit'];
            }
            if(isset($_GET['p']) && $_GET['p']!=null){
                $page = $_GET['p'];
            }
            //	$page = 1;
            $url = "https://api.tradegecko.com/orders?limit=".$limit."&page=".$page."&status=finalized";
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headr);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $Response = curl_exec($ch);
            curl_close($ch);
            $Response = json_decode($Response, TRUE);

            return $this->render('tradegeckoorder.php',[
                'order' => $Response,
            ]);
        }


    }
    public function actionSendtoliteview(){
        print_r($_GET);
    }

    public function actionCheckorder()
    {
        if (Yii::$app->user->isGuest){
            $this->redirect(array('site/dashboard'));
        }else{

            $offset = Yii::$app->request->get('offset') ;

            $tradegeckoOrder = TradegeckoOrderLog::find()->orderBy(['id' => SORT_DESC])->offset($offset)->limit(25)->All();

            $from_log_id = $tradegeckoOrder[0]->id;
            $to_log_id = $tradegeckoOrder[sizeof($tradegeckoOrder)-1]->id;
            $DataProvider = array();
            $i=1;
            if($offset !== null && $offset !=0){
                $next_offset = $offset+25;
                $prev_offset = $next_offset-50;
            }else{
                $prev_offset = 0;
                $next_offset = 25;
            }
            if($prev_offset < 0)
            {
                $prev_offset=0;
            }
            foreach($tradegeckoOrder as $key=>$data)
            {
                $DB_items = array();
                foreach($data->items as $itm){
                    $DB_items[] = $itm->item_id;
                }

                $status = "SAME";
                $token = Yii::$app->params ['tradegeckoapi'] [mt_rand ( 0, count ( Yii::$app->params ['tradegeckoapi'] ) - 1 )];
                $accesstoken = "Bearer ".$token;
                $header = array();
                $header[] = 'Content-type: application/json';
                $header[] = 'Authorization: '.$accesstoken;

                $url = "https://api.tradegecko.com/orders/".$data['tradegecko_order_id'];
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL,$url);
                curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                $ResponseOrder = curl_exec($ch);
                curl_close($ch);
                $ResponseOrder = json_decode($ResponseOrder,TRUE);

                if(sizeof($ResponseOrder)>0)
                {
                    if(isset($ResponseOrder['order']) && sizeof($data->items) == sizeof($ResponseOrder['order']['order_line_item_ids'])-1)
                    {
                        $DP_variants = array();
                        foreach($ResponseOrder['order']['order_line_item_ids'] as $k=>$v)
                        {
                            $url = "https://api.tradegecko.com/order_line_items/".$v;
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL,$url);
                            curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                            $OrderItemResponse = curl_exec($ch);
                            curl_close($ch);
                            $OrderItemResponse = json_decode($OrderItemResponse,TRUE);

                            if($OrderItemResponse['order_line_item']['variant_id'] !=null){

                                $url = "https://api.tradegecko.com/variants/".$OrderItemResponse['order_line_item']['variant_id'];
                                $variantResponse = array();
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL,$url);
                                curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                                $variantResponse = curl_exec($ch);
                                curl_close($ch);
                                $variantResponse = json_decode($variantResponse, TRUE);
                                if(isset($variantResponse['variant']['sku']))
                                {
                                    $DP_variants[] = $variantResponse['variant']['sku'];
                                }
                                if(!in_array($variantResponse['variant']['sku'], $DB_items))
                                {
                                    foreach($DP_variants as $ky=>$vl)
                                    {
                                        if($vl == $variantResponse['variant']['sku'])
                                        {
                                            $DP_variants[$ky] = "<strong style='color:red;'>".$DP_variants[$ky]."</strong>";
                                        }
                                    }
                                    $status = "DIFF";
                                }
                            }
                            if($k ==  sizeof($ResponseOrder['order']['order_line_item_ids'])-1 && $status == "DIFF")
                            {
                                asort($DB_items);
                                asort($DP_variants);
                                $DataProvider[$i]['ORDER_ID'] = $data['tradegecko_order_id'];
                                $DataProvider[$i]['LOG_ORDER_ID'] = $data['id'];
                                $DataProvider[$i]['Cus_ID']= $data['customer_id'];
                                $DataProvider[$i]['DATE'] = $data['created_at'];
                                $DataProvider[$i]['EMAIL'] = $data->email;
                                //$DataProvider[$i]['CSV'] = $data['import_csv_title'];
                                $DataProvider[$i]["CUSTOMER"] = $data->Usernames();
                                $DataProvider[$i]['DB'] = $DB_items;
                                $DataProvider[$i]['Tradegecko'] = $DP_variants;
                                $DataProvider[$i]['FLAG'] = $status;
                            }

                        }
                    }
                }$i++;
            }

            return $this->render('_match',[
                'DataProvider'=>$DataProvider,
                'offset'=>$offset,
                'next'=>$next_offset,
                'prev'=>$prev_offset,
                'from'=>$from_log_id,
                'to'=>$to_log_id,
            ]);
        }
    }

    public function actionUpdateorder()
    {

        if (Yii::$app->user->isGuest){
            $this->redirect(array('site/dashboard'));
        }else{
            $post = Yii::$app->request->post();
            $offset = $post['offset'];
            $Orders = json_decode($post['order_data'],TRUE);
            if(sizeof($Orders)>0){
                $keys = $post['keys'];
                if(sizeof($keys)>0)
                {
                    $keys = explode(',',$keys);
                }
                $i=1;

                foreach($Orders as $key=>$order)
                {
                    $order_id = $order['ORDER_ID'];
                    $customer_id = $order['Cus_ID'] ;
                    $log_order_id = $order['LOG_ORDER_ID'];
                    $order_DB = $order['DB'];
                    $order_TG = $order['Tradegecko'];

                    if(is_array($keys) && in_array($i, $keys)){

                        $order_items = TradegeckoOderItemsLog::deleteAll(['log_order_id'=>$log_order_id]);
                        foreach($order_TG as $tg_sku){
                            $item = new TradegeckoOderItemsLog();
                            $item->customer_id = $customer_id;
                            $item->log_order_id = $log_order_id;
                            $item->item_id = strip_tags($tg_sku);
                            $item->save();
                        }
                    }
                    $i++;
                }
            }
            $this->redirect(Url::to(['tradegecko/checkorder', 'offset' => $offset]));
        }
    }

    public function actionList()
    {
        if (Yii::$app->user->isGuest){
            $this->redirect(array('site/dashboard'));
        }else{

            $Response = array();
            $DIFF['orders'] = array();
            $data = '';
            $accesstoken = "Bearer ".Yii::$app->params ['tradegeckoapi'] [mt_rand ( 0, count ( Yii::$app->params ['tradegeckoapi'] ) - 1 )];


            $headr = array();
            $headr[] = 'Content-length: '.strlen($data);
            $headr[] = 'Content-type: application/json';
            $headr[] = 'Authorization: '.$accesstoken;

            $page = 1;
            $limit = 100;
            if(isset($_GET['limit']) && $_GET['limit']>0){
                $limit = $_GET['limit'];
            }
            if(isset($_GET['p']) && $_GET['p']!=null){
                $page = $_GET['p'];
            }
            //	$page = 1;
            $url = "https://api.tradegecko.com/orders?limit=".$limit."&page=".$page."&status=finalized";
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headr);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $Response = curl_exec($ch);
            curl_close($ch);
            $Response = json_decode($Response, TRUE);
            if(sizeof($Response)>0){
                foreach($Response['orders'] as $order_no=>$order)
                {
                    $check_DB = TradegeckoOrderLog::find()->where(['tradegecko_order_id'=>$order['id']])->One();;
                    if(sizeof($check_DB)>0)
                    {
                        continue;
                    }else{
                        $DIFF['orders'][] = $order;
                    }
                }
            }

            return $this->render('list.php',[
                'order' => $DIFF,
            ]);
        }
    }

   public function actionDuplicateorders(){
        if (Yii::$app->user->isGuest){
             $this->redirect(array('site/dashboard'));
                }else{

                    $Response = array();
                    $users['orders'] = array();
                    $data = '';
                    $from = null;
                    $to = null;
                    $accesstoken = "Bearer ".Yii::$app->params ['tradegeckoapi'] [mt_rand ( 0, count ( Yii::$app->params ['tradegeckoapi'] ) - 1 )];

                    $headr = array();
                    $headr[] = 'Content-length: '.strlen($data);
                    $headr[] = 'Content-type: application/json';
                    $headr[] = 'Authorization: '.$accesstoken;
                    
                    $page = 1;
                    $limit = 100;
                    if(isset($_GET['limit']) && $_GET['limit']>0){
                        $limit = $_GET['limit'];
                    }
                    if(isset($_GET['p']) && $_GET['p']!=null){
                        $page = $_GET['p'];
                    }
                    $url = "https://api.tradegecko.com/orders?limit=".$limit."&page=".$page."&status=finalized";
                    $ch = curl_init();
                    
                    curl_setopt($ch, CURLOPT_URL,$url);
                    curl_setopt($ch, CURLOPT_HTTPHEADER,$headr);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    $Response = curl_exec($ch);
                    curl_close($ch);
                    $Response = json_decode($Response, TRUE);
                    
                    if(sizeof($Response)>0){
                        $customers = array();
                        $from = $Response['orders'][0]['order_number'];
                        $to = $Response['orders'][sizeof($Response['orders'])-1]['order_number'];
                        foreach($Response['orders'] as $order_no=>$order)
                        {
                            $customers[]= $order['company_id']; 
                        }

                        if(sizeof($customers)>0)
                        {
                            foreach($customers as $customer)
                            {
                                $url = "https://api.tradegecko.com/orders?company_id=".$customer."&status=finalized";

                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL,$url);
                                curl_setopt($ch, CURLOPT_HTTPHEADER,$headr);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                                $Customer_orders = curl_exec($ch);
                                curl_close($ch);
                                $Customer_orders = json_decode($Customer_orders, TRUE);

                                if(sizeof($Customer_orders)>0)
                                {
                                    
                                    foreach($Customer_orders['orders'] as $no=>$order_detail)
                                    {
                                        foreach($Customer_orders['orders'] as $key=>$value)
                                        {
                                            $order_issued = date('Y-m',strtotime($order_detail['issued_at']));
                                            $current_date = date('Y-m-14');
                                            $prev_date = date('Y-m-14',strtotime('-1 month'));
                                            $value_issued = date('Y-m',strtotime($value['issued_at']));
                           
                                            if($key!=$no && ($order_issued>$prev_date && $order_issued<$current_date))
                                            {
                                                $users['orders'][$order_detail['company_id']][$value['id']]= $value;
                                                $users['orders'][$order_detail['company_id']][$order_detail['id']] = $order_detail;
                                            }
                                        }
                                    }
                                    
                                }
                            }
                        }
                    }else{
                        die("Tradegecko not Responding");
                    }

                    return $this->render('duplicate',['order'=>$users,
                                                      'from'=>$from,
                                                      'to'=>$to,
                                                    ]);
                }
    }
}