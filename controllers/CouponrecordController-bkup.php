<?php

namespace app\controllers;

use app\components\AController;
use Yii;
use app\models\CouponRecord;
use app\models\CouponRecordSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CouponrecordController implements the CRUD actions for CouponRecord model.
 */
class CouponrecordController extends AController
{
	public $enableCsrfValidation = false;
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all CouponRecord models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CouponRecordSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CouponRecord model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CouponRecord model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
	  $coupon = new CouponRecord();
	  if(isset($_POST['save']))
       {
		
		$couponchars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$totalcoupon = isset($_POST['coupons'])?$_POST['coupons']:'';
		$date = date('Y-m-d H:i:s');
		for ($count = 0; $count < $totalcoupon; $count++)
		{
			$prefix = $count;
		    $couponcode = $prefix."_";
		
		    for ($i = 0; $i <$_POST['characters']; $i++)
			{
		      $couponcode .= $couponchars[mt_rand(0, strlen($couponchars)-1)];     
		    }
			
			$coupon->coupon_code = $couponcode;
			$coupon->is_apply = '0';
			$coupon->created_at = $date;
			$coupon->save();
		} 
		return $this->redirect(['index']);
	   }
       
	  
		  
            return $this->render('create', [
                'model' => $coupon,
            ]);
        
	}
		
	
    /**
     * Updates an existing CouponRecord model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing CouponRecord model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CouponRecord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CouponRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CouponRecord::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
