<?php

namespace app\modules\services\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

use app\modules\services\models\LiteviewOrder;
use app\modules\services\models\LiteviewOrderSearch;
use app\modules\services\models\LiteviewOrderDetails;
use app\modules\services\models\LiteviewOrderBillingContact;
use app\modules\services\models\LiteviewOrderBillingDetails;
use app\modules\services\models\LiteviewOrderItems;
use app\modules\services\models\LiteviewOrderNotes;
use app\modules\services\models\LiteviewOrderShippingContact;
use app\modules\services\models\LiteviewOrderShippingDetails;
use app\modules\services\models\TradegeckoOrder;
use app\modules\services\models\TradegeckoOrderLineItem;
use app\modules\services\models\TradegeckoOrderSearch;
use app\modules\services\models\TradegeckoAddress;
use app\modules\services\models\TradegeckoUser;
use app\modules\services\models\TradegeckoCompany;
use app\modules\services\models\TradegeckoCurrency;

use app\modules\services\components\Liteview;

class LiteviewController extends \app\modules\services\components\AController
{
    public $demo = true;
    /**
     * This function loads index page.
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        $searchModel = new LiteviewOrderSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams)->dataProvider;

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionCreate()
    {
        if (isset($_GET['params']))
        {
            $tradegeckoOrders = TradegeckoOrder::findAll($_GET['params']);
            $billingAddress = TradegeckoAddress::find()->where(['id'=>$tradegeckoOrders[0]->billing_address_id])->one();
            $shippingAddress = TradegeckoAddress::find()->where(['id'=>$tradegeckoOrders[0]->shipping_address_id])->one();
            $user = TradegeckoUser::find()->where(['id'=>$tradegeckoOrders[0]->user_id])->one();
            $company = TradegeckoCompany::find()->where(['id'=>$tradegeckoOrders[0]->company_id])->one();

            $order = [];
            $order['ifs_order_number'] = '';
            $order['tradegecko_order_ids'] = serialize($_GET['params']);
            $order['liteview_order_status'] = 'Active';
            $order['created_at'] = date('Y-m-d');
            $order['updated_at'] = date('Y-m-d');
            $order['submitted_at'] = '';
            $modelOrder = new LiteviewOrder;
            $modelOrder->attributes = $order;
            $modelOrder->save();

            $newOrderId = $modelOrder->id;

            $orderDetails = [];
            $orderDetails['liteview_order_id'] = $newOrderId;
            $orderDetails['order_status'] = 'Active';
            $orderDetails['order_date'] = date('Y-m-d');
            $orderDetails['order_number'] = date('Ymd_His');
            /*foreach($tradegeckoOrders as $tradegeckoOrder)
                $orderDetails['order_number'] .= $tradegeckoOrder->order_number.'_';*/
            $orderDetails['order_source'] = 'Web';
            $orderDetails['order_type'] = 'Regular';
            $orderDetails['catalog_name'] = 'Default';
            $orderDetails['gift_order'] = 'False';
            $orderDetails['po_number'] = $orderDetails['order_number'];
            $orderDetails['passthrough_field_01'] = 'passthrough information';
            $orderDetails['passthrough_field_02'] = 'passthrough information';
            $orderDetails['allocate_inventory_now'] = 'TRUE';
            $modelOrderDetails = new LiteviewOrderDetails;
            $modelOrderDetails->attributes = $orderDetails;
            $modelOrderDetails->save();

            $billingContact = [];
            if ($this->demo == false)
            {
                $billingContact['liteview_order_id'] = $newOrderId;
                $billingContact['billto_prefix'] = '';
                $billingContact['billto_first_name'] = $user->first_name;
                $billingContact['billto_last_name'] = $user->last_name;
                $billingContact['billto_suffix'] = '';
                $billingContact['billto_company_name'] = $billingAddress->company_name;
                $billingContact['billto_address1'] = $billingAddress->address1;
                $billingContact['billto_address2'] = $billingAddress->address2;
                $billingContact['billto_address3'] = '';
                $billingContact['billto_city'] = $billingAddress->city;
                $billingContact['billto_state'] = $billingAddress->state;
                $billingContact['billto_postal_code'] = $billingAddress->zip_code;
                $billingContact['billto_country'] = $billingAddress->country;
                $billingContact['billto_telephone_no'] = $billingAddress->phone_number;
                $billingContact['billto_email'] = $billingAddress->email;
            }
            else
            {   
                $billingContact['liteview_order_id'] = $newOrderId;
                $billingContact['billto_prefix'] = '';
                $billingContact['billto_first_name'] = $user->first_name;
                $billingContact['billto_last_name'] = $user->last_name;
                $billingContact['billto_suffix'] = '';
                $billingContact['billto_company_name'] = $billingAddress->company_name;
                $billingContact['billto_address1'] = '14245 Artesia Blvd';
                $billingContact['billto_address2'] = '';
                $billingContact['billto_address3'] = '';
                $billingContact['billto_city'] = 'La Mirada';
                $billingContact['billto_state'] = 'CA';
                $billingContact['billto_postal_code'] = '90638';
                $billingContact['billto_country'] = 'US';
                $billingContact['billto_telephone_no'] = $billingAddress->phone_number;
                $billingContact['billto_email'] = $billingAddress->email;
            }
            $modelBillingContact = new LiteviewOrderBillingContact;
            $modelBillingContact->attributes = $billingContact;
            $modelBillingContact->save();

            $shippingContact = [];
            if ($this->demo == false)
            {
                $shippingContact['liteview_order_id'] = $newOrderId;
                $shippingContact['shipto_prefix'] = '';
                $shippingContact['shipto_first_name'] = $user->first_name;
                $shippingContact['shipto_last_name'] = $user->last_name;
                $shippingContact['shipto_suffix'] = '';
                $shippingContact['shipto_company_name'] = $shippingAddress->company_name;
                $shippingContact['shipto_address1'] = $shippingAddress->address1;
                $shippingContact['shipto_address2'] = $shippingAddress->address2;
                $shippingContact['shipto_address3'] = '';
                $shippingContact['shipto_city'] = $shippingAddress->city;
                $shippingContact['shipto_state'] = $shippingAddress->state;
                $shippingContact['shipto_postal_code'] = $shippingAddress->zip_code;
                $shippingContact['shipto_country'] = $shippingAddress->country;
                $shippingContact['shipto_telephone_no'] = $shippingAddress->phone_number;
                $shippingContact['shipto_email'] = $shippingAddress->email;
            }
            else
            {
                $shippingContact['liteview_order_id'] = $newOrderId;
                $shippingContact['shipto_prefix'] = '';
                $shippingContact['shipto_first_name'] = $user->first_name;
                $shippingContact['shipto_last_name'] = $user->last_name;
                $shippingContact['shipto_suffix'] = '';
                $shippingContact['shipto_company_name'] = $shippingAddress->company_name;
                $shippingContact['shipto_address1'] = '14245 Artesia Blvd';
                $shippingContact['shipto_address2'] = 'Ste 2';
                $shippingContact['shipto_address3'] = '';
                $shippingContact['shipto_city'] = 'La Mirada';
                $shippingContact['shipto_state'] = 'CA';
                $shippingContact['shipto_postal_code'] = '90638';
                $shippingContact['shipto_country'] = 'US';
                $shippingContact['shipto_telephone_no'] = $shippingAddress->phone_number;
                $shippingContact['shipto_email'] = $shippingAddress->email;
            }
            $modelShippingContact = new LiteviewOrderShippingContact;
            $modelShippingContact->attributes = $shippingContact;
            $modelShippingContact->save();

            $billingDetails = [];
            if ($this->demo == false)
            {
                $billingDetails['liteview_order_id'] = $newOrderId;
                $billingDetails['sub_total'] = 0;
                foreach($tradegeckoOrders as $tradegeckoOrder)
                    $billingDetails['sub_total'] += floatval($tradegeckoOrder->total);
                $billingDetails['shipping_handling'] = floatval(Yii::$app->params['tradegecko']['shipping_charge']);
                $billingDetails['sales_tax_total'] = 0;
                $billingDetails['discount_total'] = 0;
                $billingDetails['grand_total'] = $billingDetails['sub_total'] + $billingDetails['shipping_handling'] + $billingDetails['sales_tax_total'] + $billingDetails['discount_total'];
            }
            else
            {
                $billingDetails['liteview_order_id'] = $newOrderId;
                $billingDetails['sub_total'] = '10.99';
                $billingDetails['shipping_handling'] = '5.99';
                $billingDetails['sales_tax_total'] = '0.00';
                $billingDetails['discount_total'] = '0.00';
                $billingDetails['grand_total'] = '16.98';
            }
            $modelBillingDetails = new LiteviewOrderBillingDetails;
            $modelBillingDetails->attributes = $billingDetails;
            $modelBillingDetails->save();

            $shippingDetails = [];
            $shippingDetails['liteview_order_id'] = $newOrderId;
            $shippingDetails['ship_method'] = 'UPS Ground';
            $shippingDetails['signature_requested'] = 'FALSE';
            $shippingDetails['insurance_requested'] = 'FALSE';
            $shippingDetails['insurance_value'] = '300.00';
            $shippingDetails['saturday_delivery_requested'] = 'TRUE';
            $shippingDetails['third_party_billing_requested'] = 'TRUE';
            $shippingDetails['third_party_billing_account_no'] = 'AE999947';
            if ($this->demo == false)
                $shippingDetails['third_party_billing_zip'] = $shippingAddress->zip_code;
            else
                $shippingDetails['third_party_billing_zip'] = '90638';
            $shippingDetails['third_party_country'] = 'US';
            if ($this->demo == false)
            {
                $obj = new Liteview;
                $countries = $obj->getOrderCountries();
                foreach($countries as $key=>$val)
                {
                    if ($val == $shippingAddress->country)
                        $shippingDetails['third_party_country'] = $key;
                }
            }
            $shippingDetails['general_description'] = 'Test items being shipped for testing';
            $shippingDetails['content_description'] = 'sample items';
            $modelShippingDetails = new LiteviewOrderShippingDetails;
            $modelShippingDetails->attributes = $shippingDetails;
            $modelShippingDetails->save();

            $orderNotes = [];
            $orderNotes['liteview_order_id'] = $newOrderId;
            $orderNotes['note_type'] = 'shipping';
            $orderNotes['note_description'] = 'Note: You were only charged $55.00. This invoice details the retail value of your order.
                                                    Thank you, 
                                                    Trendy Butler Team';
            $orderNotes['show_on_ps'] = 'TRUE';
            $modelOrderNotes = new LiteviewOrderNotes;
            $modelOrderNotes->attributes = $orderNotes;
            $modelOrderNotes->save();

            $orderItems = [];
            if ($this->demo == false)
            {
                foreach($tradegeckoOrders as $tradegeckoOrder)
                {
                    $tradegeckoOrderLineItems = TradegeckoOrderLineItem::find()->joinWith(['tradegeckoVariant'])->where(['order_id'=>$tradegeckoOrder->id])->all();
                    if ($tradegeckoOrderLineItems != null)
                    {
                        foreach($tradegeckoOrderLineItems as $tradegeckoOrderLineItem)
                        {
                            $orderItem = [];
                            $orderItem['liteview_order_id'] = $newOrderId;
                            $orderItem['inventory_item'] = $tradegeckoOrderLineItem->tradegeckoVariant->sku;
                            $orderItem['inventory_item_sku'] = $tradegeckoOrderLineItem->tradegeckoVariant->sku;
                            $orderItem['inventory_item_description'] = $tradegeckoOrderLineItem->tradegeckoVariant->sku.' - '.$tradegeckoOrderLineItem->tradegeckoVariant->product_name.' - '.$tradegeckoOrderLineItem->tradegeckoVariant->variant_name;
                            $orderItem['inventory_item_price'] = $tradegeckoOrderLineItem->price;
                            $orderItem['inventory_item_qty'] = $tradegeckoOrderLineItem->quantity;
                            $orderItem['inventory_item_ext_price'] = floatval($tradegeckoOrderLineItem->price) * floatval($tradegeckoOrderLineItem->quantity);
                            if ($tradegeckoOrderLineItem->discount)
                                $orderItem['inventory_item_ext_price'] = floatval($tradegeckoOrderLineItem->price) * floatval($tradegeckoOrderLineItem->quantity) * (100 - floatval($tradegeckoOrderLineItem->discount)) / 100;
                            $orderItem['inventory_passthrough_01'] = 'non-related lv id';
                            $orderItem['inventory_passthrough_02'] = 'non-related lv id';
                            $orderItem['tradegecko_currency_id'] = $tradegeckoOrder->currency_id;

                            $orderItems[] = $orderItem;

                            $modelOrderItem = new LiteviewOrderItems;
                            $modelOrderItem->attributes = $orderItem;
                            $modelOrderItem->save();
                        }
                    }
                }
            }
            else
            {
                $defaultCurrency = TradegeckoCurrency::find()->one();
                $defaultCurrencyId = $defaultCurrency->id;

                $orderItem1 = [];
                $orderItem1['liteview_order_id'] = $newOrderId;
                $orderItem1['inventory_item'] = 'TEST1001';
                $orderItem1['inventory_item_sku'] = 'TEST1001';
                $orderItem1['inventory_item_description'] = 'Test Item Description';
                $orderItem1['inventory_item_price'] = '5.00';
                $orderItem1['inventory_item_qty'] = '1';
                $orderItem1['inventory_item_ext_price'] = '5.00';
                $orderItem1['inventory_passthrough_01'] = 'non-related lv id';
                $orderItem1['inventory_passthrough_02'] = 'non-related lv id';
                $orderItem1['tradegecko_currency_id'] = $defaultCurrencyId;

                $orderItem2 = [];
                $orderItem2['liteview_order_id'] = $newOrderId;
                $orderItem2['inventory_item'] = 'TEST1002';
                $orderItem2['inventory_item_sku'] = 'TEST1002';
                $orderItem2['inventory_item_description'] = 'Test Item 2 Description';
                $orderItem2['inventory_item_price'] = '6.99';
                $orderItem2['inventory_item_qty'] = '1';
                $orderItem2['inventory_item_ext_price'] = '6.99';
                $orderItem2['inventory_passthrough_01'] = 'non-related lv id';
                $orderItem2['inventory_passthrough_02'] = 'non-related lv id';
                $orderItem2['tradegecko_currency_id'] = $defaultCurrencyId;

                $orderItems[] = $orderItem1;
                $orderItems[] = $orderItem2;

                $modelOrderItem1 = new LiteviewOrderItems;
                $modelOrderItem1->attributes = $orderItem1;
                $modelOrderItem1->save();

                $modelOrderItem2 = new LiteviewOrderItems;
                $modelOrderItem2->attributes = $orderItem2;
                $modelOrderItem2->save();
            }

            $liteviewOrder = [];
            $liteviewOrder['id'] = $newOrderId;
            $liteviewOrder['submit_order'] = [];
            $liteviewOrder['submit_order']['order_info'] = [
                'order_details' => $orderDetails,
                'billing_contact' => $billingContact,
                'shipping_contact' => $shippingContact,
                'billing_details' => $billingDetails,
                'shipping_details' => $shippingDetails,
                'order_notes' => $orderNotes,
                'order_items' => $orderItems,
            ];

            $flag = '0';
            if ($modelOrder->liteview_order_status == 'Submitted' || $modelOrder->liteview_order_status == 'Re-Submitted')
                $flag = '1';
            else if ($modelOrder->liteview_order_status == 'Cancelled')
                $flag = '2';

            echo $this->renderPartial('detail', array('liteviewOrder'=>$liteviewOrder, 'flag'=>$flag, 'status'=>$modelOrder->liteview_order_status));
            Yii::$app->end();
        }
        else
        {
            $searchModel = new TradegeckoOrderSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams)->dataProvider;

            return $this->render('create', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
            ]);
        }
    }

    public function actionOrderdelete($id)
    {
        $modelLiteviewOrder = LiteviewOrder::find()->where(['id'=>$id])->one();
        if ($modelLiteviewOrder != null)
            $modelLiteviewOrder->delete();

        $modelLiteviewOrderBillingContact = LiteviewOrderBillingContact::find()->where(['liteview_order_id'=>$id])->one();
        if ($modelLiteviewOrderBillingContact != null)
            $modelLiteviewOrderBillingContact->delete();

        $modelLiteviewOrderBillingDetails = LiteviewOrderBillingDetails::find()->where(['liteview_order_id'=>$id])->one();
        if ($modelLiteviewOrderBillingDetails != null)
            $modelLiteviewOrderBillingDetails->delete();

        $modelLiteviewOrderDetails = LiteviewOrderDetails::find()->where(['liteview_order_id'=>$id])->one();
        if ($modelLiteviewOrderDetails != null)
            $modelLiteviewOrderDetails->delete();

        $modelLiteviewOrderItems = LiteviewOrderItems::find()->where(['liteview_order_id'=>$id])->all();
        if ($modelLiteviewOrderItems != null)
        {
            foreach($modelLiteviewOrderItems as $modelLiteviewOrderItem)
                $modelLiteviewOrderItem->delete();
        }

        $modelLiteviewOrderNotes = LiteviewOrderNotes::find()->where(['liteview_order_id'=>$id])->one();
        if ($modelLiteviewOrderNotes != null)
            $modelLiteviewOrderNotes->delete();

        $modelLiteviewOrderShippingContact = LiteviewOrderShippingContact::find()->where(['liteview_order_id'=>$id])->one();
        if ($modelLiteviewOrderShippingContact != null)
            $modelLiteviewOrderShippingContact->delete();

        $modelLiteviewOrderShippingDetails = LiteviewOrderShippingDetails::find()->where(['liteview_order_id'=>$id])->one();
        if ($modelLiteviewOrderShippingDetails != null)
            $modelLiteviewOrderShippingDetails->delete();

        $this->redirect('index');
    }

    public function actionOrdersubmit($id)
    {
        $modelLiteviewOrder = LiteviewOrder::find()->where(['id'=>$id])->one();
        if ($modelLiteviewOrder != null)
        {
            $orderDetails = [];
            $modelLiteviewOrderDetails = LiteviewOrderDetails::find()->where(['liteview_order_id'=>$id])->one();
            if ($modelLiteviewOrderDetails != null)
            {
                $orderDetails = $modelLiteviewOrderDetails->attributes;
                unset($orderDetails['id']);
                unset($orderDetails['future_date_release']);
                unset($orderDetails['liteview_order_id']);
            }

            $billingContact = [];
            $modelLiteviewOrderBillingContact = LiteviewOrderBillingContact::find()->where(['liteview_order_id'=>$id])->one();
            if ($modelLiteviewOrderBillingContact != null)
            {
                $billingContact = $modelLiteviewOrderBillingContact->attributes;
                unset($billingContact['id']);
                unset($billingContact['liteview_order_id']);
            }

            $shippingContact = [];
            $modelLiteviewOrderShippingContact = LiteviewOrderShippingContact::find()->where(['liteview_order_id'=>$id])->one();
            if ($modelLiteviewOrderShippingContact != null)
            {
                $shippingContact = $modelLiteviewOrderShippingContact->attributes;
                unset($shippingContact['id']);
                unset($shippingContact['liteview_order_id']);
            }

            $billingDetails = [];
            $modelLiteviewOrderBillingDetails = LiteviewOrderBillingDetails::find()->where(['liteview_order_id'=>$id])->one();
            if ($modelLiteviewOrderBillingDetails != null)
            {
                $billingDetails = $modelLiteviewOrderBillingDetails->attributes;
                unset($billingDetails['id']);
                unset($billingDetails['liteview_order_id']);
                $billingDetails['grand_total'] = floatval($billingDetails['sub_total']) + floatval($billingDetails['shipping_handling']) + floatval($billingDetails['sales_tax_total']) + floatval($billingDetails['discount_total']);
                $billingDetails['sub_total'] = floatval($billingDetails['sub_total']);
                $billingDetails['shipping_handling'] = number_format(floatval($billingDetails['shipping_handling']), 2);
                $billingDetails['sales_tax_total'] = floatval($billingDetails['sales_tax_total']);
                $billingDetails['discount_total'] = floatval($billingDetails['discount_total']);
            }

            $shippingDetails = [];
            $modelLiteviewOrderShippingDetails = LiteviewOrderShippingDetails::find()->where(['liteview_order_id'=>$id])->one();
            if ($modelLiteviewOrderShippingDetails != null)
            {
                $shippingOptions = $modelLiteviewOrderShippingDetails->attributes;
                unset($shippingOptions['id']);
                unset($shippingOptions['liteview_order_id']);
                $shippingDetails['ship_method'] = $shippingOptions['ship_method'];
                unset($shippingOptions['ship_method']);
                $shippingDetails['ship_options'] = $shippingOptions;
            }

            $orderNotes = [];
            $modelLiteviewOrderNotes = LiteviewOrderNotes::find()->where(['liteview_order_id'=>$id])->one();
            if ($modelLiteviewOrderNotes != null)
            {
                $orderNotes['note'] = $modelLiteviewOrderNotes->attributes;
                unset($orderNotes['note']['id']);
                unset($orderNotes['note']['liteview_order_id']);
            }

            $orderItems = [];
            $modelLiteviewOrderItems = LiteviewOrderItems::find()->where(['liteview_order_id'=>$id])->all();
            if ($modelLiteviewOrderItems != null)
            {
                $orderItems['total_line_items'] = count($modelLiteviewOrderItems);
                foreach($modelLiteviewOrderItems as $modelLiteviewOrderItem)
                {
                    $orderItem = $modelLiteviewOrderItem->attributes;
                    unset($orderItem['id']);
                    unset($orderItem['liteview_order_id']);
                    unset($orderItem['tradegecko_currency_id']);
                    $orderItem['inventory_item_price'] = floatval($orderItem['inventory_item_price']);
                    $orderItem['inventory_item_ext_price'] = floatval($orderItem['inventory_item_ext_price']);
                    $orderItems[] = $orderItem;
                }
            }

            $params = [];
            $params['submit_order'] = [];
            $params['submit_order']['order_info'] = [
                'order_details' => $orderDetails,
                'billing_contact' => $billingContact,
                'shipping_contact' => $shippingContact,
                'billing_details' => $billingDetails,
                'shipping_details' => $shippingDetails,
                'order_notes' => $orderNotes,
                'order_items' => $orderItems,
            ];

            $obj = new Liteview;
            $ret = $obj->submitOrder($params);

            if ($ret['code'] == '0') // ERROR
            {
            }
            else if ($ret['code'] == '1') // SUCCESS
            {
                $ifsOrderNumber = $ret['details']['ifs_order_number'];
                $modelLiteviewOrder->ifs_order_number = $ifsOrderNumber;
                $modelLiteviewOrder->liteview_order_status = 'Submitted';
                $modelLiteviewOrder->submitted_at = date('Y-m-d H:i:s');
                $modelLiteviewOrder->save();
            }

            $retStr = json_encode($ret);
            echo $retStr;
        }

        Yii::$app->end();
    }

    public function actionOrderedit($id)
    {
        $modelLiteviewOrder = LiteviewOrder::find()->where(['id'=>$id])->one();
        if ($modelLiteviewOrder != null)
        {
            $searchModel = new TradegeckoOrderSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams)->dataProvider;

            $orderDetails = [];
            $modelLiteviewOrderDetails = LiteviewOrderDetails::find()->where(['liteview_order_id'=>$id])->one();
            if ($modelLiteviewOrderDetails != null)
                $orderDetails = $modelLiteviewOrderDetails->attributes;

            $billingContact = [];
            $modelLiteviewOrderBillingContact = LiteviewOrderBillingContact::find()->where(['liteview_order_id'=>$id])->one();
            if ($modelLiteviewOrderBillingContact != null)
                $billingContact = $modelLiteviewOrderBillingContact->attributes;

            $shippingContact = [];
            $modelLiteviewOrderShippingContact = LiteviewOrderShippingContact::find()->where(['liteview_order_id'=>$id])->one();
            if ($modelLiteviewOrderShippingContact != null)
                $shippingContact = $modelLiteviewOrderShippingContact->attributes;

            $billingDetails = [];
            $modelLiteviewOrderBillingDetails = LiteviewOrderBillingDetails::find()->where(['liteview_order_id'=>$id])->one();
            if ($modelLiteviewOrderBillingDetails != null)
            {
                $billingDetails = $modelLiteviewOrderBillingDetails->attributes;
                $billingDetails['grand_total'] = floatval($billingDetails['sub_total']) + floatval($billingDetails['shipping_handling']) + floatval($billingDetails['sales_tax_total']) + floatval($billingDetails['discount_total']);
                $billingDetails['sub_total'] = floatval($billingDetails['sub_total']);
                $billingDetails['shipping_handling'] = number_format(floatval($billingDetails['shipping_handling']), 2);
                $billingDetails['sales_tax_total'] = floatval($billingDetails['sales_tax_total']);
                $billingDetails['discount_total'] = floatval($billingDetails['discount_total']);
            }

            $shippingDetails = [];
            $modelLiteviewOrderShippingDetails = LiteviewOrderShippingDetails::find()->where(['liteview_order_id'=>$id])->one();
            if ($modelLiteviewOrderShippingDetails != null)
                $shippingDetails = $modelLiteviewOrderShippingDetails->attributes;

            $orderNotes = [];
            $modelLiteviewOrderNotes = LiteviewOrderNotes::find()->where(['liteview_order_id'=>$id])->one();
            if ($modelLiteviewOrderNotes != null)
                $orderNotes = $modelLiteviewOrderNotes->attributes;

            $orderItems = [];
            $modelLiteviewOrderItems = LiteviewOrderItems::find()->where(['liteview_order_id'=>$id])->all();
            if ($modelLiteviewOrderItems != null)
            {
                foreach($modelLiteviewOrderItems as $modelLiteviewOrderItem)
                {
                    $orderItem = $modelLiteviewOrderItem->attributes;
                    $orderItem['inventory_item_price'] = floatval($orderItem['inventory_item_price']);
                    $orderItem['inventory_item_ext_price'] = floatval($orderItem['inventory_item_ext_price']);
                    $orderItems[] = $orderItem;
                }
            }

            $liteviewOrder = [];
            $liteviewOrder['id'] = $id;
            $liteviewOrder['submit_order'] = [];
            $liteviewOrder['submit_order']['order_info'] = [
                'order_details' => $orderDetails,
                'billing_contact' => $billingContact,
                'shipping_contact' => $shippingContact,
                'billing_details' => $billingDetails,
                'shipping_details' => $shippingDetails,
                'order_notes' => $orderNotes,
                'order_items' => $orderItems,
            ];

            return $this->render('edit', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'modelLiteviewOrder' => $modelLiteviewOrder,
                'liteviewOrder' => $liteviewOrder,
            ]);
        }
        else
            throw new NotFoundHttpException('The requested order not exist.'); 
    }

    public function actionEditable($id)
    {
        $modelLiteviewOrder = LiteviewOrder::find()->where(['id'=>$id])->one();
        if ($modelLiteviewOrder != null)
        {
            $shippingMethods = [];
            $obj = new Liteview;
            $shippingMethods = $obj->getOrderMethods();

            $orderDetails = [];
            $modelLiteviewOrderDetails = LiteviewOrderDetails::find()->where(['liteview_order_id'=>$id])->one();
            if ($modelLiteviewOrderDetails != null)
                $orderDetails = $modelLiteviewOrderDetails->attributes;

            $billingContact = [];
            $modelLiteviewOrderBillingContact = LiteviewOrderBillingContact::find()->where(['liteview_order_id'=>$id])->one();
            if ($modelLiteviewOrderBillingContact != null)
                $billingContact = $modelLiteviewOrderBillingContact->attributes;

            $shippingContact = [];
            $modelLiteviewOrderShippingContact = LiteviewOrderShippingContact::find()->where(['liteview_order_id'=>$id])->one();
            if ($modelLiteviewOrderShippingContact != null)
                $shippingContact = $modelLiteviewOrderShippingContact->attributes;

            $billingDetails = [];
            $modelLiteviewOrderBillingDetails = LiteviewOrderBillingDetails::find()->where(['liteview_order_id'=>$id])->one();
            if ($modelLiteviewOrderBillingDetails != null)
            {
                $billingDetails = $modelLiteviewOrderBillingDetails->attributes;
                $billingDetails['grand_total'] = floatval($billingDetails['sub_total']) + floatval($billingDetails['shipping_handling']) + floatval($billingDetails['sales_tax_total']) + floatval($billingDetails['discount_total']);
                $billingDetails['sub_total'] = floatval($billingDetails['sub_total']);
                $billingDetails['shipping_handling'] = number_format(floatval($billingDetails['shipping_handling']), 2);
                $billingDetails['sales_tax_total'] = floatval($billingDetails['sales_tax_total']);
                $billingDetails['discount_total'] = floatval($billingDetails['discount_total']);
            }

            $shippingDetails = [];
            $modelLiteviewOrderShippingDetails = LiteviewOrderShippingDetails::find()->where(['liteview_order_id'=>$id])->one();
            if ($modelLiteviewOrderShippingDetails != null)
                $shippingDetails = $modelLiteviewOrderShippingDetails->attributes;

            $orderNotes = [];
            $modelLiteviewOrderNotes = LiteviewOrderNotes::find()->where(['liteview_order_id'=>$id])->one();
            if ($modelLiteviewOrderNotes != null)
                $orderNotes = $modelLiteviewOrderNotes->attributes;

            $orderItems = [];
            $modelLiteviewOrderItems = LiteviewOrderItems::find()->where(['liteview_order_id'=>$id])->all();
            if ($modelLiteviewOrderItems != null)
            {
                foreach($modelLiteviewOrderItems as $modelLiteviewOrderItem)
                {
                    $orderItem = $modelLiteviewOrderItem->attributes;
                    $orderItem['inventory_item_price'] = floatval($orderItem['inventory_item_price']);
                    $orderItem['inventory_item_ext_price'] = floatval($orderItem['inventory_item_ext_price']);
                    $orderItems[] = $orderItem;
                }
            }

            $liteviewOrder = [];
            $liteviewOrder['id'] = $id;
            $liteviewOrder['submit_order'] = [];
            $liteviewOrder['submit_order']['order_info'] = [
                'order_details' => $orderDetails,
                'billing_contact' => $billingContact,
                'shipping_contact' => $shippingContact,
                'billing_details' => $billingDetails,
                'shipping_details' => $shippingDetails,
                'order_notes' => $orderNotes,
                'order_items' => $orderItems,
            ];

            $flag = '0';
            if ($modelLiteviewOrder->liteview_order_status == 'Submitted' || $modelLiteviewOrder->liteview_order_status == 'Re-Submitted')
                $flag = '1';
            else if ($modelLiteviewOrder->liteview_order_status == 'Cancelled')
                $flag = '2';

            echo $this->renderPartial('editable', ['liteviewOrder' => $liteviewOrder, 'shippingMethods'=>$shippingMethods, 'flag'=>$flag, 'status'=>$modelLiteviewOrder->liteview_order_status]);
            Yii::$app->end();
        }
        else
            throw new NotFoundHttpException('The requested order not exist.'); 
    }

    public function actionUpdate($id)
    {
        $params = Yii::$app->request->get();

        $shippingDetails = $params['shippingDetails'];
        $modelShippingDetails = LiteviewOrderShippingDetails::find()->where(['liteview_order_id' => $id])->one();
        if ($modelShippingDetails != null)
        {
            $modelShippingDetails->attributes = $shippingDetails;
            $modelShippingDetails->save();
        }

        $orderNotes = $params['orderNotes'];
        $modelOrderNotes = LiteviewOrderNotes::find()->where(['liteview_order_id' => $id])->one();
        if ($modelOrderNotes != null)
        {
            $modelOrderNotes->attributes = $orderNotes;
            $modelOrderNotes->save();
        }

        $orderDetails = [];
        $modelLiteviewOrderDetails = LiteviewOrderDetails::find()->where(['liteview_order_id'=>$id])->one();
        if ($modelLiteviewOrderDetails != null)
            $orderDetails = $modelLiteviewOrderDetails->attributes;

        $billingContact = [];
        $modelLiteviewOrderBillingContact = LiteviewOrderBillingContact::find()->where(['liteview_order_id'=>$id])->one();
        if ($modelLiteviewOrderBillingContact != null)
            $billingContact = $modelLiteviewOrderBillingContact->attributes;

        $shippingContact = [];
        $modelLiteviewOrderShippingContact = LiteviewOrderShippingContact::find()->where(['liteview_order_id'=>$id])->one();
        if ($modelLiteviewOrderShippingContact != null)
            $shippingContact = $modelLiteviewOrderShippingContact->attributes;

        $billingDetails = [];
        $modelLiteviewOrderBillingDetails = LiteviewOrderBillingDetails::find()->where(['liteview_order_id'=>$id])->one();
        if ($modelLiteviewOrderBillingDetails != null)
        {
            $billingDetails = $modelLiteviewOrderBillingDetails->attributes;
            $billingDetails['grand_total'] = floatval($billingDetails['sub_total']) + floatval($billingDetails['shipping_handling']) + floatval($billingDetails['sales_tax_total']) + floatval($billingDetails['discount_total']);
            $billingDetails['sub_total'] = floatval($billingDetails['sub_total']);
            $billingDetails['shipping_handling'] = number_format(floatval($billingDetails['shipping_handling']), 2);
            $billingDetails['sales_tax_total'] = floatval($billingDetails['sales_tax_total']);
            $billingDetails['discount_total'] = floatval($billingDetails['discount_total']);
        }

        $shippingDetails = [];
        $modelLiteviewOrderShippingDetails = LiteviewOrderShippingDetails::find()->where(['liteview_order_id'=>$id])->one();
        if ($modelLiteviewOrderShippingDetails != null)
            $shippingDetails = $modelLiteviewOrderShippingDetails->attributes;

        $orderNotes = [];
        $modelLiteviewOrderNotes = LiteviewOrderNotes::find()->where(['liteview_order_id'=>$id])->one();
        if ($modelLiteviewOrderNotes != null)
            $orderNotes = $modelLiteviewOrderNotes->attributes;

        $orderItems = [];
        $modelLiteviewOrderItems = LiteviewOrderItems::find()->where(['liteview_order_id'=>$id])->all();
        if ($modelLiteviewOrderItems != null)
        {
            foreach($modelLiteviewOrderItems as $modelLiteviewOrderItem)
            {
                $orderItem = $modelLiteviewOrderItem->attributes;
                $orderItem['inventory_item_price'] = floatval($orderItem['inventory_item_price']);
                $orderItem['inventory_item_ext_price'] = floatval($orderItem['inventory_item_ext_price']);
                $orderItems[] = $orderItem;
            }
        }

        $liteviewOrder = [];
        $liteviewOrder['id'] = $id;
        $liteviewOrder['submit_order'] = [];
        $liteviewOrder['submit_order']['order_info'] = [
            'order_details' => $orderDetails,
            'billing_contact' => $billingContact,
            'shipping_contact' => $shippingContact,
            'billing_details' => $billingDetails,
            'shipping_details' => $shippingDetails,
            'order_notes' => $orderNotes,
            'order_items' => $orderItems,
        ];

        $modelLiteviewOrder = LiteviewOrder::find()->where(['id' => $id])->one();
        $flag = '0';
        if ($modelLiteviewOrder->liteview_order_status == 'Submitted' || $modelLiteviewOrder->liteview_order_status == 'Re-Submitted')
            $flag = '1';
        else if ($modelLiteviewOrder->liteview_order_status == 'Cancelled')
            $flag = '2';

        echo $this->renderPartial('detail', ['liteviewOrder' => $liteviewOrder, 'flag' => $flag, 'status'=>$modelLiteviewOrder->liteview_order_status]);
        Yii::$app->end();
    }

    public function actionOrdercancel($id)
    {
        $modelLiteviewOrder = LiteviewOrder::find()->where(['id'=>$id])->one();
        if ($modelLiteviewOrder != null)
        {
            $orderDetails = [];

            $orderDetails['client_order_number'] = '';
            $modelLiteviewOrderDetails = LiteviewOrderDetails::find()->where(['liteview_order_id'=>$id])->one();
            if ($modelLiteviewOrderDetails != null)
                $orderDetails['client_order_number'] = $modelLiteviewOrderDetails->order_number;
            $orderDetails['ifs_order_number'] = $modelLiteviewOrder->ifs_order_number;
            $orderDetails['notes_for_cancellation'] = 'cancel order';

            $orderInfo = [];
            $orderInfo['order_details'] = $orderDetails;

            $cancelOrder = [];
            $cancelOrder['order_info'] = $orderInfo;

            $params = [];
            $params['cancel_order'] = $cancelOrder;

            $obj = new Liteview;
            $ret = $obj->cancelOrder($params);

            if ($ret['code'] == '1')
            {
                if ($ret['details']['status'] == 'Cancelled' && $ret['details']['ifs_order_number'] == $orderDetails['ifs_order_number'])
                {
                    $modelLiteviewOrder->cancelled_at = date('Y-m-d H:i:s');
                    $modelLiteviewOrder->liteview_order_status = 'Cancelled';
                    $modelLiteviewOrder->save();
                }
            }

            $retStr = json_encode($ret);
            echo $retStr;

            Yii::$app->end();

            //$this->redirect('index');
        }
        else
            throw new NotFoundHttpException('The requested order not exist.'); 
    }

    public function actionOrderresubmit($id)
    {
        $modelLiteviewOrder = LiteviewOrder::find()->where(['id'=>$id])->one();
        if ($modelLiteviewOrder != null)
        {
            $orderDetails = [];
            $modelLiteviewOrderDetails = LiteviewOrderDetails::find()->where(['liteview_order_id'=>$id])->one();
            if ($modelLiteviewOrderDetails != null)
            {
                $orderDetails = $modelLiteviewOrderDetails->attributes;
                unset($orderDetails['id']);
                unset($orderDetails['future_date_release']);
                unset($orderDetails['liteview_order_id']);
            }

            $billingContact = [];
            $modelLiteviewOrderBillingContact = LiteviewOrderBillingContact::find()->where(['liteview_order_id'=>$id])->one();
            if ($modelLiteviewOrderBillingContact != null)
            {
                $billingContact = $modelLiteviewOrderBillingContact->attributes;
                unset($billingContact['id']);
                unset($billingContact['liteview_order_id']);
            }

            $shippingContact = [];
            $modelLiteviewOrderShippingContact = LiteviewOrderShippingContact::find()->where(['liteview_order_id'=>$id])->one();
            if ($modelLiteviewOrderShippingContact != null)
            {
                $shippingContact = $modelLiteviewOrderShippingContact->attributes;
                unset($shippingContact['id']);
                unset($shippingContact['liteview_order_id']);
            }

            $billingDetails = [];
            $modelLiteviewOrderBillingDetails = LiteviewOrderBillingDetails::find()->where(['liteview_order_id'=>$id])->one();
            if ($modelLiteviewOrderBillingDetails != null)
            {
                $billingDetails = $modelLiteviewOrderBillingDetails->attributes;
                unset($billingDetails['id']);
                unset($billingDetails['liteview_order_id']);
                $billingDetails['grand_total'] = floatval($billingDetails['sub_total']) + floatval($billingDetails['shipping_handling']) + floatval($billingDetails['sales_tax_total']) + floatval($billingDetails['discount_total']);
                $billingDetails['sub_total'] = floatval($billingDetails['sub_total']);
                $billingDetails['shipping_handling'] = number_format(floatval($billingDetails['shipping_handling']), 2);
                $billingDetails['sales_tax_total'] = floatval($billingDetails['sales_tax_total']);
                $billingDetails['discount_total'] = floatval($billingDetails['discount_total']);
            }

            $shippingDetails = [];
            $modelLiteviewOrderShippingDetails = LiteviewOrderShippingDetails::find()->where(['liteview_order_id'=>$id])->one();
            if ($modelLiteviewOrderShippingDetails != null)
            {
                $shippingOptions = $modelLiteviewOrderShippingDetails->attributes;
                unset($shippingOptions['id']);
                unset($shippingOptions['liteview_order_id']);
                $shippingDetails['ship_method'] = $shippingOptions['ship_method'];
                unset($shippingOptions['ship_method']);
                $shippingDetails['ship_options'] = $shippingOptions;
            }

            $orderNotes = [];
            $modelLiteviewOrderNotes = LiteviewOrderNotes::find()->where(['liteview_order_id'=>$id])->one();
            if ($modelLiteviewOrderNotes != null)
            {
                $orderNotes['note'] = $modelLiteviewOrderNotes->attributes;
                unset($orderNotes['note']['id']);
                unset($orderNotes['note']['liteview_order_id']);
            }

            $orderItems = [];
            $modelLiteviewOrderItems = LiteviewOrderItems::find()->where(['liteview_order_id'=>$id])->all();
            if ($modelLiteviewOrderItems != null)
            {
                $orderItems['total_line_items'] = count($modelLiteviewOrderItems);
                foreach($modelLiteviewOrderItems as $modelLiteviewOrderItem)
                {
                    $orderItem = $modelLiteviewOrderItem->attributes;
                    unset($orderItem['id']);
                    unset($orderItem['liteview_order_id']);
                    unset($orderItem['tradegecko_currency_id']);
                    $orderItem['inventory_item_price'] = floatval($orderItem['inventory_item_price']);
                    $orderItem['inventory_item_ext_price'] = floatval($orderItem['inventory_item_ext_price']);
                    $orderItems[] = $orderItem;
                }
            }

            $params = [];
            $params['submit_order'] = [];
            $params['submit_order']['order_info'] = [
                'order_details' => $orderDetails,
                'billing_contact' => $billingContact,
                'shipping_contact' => $shippingContact,
                'billing_details' => $billingDetails,
                'shipping_details' => $shippingDetails,
                'order_notes' => $orderNotes,
                'order_items' => $orderItems,
            ];

            $obj = new Liteview;
            $ret = $obj->resubmitOrder($params);

            if ($ret['code'] == '0') // ERROR
            {
            }
            else if ($ret['code'] == '1') // SUCCESS
            {
                $ifsOrderNumber = $ret['details']['ifs_order_number'];
                $modelLiteviewOrder->ifs_order_number = $ifsOrderNumber;
                $modelLiteviewOrder->liteview_order_status = 'Re-Submitted';
                $modelLiteviewOrder->submitted_at = date('Y-m-d H:i:s');
                $modelLiteviewOrder->save();
            }

            $retStr = json_encode($ret);
            echo $retStr;
        }

        Yii::$app->end();
    }

}
