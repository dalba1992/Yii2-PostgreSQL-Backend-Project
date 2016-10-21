<?php

namespace app\controllers;

use app\models\Order;
use app\models\UploadCsvForm;
use Yii;
use app\models\TradegeckoOrderLog;
use app\models\TradegeckoOrderLogSearch;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\CustomerSubscription;
use app\models\TradegeckoInfo;
use app\models\TradegeckoOderItemsLog;

use yii\filters\AccessControl;
use app\models\Variants;
use yii\web\UploadedFile;

/**
 * OrderController implements the CRUD actions for TradegeckoOrderLog model.
 */
class OrderController extends \app\components\AController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all TradegeckoOrderLog models.
     * @return mixed
     */
    public function actionCreateOrder()
    {
        $csvModel = new UploadCsvForm();
        if(isset($_POST['UploadCsvForm'])) {
            $csvModel->csvFile = UploadedFile::getInstance($csvModel, 'csvFile');
            if($csvModel->upload()) {
                $data = $csvModel->data;
                if(count($data) > 0) {
                    foreach($data as $item) {
                        $order = new Order();
                        $order->attributes = [
                            'userId' => Yii::$app->user->getId(),
                            'customerId' => $item['customer_id'],
                            'items' => json_encode($item['items']),
                            'status' => 'active', // draft, active, finalized and fullfiled
                            'date' => date('Y-m-d', strtotime($item['date'])),
                            'csvFile' => $csvModel->fileName
                        ];
                        $order->save();
                    }
                }
                $this->redirect(['manualorder/index']);
            }
        }

        $searchModel = new TradegeckoOrderLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $session_msg = Yii::$app->session->get('order_log_error');
        Yii::$app->session->set('order_log_error', FALSE);
        return $this->render('createorder', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'session_msg' => $session_msg,
            'csvModel' => $csvModel
        ]);
    }

    /**
     * Displays a single TradegeckoOrderLog model.
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
     * @return \yii\web\Response
     */
    public function actionSubmitOrder()
    {
        $error_msg = array();
        $date = date('Y-m');

        try{
            if(isset($_FILES['file']['name'])){
                $filedata = explode('.',$_FILES['file']['name']);
                $filename = 'tradegecko_'.time().'.'.$filedata[1];
                move_uploaded_file($_FILES['file']['tmp_name'], Yii::getAlias('@webroot').'/upload/tradegecko_ordercsv/'.$filename);
                $filepath = Yii::getAlias('@webroot').'/upload/tradegecko_ordercsv/'.$filename;

                if (file_exists($filepath)) {      // make sure first that the file is there
                    $file = fopen($filepath, 'r'); // open file for reading
                    $line = fgetcsv($file);        // skip over the first line
                    $row_id = 0;

                    while (($line = fgetcsv($file)) !== FALSE) {
                        $row_id++;
                        $customerOrderDate =  $line['1'];
                        if($customerOrderDate !=null){
                            $customerOrderDate = explode('-',$customerOrderDate);
                            $orderDate =  $customerOrderDate[0].'-'.$customerOrderDate[1];
                            $order_create_date = $customerOrderDate[0].'-'.$customerOrderDate[1].'-'.$customerOrderDate[2];

                            $customerStatus = TradegeckoOrderLog::find()->where(['customer_id' => $line[0]])->andWhere(['like', 'created_at', $orderDate])->one();

                            if(!$customerStatus) {
                                if($line[0]=='') {
                                    $error_msg[] = 'Customer ID empty at Row Id -'.$row_id;
                                    continue;
                                }
                                $products = array();
                                foreach($line as $key => $value) {
                                    if($key>1 && $value!='') {
                                        $products[] = array('sku_id'=>$value);
                                    }
                                }

                                $orderCreate = Yii::$app->tradeGeckoHelper->tradegeckoordercreate($row_id, $line[0], $products);

                                if($orderCreate['error']==true) {
                                    $error_msg[] = $orderCreate['errmsg']. ' at row number : '.$row_id;
                                    continue;
                                }

                                try {
                                    $orderLog = new TradegeckoOrderLog();
                                    $orderLog->customer_id = $line[0];
                                    $orderLog->created_at = $order_create_date;
                                    $orderLog->import_csv_title = $filename;
                                    $orderLog->tradegecko_order_number = $orderCreate['order_number'];
                                    $orderLog->tradegecko_order_id = $orderCreate['order_id'];
                                    $orderLog->save();
                                } catch(Exception $e) {
                                    $error_msg[] = $e->getMessage.' at Row Id - '.$row_id;
                                    continue;
                                }

                                if(!$orderLog->id) {
                                    $error_msg[] = 'Order not created successfully at row number : '.$row_id;
                                    continue;
                                }
                                $connection = Yii::$app->db;
                                $productsTradegecko = array();
                                $productsTradegecko = $orderCreate['productsTradegecko'];
                                if(count($productsTradegecko)>0) {
                                    foreach($productsTradegecko as $keyPr => $valPr) {
                                        $orderItemLog = new TradegeckoOderItemsLog();
                                        $orderItemLog->log_order_id = $orderLog->id;
                                        $orderItemLog->item_id = $valPr['sku_id'];
                                        $orderItemLog->customer_id = $line[0];
                                        $orderItemLog->save();
                                        $command = $connection->createCommand ( "update tb_product_variants set qty=(qty-1) where sku='" . $valPr['sku_id'] . "'" );
                                        $command->execute ();	//Decrease the quantity of products in our database
                                    }
                                }
                            }
                            else {
                                $error_msg[] = 'Customer already subscribed for this month. Customer Id : '.$line[0].' row number : '.$row_id;
                            }
                        }
                    }
                    fclose($file);

                    if(count($error_msg)==0){
                        Yii::$app->session->set('order_log_error',array('error'=>false,'success_msg'=>'Orders created successfully.'));

                    }
                    else
                        Yii::$app->session->set('order_log_error',array('error'=>true,'error_msg'=>$error_msg));

                    return $this->redirect(['create-order']);
                }
            }
        }
        catch(Exception $e){
            echo $e;
        }
    }

    public function actionOrdercheck()
    {
        echo "hello".$_SERVER['REMOTE_ADDR'];die;

    }

    /**
     * Creates a new TradegeckoOrderLog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TradegeckoOrderLog();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TradegeckoOrderLog model.
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
     * Deletes an existing TradegeckoOrderLog model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['createorder']);
    }

    /**
     * Finds the TradegeckoOrderLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TradegeckoOrderLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TradegeckoOrderLog::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}