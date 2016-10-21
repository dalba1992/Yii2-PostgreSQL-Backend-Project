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
/**
 * PostController implements the CRUD actions for Post model.
 */
class OrderController extends \app\components\AController
{
	public function actionCreateorder(){
		return $this->render('createorder');
	}
	
	public function actionSubmitorder(){
		$filepath = $_SERVER['DOCUMENT_ROOT'].'/bt-concierge-admin/upload/tradegecko_ordercsv/tradegecko_1423751334.csv';
		$filename = 'tradegecko_1423751334.csv';
		$date = date('Y-m-d H:i:s');
		if (file_exists($filepath)) {
			$file = fopen($filepath, 'r');
			$line = fgetcsv($file);
			
			while (($line = fgetcsv($file)) !== FALSE) {
				
				  $customer_tradegeckoOrderLog= new TradegeckoOrderLog();
				  $customer_tradegeckoOrderLog->customer_id=$line[0];
				  $customer_tradegeckoOrderLog->tradegecko_order_id='1';
				  $item1=$line[1];
				  $item2=$line[2];
				  $arr= array($item1,$item2);
				  $item_id=implode(',',$arr);
				  $customer_tradegeckoOrderLog->item_id=$item_id;
				  $customer_tradegeckoOrderLog->created_at=$date;
				  if($customer_tradegeckoOrderLog->validate())
				  {
					$customer_tradegeckoOrderLog->save();  
				  }
			}
			fclose($file);
			return $this->redirect(['createorder']);
		}
	
		die;
		try{
			if(isset($_FILES['file']['name'])){
				$filedata = explode('.',$_FILES['file']['name']);
				$filename = 'tradegecko_'.time().'.'.$filedata[1];
				move_uploaded_file($_FILES['file']['tmp_name'], 'upload/tradegecko_ordercsv/'.$filename);
				$filepath = $_SERVER['DOCUMENT_ROOT'].'/bt-concierge-admin/upload/tradegecko_ordercsv/'.$filename;
				if (file_exists($filepath)) {
					$file = fopen($filepath, 'r');
					$line = fgetcsv($file);
					while (($line = fgetcsv($file)) !== FALSE) {
						  print_r($line);
					}
					fclose($file);
				}
			}
		
		}catch(Exception $e)
		{
			echo $e;
		}
	}
}
