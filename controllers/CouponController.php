<?php

namespace app\controllers;

use app\components\AController;
use Yii;
use app\models\Coupon;
use app\models\CouponSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

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
 * CouponController implements the CRUD actions for Coupon model.
 */
class CouponController extends AController
{
    public function behaviors()
    {
    	$access = parent::behaviors();
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => $access['access'],
        ];
    }

    /**
     * Lists all Coupon models.
     * @return mixed
     */
    public function actionIndex()
    {
		if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $searchModel = new CouponSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Coupon model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Coupon model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new Coupon();
		if(isset($_POST['Coupon']) && sizeof($_POST['Coupon'])>0 && isset(Yii::$app->params['stripe']['stripesecretkey'])){
			//print_r($_POST);die;
			$stripeSecret_key = Yii::$app->params['stripe']['stripesecretkey'];
			Stripe::setApiKey($stripeSecret_key);
			$created_at = $updated_at = date('Y-m-d');
			
			try{
				/**********************  FIxed Amount Discount Coupon Create on Stripe  **************************/
				$coupon = trim($_POST['Coupon']['coupon_code']);
				$discount_type = $_POST['Coupon']['discount_type'];
				$duration = $_POST['Coupon']['duration'];
				$amount_off = $_POST['Coupon']['amount_off'];
				if($amount_off == '0'){
					$created_at = date('Y-m-d');
						$model->coupon_name = $_POST['Coupon']['coupon_name'];
						$model->coupon_code = $coupon;
						$model->discount_type = $discount_type;
						$model->amount_off = $amount_off;
						$model->duration = $duration;
						if($duration == 3){
							$duration_in_month = $_POST['Coupon']['duration_time'];
							$model->duration_time = $duration_in_month;
						}
						$model->created_at = $created_at;
						//$model->updated_at = $updated_at;
						$model->is_apply = '0';
						$model->use_count = '0';
						$model->message = $_POST['Coupon']['message'];
						$model->status = $_POST['Coupon']['status'];
						$model->save();
						Yii::$app->session->setFlash('success','Coupon Added Successfully!');
						return $this->redirect(['index']);
				}else{
					if($discount_type == '1'){
						$amount_off_cent = $amount_off * 100;
						if($duration == 1){ // once
							$duration_type = 'once';
							$couponResponse = Stripe_Coupon::create(array(
														  "amount_off" => $amount_off_cent,
														  "currency" => 'usd',
														  "duration" => $duration_type,
														  "id" => $coupon)
														);
						}else if($duration == 2){ // forever
							$duration_type = 'forever';
							$couponResponse = Stripe_Coupon::create(array(
														  "amount_off" => $amount_off_cent,
														  "currency" => 'usd',
														  "duration" => $duration_type,
														  "id" => $coupon)
														);
						}else if($duration == 3){ // repeating
							$duration_type = 'repeating';
							$duration_in_month = $_POST['Coupon']['duration_time'];
							$couponResponse = Stripe_Coupon::create(array(
																"amount_off" => $amount_off_cent,
																"currency" => 'usd',
																"duration" => $duration_type,
																"duration_in_months" => $duration_in_month,
																"id" => $coupon)
														);
						}
					/********************** Fixed Amount Discount Coupon Create on Stripe End ************************************/	
					}else if($discount_type == '2'){
						if($duration == 1){ // once
							$duration_type = 'once';
							$couponResponse = Stripe_Coupon::create(array(
														  "percent_off" => $amount_off,
														  "currency" => 'usd',
														  "duration" => $duration_type,
														  "id" => $coupon)
														);
						}else if($duration == 2){ // forever
								$duration_type = 'forever';
								$couponResponse = Stripe_Coupon::create(array(
															  "percent_off" => $amount_off,
															  "currency" => 'usd',
															  "duration" => $duration_type,
															  "id" => $coupon)
															);
						}else if($duration == 3){ // repeating
							$duration_type = 'repeating';
							$duration_in_month = $_POST['Coupon']['duration_time'];
							$couponResponse = Stripe_Coupon::create(array(
																"percent_off" => $amount_off,
																"currency" => 'usd',
																"duration" => $duration_type,
																"duration_in_months" => $duration_in_month,
																"id" => $coupon)
														);
						}
					
					}
					if(isset($couponResponse->created) && $couponResponse->created!=null ){
						$created_at = date('Y-m-d',$couponResponse->created);
						$model->coupon_name = $_POST['Coupon']['coupon_name'];
						$model->coupon_code = $coupon;
						$model->discount_type = $discount_type;
						$model->amount_off = $amount_off;
						$model->duration = $duration;
						if($duration == 3){
							$model->duration_time = $duration_in_month;
						}
						$model->created_at = $created_at;
						//$model->updated_at = $updated_at;
						$model->is_apply = '0';
						$model->use_count = '0';
						$model->message = $_POST['Coupon']['message'];
						$model->status = $_POST['Coupon']['status'];
						$model->save();
						Yii::$app->session->setFlash('success','Coupon Added Successfully!');
						return $this->redirect(['index']); 
					}
				}
				
			}catch (Exception $e){
				//echo '1'.$e->getMessage(); die;
				$error = $e->getMessage();
				Yii::$app->session->setFlash('fail',$error);
				return $this->redirect(['create']); 
			}
			catch (Stripe_ApiConnectionError $e) {
				//echo '2'.$e->getMessage(); die;
				$error = $e->getMessage();
				Yii::$app->session->setFlash('fail',$error);
				return $this->redirect(['create']); 
			}catch (Stripe_InvalidRequestError $e) {
				//echo '3'.$e->getMessage(); die;
				$error = $e->getMessage();
				Yii::$app->session->setFlash('fail',$error);
				return $this->redirect(['create']);
			}catch (Stripe_ApiError $e) {
				//echo '4'.$e->getMessage(); die;
				$error = $e->getMessage();
				Yii::$app->session->setFlash('fail',$error);
				return $this->redirect(['create']);
			}
			
		}else{
			 return $this->render('create', [
                'model' => $model,
            ]);
		}
    }

    /**
     * Updates an existing Coupon model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
	
		if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
		$model = $this->findModel($id);
	
		
		if(isset($_POST['Coupon']) && (sizeof($_POST['Coupon'])) && $_POST['Coupon']['status']!=null){ 
			$model->coupon_name = $_POST['Coupon']['coupon_name'];
			$model->message = $_POST['Coupon']['message'];
			$model->status = $_POST['Coupon']['status'];
			$model->save();
			Yii::$app->session->setFlash('success','Coupon Updated Successfully!');
			return $this->render('update', [
				'model' => $model,
			]);
		}else{
			return $this->render('update', [
				'model' => $model,
			]);
		}
    }

    /**
     * Deletes an existing Coupon model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
		if(isset(Yii::$app->params['stripe']['stripesecretkey'])){
			$stripeSecret_key = Yii::$app->params['stripe']['stripesecretkey'];
			Stripe::setApiKey($stripeSecret_key);
			try{
				$Couponrecord = $this->findModel($id);
				// Delete coupon From Stripe
				if($Couponrecord->coupon_code!=null && $Couponrecord->amount_off ==0){
					$Couponrecord->delete();
					Yii::$app->session->setFlash('success','Coupon Deleted Successfully!');
				}else{
					$coupon = Stripe_Coupon::retrieve($Couponrecord->coupon_code);
					$coupon->delete(); 
					$Couponrecord->delete();
					Yii::$app->session->setFlash('success','Coupon Deleted Successfully!');
				}
				return $this->redirect(['index']);

			}catch(Exception $e){
				//echo '1'.$e->getMessage(); die;
				$error = $e->getMessage();
				Yii::$app->session->setFlash('fail',$error);
				return $this->redirect(['index']);
			}catch (Stripe_ApiConnectionError $e) {
				//echo '2'.$e->getMessage(); die;
				$error = $e->getMessage();
				Yii::$app->session->setFlash('fail',$error);
				return $this->redirect(['index']); 
			}catch (Stripe_InvalidRequestError $e) {
				//echo '3'.$e->getMessage(); die;
				$error = $e->getMessage();
				Yii::$app->session->setFlash('fail',$error);
				return $this->redirect(['index']);
			}catch (Stripe_ApiError $e) {
				//echo '4'.$e->getMessage(); die;
				$error = $e->getMessage();
				Yii::$app->session->setFlash('fail',$error);
				return $this->redirect(['index']);
			}
			
		}
    }

    /**
     * Finds the Coupon model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Coupon the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Coupon::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
