<?php

namespace app\modules\services\commands;

use Yii;
use yii\base\Exception;
use yii\base\ExitException;
use yii\base\InvalidParamException;
use yii\console\Controller;
use yii\base\ErrorException;

use app\modules\services\components\Liteview;
use yii\base\Component;
use yii\log\Logger;

use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

use app\modules\services\models\TradegeckoOrder;

class LiteviewController extends Controller
{
    /**
     * This command clears liteview-related tables in a database
     *
     * @return 0
     */
    public function actionClear()
    {
        Yii::$app->db->createCommand('truncate liteview_order')->query();
        Yii::$app->db->createCommand('truncate liteview_order_billing_contact')->query();
        Yii::$app->db->createCommand('truncate liteview_order_billing_details')->query();
        Yii::$app->db->createCommand('truncate liteview_order_details')->query();
        Yii::$app->db->createCommand('truncate liteview_order_items')->query();
        Yii::$app->db->createCommand('truncate liteview_order_notes')->query();
        Yii::$app->db->createCommand('truncate liteview_order_shipping_contact')->query();
        Yii::$app->db->createCommand('truncate liteview_order_shipping_details')->query();

        Yii::$app->db->createCommand('ALTER SEQUENCE liteview_order_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE liteview_order_billing_contact_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE liteview_order_billing_details_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE liteview_order_details_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE liteview_order_items_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE liteview_order_notes_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE liteview_order_shipping_contact_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE liteview_order_shipping_details_id_seq RESTART WITH 1')->query();

        echo PHP_EOL."CLEARED Liteview-realted tables successfully.". PHP_EOL;
        return 0;
    }

	public function actionIndex()
	{
        $orderDetails = [];
        $orderDetails['order_status'] = 'Active';
        $orderDetails['order_date'] = '2015-12-22';
        $orderDetails['order_number'] = '199901-011';
        $orderDetails['order_source'] = 'Web';
        $orderDetails['order_type'] = 'Regular';
        $orderDetails['catalog_name'] = 'Default';
        $orderDetails['gift_order'] = 'False';
        $orderDetails['po_number'] = 'PO#12131123121-123123211';
        $orderDetails['passthrough_field_01'] = 'passthrough information';
        $orderDetails['passthrough_field_02'] = 'passthrough information';
        $orderDetails['allocate_inventory_now'] = 'TRUE';

        $billingContact = [];
        $billingContact['billto_prefix'] = '';
        $billingContact['billto_first_name'] = 'John';
        $billingContact['billto_last_name'] = 'Doe';
        $billingContact['billto_suffix'] = '';
        $billingContact['billto_company_name'] = 'Test Company';
        $billingContact['billto_address1'] = '14245 Artesia Blvd';
        $billingContact['billto_address2'] = '';
        $billingContact['billto_address3'] = '';
        $billingContact['billto_city'] = 'La Mirada';
        $billingContact['billto_state'] = 'CA';
        $billingContact['billto_postal_code'] = '068590';
        $billingContact['billto_country'] = 'Singapore';
        $billingContact['billto_telephone_no'] = '3102174600';
        $billingContact['billto_email'] = 'ph@test.com';

        $shippingContact = [];
        $shippingContact['shipto_prefix'] = '';
        $shippingContact['shipto_first_name'] = 'Johnny';
        $shippingContact['shipto_last_name'] = 'Appleseed';
        $shippingContact['shipto_suffix'] = '';
        $shippingContact['shipto_company_name'] = 'Apple and CO';
        $shippingContact['shipto_address1'] = '14245 Artesia Blvd';
        $shippingContact['shipto_address2'] = 'Ste 2';
        $shippingContact['shipto_address3'] = '';
        $shippingContact['shipto_city'] = 'La Mirada';
        $shippingContact['shipto_state'] = 'CA';
        $shippingContact['shipto_postal_code'] = '068590';
        $shippingContact['shipto_country'] = 'Singapore';
        $shippingContact['shipto_telephone_no'] = '3102174600';
        $shippingContact['shipto_email'] = 'johnnya@imaginefulfillment.com';

        $billingDetails = [];
        $billingDetails['sub_total'] = '10.99';
        $billingDetails['shipping_handling'] = '5.99';
        $billingDetails['sales_tax_total'] = '0.00';
        $billingDetails['discount_total'] = '0.00';
        $billingDetails['grand_total'] = '16.98';

        $shippingDetails = [];
        $shippingDetails['ship_method'] = 'UPS Ground';
        $shippingDetails['ship_options']['signature_requested'] = 'FALSE';
        $shippingDetails['ship_options']['insurance_requested'] = 'FALSE';
        $shippingDetails['ship_options']['insurance_value'] = '300.00';
        $shippingDetails['ship_options']['saturday_delivery_requested'] = 'TRUE';
        $shippingDetails['ship_options']['third_party_billing_requested'] = 'TRUE';
        $shippingDetails['ship_options']['third_party_billing_account_no'] = 'AE999947';
        $shippingDetails['ship_options']['third_party_billing_zip'] = '068590';
        $shippingDetails['ship_options']['third_party_country'] = 'SG';
        $shippingDetails['ship_options']['general_description'] = 'Test items being shipped for testing';
        $shippingDetails['ship_options']['content_description'] = 'sample items';

        $orderNotes = [];
        $orderNotes['note']['note_type'] = 'shipping';
        $orderNotes['note']['note_description'] = 'Note: You were only charged $55.00. This invoice details the retail value of your order.
                                                    Thank you, 
                                                    Trendy Butler Team';
        $orderNotes['note']['show_on_ps'] = 'TRUE';

        $orderItems = [];
        $orderItems['total_line_items'] = '2';
        $orderItems[0] = [];
        $orderItems[0]['inventory_item'] = 'TEST1001';
        $orderItems[0]['inventory_item_sku'] = 'TEST1001';
        $orderItems[0]['inventory_item_description'] = 'Test Item Description';
        $orderItems[0]['inventory_item_price'] = '5.00';
        $orderItems[0]['inventory_item_qty'] = '1';
        $orderItems[0]['inventory_item_ext_price'] = '5.00';
        $orderItems[0]['inventory_passthrough_01'] = 'non-related lv id';
        $orderItems[0]['inventory_passthrough_02'] = 'non-related lv id';
        $orderItems[1] = [];
        $orderItems[1]['inventory_item'] = 'TEST1002';
        $orderItems[1]['inventory_item_sku'] = 'TEST1002';
        $orderItems[1]['inventory_item_description'] = 'Test Item 2 Description';
        $orderItems[1]['inventory_item_price'] = '6.99';
        $orderItems[1]['inventory_item_qty'] = '1';
        $orderItems[1]['inventory_item_ext_price'] = '6.99';

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

        print_r($ret);
	}

    public function actionSubmitOrder($orderNumber)
    {
        $orderModel = TradegeckoOrder::find()->where(['order_number' => $orderNumber])->one();
        if ($orderModel != null)
        {
            $orderDetails = [];
            $orderDetails['order_status'] = $orderModel->order_status;
            $orderDetails['order_date'] = $orderModel->created_at;
            $orderDetails['order_number'] = $orderModel->order_number;
            $orderDetails['order_source'] = '';
            $orderDetails['order_type'] = 'Regular';
            $orderDetails['catalog_name'] = 'Default';
            $orderDetails['gift_order'] = 'False';
            $orderDetails['po_number'] = 'PO#12131123121-12312321';
            $orderDetails['passthrough_field_01'] = 'passthrough information';
            $orderDetails['passthrough_field_02'] = 'passthrough information';
            $orderDetails['future_date_release'] = '2014-09-10';
            $orderDetails['allocate_inventory_now'] = 'TRUE';
        }
        else
            throw new NotFoundHttpException('Not found matching order.');
    }

    public function actionGetCountries()
    {
        $obj = new Liteview;
        $ret = $obj->getOrderCountries();

        print_r($ret);
    }

    public function actionGetMethods()
    {
        $obj = new Liteview;
        $ret = $obj->getOrderMethods();

        print_r($ret);
    }

    public function actionGetInventories()
    {
        $obj = new Liteview;
        $ret = $obj->getInventoryList();

        print_r($ret);
    }

    public function actionGetOrders()
    {
        $obj = new Liteview;
        $ret = $obj->orderRetunsList('2015-01-22', '2016-12-25');
        print_r($ret);
    }

    public function actionGetOrder($id)
    {
        $obj = new Liteview;
        $ret = $obj->orderStatus($id);
        print_r($ret);   
    }

    public function actionCancelOrder()
    {
        $orderDetails = [];
        $orderDetails['client_order_number'] = 'TES25175002';
        $orderDetails['ifs_order_number'] = 'TES25175004';
        $orderDetails['notes_for_cancellation'] = 'cancel order';

        $orderInfo = [];
        $orderInfo['order_details'] = $orderDetails;

        $cancelOrder = [];
        $cancelOrder['order_info'] = $orderInfo;

        $params = [];
        $params['cancel_order'] = $cancelOrder;

        $obj = new Liteview;
        $ret = $obj->cancelOrder($params);
        print_r($ret);
    }
}

?>
