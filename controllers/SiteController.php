<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;

use app\components\AController;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\CustomerSubscriptionOrder;
use app\models\Order;
use app\models\TradegeckoOrderLog;

class SiteController extends AController
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'logout' => ['post'],
//                ],
//            ],
        ];
    }

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function beforeAction($action)
    {
        //$this->registerJs['unicorn-dashboard'] = '/web/js/unicorn.dashboard.js';
        return parent::beforeAction($action);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        $this->layout = 'login';

        $model = new LoginForm();
        if(isset($_POST) && sizeof($_POST)>0){
            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                return $this->goBack();
            } else {
                return $this->render('index', [
                    'model' => $model,
                ]);
            }
        }

        if (!Yii::$app->user->isGuest){
            $this->redirect(array('site/dashboard'));
        }else{			
            return $this->render('index', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @return \yii\web\Response
     */
    public function actionLogin()
    {
        //print_r($_POST);die;
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        $orders = CustomerSubscriptionOrder::find()->indexBy('id')->count();

        if ($model->load(Yii::$app->request->post())&& $model->login()) {

            $this->redirect(array('site/dashboard'));

        } else {
            return $this->goHome();
        }
    }

    /**
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionDashboard() {
        if(!Yii::$app->user->isGuest){
            $query = Order::find();
            $totalOrders = $query->count();

            $activeOrders = 0;
            $query = Order::findActiveOrders();
            $activeOrders = $query->count();

            $draftOrders = 0;
            $query = Order::findDraftOrders();
            $draftOrders = $query->count();

            $finalizedOrders = 0;
            $query = Order::findFinalizedOrders();
            $finalizedOrders = $query->count();

            $fulfilledOrders = 0;
            $query = Order::findFulfilledOrders();
            $fulfilledOrders = $query->count();

            $returnedOrders = 0;
            $query = Order::findReturnedOrders();
            $returnedOrders = $query->count();

            $paidOrders = 0;
            $query = Order::findPaidOrders();
            $paidOrders = $query->count();

            $unpaidOrders = 0;
            $query = Order::findUnpaidOrders();
            $unpaidOrders = $query->count();

            $sentOrders = 0;
            $query = Order::findSentOrders();
            $sentOrders = $query->count();

            //Weekly Sales
            $first_date_of_this_week = date('Y-m-d', strtotime('this monday'));
            $monday_of_last_week = date('Y-m-d', strtotime('-7 days', strtotime($first_date_of_this_week)));
            $sunday_of_last_week = date('Y-m-d', strtotime('-1 days', strtotime($first_date_of_this_week)));

            $stats = array();
            for($i = 0; $i < 7; $i++)
            {
                $stats[$i] = 0.0;
            }

//            $payments = Yii::$app->tradeGecko->getPayments();
//            if($payments){
//                foreach($payments as $payment)
//                {
//                    $paid_at = strtotime(date('Y-m-d', strtotime($payment['paid_at'])));
//                    $d = $paid_at - strtotime($monday_of_last_week);
//                    $d = floor($d/(60*60*24));
//                    if ($d >= 0 and $d <= 7)
//                        $stats[$d] += floatval($payment['amount']);
//                }
//            }

            return $this->render('dashboard', [
                'totalOrders' => $totalOrders,
                'activeOrders' => $activeOrders,
                'draftOrders' => $draftOrders,
                'finalizedOrders' => $finalizedOrders,
                'fulfilledOrders' => $fulfilledOrders,
                'returnedOrders' => $returnedOrders,
                'paidOrders' => $paidOrders,
                'unpaidOrders' => $unpaidOrders,
                'sentOrders' => $sentOrders,
                'stats' => $stats,
                'monday' => $monday_of_last_week,
                'sunday' => $sunday_of_last_week,
            ]);
        }else{
            return $this->goHome();
        }

    }
}