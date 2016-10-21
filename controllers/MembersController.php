<?php

namespace app\controllers;

use app\models\Order;
use Yii;
use yii\filters\AccessControl;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\HttpException;
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
/**
 * PostController implements the CRUD actions for Post model.
 */
class MembersController extends \app\components\AController
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
     * Lists all members
     * @var string $status
     * @return mixed
     */
    public function actionIndex($status = null)
    {
        $query = CustomerEntity::find();

        $searchModel = new CustomerEntitySearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams)->byStatus($status)->dataProvider;

        return $this->render('grid', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * List Members
     * @return mixed
     */
    public function actionList($status = null)
    {
        $searchModel = new CustomerEntitySearch();

        if(!$status)
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams)->dataProvider;
        else{
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams)->byStatus($status)->dataProvider;
        }

        /**
         * @var ActiveDataProvider $dataProvider
         */

//        echo $dataProvider->pagination->totalCount;
//        $dataProvider->pagination->totalCount = $dataProvider->totalCount;
//        echo $dataProvider->totalCount;
//die;
        return $this->render('list', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Lists all Post models.
     * @return mixed
     */
    public function actionAll()
    {
        $query = CustomerEntity::find();

        $searchModel = new CustomerEntitySearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams)->dataProvider;

        return $this->render('all', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Post model.
     * @param integer $id
     * @return mixed
     */
    public function actionActive()
    {
        $query = CustomerEntity::find();

        $searchModel = new CustomerEntitySearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams)->dataProvider;

        return $this->render('all', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionDeclined()
    {
        return $this->render('declined');
    }

    protected function findModel($id)
    {
        if (($model = Post::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionProfile(){
        if(!Yii::$app->user->isGuest){
            if(isset($_GET['member']) && $_GET['member']>0){
                //$customerInfo = CustomerEntityInfo::find()->where(['customer_id' => $_GET['member'],])->one();
                $customer = CustomerEntity::find()->where(['id' => $_GET['member'],])->one();
                if(!$customer)
                    throw new \yii\web\HttpException(404, 'The member could not be found.');

                //if(!$customer->customerSubscriptionStatus)
                // do create a subscription

                return $this->render('profile', ['model' => $customer]);
            }else{
                throw new NotFoundHttpException('Member does not exist.');
            }
        }else{
            $this->redirect(array('site/index'));
        }
    }

    /**
     * @description
     * @return \yii\web\Response
     */
    public function actionUpdateStyle()
    {
        if(isset($_POST['style']) && sizeof($_POST['style'])>0 && isset($_GET['member']) && $_GET['member'] >0)
        {
            $params=$_POST['style'];
            foreach($params as $k=>$v)
            {
                $TbAttribute = 	TbAttribute::findOne($k);
                if($TbAttribute->is_active && $TbAttribute->is_multiselect==false)
                {
                    $customerAttribute = CustomerEntityAttribute::find()->where(['customer_id' => $_GET['member'],'attribute_id' => $k])->one();

                    if(sizeof($customerAttribute)>0)
                    {
                        $customerAttribute->value=$v;
                        $customerAttribute->save();
                        Yii::$app->session->setFlash('success', 'Style Information updated succesfully...');

                    } else {

                        $date = date('Y-m-d H:i:s');
                        $customerAttribute = new CustomerEntityAttribute();
                        $customerAttribute->customer_id 	= $_GET['member'];
                        $customerAttribute->attribute_id 	= $k;
                        $customerAttribute->value 			= $v;
                        $customerAttribute->created_at 		= $date;
                        $customerAttribute->updated_at 		= $date;
                        $customerAttribute->save();

                        Yii::$app->session->setFlash('success', 'Style Information updated succesfully...');
                    }
                }

                if($TbAttribute->is_active && $TbAttribute->is_multiselect==true)
                {

                    $date = date('Y-m-d H:i:s');
                    CustomerEntityAttribute::deleteAll('customer_id = :cust_id AND attribute_id = :attr_id', [':cust_id' => $_GET['member'], ':attr_id' => $k]);

                    foreach($v as $val)
                    {
                        $customerAttribute = new CustomerEntityAttribute();
                        $customerAttribute->customer_id 	= $_GET['member'];
                        $customerAttribute->attribute_id 	= $k;
                        $customerAttribute->value 			= $val;
                        $customerAttribute->created_at 		= $date;
                        $customerAttribute->updated_at 		= $date;
                        $customerAttribute->save();

                        Yii::$app->session->setFlash('success', 'Style Information updated succesfully...');
                    }
                }
            }
            return $this->redirect(['profile', 'member' => $_GET['member']]);
        }else{
            return $this->redirect(['all']);
        }
    }

    /**
     * Update Member - Customer Profile info
     * @return \yii\web\Response
     */
    public function actionUpdateProfile(){
        if(isset($_POST['Profile']) && !empty($_POST['Profile']) && isset($_GET['member']) && $_GET['member'] >0){
            /**
             * @var CustomerEntity $member
             */
            $member = CustomerEntity::findOne($_GET['member']);
            if($member){
                $params = $_POST['Profile'];
                if(isset($params['email']))
                    $member->customerEntityInfo->email = $params['email'];
                if(isset($params['phone']))
                    $member->customerEntityInfo->phone = $params['phone'];
                if(isset($params['password']) && trim($params['password']) != '') {
                    $member->customerEntityInfo->password = md5($params['password']);
                }
                if($member->customerEntityInfo->save(false)) {
                    Yii::$app->stripe->updateCustomer($member, ['email' => $member->customerEntityInfo->email]);
                    $member->save(false);
                }
            }
            return $this->redirect(['profile', 'member' => $_GET['member']]);
        }else{
            return $this->redirect(['all']);
        }
    }

    /**
     * @param $member
     *
     * @return \yii\web\Response
     */
    public function actionUpdateBilling($member)
    {
        if (isset($_POST['billing']) && sizeof($_POST['billing']) > 0) {

            $Customeraddress = CustomerEntityAddress::find()->where(['customer_id' => $member, 'type_id' => 1])->one();
            if (count($Customeraddress) > 0) {
                $Customeraddress->first_name = $_POST['billing']['first_name'];
                $Customeraddress->last_name = $_POST['billing']['last_name'];
                $Customeraddress->address = $_POST['billing']['address'];
                $Customeraddress->address2 = $_POST['billing']['address2'];
                $Customeraddress->city = $_POST['billing']['city'];
                $Customeraddress->state = $_POST['billing']['state'];
                $Customeraddress->country = $_POST['billing']['country'];
                $Customeraddress->zipcode = $_POST['billing']['zipcode'];

                if ($Customeraddress->validate()) {
                    $Customeraddress->save();
                    Yii::$app->session->setFlash('success', 'Billing Information updated succesfully...');

                    return $this->redirect(['profile', 'member' => $member]);

                } else {
                    Yii::$app->session->setFlash('error', 'Failed To Update Billing Information...');
                    return $this->redirect(['profile', 'member' => $member]);
                }
            } else {
                Yii::$app->session->setFlash('error', 'Customer does not exist,Failed To Update...');
                return $this->redirect(['profile', 'member' => $member]);
            }
        }
    }

    public function actionUpdateShipping($member)
    {
        if(isset($_POST['shipping']) && sizeof($_POST['shipping'])>0) {

            $Customeraddress = CustomerEntityAddress::find()->where(['customer_id'=>$member,'type_id'=>2])->one();
            if(count($Customeraddress)>0)
            {
                $Customeraddress->first_name 	= $_POST['shipping']['first_name'];
                $Customeraddress->last_name 	= $_POST['shipping']['last_name'];
                $Customeraddress->address 		= $_POST['shipping']['address'];
                $Customeraddress->address2 		= $_POST['shipping']['address2'];
                $Customeraddress->city 			= $_POST['shipping']['city'];
                $Customeraddress->state 		= $_POST['shipping']['state'];
                $Customeraddress->country 		= $_POST['shipping']['country'];
                $Customeraddress->zipcode 		= $_POST['shipping']['zipcode'];

                if($Customeraddress->validate())
                {
                    $Customeraddress->save();
                    Yii::$app->session->setFlash('success', 'Shipping Information updated succesfully...');

                    return $this->redirect(['profile', 'member' => $member]);

                } else {
                    Yii::$app->session->setFlash('error', 'Failed To Update Shipping Information...');
                    return $this->redirect(['profile', 'member' => $member]);
                }
            } else {
                Yii::$app->session->setFlash('error', 'Customer does not exist,Failed To Update...');
                return $this->redirect(['profile', 'member' => $member]);
            }
        }
    }

    /**
     * Create a manual order
     * @param $member Member id
     * @return \yii\web\Response
     * @throws HttpException when the member was not found
     */
    public function actionCreateOrder($member) {
        $customerEntity = CustomerEntity::find()->where(['id' => $member])->one();
        if($customerEntity) {
            $orderModel = new Order();
            $orderModel->attributes = [
                'userId' => Yii::$app->user->getId(),
                'customerId' => $customerEntity->id,
                'status' => 'active', // draft, active, finalized and fullfiled
                'date' => date('Y-m-d'),
            ];
            if($orderModel->save()) {
                return $this->redirect(['manualorder/index']);
            }
        } else {
            throw new HttpException(404, 'Member was not found');
        }
    }
}