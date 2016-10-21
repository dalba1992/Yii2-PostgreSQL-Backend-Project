<?php
namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

use app\models\Product;
use app\models\Brand;
use app\models\Category;
use app\models\Productattribute;
use app\models\Productattributeoption;
use app\models\Productattributevalue;
use app\models\Variants;
use app\models\Variantsimage;


class InventoryOldController extends \app\components\AController
{

	public function actionImportprod() {
		
		$token = Yii::$app->params ['tradegeckoapi'] [mt_rand ( 0, count ( Yii::$app->params ['tradegeckoapi'] ) - 1 )];
		$accesstoken = "Bearer " . $token;
	
		$header = array ();
		$header [] = 'Content-type: application/json';
		$header [] = 'Authorization: ' . $accesstoken;
	
		$connection = Yii::$app->db;
		$command = $connection->createCommand ( 'SELECT * FROM tb_product_cron WHERE created_at=(select max(created_at) from tb_product_cron)' );
		$model = $command->queryAll ();
		if (isset ( $model [0] ['page'] )) {
			$page = $model [0] ['page']+1;
		} else {
			$page = 1;
		}
	
		$url = "https://api.tradegecko.com/products?limit=50&page=" . $page;
		$ch = curl_init ( $url );
	
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
		curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "GET" );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
	
		$Response = curl_exec ( $ch );
		curl_close ( $ch );
	
		$ProductResponse = json_decode ( $Response );
		if (isset ( $ProductResponse->products ) && sizeof ( $ProductResponse->products ) > 0) {
			foreach ( $ProductResponse->products as $product ) {
				try {
					$Product = Product::find ()->where ( [
							'tradegecko_product_id' => $product->id
							] )->one ();
					if (sizeof ( $Product ) != 0 && $Product->updated_at < $product->updated_at) {
						// perform deletion of product
	
						$Product->delete();
	
						$Product = array ();
					}
						
					// perform insertion of product with updated values and its other details if different tables
					if (sizeof ( $Product ) == 0) {
	
						$tradegecko_product_id = $product->id;
						$name = $product->name;
						$created_at = date ( 'Y-m-d H:i:s', strtotime ( $product->created_at ) );
						$updated_at = date ( 'Y-m-d H:i:s', strtotime ( $product->updated_at ) );
						$category = $product->product_type;
						$category_id = '';
						$brand = $product->brand;
						$brand_id = '';
						if ($brand != '') {
							$Brand = Brand::find ()->where ( [
									'brand' => $brand
									] )->one ();
							if (sizeof ( $Brand ) > 0) {
								$brand_id = $Brand->id;
							} else {
								$Brand = new Brand ();
								$Brand->brand = $brand;
								$Brand->save();
								$brand_id = $Brand->id;
							}
						}
						if ($category != '') {
							$Category = Category::find ()->where ( [
									'category' => $category
									] )->one ();
							if (sizeof ( $Category ) > 0) {
								$category_id = $Category->id;
							} else {
								$Category = new Category ();
								$Category->category = $category;
								$Category->save();
								$category_id = $Category->id;
							}
						}
						/**
						 * ***************** Product Import Save ****************
						 */
						$TradegeckoProduct = new Product ();
						$TradegeckoProduct->tradegecko_product_id = $tradegecko_product_id;
						$TradegeckoProduct->name = $name;
						$TradegeckoProduct->category_id = $category_id;
						$TradegeckoProduct->brand_id = $brand_id;
						$TradegeckoProduct->created_at = $created_at;
						$TradegeckoProduct->updated_at = $updated_at;
						$TradegeckoProduct->save();
						/**
						 * ****************** Product Save End ****************
						*/
						/**
						 * **************** Variants Check ********************
						*/
						$url = "https://api.tradegecko.com/variants/?product_id=" . $product->id;
						$ch = curl_init ();
						curl_setopt ( $ch, CURLOPT_URL, $url );
						curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
						curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, TRUE );
						$VariantDetail = curl_exec ( $ch );
						curl_close ( $ch );
	
						$VariantDetail = json_decode ( $VariantDetail, TRUE );
	
						/**
						 * ************************** Variants Check End *****************************
						*/
						if (isset ( $VariantDetail ['variants'] ) && (sizeof ( $VariantDetail ['variants'] ) > 0)) {
							$VariantDetail = $VariantDetail ['variants'];
							foreach ( $VariantDetail as $Variantinfo ) {
								if (isset ( $product->opt1 )) {
									$attribute1 = $product->opt1;
										
									$Productattribute = Productattribute::find ()->where ( [
											'attribute_name' => $attribute1
											] )->one ();
									if (sizeof ( $Productattribute ) > 0) {
										$attribute1_id = $Productattribute->id;
									} else {
										$Productattribute = new Productattribute ();
										$Productattribute->attribute_name = $attribute1;
										$Productattribute->save();
										$attribute1_id = $Productattribute->id;
									}
									$variants1 = $Variantinfo ['opt1'];
									$find_optionvalue = Productattributeoption::find ()->where ( [
											'attribute_id' => $attribute1_id,
											'attribute_value' => $variants1
											] )->one ();
									if (sizeof ( $find_optionvalue ) > 0) {
										$option_id = $find_optionvalue->id;
									} else {
										$Productattributeoption = new Productattributeoption ();
										$Productattributeoption->attribute_id = $attribute1_id;
	
										$Productattributeoption->attribute_value = $variants1;
										$Productattributeoption->save();
										$option_id = $Productattributeoption->id;
									}
										
									$Productattributevalue = new Productattributevalue ();
									$Productattributevalue->product_entity_info_id = $TradegeckoProduct->id;
									$Productattributevalue->tradegecko_product_id = $tradegecko_product_id;
									$Productattributevalue->variant_id = $Variantinfo ['id'];
									$Productattributevalue->attribute_id = $attribute1_id;
									$Productattributevalue->attribute_option_id = $option_id;
									$Productattributevalue->save();
								}
								if (isset ( $product->opt2 )) {
										
									$attribute2 = $product->opt2;
										
									$Productattribute = Productattribute::find ()->where ( [
											'attribute_name' => $attribute2
											] )->one ();
									if (sizeof ( $Productattribute ) > 0) {
										$attribute2_id = $Productattribute->id;
									} else {
										$Productattribute = new Productattribute ();
										$Productattribute->attribute_name = $attribute2;
										$Productattribute->save();
										$attribute2_id = $Productattribute->id;
									}
									$variants2 = $Variantinfo ['opt2'];
									$find_optionvalue = Productattributeoption::find ()->where ( [
											'attribute_id' => $attribute2_id,
											'attribute_value' => $variants2
											] )->one ();
									if (sizeof ( $find_optionvalue ) > 0) {
										$option_id = $find_optionvalue->id;
									} else {
										$Productattributeoption = new Productattributeoption ();
										$Productattributeoption->attribute_id = $attribute2_id;
										$Productattributeoption->attribute_value = $variants2;
										$Productattributeoption->save();
										$option_id = $Productattributeoption->id;
									}
									$Productattributevalue = new Productattributevalue ();
									$Productattributevalue->product_entity_info_id = $TradegeckoProduct->id;
									$Productattributevalue->tradegecko_product_id = $tradegecko_product_id;
									$Productattributevalue->variant_id = $Variantinfo ['id'];
									$Productattributevalue->attribute_id = $attribute2_id;
									$Productattributevalue->attribute_option_id = $option_id;
									$Productattributevalue->save();
								}
								$Variants = new Variants ();
								$Variants->product_entity_info_id = $TradegeckoProduct->id;
								$Variants->variant_id = $Variantinfo ['id'];
								$Variants->sku = $Variantinfo ['sku'];
								$Variants->wholesale_price = $Variantinfo ['wholesale_price'];
								$Variants->buy_price = $Variantinfo['buy_price'];
								$Variants->retail_price = $Variantinfo['retail_price'];
								$Variants->qty = $Variantinfo['stock_on_hand'];
								$Variants->created_at = date('Y-m-d H:i:s',strtotime($Variantinfo['created_at']));
								$Variants->updated_at = date('Y-m-d H:i:s',strtotime($Variantinfo['updated_at']));
								$Variants->save();
								if(isset($Variantinfo['image_ids'][0])){
									$image_id = $Variantinfo ['image_ids'] [0];
									$url = "https://api.tradegecko.com/images/" . $image_id;
									$ch = curl_init ();
									curl_setopt ( $ch, CURLOPT_URL, $url );
									curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
									curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, TRUE );
									$variant_image = curl_exec ( $ch );
									curl_close ( $ch );
									$variant_image = json_decode ( $variant_image, TRUE );
										
									if (isset ( $variant_image ['images'] ) && sizeof ( $variant_image ['images'] ) > 0 && isset ( $variant_image ['images'] ['base_path'] )) {
										$image_url = $variant_image ['images'] ['base_path'] . '/' . $variant_image ['images'] ['file_name'];
										$Variantsimage = new Variantsimage ();
										$Variantsimage->variant_id = $Variants->id;
										$Variantsimage->img_url = $image_url;
										$Variantsimage->save();
									}
								}
							}
						}
	
						//insert logs in tb_product_log
						$command = $connection->createCommand ('Insert into tb_product_log (tradegecko_product_id,updated_at)values (:prd_id,:time)');
						$command->bindValue(':prd_id',$product->id);
						$command->bindValue(':time',time());
						$command->execute();
					}
				} catch ( Exception $e ) {
					echo $e->getMessage ();
				}
			}
		}
		// insert the data for page and updated_at tb_product_cron

		$command = $connection->createCommand ( 'Insert into tb_product_cron (page,created_at) values(:page,:time)' );
		$command->bindValue(':page',$page);
		$command->bindValue(':time',time());
		$command->execute();
	}
	
	
    public function actionImportproduct(){
		die;
		# ------------------------------------------------------------------------------------------------
		# TradeGeko  API 
		# ------------------------------------------------------------------------------------------------
		
		$accesstoken = "Bearer ".$this->Randomtradegeckoapi();
		# ------------------------------------------------------------------------------------------------
		# Create Request Header
		# ------------------------------------------------------------------------------------------------
		$header = array();
		$header[] = 'Content-type: application/json';
		$header[] = 'Authorization: '.$accesstoken;
		# ------------------------------------------------------------------------------------------------
		# Initiate cURL, Set Option for URL, Header, Return data and then close cUrl
		# ------------------------------------------------------------------------------------------------
		$url = "https://api.tradegecko.com/products?limit=10000";
		$ch = curl_init($url);
			
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$Response = curl_exec($ch);
		curl_close($ch);
		$ProductResponse = json_decode($Response);
		if(isset($_GET['showprod']) && $_GET['showprod']==1){
			print_r($ProductResponse);die;
		}
		//print_r($ProductResponse);die;
		if(isset($ProductResponse->products) && sizeof($ProductResponse->products)>0){
			foreach($ProductResponse->products as $product){
				try{
					$Product = Product::find()->where(['tradegecko_product_id'=>$product->id])->one();
					if(sizeof($Product) == 0){
						$tradegecko_product_id = $product->id;
						$name = $product->name;
						$created_at = date('Y-m-d H:i:s',strtotime($product->created_at));
						$updated_at = date('Y-m-d H:i:s',strtotime($product->updated_at));
						$category = $product->product_type;
						$category_id = '';
						$brand = $product->brand;
						$brand_id = '';
						if($brand!=''){
							$Brand = Brand::find()->where(['brand'=>$brand])->one();
							if(sizeof($Brand)>0){
								$brand_id = $Brand->id;
							}else{
								$Brand = new Brand();
								$Brand->brand = $brand;
								$Brand->save();
								$brand_id = $Brand->id;
							}
						}
						if($category!=''){
							$Category = Category::find()->where(['category'=>$category])->one();
							if(sizeof($Category)>0){
								$category_id = $Category->id;
							}else{
								$Category = new Category();
								$Category->category = $category;
								$Category->save();
								$category_id = $Category->id;						
							}
						}
						/******************* Product Import Save *****************/
						$TradegeckoProduct = new Product();
						$TradegeckoProduct->tradegecko_product_id = $tradegecko_product_id;
						$TradegeckoProduct->name = $name;
						$TradegeckoProduct->category_id = $category_id;
						$TradegeckoProduct->brand_id = $brand_id;
						$TradegeckoProduct->created_at = $created_at;
						$TradegeckoProduct->updated_at = $updated_at;
						$TradegeckoProduct->save();
						/******************** Product Save End *****************/
						/****************** Variants Check *********************/
						$url = "https://api.tradegecko.com/variants/?product_id=".$product->id;
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL,$url);
						curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
						$VariantDetail = curl_exec($ch);
						curl_close($ch);
						
						$VariantDetail = json_decode($VariantDetail,TRUE);
						
						/**************************** Variants Check End ******************************/
						if(isset($VariantDetail['variants']) && (sizeof($VariantDetail['variants'])>0)){
							$VariantDetail = $VariantDetail['variants'];
							foreach($VariantDetail as $Variantinfo)
							{
								if(isset($product->opt1)){
									$attribute1 = $product->opt1;
									
									$Productattribute = Productattribute::find()->where(['attribute_name'=>$attribute1])->one();
									if(sizeof($Productattribute)>0){
										$attribute1_id = $Productattribute->id;
									}else{
										$Productattribute = new Productattribute();
										$Productattribute->attribute_name = $attribute1;
										$Productattribute->save();
										$attribute1_id = $Productattribute->id;
									}
									$variants1 = $Variantinfo['opt1'];
									$find_optionvalue = Productattributeoption::find()->where(['attribute_id'=>$attribute1_id,
																								'attribute_value'=>$variants1])->one();
									//echo "->".sizeof($find_optionvalue);die;
									if(sizeof($find_optionvalue)>0){
										$option_id = $find_optionvalue->id;	
									}else{
										$Productattributeoption = new Productattributeoption();
										$Productattributeoption->attribute_id = $attribute1_id;
										
										$Productattributeoption->attribute_value = $variants1;
										$Productattributeoption->save();
										$option_id = $Productattributeoption->id;
									}
									
									$Productattributevalue = new Productattributevalue();
									$Productattributevalue->product_entity_info_id = $TradegeckoProduct->id;
									$Productattributevalue->tradegecko_product_id = $tradegecko_product_id;
									$Productattributevalue->variant_id = $Variantinfo['id'];
									$Productattributevalue->attribute_id = $attribute1_id;
									$Productattributevalue->attribute_option_id = $option_id;							
									$Productattributevalue->save();
								}
								if(isset($product->opt2)){
									
									$attribute2 = $product->opt2;
									
									
									$Productattribute = Productattribute::find()->where(['attribute_name'=>$attribute2])->one();
									if(sizeof($Productattribute)>0){
										$attribute2_id = $Productattribute->id;
									}else{
										$Productattribute = new Productattribute();
										$Productattribute->attribute_name = $attribute2;
										$Productattribute->save();
										$attribute2_id = $Productattribute->id;
									}
									$variants2 = $Variantinfo['opt2'];
									$find_optionvalue = Productattributeoption::find()
																				->where(['attribute_id'=>$attribute2_id,
																						'attribute_value'=>$variants2])->one();
									if(sizeof($find_optionvalue)>0){
										$option_id = $find_optionvalue->id;	
									}else{
										$Productattributeoption = new Productattributeoption();
										$Productattributeoption->attribute_id = $attribute2_id;								
										$Productattributeoption->attribute_value = $variants2;
										$Productattributeoption->save();
										$option_id = $Productattributeoption->id;
									}
									$Productattributevalue = new Productattributevalue();
									$Productattributevalue->product_entity_info_id = $TradegeckoProduct->id;
									$Productattributevalue->tradegecko_product_id = $tradegecko_product_id;
									$Productattributevalue->variant_id = $Variantinfo['id'];
									$Productattributevalue->attribute_id = $attribute2_id;
									$Productattributevalue->attribute_option_id = $option_id;							
									$Productattributevalue->save();
								}
								$Variants = new Variants();
								$Variants->product_entity_info_id = $TradegeckoProduct->id;
								$Variants->variant_id = $Variantinfo['id'];
								$Variants->sku = $Variantinfo['sku'];
								$Variants->wholesale_price = $Variantinfo['wholesale_price'];
								$Variants->buy_price = $Variantinfo['buy_price'];
								$Variants->retail_price = $Variantinfo['retail_price'];
								$Variants->qty = $Variantinfo['stock_on_hand'];
								$Variants->created_at = date('Y-m-d H:i:s',strtotime($Variantinfo['created_at']));
								$Variants->updated_at = date('Y-m-d H:i:s',strtotime($Variantinfo['updated_at']));
								$Variants->save();
								if(isset($Variantinfo['image_ids'][0])){
									$image_id = $Variantinfo['image_ids'][0];
									$url = "https://api.tradegecko.com/images/".$image_id;
									$ch = curl_init();
									curl_setopt($ch, CURLOPT_URL,$url);
									curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
									curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
									$variant_image = curl_exec($ch);
									curl_close($ch);
									$variant_image = json_decode($variant_image,TRUE);
									
									if(isset($variant_image['images']) && sizeof($variant_image['images'])>0 && isset($variant_image['images']['base_path'])){
										$image_url = $variant_image['images']['base_path'].'/'.$variant_image['images']['file_name'];
										$Variantsimage = new Variantsimage();
										$Variantsimage->variant_id = $Variants->id;
										$Variantsimage->img_url = $image_url;
										$Variantsimage->save();
									}	
								}
							}
						}
					}
				}catch(Exception $e){
					echo $e->getMessage(); 
				}	
			}
			echo "Done!";
		}
	}
	public function Randomtradegeckoapi(){
		$random = array('236c41d1c88f9d4a1b814c1db9669c8294c43db944f49af7de3bee0b9a25581f',
						'c98c9e5098a57d4601a24fec718cd8967586b11f03e31fd0e7835876c0bdc737',
						'0e2aeba42311646706550a622ba8327dee6bf1d18b30743aa2a9c94664bb7fb5',
						'2d6d22d28589159055f5f53beeb325e336907f608f761e12d403534d9ba2db2a',
						'a05b05acc15594df6e8c7764f285eff4af09c40d94fb3878dffb1d7d1af616f5',
						'6d89130532823ca8835782f977a2d3dcada3de47387cf31297ccf3947c848149',
						'4e1660749bf2055e015500f9fb933dce508bf4e62f6a246dc09fe1959cfe75b7',
						'9a38884332094b1ffcd7f7f88113bce33f824de7de17cfcac4c4a677732dff92',
						'dc343f5821e28b8f284450189c840822bd3a18ff2fcafac0ffbdbbee60cc1f45',
						'd2dd9106aa3c699acffd6af4c3ce7f9b5eb2d988beb8111db5a6c8637aeabcdd'
					);
		$random = $random[mt_rand(0, count($random) - 1)];		
		return $random;
	}
}
