<?php

namespace app\modules\services\commands;

use Yii;
use yii\base\Exception;
use yii\base\ExitException;
use yii\base\InvalidParamException;
use yii\console\Controller;
use yii\base\ErrorException;

use app\modules\services\components\TradeGecko;
use yii\base\Component;
use yii\log\Logger;

use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

use app\modules\services\models\TradegeckoProduct;
use app\modules\services\models\TradegeckoOrder;
use app\modules\services\models\TradegeckoOrderLineItem;
use app\modules\services\models\TradegeckoInvoice;
use app\modules\services\models\TradegeckoInvoiceLineItem;
use app\modules\services\models\TradegeckoAddress;
use app\modules\services\models\TradegeckoCompany;
use app\modules\services\models\TradegeckoContact;
use app\modules\services\models\TradegeckoCurrency;
use app\modules\services\models\TradegeckoFulfillment;
use app\modules\services\models\TradegeckoFulfillmentLineItem;
use app\modules\services\models\TradegeckoLocation;
use app\modules\services\models\TradegeckoPaymentTerm;
use app\modules\services\models\TradegeckoPriceList;
use app\modules\services\models\TradegeckoTaxComponent;
use app\modules\services\models\TradegeckoTaxType;
use app\modules\services\models\TradegeckoUser;
use app\modules\services\models\TradegeckoVariant;
use app\modules\services\models\TradegeckoVariantLocation;
use app\modules\services\models\TradegeckoVariantStockLevel;
use app\modules\services\models\TradegeckoVariantCommittedStockLevel;
use app\modules\services\models\TradegeckoVariantIncomingStockLevel;
use app\modules\services\models\TradegeckoPayment;
use app\modules\services\models\TradegeckoImage;

use app\models\Products;
use app\models\ProductVariants;

class TradegeckoController extends Controller
{
	public function actionIndex()
	{
        echo "\n\n";
        echo "-------------------List of commands--------------------\n\n";
        echo "1. index\n\tLists all available commands\n";
        echo "2. clear\n\tClears a tradegecko-db\n";
        echo "3. sync-product\n\tPulling product data from Tradegecko\n";
        echo "4. sync-order\n\tPulling order and order line item data from Tradegecko\n";
        echo "5. sync-invoice\n\tPulling invoice and invoice line item data from Tradegecko\n";
        echo "6. sync-address\n\tPulling address data from Tradegecko\n";
        echo "7. sync-company\n\tPulling company data from Tradegecko\n";
        echo "8. sync-contact\n\tPulling contact data from Tradegecko\n";
        echo "9. sync-currency\n\tPulling currency data from Tradegecko\n";
        echo "10. sync-fulfillment\n\tPulling fulfillment and fulfillment line item data from Tradegecko\n";
        echo "11. sync-location\n\tPulling location data from Tradegecko\n";
        echo "12. sync-payment-term\n\tPulling payment term data from Tradegecko\n";
        echo "13. sync-price-list\n\tPulling price list data from Tradegecko\n";
        echo "14. sync-procurement\n\tPulling procurement data from Tradegecko\n";
        echo "15. sync-purchase-order\n\tPulling purchase order data from Tradegecko\n";
        echo "16. sync-purchase-order-line-item\n\tPulling purchase order line item data from Tradegecko\n";
        echo "17. sync-tax-component\n\tPulling tax component data from Tradegecko\n";
        echo "18. sync-tax-type\n\tPulling tax type data from Tradegecko\n";
        echo "19. sync-user\n\tPulling user data from Tradegecko\n";
        echo "20. sync-variant\n\tPulling variant data from Tradegecko\n";
        echo "21. sync-payment\n\tPulling payment data from Tradegecko\n";
        echo "22. sync-inventory\n\tPulling product and variant data from Tradegecko\n";
        echo "23. sync-image\n\tPulling image data from Tradegecko\n";
        echo "24. sync-db\n\tSyncing both inventory\n";
        echo "25. sync\n\tRunning sync commands one by one\n";

        return 0;
	}

    /**
     * This command updates all tradegecko-related data, by calling all sync actions
     *
     * @return 0
     */

    public function actionSync()
    {
        \Yii::$app->runAction('services/tradegecko/sync-product');
        \Yii::$app->runAction('services/tradegecko/sync-order');
        \Yii::$app->runAction('services/tradegecko/sync-invoice');
        \Yii::$app->runAction('services/tradegecko/sync-address');
        \Yii::$app->runAction('services/tradegecko/sync-company');
        \Yii::$app->runAction('services/tradegecko/sync-contact');
        \Yii::$app->runAction('services/tradegecko/sync-currency');
        \Yii::$app->runAction('services/tradegecko/sync-fulfillment');
        \Yii::$app->runAction('services/tradegecko/sync-location');
        \Yii::$app->runAction('services/tradegecko/sync-payment-term');
        \Yii::$app->runAction('services/tradegecko/sync-price-list');
        \Yii::$app->runAction('services/tradegecko/sync-procurement');
        \Yii::$app->runAction('services/tradegecko/sync-purchase-order');
        \Yii::$app->runAction('services/tradegecko/sync-purchase-order-line-item');
        \Yii::$app->runAction('services/tradegecko/sync-tax-component');
        \Yii::$app->runAction('services/tradegecko/sync-tax-type');
        \Yii::$app->runAction('services/tradegecko/sync-user');
        \Yii::$app->runAction('services/tradegecko/sync-variant');
        \Yii::$app->runAction('services/tradegecko/sync-payment');
        \Yii::$app->runAction('services/tradegecko/sync-image');
        \Yii::$app->runAction('services/tradegecko/sync-db');
    }

    /**
     * This command updates all tradegecko-related inventory data including products and variants.
     *
     * @return 0
     */

    public function actionSyncInventory()
    {
        \Yii::$app->runAction('services/tradegecko/sync-product');
        \Yii::$app->runAction('services/tradegecko/sync-variant');
        \Yii::$app->runAction('services/tradegecko/sync-image');
        \Yii::$app->runAction('services/tradegecko/sync-db');
    }

    /**
     * This command clears tradegecko-related tables in a database
     *
     * @return 0
     */
    public function actionClear()
    {
        Yii::$app->db->createCommand('truncate tradegecko_product')->query();
        Yii::$app->db->createCommand('truncate tradegecko_order')->query();
        Yii::$app->db->createCommand('truncate tradegecko_invoice')->query();
        Yii::$app->db->createCommand('truncate tradegecko_company')->query();
        Yii::$app->db->createCommand('truncate tradegecko_address')->query();
        Yii::$app->db->createCommand('truncate tradegecko_contact')->query();
        Yii::$app->db->createCommand('truncate tradegecko_currency')->query();
        Yii::$app->db->createCommand('truncate tradegecko_fulfillment')->query();
        Yii::$app->db->createCommand('truncate tradegecko_fulfillment_line_item')->query();
        Yii::$app->db->createCommand('truncate tradegecko_invoice_line_item')->query();
        Yii::$app->db->createCommand('truncate tradegecko_order_line_item')->query();
        Yii::$app->db->createCommand('truncate tradegecko_location')->query();
        Yii::$app->db->createCommand('truncate tradegecko_payment_term')->query();
        Yii::$app->db->createCommand('truncate tradegecko_price_list')->query();
        Yii::$app->db->createCommand('truncate tradegecko_procurement')->query();
        Yii::$app->db->createCommand('truncate tradegecko_purchase_order')->query();
        Yii::$app->db->createCommand('truncate tradegecko_purchase_order_line_item')->query();
        Yii::$app->db->createCommand('truncate tradegecko_tax_component')->query();
        Yii::$app->db->createCommand('truncate tradegecko_tax_type')->query();
        Yii::$app->db->createCommand('truncate tradegecko_user')->query();
        Yii::$app->db->createCommand('truncate tradegecko_variant')->query();
        Yii::$app->db->createCommand('truncate tradegecko_variant_location')->query();
        Yii::$app->db->createCommand('truncate tradegecko_variant_stock_level')->query();
        Yii::$app->db->createCommand('truncate tradegecko_variant_committed_stock_level')->query();
        Yii::$app->db->createCommand('truncate tradegecko_variant_incoming_stock_level')->query();
        Yii::$app->db->createCommand('truncate tradegecko_payment')->query();
        Yii::$app->db->createCommand('truncate tradegecko_image')->query();

        Yii::$app->db->createCommand('ALTER SEQUENCE tradegecko_product_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE tradegecko_order_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE tradegecko_invoice_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE tradegecko_company_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE tradegecko_address_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE tradegecko_contact_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE tradegecko_currency_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE tradegecko_fulfillment_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE tradegecko_fulfillment_line_item_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE tradegecko_invoice_line_item_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE tradegecko_order_line_item_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE tradegecko_location_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE tradegecko_payment_term_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE tradegecko_procurement_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE tradegecko_purchase_order_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE tradegecko_purchase_order_line_item_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE tradegecko_tax_component_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE tradegecko_tax_type_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE tradegecko_user_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE tradegecko_variant_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE tradegecko_variant_location_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE tradegecko_variant_stock_level_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE tradegecko_variant_committed_stock_level_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE tradegecko_variant_incoming_stock_level_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE tradegecko_payment_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE tradegecko_image_id_seq RESTART WITH 1')->query();

        echo PHP_EOL."CLEARED Tradegecko-realted tables successfully.". PHP_EOL;
        self::logMessage('CLEARED Tradegecko-realted tables successfully.');

        return 0;
    }

    /**
     * This command updates all product data from tradegecko
     *
     * @return 0
     */
	public function actionSyncProduct()
	{
		self::logMessage('Syncing Products from Tradegecko...');

        $realArr = [];
        $realArr['product'] = 0;
        $realArr['deleted'] = 0;

        $storedArr = [];
        $storedArr['product'] = 0;
        $storedArr['deleted'] = 0;

        $startTime = microtime(true);

        try{
            $obj = self::getTradegeckoObject();
            if ($obj == null)
                return -1;
            
            $products = $obj->getProducts();
            self::checkData($products, "Products");
        }
        catch(\Exception $ex)
        {
            echo 'Syncing Products Failed. API Request Error occured'.PHP_EOL;
            echo $ex->getMessage();
            self::logMessage('Syncing Products Failed. API Request Error occured');
            return;
        }

        if ($products)
        {
            $tradeGeckoProductIds = array_map(function($param){ return $param['id'];}, $products);

            $results = TradegeckoProduct::find()->all();
            $localProducts = [];
            foreach($results as $res)
                $localProducts[$res->id] = $res;
            $localProductIds = array_map(function($param){ return $param->id;}, $localProducts);

            foreach($products as $product)
            {
                if ($product['id'] != null)
                {
                    $itemProduct = [];
                    $itemProduct['id'] = $product['id'];
                    $itemProduct['product_name'] = $product['name'];
                    $itemProduct['created_at'] = $product['created_at'];
                    $itemProduct['image_url'] = $product['image_url'];
                    $itemProduct['opt1'] = $product['opt1'];
                    $itemProduct['opt2'] = $product['opt2'];
                    $itemProduct['opt3'] = $product['opt3'];
                    $itemProduct['product_type'] = $product['product_type'];
                    $itemProduct['search_cache'] = $product['search_cache'];
                    $itemProduct['product_status'] = $product['status'];
                    $itemProduct['tags'] = $product['tags'];
                    $itemProduct['variant_ids'] = serialize($product['variant_ids']);
                    $itemProduct['description'] = $product['description'];
                    $itemProduct['supplier'] = $product['supplier'];
                    $itemProduct['brand'] = $product['brand'];
                    $itemProduct['updated_at'] = $product['updated_at'];
                    $itemProduct['quantity'] = $product['quantity'];
                    $itemProduct['vendor'] = $product['vendor'];
                    $itemProduct['supplier_ids'] = serialize($product['supplier_ids']);
                    $itemProduct['variant_num'] = count($product['variant_ids']);
                    $itemProduct['shop'] = '0';

                    $modelProduct = TradegeckoProduct::find()->where(['id' => $product['id']])->one();
                    if ($modelProduct == null)
                        $modelProduct = new TradegeckoProduct();

                    $modelProduct->attributes = $itemProduct;
                    if ($modelProduct->save()) {
                        $storedArr['product'] ++;
                    }
                    $realArr['product'] ++;
                }
            }

            // check if a product was deleted from TradeGecko
            $deletableProductIds = array_diff($localProductIds, $tradeGeckoProductIds);

            foreach($deletableProductIds as $productId) {
                $model = TradegeckoProduct::find()->where(['id' => $productId])->one();
                if($model) {
                    $model->attributes = [
                        'product_status' => 'deleted'
                    ];
                    if ($model->save()) {
                        $storedArr['deleted'] ++;
                    }
                    $realArr['deleted'] ++;
                }
            }
        }

        self::logMessage($realArr['product'].' Updates Found');
        self::logMessage($storedArr['product'].' Updates were Applied to DB correctly');
        self::logMessage($realArr['deleted'].' Products were deleted');
        self::logMessage($storedArr['product'].' Products were deleted on local DB correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Product\t\t".$realArr['product']."\t".$storedArr['product'].PHP_EOL;
        echo "Deleted\t\t".$realArr['deleted']."\t".$storedArr['deleted'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Products from Tradegecko Done');

        return 0;
	}

    /**
     * This command updates all order and order line item data from tradegecko
     *
     * @return 0
     */
    public function actionSyncOrder()
    {
        self::logMessage('Syncing Orders from Tradegecko...');

        $realArr = [];
        $realArr['order'] = 0;
        $realArr['orderLineItem'] = 0;

        $storedArr = [];
        $storedArr['order'] = 0;
        $storedArr['orderLineItem'] = 0;

        $startTime = microtime(true);

        try{
            $obj = self::getTradegeckoObject();
            if ($obj == null)
                return -1;
            
            $orders = $obj->getOrders();
            self::checkData($orders, "Orders");
        }
        catch(\Exception $ex)
        {
            echo 'Syncing Orders Failed. API Request Error occured';
            self::logMessage('Syncing Orders Failed. API Request Error occured');
            return;
        }

        if($orders){
            foreach($orders as $order)
            {
                if ($order['id'] != null)
                {
                    $itemOrder = [];
                    $itemOrder['id'] = $order['id'];
                    $itemOrder['created_at'] = $order['created_at'];
                    $itemOrder['updated_at'] = $order['updated_at'];
                    $itemOrder['document_url'] = $order['document_url'];
                    $itemOrder['assignee_id'] = $order['assignee_id'];
                    $itemOrder['billing_address_id'] = $order['billing_address_id'];
                    $itemOrder['company_id'] = $order['company_id'];
                    $itemOrder['contact_id'] = $order['contact_id'];
                    $itemOrder['currency_id'] = $order['currency_id'];
                    $itemOrder['shipping_address_id'] = $order['shipping_address_id'];
                    $itemOrder['stock_location_id'] = $order['stock_location_id'];
                    $itemOrder['user_id'] = $order['user_id'];
                    $itemOrder['default_price_list_id'] = $order['default_price_list_id'];
                    $itemOrder['email'] = $order['email'];
                    $itemOrder['fulfillment_status'] = $order['fulfillment_status'];
                    $itemOrder['invoice_status'] = $order['invoice_status'];
                    $itemOrder['issued_at'] = $order['issued_at'];
                    $itemOrder['notes'] = $order['notes'];
                    $itemOrder['order_number'] = $order['order_number'];
                    $itemOrder['packed_status'] = $order['packed_status'];
                    $itemOrder['payment_status'] = $order['payment_status'];
                    $itemOrder['phone_number'] = $order['phone_number'];
                    $itemOrder['reference_number'] = $order['reference_number'];
                    $itemOrder['return_status'] = $order['return_status'];
                    $itemOrder['returning_status'] = $order['returning_status'];
                    $itemOrder['ship_at'] = $order['ship_at'];
                    $itemOrder['source_url'] = $order['source_url'];
                    $itemOrder['order_status'] = $order['status'];
                    $itemOrder['tax_label'] = $order['tax_label'];
                    $itemOrder['tax_override'] = $order['tax_override'];
                    $itemOrder['tax_treatment'] = $order['tax_treatment'];
                    $itemOrder['total'] = $order['total'];
                    if (is_array($order['tags']))
                        $itemOrder['tags'] = serialize($order['tags']);
                    else
                        $itemOrder['tags'] = $order['tags'];
                    $itemOrder['fulfillment_ids'] = serialize($order['fulfillment_ids']);
                    $itemOrder['fulfillment_return_ids'] = serialize($order['fulfillment_return_ids']);
                    $itemOrder['invoice_ids'] = serialize($order['invoice_ids']);
                    $itemOrder['payment_ids'] = serialize($order['payment_ids']);
                    $itemOrder['refund_ids'] = serialize($order['refund_ids']);
                    $itemOrder['cached_total'] = $order['cached_total'];
                    $itemOrder['default_price_type_id'] = $order['default_price_type_id'];
                    $itemOrder['source'] = $order['source'];
                    $itemOrder['tax_type'] = $order['tax_type'];
                    $itemOrder['tracking_number'] = $order['tracking_number'];
                    $itemOrder['url'] = $order['url'];
                    $itemOrder['source_id'] = $order['source_id'].'';
                    $itemOrder['invoice_numbers'] = serialize($order['invoice_numbers']);
                    $itemOrder['order_line_item_ids'] = serialize($order['order_line_item_ids']);

                    foreach($order['order_line_item_ids'] as $orderLineItemId)
                    {
                        $orderLineItem = $obj->getOrderLineItem($orderLineItemId);
                        if ($orderLineItem && $orderLineItem['id'] != null)
                        {
                            $itemOrderLineItem = [];
                            $itemOrderLineItem['id'] = $orderLineItem['id'];
                            $itemOrderLineItem['created_at'] = $orderLineItem['created_at'];
                            $itemOrderLineItem['updated_at'] = $orderLineItem['updated_at'];
                            $itemOrderLineItem['order_id'] = $orderLineItem['order_id'];
                            $itemOrderLineItem['variant_id'] = $orderLineItem['variant_id'];
                            $itemOrderLineItem['tax_type_id'] = $orderLineItem['tax_type_id'];
                            $itemOrderLineItem['discount'] = $orderLineItem['discount'];
                            $itemOrderLineItem['quantity'] = $orderLineItem['quantity'];
                            $itemOrderLineItem['freeform'] = '';//$orderLineItem['freeform'];
                            $itemOrderLineItem['image_url'] = $orderLineItem['image_url'];
                            $itemOrderLineItem['label'] = $orderLineItem['label'];
                            $itemOrderLineItem['line_type'] = $orderLineItem['line_type'];
                            $itemOrderLineItem['position'] = $orderLineItem['position'];
                            $itemOrderLineItem['price'] = $orderLineItem['price'];
                            $itemOrderLineItem['tax_rate_override'] = $orderLineItem['tax_rate_override'];
                            $itemOrderLineItem['tax_rate'] = $orderLineItem['tax_rate'];
                            $itemOrderLineItem['fulfillment_line_item_ids'] = serialize($orderLineItem['fulfillment_line_item_ids']);
                            $itemOrderLineItem['fulfillment_return_line_item_ids'] = serialize($orderLineItem['fulfillment_return_line_item_ids']);
                            $itemOrderLineItem['invoice_line_item_ids'] = serialize($orderLineItem['invoice_line_item_ids']);

                            $modelOrderLineItem = TradegeckoOrderLineItem::find()->where(['id' => $orderLineItem['id']])->one();
                            if ($modelOrderLineItem == null)
                                $modelOrderLineItem = new TradegeckoOrderLineItem();

                            $modelOrderLineItem->attributes = $itemOrderLineItem;
                            if ($modelOrderLineItem->save()) {
                                $storedArr['orderLineItem'] ++;
                            }
                            else
                            {
                                print_r($orderLineItem);
                                print_r($modelOrderLineItem->getErrors()); exit;
                            }
                            $realArr['orderLineItem'] ++;
                        }
                    }

                    $modelOrder = TradegeckoOrder::find()->where(['id' => $order['id']])->one();
                    if ($modelOrder == null)
                        $modelOrder = new TradegeckoOrder();

                    $modelOrder->attributes = $itemOrder;
                    if ($modelOrder->save()) {
                        $storedArr['order'] ++;
                    }
                    $realArr['order'] ++;
                }
            }
        }

        self::logMessage($realArr['order'].' Updates Found');
        self::logMessage($storedArr['order'].' Updates were Applied to DB correctly');
        self::logMessage($realArr['orderLineItem'].' Updates (Line Item) Found');
        self::logMessage($storedArr['orderLineItem'].' Updates (Line Item) were Applied to DB correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Order\t\t".$realArr['order']."\t".$storedArr['order'].PHP_EOL;
        echo "Line Item\t".$realArr['orderLineItem']."\t".$storedArr['orderLineItem'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Orders from Tradegecko Done');

        return 0;
    }

    /**
     * This command updates all invoice and invoice line item data from tradegecko
     *
     * @return 0
     */
    public function actionSyncInvoice()
    {
        self::logMessage('Syncing Invoices from Tradegecko...');

        $realArr = [];
        $realArr['invoice'] = 0;
        $realArr['invoiceLineItem'] = 0;

        $storedArr = [];
        $storedArr['invoice'] = 0;
        $storedArr['invoiceLineItem'] = 0;

        $startTime = microtime(true);

        try{
            $obj = self::getTradegeckoObject();
            if ($obj == null)
                return -1;
            
            $invoices = $obj->getInvoices();
            self::checkData($invoices, "Invoices");
        }
        catch(\Exception $ex)
        {
            echo 'Syncing Invoices Failed. API Request Error occured';
            self::logMessage('Syncing Invoices Failed. API Request Error occured');
            return;
        }

        if($invoices){
            foreach($invoices as $invoice)
            {
                if ($invoice['id'] != null)
                {
                    $itemInvoice = [];
                    $itemInvoice['id'] = $invoice['id'];
                    $itemInvoice['created_at'] = $invoice['created_at'];
                    $itemInvoice['updated_at'] = $invoice['updated_at'];
                    $itemInvoice['document_url'] = $invoice['document_url'];
                    $itemInvoice['order_id'] = $invoice['order_id'];
                    $itemInvoice['shipping_address_id'] = $invoice['shipping_address_id'];
                    $itemInvoice['billing_address_id'] = $invoice['billing_address_id'];
                    $itemInvoice['payment_term_id'] = $invoice['payment_term_id'];
                    $itemInvoice['due_at'] = $invoice['due_at'];
                    $itemInvoice['exchange_rate'] = $invoice['exchange_rate'];
                    $itemInvoice['invoice_number'] = $invoice['invoice_number'];
                    $itemInvoice['invoiced_at'] = $invoice['invoiced_at'];
                    $itemInvoice['notes'] = $invoice['notes'];
                    $itemInvoice['cached_total'] = $invoice['cached_total'];
                    $itemInvoice['payment_status'] = $invoice['payment_status'];
                    $itemInvoice['order_number'] = $invoice['order_number'];
                    $itemInvoice['company_id'] = $invoice['company_id'];
                    $itemInvoice['currency_id'] = $invoice['currency_id'];
                    $itemInvoice['invoice_line_item_ids'] = serialize($invoice['invoice_line_item_ids']);
                    $itemInvoice['payment_ids'] = serialize($invoice['payment_ids']);
                    $itemInvoice['refund_ids'] = serialize($invoice['refund_ids']);

                    foreach($invoice['invoice_line_item_ids'] as $invoiceLineItemId)
                    {
                        $invoiceLineItem = $obj->getInvoiceLineItem($invoiceLineItemId);
                        if ($invoiceLineItem && $invoiceLineItem['id'] != null)
                        {
                            $itemInvoiceLineItem = [];
                            $itemInvoiceLineItem['id'] = $invoiceLineItem['id'];
                            $itemInvoiceLineItem['created_at'] = $invoiceLineItem['created_at'];
                            $itemInvoiceLineItem['updated_at'] = $invoiceLineItem['updated_at'];
                            $itemInvoiceLineItem['invoice_id'] = $invoiceLineItem['invoice_id'];
                            $itemInvoiceLineItem['order_line_item_id'] = $invoiceLineItem['order_line_item_id'];
                            $itemInvoiceLineItem['ledger_account_id'] = $invoiceLineItem['ledger_account_id'];
                            $itemInvoiceLineItem['position'] = $invoiceLineItem['position'];
                            $itemInvoiceLineItem['quantity'] = $invoiceLineItem['quantity'];

                            $modelInvoiceLineItem = TradegeckoInvoiceLineItem::find()->where(['id' => $invoiceLineItem['id']])->one();
                            if ($modelInvoiceLineItem == null)
                                $modelInvoiceLineItem = new TradegeckoInvoiceLineItem();

                            $modelInvoiceLineItem->attributes = $itemInvoiceLineItem;
                            if ($modelInvoiceLineItem->save()) {
                                $storedArr['invoiceLineItem'] ++;
                            }
                            $realArr['invoiceLineItem'] ++;
                        }
                    }

                    $modelInvoice = TradegeckoInvoice::find()->where(['id' => $invoice['id']])->one();
                    if ($modelInvoice == null)
                        $modelInvoice = new TradegeckoInvoice();

                    $modelInvoice->attributes = $itemInvoice;
                    if ($modelInvoice->save()) {
                        $storedArr['invoice'] ++;
                    }
                    $realArr['invoice'] ++;
                }
            }
        }

        self::logMessage($realArr['invoice'].' Updates Found');
        self::logMessage($storedArr['invoice'].' Updates were Applied to DB correctly');
        self::logMessage($realArr['invoiceLineItem'].' Updates (Line Item) Found');
        self::logMessage($storedArr['invoiceLineItem'].' Updates (Line Item) were Applied to DB correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Invoice\t\t".$realArr['invoice']."\t".$storedArr['invoice'].PHP_EOL;
        echo "Line Item\t".$realArr['invoiceLineItem']."\t".$storedArr['invoiceLineItem'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Invoices from Tradegecko Done');

        return 0;
    }

    /**
     * This command updates all address data from tradegecko
     *
     * @return 0
     */
    public function actionSyncAddress()
    {
        self::logMessage('Syncing Addresses from Tradegecko...');

        $realArr = [];
        $realArr['address'] = 0;

        $storedArr = [];
        $storedArr['address'] = 0;

        $startTime = microtime(true);

        try{
            $obj = self::getTradegeckoObject();
            if ($obj == null)
                return -1;
            
            $addresses = $obj->getAddresses();
            self::checkData($addresses, "Addresses");
        }
        catch(\Exception $ex)
        {
            echo 'Syncing Addresses Failed. API Request Error occured';
            self::logMessage('Syncing Addresses Failed. API Request Error occured');
            return;
        }

        if($addresses){
            foreach($addresses as $address)
            {
                if ($address['id'] != null)
                {
                    $itemAddress = [];
                    $itemAddress['id'] = $address['id'];
                    $itemAddress['created_at'] = $address['created_at'];
                    $itemAddress['updated_at'] = $address['updated_at'];
                    $itemAddress['company_id'] = $address['company_id'];
                    $itemAddress['address1'] = $address['address1'];
                    $itemAddress['address2'] = $address['address2'];
                    $itemAddress['city'] = $address['city'];
                    $itemAddress['company_name'] = $address['company_name'];
                    $itemAddress['country'] = $address['country'];
                    $itemAddress['email'] = $address['email'];
                    $itemAddress['first_name'] = $address['first_name'];
                    $itemAddress['last_name'] = $address['last_name'];
                    $itemAddress['label'] = $address['label'];
                    $itemAddress['phone_number'] = $address['phone_number'];
                    $itemAddress['state'] = $address['state'];
                    $itemAddress['address_status'] = $address['status'];
                    $itemAddress['suburb'] = $address['suburb'];
                    $itemAddress['zip_code'] = $address['zip_code'];

                    $modelAddress = TradegeckoAddress::find()->where(['id' => $address['id']])->one();
                    if ($modelAddress == null)
                        $modelAddress = new TradegeckoAddress();

                    $modelAddress->attributes = $itemAddress;
                    if ($modelAddress->save()) {
                        $storedArr['address'] ++;
                    }
                    $realArr['address'] ++;
                }
            }
        }

        self::logMessage($realArr['address'].' Updates Found');
        self::logMessage($storedArr['address'].' Updates were Applied to DB correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Address\t\t".$realArr['address']."\t".$storedArr['address'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Addresses from Tradegecko Done');

        return 0;
    }

    /**
     * This command updates all company data from tradegecko
     *
     * @return 0
     */
    public function actionSyncCompany()
    {
        self::logMessage('Syncing Companies from Tradegecko...');

        $realArr = [];
        $realArr['company'] = 0;

        $storedArr = [];
        $storedArr['company'] = 0;

        $startTime = microtime(true);

        try{
            $obj = self::getTradegeckoObject();
            if ($obj == null)
                return -1;
            
            $companies = $obj->getCompanies();
            self::checkData($companies, "Companies");
        }
        catch(\Exception $ex)
        {
            echo 'Syncing Companies Failed. API Request Error occured';
            self::logMessage('Syncing Companies Failed. API Request Error occured');
            return;
        }

        if($companies){
            foreach($companies as $company)
            {
                if ($company['id'] != null)
                {
                    $itemCompany = [];
                    $itemCompany['id'] = $company['id'];
                    $itemCompany['created_at'] = $company['created_at'];
                    $itemCompany['updated_at'] = $company['updated_at'];
                    $itemCompany['assignee_id'] = $company['assignee_id'];
                    $itemCompany['default_ledger_account_id'] = $company['default_ledger_account_id'];
                    $itemCompany['default_payment_term_id'] = $company['default_payment_term_id'];
                    $itemCompany['default_stock_location_id'] = $company['default_stock_location_id'];
                    $itemCompany['default_tax_type_id'] = $company['default_tax_type_id'];
                    $itemCompany['company_code'] = $company['company_code'];
                    $itemCompany['company_type'] = $company['company_type'];
                    $itemCompany['default_discount_rate'] = $company['default_discount_rate'];
                    $itemCompany['default_price_list_id'] = $company['default_price_list_id'];
                    $itemCompany['default_tax_rate'] = $company['default_tax_rate'];
                    $itemCompany['default_document_theme_id'] = $company['default_document_theme_id'];
                    $itemCompany['description'] = $company['description'];
                    $itemCompany['email'] = $company['email'];
                    $itemCompany['fax'] = $company['fax'];
                    $itemCompany['company_name'] = $company['name'];
                    $itemCompany['phone_number'] = $company['phone_number'];
                    $itemCompany['company_status'] = $company['status'];
                    $itemCompany['tax_number'] = $company['tax_number'];
                    $itemCompany['website'] = $company['website'];
                    $itemCompany['address_ids'] = serialize($company['address_ids']);
                    $itemCompany['contact_ids'] = serialize($company['contact_ids']);
                    $itemCompany['note_ids'] = serialize($company['note_ids']);
                    $itemCompany['default_price_type_id'] = $company['default_price_type_id'];

                    $modelCompany = TradegeckoCompany::find()->where(['id' => $company['id']])->one();
                    if ($modelCompany == null)
                        $modelCompany = new TradegeckoCompany();

                    $modelCompany->attributes = $itemCompany;
                    if ($modelCompany->save()) {
                        $storedArr['company'] ++;
                    }
                    $realArr['company'] ++;
                }
            }
        }

        self::logMessage($realArr['company'].' Updates Found');
        self::logMessage($storedArr['company'].' Updates were Applied to DB correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Company\t\t".$realArr['company']."\t".$storedArr['company'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Companies from Tradegecko Done');

        return 0;
    }

    /**
     * This command updates all contact data from tradegecko
     *
     * @return 0
     */
    public function actionSyncContact()
    {
        self::logMessage('Syncing Contacts from Tradegecko...');

        $realArr = [];
        $realArr['contact'] = 0;

        $storedArr = [];
        $storedArr['contact'] = 0;

        $startTime = microtime(true);

        try{
            $obj = self::getTradegeckoObject();
            if ($obj == null)
                return -1;
            
            $contacts = $obj->getContacts();
            self::checkData($contacts, "Contacts");
        }
        catch(\Exception $ex)
        {
            echo 'Syncing Contacts Failed. API Request Error occured';
            self::logMessage('Syncing Contacts Failed. API Request Error occured');
            return;
        }

        if($contacts){
            foreach($contacts as $contact)
            {
                if ($contact['id'] != null)
                {
                    $itemContact = [];
                    $itemContact['id'] = $contact['id'];
                    $itemContact['created_at'] = $contact['created_at'];
                    $itemContact['updated_at'] = $contact['updated_at'];
                    $itemContact['company_id'] = $contact['company_id'];
                    $itemContact['email'] = $contact['email'];
                    $itemContact['fax'] = $contact['fax'];
                    $itemContact['first_name'] = $contact['first_name'];
                    $itemContact['last_name'] = $contact['last_name'];
                    $itemContact['location'] = $contact['location'];
                    $itemContact['mobile'] = $contact['mobile'];
                    $itemContact['notes'] = $contact['notes'];
                    $itemContact['phone_number'] = $contact['phone_number'];
                    $itemContact['position'] = $contact['position'];
                    $itemContact['status'] = $contact['status'];
                    $itemContact['iguana_admin'] = $contact['iguana_admin'];
                    $itemContact['online_ordering'] = $contact['online_ordering'];
                    $itemContact['invitation_accepted_at'] = $contact['invitation_accepted_at'];
                    $itemContact['phone'] = $contact['phone'];

                    $modelContact = TradegeckoContact::find()->where(['id' => $contact['id']])->one();
                    if ($modelContact == null)
                        $modelContact = new TradegeckoContact();

                    $modelContact->attributes = $itemContact;
                    if ($modelContact->save()) {
                        $storedArr['contact'] ++;
                    }
                    $realArr['contact'] ++;
                }
            }
        }

        self::logMessage($realArr['contact'].' Updates Found');
        self::logMessage($storedArr['contact'].' Updates were Applied to DB correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Contact\t\t".$realArr['contact']."\t".$storedArr['contact'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Contacts from Tradegecko Done');

        return 0;
    }

    /**
     * This command updates all currency data from tradegecko
     *
     * @return 0
     */
    public function actionSyncCurrency()
    {
        self::logMessage('Syncing Currencies from Tradegecko...');

        $realArr = [];
        $realArr['currency'] = 0;

        $storedArr = [];
        $storedArr['currency'] = 0;

        $startTime = microtime(true);

        try{
            $obj = self::getTradegeckoObject();
            if ($obj == null)
                return -1;
            
            $currencies = $obj->getCurrencies();
            self::checkData($currencies, "Currencies");
        }
        catch(\Exception $ex)
        {
            echo 'Syncing Currencies Failed. API Request Error occured';
            self::logMessage('Syncing Currencies Failed. API Request Error occured');
            return;
        }

        if($currencies){
            foreach($currencies as $currency)
            {
                if ($currency['id'] != null)
                {
                    $itemCurrency = [];
                    $itemCurrency['id'] = $currency['id'];
                    $itemCurrency['created_at'] = $currency['created_at'];
                    $itemCurrency['updated_at'] = $currency['updated_at'];
                    $itemCurrency['delimiter'] = $currency['delimiter'];
                    $itemCurrency['format'] = $currency['format'];
                    $itemCurrency['iso'] = $currency['iso'];
                    $itemCurrency['currency_name'] = $currency['name'];
                    $itemCurrency['currency_precision'] = $currency['precision'];
                    $itemCurrency['rate'] = $currency['rate'];
                    $itemCurrency['currency_separator'] = $currency['separator'];
                    $itemCurrency['symbol'] = $currency['symbol'];
                    $itemCurrency['currency_status'] = $currency['status'];
                    $itemCurrency['subunit'] = $currency['subunit'];

                    $modelCurrency = TradegeckoCurrency::find()->where(['id' => $currency['id']])->one();
                    if ($modelCurrency == null)
                        $modelCurrency = new TradegeckoCurrency();

                    $modelCurrency->attributes = $itemCurrency;
                    if ($modelCurrency->save()) {
                        $storedArr['currency'] ++;
                    }
                    $realArr['currency'] ++;
                }
            }
        }

        self::logMessage($realArr['currency'].' Updates Found');
        self::logMessage($storedArr['currency'].' Updates were Applied to DB correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Currency\t".$realArr['currency']."\t".$storedArr['currency'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Currencies from Tradegecko Done');

        return 0;
    }

    /**
     * This command updates all fulfillment and fulfillment line item data from tradegecko
     *
     * @return 0
     */
    public function actionSyncFulfillment()
    {
        self::logMessage('Syncing Fulfillments from Tradegecko...');

        $realArr = [];
        $realArr['fulfillment'] = 0;
        $realArr['fulfillmentLineItem'] = 0;

        $storedArr = [];
        $storedArr['fulfillment'] = 0;
        $storedArr['fulfillmentLineItem'] = 0;

        $startTime = microtime(true);

        try{
            $obj = self::getTradegeckoObject();
            if ($obj == null)
                return -1;
            
            $fulfillments = $obj->getFulfillments();
            self::checkData($fulfillments, "Fulfillments");
        }
        catch(\Exception $ex)
        {
            echo 'Syncing Fulfillments Failed. API Request Error occured';
            self::logMessage('Syncing Fulfillments Failed. API Request Error occured');
            return;
        }

        if($fulfillments){
            foreach($fulfillments as $fulfillment)
            {
                if ($fulfillment['id'] != null)
                {
                    $itemFulfillment = [];
                    $itemFulfillment['id'] = $fulfillment['id'];
                    $itemFulfillment['created_at'] = $fulfillment['created_at'];
                    $itemFulfillment['updated_at'] = $fulfillment['updated_at'];
                    $itemFulfillment['order_id'] = $fulfillment['order_id'];
                    $itemFulfillment['shipping_address_id'] = $fulfillment['shipping_address_id'];
                    $itemFulfillment['billing_address_id'] = $fulfillment['billing_address_id'];
                    $itemFulfillment['stock_location_id'] = $fulfillment['stock_location_id'];
                    $itemFulfillment['delivery_type'] = $fulfillment['delivery_type'];
                    $itemFulfillment['exchange_rate'] = $fulfillment['exchange_rate'];
                    $itemFulfillment['notes'] = $fulfillment['notes'];
                    $itemFulfillment['packed_at'] = $fulfillment['packed_at'];
                    $itemFulfillment['received_at'] = $fulfillment['received_at'];
                    $itemFulfillment['service'] = $fulfillment['service'];
                    $itemFulfillment['shipped_at'] = $fulfillment['shipped_at'];
                    $itemFulfillment['fulfillment_status'] = $fulfillment['status'];
                    $itemFulfillment['tracking_company'] = $fulfillment['tracking_company'];
                    $itemFulfillment['tracking_number'] = $fulfillment['tracking_number'];
                    $itemFulfillment['tracking_url'] = $fulfillment['tracking_url'];
                    $itemFulfillment['order_number'] = $fulfillment['order_number'];
                    $itemFulfillment['company_id'] = $fulfillment['company_id'];
                    $itemFulfillment['fulfillment_line_item_ids'] = serialize($fulfillment['fulfillment_line_item_ids']);

                    foreach($fulfillment['fulfillment_line_item_ids'] as $fulfillmentLineItemId)
                    {
                        $fulfillmentLineItem = $obj->getFulfillmentLineItem($fulfillmentLineItemId);
                        if ($fulfillmentLineItem && $fulfillmentLineItem['id'] != null)
                        {
                            $itemFulfillmentLineItem = [];
                            $itemFulfillmentLineItem['id'] = $fulfillmentLineItem['id'];
                            $itemFulfillmentLineItem['created_at'] = $fulfillmentLineItem['created_at'];
                            $itemFulfillmentLineItem['updated_at'] = $fulfillmentLineItem['updated_at'];
                            $itemFulfillmentLineItem['fulfillment_id'] = $fulfillmentLineItem['fulfillment_id'];
                            $itemFulfillmentLineItem['order_line_item_id'] = $fulfillmentLineItem['order_line_item_id'];
                            $itemFulfillmentLineItem['base_price'] = $fulfillmentLineItem['base_price'];
                            $itemFulfillmentLineItem['position'] = $fulfillmentLineItem['position'];
                            $itemFulfillmentLineItem['quantity'] = $fulfillmentLineItem['quantity'];

                            $modelFulfillmentLineItem = TradegeckoFulfillmentLineItem::find()->where(['id' => $fulfillmentLineItem['id']])->one();
                            if ($modelFulfillmentLineItem == null)
                                $modelFulfillmentLineItem = new TradegeckoFulfillmentLineItem();

                            $modelFulfillmentLineItem->attributes = $itemFulfillmentLineItem;
                            if ($modelFulfillmentLineItem->save()) {
                                $storedArr['fulfillmentLineItem'] ++;
                            }
                            $realArr['fulfillmentLineItem'] ++;
                        }
                    }

                    $modelFulfillment = TradegeckoFulfillment::find()->where(['id' => $fulfillment['id']])->one();
                    if ($modelFulfillment == null)
                        $modelFulfillment = new TradegeckoFulfillment();

                    $modelFulfillment->attributes = $itemFulfillment;
                    if ($modelFulfillment->save()) {
                        $storedArr['fulfillment'] ++;
                    }
                    $realArr['fulfillment'] ++;
                }
            }
        }

        self::logMessage($realArr['fulfillment'].' Updates Found');
        self::logMessage($storedArr['fulfillment'].' Updates were Applied to DB correctly');
        self::logMessage($realArr['fulfillmentLineItem'].' Updates (Line Item) Found');
        self::logMessage($storedArr['fulfillmentLineItem'].' Updates (Line Item) were Applied to DB correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Fulfillment\t".$realArr['fulfillment']."\t".$storedArr['fulfillment'].PHP_EOL;
        echo "Line Item\t".$realArr['fulfillmentLineItem']."\t".$storedArr['fulfillmentLineItem'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Fulfillments from Tradegecko Done');

        return 0;
    }

    /**
     * This command updates all location data from tradegecko
     *
     * @return 0
     */
    public function actionSyncLocation()
    {
        self::logMessage('Syncing Locations from Tradegecko...');

        $realArr = [];
        $realArr['location'] = 0;

        $storedArr = [];
        $storedArr['location'] = 0;

        $startTime = microtime(true);

        try{
            $obj = self::getTradegeckoObject();
            if ($obj == null)
                return -1;
            
            $locations = $obj->getLocations();
            self::checkData($locations, "Locations");
        }
        catch(\Exception $ex)
        {
            echo 'Syncing Locations Failed. API Request Error occured';
            self::logMessage('Syncing Locations Failed. API Request Error occured');
            return;
        }

        if($locations){
            foreach($locations as $location)
            {
                if ($location['id'] != null)
                {
                    $itemLocation = [];
                    $itemLocation['id'] = $location['id'];
                    $itemLocation['created_at'] = $location['created_at'];
                    $itemLocation['updated_at'] = $location['updated_at'];
                    $itemLocation['holds_stock'] = $location['holds_stock'];
                    $itemLocation['address1'] = $location['address1'];
                    $itemLocation['address2'] = $location['address2'];
                    $itemLocation['city'] = $location['city'];
                    $itemLocation['country'] = $location['country'];
                    $itemLocation['label'] = $location['label'];
                    $itemLocation['state'] = $location['state'];
                    $itemLocation['location_status'] = $location['status'];
                    $itemLocation['suburb'] = $location['suburb'];
                    $itemLocation['zip_code'] = $location['zip_code'];

                    $modelLocation = TradegeckoLocation::find()->where(['id' => $location['id']])->one();
                    if ($modelLocation == null)
                        $modelLocation = new TradegeckoLocation();

                    $modelLocation->attributes = $itemLocation;
                    if ($modelLocation->save()) {
                        $storedArr['location'] ++;
                    }
                    $realArr['location'] ++;
                }
            }
        }

        self::logMessage($realArr['location'].' Updates Found');
        self::logMessage($storedArr['location'].' Updates were Applied to DB correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Locations\t".$realArr['location']."\t".$storedArr['location'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Locations from Tradegecko Done');

        return 0;
    }

    /**
     * This command updates all payment term data from tradegecko
     *
     * @return 0
     */
    public function actionSyncPaymentTerm()
    {
        self::logMessage('Syncing Payment Terms from Tradegecko...');

        $realArr = [];
        $realArr['paymentTerm'] = 0;

        $storedArr = [];
        $storedArr['paymentTerm'] = 0;

        $startTime = microtime(true);

        try{
            $obj = self::getTradegeckoObject();
            if ($obj == null)
                return -1;
            
            $paymentTerms = $obj->getPaymentTerms();
            self::checkData($paymentTerms, "Payment Terms");
        }
        catch(\Exception $ex)
        {
            echo 'Syncing Payment Terms Failed. API Request Error occured';
            self::logMessage('Syncing Payment Terms Failed. API Request Error occured');
            return;
        }

        if($paymentTerms){
            foreach($paymentTerms as $paymentTerm)
            {
                if ($paymentTerm['id'] != null)
                {
                    $itemPaymentTerm = [];
                    $itemPaymentTerm['id'] = $paymentTerm['id'];
                    $itemPaymentTerm['due_in_days'] = $paymentTerm['due_in_days'];
                    $itemPaymentTerm['payment_term_name'] = $paymentTerm['name'];
                    $itemPaymentTerm['payment_term_status'] = $paymentTerm['status'];
                    $itemPaymentTerm['payment_term_from'] = $paymentTerm['from'];

                    $modelPaymentTerm = TradegeckoPaymentTerm::find()->where(['id' => $paymentTerm['id']])->one();
                    if ($modelPaymentTerm == null)
                        $modelPaymentTerm = new TradegeckoPaymentTerm();

                    $modelPaymentTerm->attributes = $itemPaymentTerm;
                    if ($modelPaymentTerm->save()) {
                        $storedArr['paymentTerm'] ++;
                    }
                    $realArr['paymentTerm'] ++;
                }
            }
        }

        self::logMessage($realArr['paymentTerm'].' Updates Found');
        self::logMessage($storedArr['paymentTerm'].' Updates were Applied to DB correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Payment Terms\t".$realArr['paymentTerm']."\t".$storedArr['paymentTerm'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Payment Terms from Tradegecko Done');

        return 0;
    }

    /**
     * This command updates all price list data from tradegecko
     *
     * @return 0
     */
    public function actionSyncPriceList()
    {
        self::logMessage('Syncing Price List from Tradegecko...');

        $realArr = [];
        $realArr['priceList'] = 0;

        $storedArr = [];
        $storedArr['priceList'] = 0;

        $startTime = microtime(true);

        try{
            $obj = self::getTradegeckoObject();
            if ($obj == null)
                return -1;
            
            $priceLists = $obj->getPriceLists();
            self::checkData($priceLists, "Price List");
        }
        catch(\Exception $ex)
        {
            echo 'Syncing Price List Failed. API Request Error occured';
            self::logMessage('Syncing Price List Failed. API Request Error occured');
            return;
        }

        if($priceLists){
            foreach($priceLists as $priceList)
            {
                if ($priceList['id'] != null)
                {
                    $itemPriceList = [];
                    $itemPriceList['id'] = $priceList['id'];
                    $itemPriceList['price_list_code'] = $priceList['code'];
                    $itemPriceList['price_list_name'] = $priceList['name'];
                    $itemPriceList['is_cost'] = $priceList['is_cost'] == 1 ? 'true' : 'false';
                    $itemPriceList['currency_id'] = $priceList['currency_id'];
                    $itemPriceList['currency_symbol'] = $priceList['currency_symbol'];
                    $itemPriceList['currency_iso'] = $priceList['currency_iso'];
                    $itemPriceList['is_default'] = $priceList['is_default'];

                    $modelPriceList = TradegeckoPriceList::find()->where(['id' => $priceList['id']])->one();
                    if ($modelPriceList == null)
                        $modelPriceList = new TradegeckoPriceList();

                    $modelPriceList->attributes = $itemPriceList;
                    if ($modelPriceList->save()) {
                        $storedArr['priceList'] ++;
                    }
                    $realArr['priceList'] ++;
                }
            }
        }

        self::logMessage($realArr['priceList'].' Updates Found');
        self::logMessage($storedArr['priceList'].' Updates were Applied to DB correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Price Lists\t".$realArr['priceList']."\t".$storedArr['priceList'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Price List from Tradegecko Done');

        return 0;
    }

    /**
     * This command updates all procurement data from tradegecko
     *
     * @return 0
     */
    public function actionSyncProcurement()
    {
        self::logMessage('Syncing Procurements from Tradegecko...');

        $realArr = [];
        $realArr['procurement'] = 0;

        $storedArr = [];
        $storedArr['procurement'] = 0;

        $startTime = microtime(true);

        try{
            $obj = self::getTradegeckoObject();
            if ($obj == null)
                return -1;
            
            $procurements = $obj->getProcurements();
            self::checkData($procurements, "Procurements");
        }
        catch(\Exception $ex)
        {
            echo 'Syncing Procurements Failed. API Request Error occured';
            self::logMessage('Syncing Procurements Failed. API Request Error occured');
            return;
        }

        if($procurements){
            foreach($procurements as $procurement)
            {
                if ($procurement['id'] != null)
                {
                    $itemProcurement = [];
                    $itemProcurement['id'] = $procurement['id'];
                    $itemProcurement['created_at'] = $procurement['created_at'];
                    $itemProcurement['updated_at'] = $procurement['updated_at'];
                    $itemProcurement['received_at'] = $procurement['received_at'];
                    $itemProcurement['purchase_order_id'] = $procurement['purchase_order_id'];
                    $itemProcurement['purchase_order_line_item_ids'] = serialize($procurement['purchase_order_line_item_ids']);

                    $modelProcurement = TradegeckoProcurement::find()->where(['id' => $procurement['id']])->one();
                    if ($modelProcurement == null)
                        $modelProcurement = new TradegeckoProcurement();

                    $modelProcurement->attributes = $itemProcurement;
                    if ($modelProcurement->save()) {
                        $storedArr['procurement'] ++;
                    }
                    $realArr['procurement'] ++;
                }
            }
        }

        self::logMessage($realArr['procurement'].' Updates Found');
        self::logMessage($storedArr['procurement'].' Updates were Applied to DB correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Procurements\t".$realArr['procurement']."\t".$storedArr['procurement'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Procurements from Tradegecko Done');

        return 0;
    }

    /**
     * This command updates all purchase order data from tradegecko
     *
     * @return 0
     */
    public function actionSyncPurchaseOrder()
    {
        self::logMessage('Syncing Purchase Orders from Tradegecko...');

        $realArr = [];
        $realArr['purchaseOrder'] = 0;

        $storedArr = [];
        $storedArr['purchaseOrder'] = 0;

        $startTime = microtime(true);

        try{
            $obj = self::getTradegeckoObject();
            if ($obj == null)
                return -1;
            
            $purchaseOrders = $obj->getPurchaseOrders();
            self::checkData($purchaseOrders, "Purchase Orders");
        }
        catch(\Exception $ex)
        {
            echo 'Syncing Purchase Orders Failed. API Request Error occured';
            self::logMessage('Syncing Purchase Orders Failed. API Request Error occured');
            return;
        }

        if($purchaseOrders){
            foreach($purchaseOrders as $purchaseOrder)
            {
                if ($purchaseOrder['id'] != null)
                {
                    $itemPurchaseOrder = [];
                    $itemPurchaseOrder['id'] = $purchaseOrder['id'];
                    $itemPurchaseOrder['created_at'] = $purchaseOrder['created_at'];
                    $itemPurchaseOrder['updated_at'] = $purchaseOrder['updated_at'];
                    $itemPurchaseOrder['billing_address_id'] = $purchaseOrder['billing_address_id'];
                    $itemPurchaseOrder['company_id'] = $purchaseOrder['company_id'];
                    $itemPurchaseOrder['due_at'] = $purchaseOrder['due_at'];
                    $itemPurchaseOrder['email'] = $purchaseOrder['email'];
                    $itemPurchaseOrder['notes'] = $purchaseOrder['notes'];
                    $itemPurchaseOrder['order_number'] = $purchaseOrder['order_number'];
                    $itemPurchaseOrder['procurement_ids'] = serialize($purchaseOrder['procurement_ids']);
                    $itemPurchaseOrder['procurement_status'] = $purchaseOrder['procurement_status'];
                    $itemPurchaseOrder['purchase_order_line_item_ids'] = serialize($purchaseOrder['purchase_order_line_item_ids']);
                    $itemPurchaseOrder['reference_number'] = $purchaseOrder['reference_number'];
                    $itemPurchaseOrder['purchase_order_status'] = $purchaseOrder['status'];
                    $itemPurchaseOrder['stock_location_id'] = $purchaseOrder['stock_location_id'];
                    $itemPurchaseOrder['tax_type'] = $purchaseOrder['tax_type'];
                    $itemPurchaseOrder['xero'] = $purchaseOrder['xero'];
                    $itemPurchaseOrder['vendor_address_id'] = $purchaseOrder['vendor_address_id'];

                    $modelPurchaseOrder = TradegeckoPurchaseOrder::find()->where(['id' => $purchaseOrder['id']])->one();
                    if ($modelPurchaseOrder == null)
                        $modelPurchaseOrder = new TradegeckoPurchaseOrder();

                    $modelPurchaseOrder->attributes = $itemPurchaseOrder;
                    if ($modelPurchaseOrder->save()) {
                        $storedArr['purchaseOrder'] ++;
                    }
                    $realArr['purchaseOrder'] ++;
                }
            }
        }

        self::logMessage($realArr['purchaseOrder'].' Updates Found');
        self::logMessage($storedArr['purchaseOrder'].' Updates were Applied to DB correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Purchase Orders\t".$realArr['purchaseOrder']."\t".$storedArr['purchaseOrder'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Purchase Orders from Tradegecko Done');

        return 0;
    }

    /**
     * This command updates all purchase order line item data from tradegecko
     *
     * @return 0
     */
    public function actionSyncPurchaseOrderLineItem()
    {
        self::logMessage('Syncing Purchase Order Line Items from Tradegecko...');

        $realArr = [];
        $realArr['purchaseOrderLineItem'] = 0;

        $storedArr = [];
        $storedArr['purchaseOrderLineItem'] = 0;

        $startTime = microtime(true);

        try{
            $obj = self::getTradegeckoObject();
            if ($obj == null)
                return -1;
            
            $purchaseOrderLineItems = $obj->getPurchaseOrderLineItems();
            self::checkData($purchaseOrderLineItems, "Purchase Order Line Items");
        }
        catch(\Exception $ex)
        {
            echo 'Syncing Purchase Order Line Items Failed. API Request Error occured';
            self::logMessage('Syncing Purchase Order Line Items Failed. API Request Error occured');
            return;
        }

        if($purchaseOrderLineItems){
            foreach($purchaseOrderLineItems as $purchaseOrderLineItem)
            {
                if ($purchaseOrderLineItem['id'] != null)
                {
                    $itemPurchaseOrderLineItem = [];
                    $itemPurchaseOrderLineItem['id'] = $purchaseOrderLineItem['id'];
                    $itemPurchaseOrderLineItem['purchase_order_id'] = $purchaseOrderLineItem['purchase_order_id'];
                    $itemPurchaseOrderLineItem['variant_id'] = $purchaseOrderLineItem['variant_id'];
                    $itemPurchaseOrderLineItem['procurement_id'] = $purchaseOrderLineItem['procurement_id'];
                    $itemPurchaseOrderLineItem['quantity'] = $purchaseOrderLineItem['quantity'];
                    $itemPurchaseOrderLineItem['position'] = $purchaseOrderLineItem['position'];
                    $itemPurchaseOrderLineItem['tax_type_id'] = $purchaseOrderLineItem['tax_type_id'];
                    $itemPurchaseOrderLineItem['tax_rate_override'] = $purchaseOrderLineItem['tax_rate_override'];
                    $itemPurchaseOrderLineItem['price'] = $purchaseOrderLineItem['price'];
                    $itemPurchaseOrderLineItem['label'] = $purchaseOrderLineItem['label'];
                    $itemPurchaseOrderLineItem['freeform'] = $purchaseOrderLineItem['freeform'];
                    $itemPurchaseOrderLineItem['base_price'] = $purchaseOrderLineItem['base_price'];

                    $modelPurchaseOrderLineItem = TradegeckoPurchaseOrderLineItem::find()->where(['id' => $purchaseOrderLineItem['id']])->one();
                    if ($modelPurchaseOrderLineItem == null)
                        $modelPurchaseOrderLineItem = new TradegeckoPurchaseOrderLineItem();

                    $modelPurchaseOrderLineItem->attributes = $itemPurchaseOrderLineItem;
                    if ($modelPurchaseOrderLineItem->save()) {
                        $storedArr['purchaseOrderLineItem'] ++;
                    }
                    $realArr['purchaseOrderLineItem'] ++;
                }
            }
        }

        self::logMessage($realArr['purchaseOrderLineItem'].' Updates Found');
        self::logMessage($storedArr['purchaseOrderLineItem'].' Updates were Applied to DB correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Purchase OLIs\t".$realArr['purchaseOrderLineItem']."\t".$storedArr['purchaseOrderLineItem'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Purchase Order Line Items from Tradegecko Done');

        return 0;
    }

    /**
     * This command updates all tax component data from tradegecko
     *
     * @return 0
     */
    public function actionSyncTaxComponent()
    {
        self::logMessage('Syncing Tax Components from Tradegecko...');

        $realArr = [];
        $realArr['taxComponent'] = 0;

        $storedArr = [];
        $storedArr['taxComponent'] = 0;

        $startTime = microtime(true);

        try{
            $obj = self::getTradegeckoObject();
            if ($obj == null)
                return -1;
            
            $taxComponents = $obj->getTaxComponents();
            self::checkData($taxComponents, "Tax Components");
        }
        catch(\Exception $ex)
        {
            echo 'Syncing Tax Components Failed. API Request Error occured';
            self::logMessage('Syncing Tax Components Failed. API Request Error occured');
            return;
        }

        if($taxComponents){
            foreach($taxComponents as $taxComponent)
            {
                if ($taxComponent['id'] != null)
                {
                    $itemTaxComponent = [];
                    $itemTaxComponent['id'] = $taxComponent['id'];
                    $itemTaxComponent['tax_type_id'] = $taxComponent['tax_type_id'];
                    $itemTaxComponent['position'] = $taxComponent['position'];
                    $itemTaxComponent['label'] = $taxComponent['label'];
                    $itemTaxComponent['rate'] = $taxComponent['rate'];
                    $itemTaxComponent['compound'] = $taxComponent['compound'] == 1 ? 'true' : 'false';

                    $modelTaxComponent = TradegeckoTaxComponent::find()->where(['id' => $taxComponent['id']])->one();
                    if ($modelTaxComponent == null)
                        $modelTaxComponent = new TradegeckoTaxComponent();

                    $modelTaxComponent->attributes = $itemTaxComponent;
                    if ($modelTaxComponent->save()) {
                        $storedArr['taxComponent'] ++;
                    }
                    $realArr['taxComponent'] ++;
                }
            }
        }

        self::logMessage($realArr['taxComponent'].' Updates Found');
        self::logMessage($storedArr['taxComponent'].' Updates were Applied to DB correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Tax Components\t".$realArr['taxComponent']."\t".$storedArr['taxComponent'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Tax Components from Tradegecko Done');

        return 0;
    }

    /**
     * This command updates all tax type data from tradegecko
     *
     * @return 0
     */
    public function actionSyncTaxType()
    {
        self::logMessage('Syncing Tax Types from Tradegecko...');

        $realArr = [];
        $realArr['taxType'] = 0;

        $storedArr = [];
        $storedArr['taxType'] = 0;

        $startTime = microtime(true);

        try{
            $obj = self::getTradegeckoObject();
            if ($obj == null)
                return -1;
            
            $taxTypes = $obj->getTaxTypes();
            self::checkData($taxTypes, "Tax Types");
        }
        catch(\Exception $ex)
        {
            echo 'Syncing Tax Types Failed. API Request Error occured';
            self::logMessage('Syncing Tax Types Failed. API Request Error occured');
            return;
        }

        if($taxTypes){
            foreach($taxTypes as $taxType)
            {
                if ($taxType['id'] != null)
                {
                    $itemTaxType = [];
                    $itemTaxType['id'] = $taxType['id'];
                    $itemTaxType['tax_type_code'] = $taxType['code'];
                    $itemTaxType['imported_from'] = $taxType['imported_from'];
                    $itemTaxType['tax_type_name'] = $taxType['name'];
                    $itemTaxType['tax_type_status'] = $taxType['status'];
                    $itemTaxType['quickbooks_online_id'] = $taxType['quickbooks_online_id'];
                    $itemTaxType['xero_online_id'] = $taxType['xero_online_id'];
                    $itemTaxType['effective_rate'] = $taxType['effective_rate'];
                    $itemTaxType['tax_component_ids'] = serialize($taxType['tax_component_ids']);

                    $modelTaxType = TradegeckoTaxType::find()->where(['id' => $taxType['id']])->one();
                    if ($modelTaxType == null)
                        $modelTaxType = new TradegeckoTaxType();

                    $modelTaxType->attributes = $itemTaxType;
                    if ($modelTaxType->save()) {
                        $storedArr['taxType'] ++;
                    }
                    $realArr['taxType'] ++;
                }
            }
        }

        self::logMessage($realArr['taxType'].' Updates Found');
        self::logMessage($storedArr['taxType'].' Updates were Applied to DB correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Tax Types\t".$realArr['taxType']."\t".$storedArr['taxType'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Tax Types from Tradegecko Done');

        return 0;
    }

    /**
     * This command updates all user data from tradegecko
     *
     * @return 0
     */
    public function actionSyncUser()
    {
        self::logMessage('Syncing Users from Tradegecko...');

        $realArr = [];
        $realArr['user'] = 0;

        $storedArr = [];
        $storedArr['user'] = 0;

        $startTime = microtime(true);

        try{
            $obj = self::getTradegeckoObject();
            if ($obj == null)
                return -1;
            
            $users = $obj->getUsers();
            self::checkData($users, "Users");
        }
        catch(\Exception $ex)
        {
            echo 'Syncing Users Failed. API Request Error occured';
            self::logMessage('Syncing Users Failed. API Request Error occured');
            return;
        }

        if($users){
            foreach($users as $user)
            {
                if ($user['id'] != null)
                {
                    $itemUser = [];
                    $itemUser['id'] = $user['id'];
                    $itemUser['created_at'] = $user['created_at'];
                    $itemUser['updated_at'] = $user['updated_at'];
                    $itemUser['action_items_email'] = $user['action_items_email'];
                    $itemUser['avatar_url'] = $user['avatar_url'];
                    $itemUser['billing_contact'] = $user['billing_contact'];
                    $itemUser['email'] = $user['email'];
                    $itemUser['first_name'] = $user['first_name'];
                    $itemUser['last_name'] = $user['last_name'];
                    $itemUser['last_sign_in_at'] = $user['last_sign_in_at'];
                    $itemUser['location'] = $user['location'];
                    $itemUser['login_id'] = $user['login_id'];
                    $itemUser['mobile'] = $user['mobile'];
                    $itemUser['notification_email'] = $user['notification_email'];
                    $itemUser['permissions'] = serialize($user['permissions']);
                    $itemUser['phone_number'] = $user['phone_number'];
                    $itemUser['position'] = $user['position'];
                    $itemUser['sales_report_email'] = $user['sales_report_email'];
                    $itemUser['user_status'] = $user['status'];
                    $itemUser['account_id'] = $user['account_id'];

                    $modelUser = TradegeckoUser::find()->where(['id' => $user['id']])->one();
                    if ($modelUser == null)
                        $modelUser = new TradegeckoUser();

                    $modelUser->attributes = $itemUser;
                    if ($modelUser->save()) {
                        $storedArr['user'] ++;
                    }
                    $realArr['user'] ++;
                }
            }
        }

        self::logMessage($realArr['user'].' Updates Found');
        self::logMessage($storedArr['user'].' Updates were Applied to DB correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Users\t\t".$realArr['user']."\t".$storedArr['user'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Users from Tradegecko Done');

        return 0;
    }

    /**
     * This command updates all variant data from tradegecko
     *
     * @return 0
     */
    public function actionSyncVariant()
    {
        self::logMessage('Syncing Variants from Tradegecko...');

        $realArr = [];
        $realArr['variant'] = 0;
        $realArr['variantLocation'] = 0;
        $realArr['variantStockLevel'] = 0;
        $realArr['variantCommittedStockLevel'] = 0;
        $realArr['variantIncomingStockLevel'] = 0;
        $realArr['deleted'] = 0;

        $storedArr = [];
        $storedArr['variant'] = 0;
        $storedArr['variantLocation'] = 0;
        $storedArr['variantStockLevel'] = 0;
        $storedArr['variantCommittedStockLevel'] = 0;
        $storedArr['variantIncomingStockLevel'] = 0;
        $storedArr['deleted'] = 0;

        $startTime = microtime(true);

        try{
            $obj = self::getTradegeckoObject();
            if ($obj == null)
                return -1;
            
            $variants = $obj->getVariants();
            self::checkData($variants, "Variants");
        }
        catch(\Exception $ex)
        {
            echo 'Syncing Variants Failed. API Request Error occured';
            self::logMessage('Syncing Variants Failed. API Request Error occured');
            return;
        }

        if ($variants)
        {
            $tradeGeckoVariantIds = array_map(function($param){ return $param['id'];}, $variants);

            $results = TradegeckoVariant::find()->all();
            $localVariants = [];
            foreach($results as $res)
                $localVariants[$res->id] = $res;
            $localVariantIds = array_map(function($param){ return $param->id;}, $localVariants);

            foreach($variants as $variant)
            {
                if ($variant['id'] != null)
                {
                    $itemVariant = [];
                    $itemVariant['id'] = $variant['id'];
                    $itemVariant['created_at'] = $variant['created_at'];
                    $itemVariant['updated_at'] = $variant['updated_at'];
                    $itemVariant['product_id'] = $variant['product_id'];
                    $itemVariant['default_ledger_account_id'] = $variant['default_ledger_account_id'];
                    $itemVariant['buy_price'] = $variant['buy_price'];
                    $itemVariant['committed_stock'] = $variant['committed_stock'];
                    $itemVariant['incoming_stock'] = $variant['incoming_stock'];
                    $itemVariant['composite'] = $variant['composite'];
                    $itemVariant['description'] = $variant['description'];
                    $itemVariant['is_online'] = $variant['is_online'] == 1 ? 'true' : 'false';
                    $itemVariant['keep_selling'] = $variant['keep_selling'] == 1 ? 'true' : 'false';
                    $itemVariant['last_cost_price'] = $variant['last_cost_price'];
                    $itemVariant['manage_stock'] = $variant['manage_stock'] == 1 ? 'true' : 'false';
                    $itemVariant['max_online'] = $variant['max_online'];
                    $itemVariant['moving_average_cost'] = $variant['moving_average_cost'];
                    $itemVariant['variant_name'] = $variant['name'];
                    $itemVariant['online_ordering'] = $variant['online_ordering'] == 1 ? 'true' : 'false';
                    $itemVariant['opt1'] = $variant['opt1'];
                    $itemVariant['opt2'] = $variant['opt2'];
                    $itemVariant['opt3'] = $variant['opt3'];
                    $itemVariant['position'] = $variant['position'];
                    $itemVariant['product_name'] = $variant['product_name'];
                    $itemVariant['product_status'] = $variant['product_status'];
                    $itemVariant['product_type'] = $variant['product_type'];
                    $itemVariant['retail_price'] = $variant['retail_price'];
                    $itemVariant['sellable'] = $variant['sellable'] == 1 ? 'true' : 'false';
                    $itemVariant['sku'] = $variant['sku'];
                    $itemVariant['variant_status'] = $variant['status'];
                    $itemVariant['stock_on_hand'] = $variant['stock_on_hand'];
                    $itemVariant['supplier_code'] = $variant['supplier_code'];
                    $itemVariant['taxable'] = $variant['taxable'] == 1 ? 'true' : 'false';
                    $itemVariant['upc'] = $variant['upc'];
                    $itemVariant['weight'] = $variant['weight'];
                    $itemVariant['wholesale_price'] = $variant['wholesale_price'];
                    $itemVariant['image_ids'] = serialize($variant['image_ids']);

                    $modelVariant = TradegeckoVariant::find()->where(['id' => $variant['id']])->one();
                    if ($modelVariant == null)
                        $modelVariant = new TradegeckoVariant();

                    $modelVariant->attributes = $itemVariant;
                    if ($modelVariant->save()) {
                        $storedArr['variant'] ++;
                    }
                    $realArr['variant'] ++;

                    // locations
                    if (isset($variant['locations']) && $variant['locations'] != null)
                    {
                        foreach($variant['locations'] as $variantLocation)
                        {                
                            if ($variantLocation['location_id'] != null)
                            {
                                $itemVariantLocation = [];
                                $itemVariantLocation['variant_id'] = $variant['id'];
                                $itemVariantLocation['location_id'] = $variantLocation['location_id'];
                                $itemVariantLocation['stock_on_hand'] = $variantLocation['stock_on_hand'];
                                $itemVariantLocation['committed'] = $variantLocation['committed'];
                                $itemVariantLocation['bin_location'] = $variantLocation['bin_location'];
                                $itemVariantLocation['reorder_point'] = $variantLocation['reorder_point'];

                                $modelVariantLocation = TradegeckoVariantLocation::find()->where(['variant_id' => $variant['id'], 'location_id' => $variantLocation['location_id']])->one();
                                if ($modelVariantLocation == null)
                                    $modelVariantLocation = new TradegeckoVariantLocation();

                                $modelVariantLocation->attributes = $itemVariantLocation;
                                if ($modelVariantLocation->save()) {
                                    $storedArr['variantLocation'] ++;
                                }
                                $realArr['variantLocation'] ++;
                            }
                        }
                    }

                    // stock levels
                    if (isset($variant['stock_levels']) && $variant['stock_levels'] != null)
                    {
                        foreach($variant['stock_levels'] as $key=>$value)
                        {                
                            $itemVariantStockLevel = [];
                            $itemVariantStockLevel['variant_id'] = $variant['id'];
                            $itemVariantStockLevel['stock_level_id'] = $key;
                            $itemVariantStockLevel['stock_level_value'] = $value.'';

                            $modelVariantStockLevel = TradegeckoVariantStockLevel::find()->where(['variant_id' => $variant['id'], 'stock_level_id' => $key])->one();
                            if ($modelVariantStockLevel == null)
                                $modelVariantStockLevel = new TradegeckoVariantStockLevel();

                            $modelVariantStockLevel->attributes = $itemVariantStockLevel;
                            if ($modelVariantStockLevel->save()) {
                                $storedArr['variantStockLevel'] ++;
                            }
                            $realArr['variantStockLevel'] ++;
                        }
                    }

                    // committed stock levels
                    if (isset($variant['committed_stock_levels']) && $variant['committed_stock_levels'] != null)
                    {
                        foreach($variant['committed_stock_levels'] as $key=>$value)
                        {                
                            $itemVariantCommittedStockLevel = [];
                            $itemVariantCommittedStockLevel['variant_id'] = $variant['id'];
                            $itemVariantCommittedStockLevel['committed_stock_level_id'] = $key;
                            $itemVariantCommittedStockLevel['committed_stock_level_value'] = $value.'';

                            $modelVariantCommittedStockLevel = TradegeckoVariantCommittedStockLevel::find()->where(['variant_id' => $variant['id'], 'committed_stock_level_id' => $key])->one();
                            if ($modelVariantCommittedStockLevel == null)
                                $modelVariantCommittedStockLevel = new TradegeckoVariantCommittedStockLevel();

                            $modelVariantCommittedStockLevel->attributes = $itemVariantCommittedStockLevel;
                            if ($modelVariantCommittedStockLevel->save()) {
                                $storedArr['variantCommittedStockLevel'] ++;
                            }
                            $realArr['variantCommittedStockLevel'] ++;
                        }
                    }

                    // incoming stock levels
                    if(isset($variant['incoming_stock_levels']) && $variant['incoming_stock_levels'] != null) {
                        foreach($variant['incoming_stock_levels'] as $key=>$value)
                        {
                            $itemVariantIncomingStockLevel = [];
                            $itemVariantIncomingStockLevel['variant_id'] = $variant['id'];
                            $itemVariantIncomingStockLevel['incoming_stock_level_id'] = $key;
                            $itemVariantIncomingStockLevel['incoming_stock_level_value'] = $value.'';

                            $modelVariantIncomingStockLevel = TradegeckoVariantIncomingStockLevel::find()->where(['variant_id' => $variant['id'], 'incoming_stock_level_id' => $key])->one();
                            if ($modelVariantIncomingStockLevel == null)
                                $modelVariantIncomingStockLevel = new TradegeckoVariantIncomingStockLevel();

                            $modelVariantIncomingStockLevel->attributes = $itemVariantIncomingStockLevel;
                            if ($modelVariantIncomingStockLevel->save()) {
                                $storedArr['variantIncomingStockLevel'] ++;
                            }
                            $realArr['variantIncomingStockLevel'] ++;
                        }
                    }
                }
            }

            // check if a variant was deleted from TradeGecko
            $deletableVariantIds = array_diff($localVariantIds, $tradeGeckoVariantIds);

            foreach($deletableVariantIds as $variantId) {
                $model = TradegeckoVariant::find()->where(['id' => $variantId])->one();
                if($model) {
                    $model->attributes = [
                        'variant_status' => 'deleted'
                    ];
                    if ($model->save()) {
                        $storedArr['deleted'] ++;
                    }
                    $realArr['deleted'] ++;
                }
            }
        }

        self::logMessage($realArr['variant'].' Updates Found');
        self::logMessage($storedArr['variant'].' Updates were Applied to DB correctly');
        self::logMessage($realArr['deleted'].' Variants were deleted');
        self::logMessage($storedArr['deleted'].' Variants were deleted on local DB correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Variants\t".$realArr['variant']."\t".$storedArr['variant'].PHP_EOL;
        echo "Variant Loc\t".$realArr['variantLocation']."\t".$storedArr['variantLocation'].PHP_EOL;
        echo "Variant SL\t".$realArr['variantStockLevel']."\t".$storedArr['variantStockLevel'].PHP_EOL;
        echo "Variant CSL\t".$realArr['variantCommittedStockLevel']."\t".$storedArr['variantCommittedStockLevel'].PHP_EOL;
        echo "Variant ISL\t".$realArr['variantIncomingStockLevel']."\t".$storedArr['variantIncomingStockLevel'].PHP_EOL;
        echo "Deleted\t\t".$realArr['deleted']."\t".$storedArr['deleted'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Variants from Tradegecko Done');

        return 0;
    }

    /**
     * This command updates all payment data from tradegecko
     *
     * @return 0
     */
    public function actionSyncPayment()
    {
        self::logMessage('Syncing Payments from Tradegecko...');

        $realArr = [];
        $realArr['payment'] = 0;

        $storedArr = [];
        $storedArr['payment'] = 0;

        $startTime = microtime(true);

        try{
            $obj = self::getTradegeckoObject();
            if ($obj == null)
                return -1;

            $payments = $obj->getPayments();
            self::checkData($payments, "Payments");
        }
        catch(\Exception $ex)
        {
            echo 'Syncing Payments Failed. API Request Error occured';
            self::logMessage('Syncing Payments Failed. API Request Error occured');
            return;
        }

        if($payments){
            foreach($payments as $payment)
            {
                if ($payment['id'] != null)
                {
                    $itemPayment = [];
                    $itemPayment['id'] = $payment['id'];
                    $itemPayment['created_at'] = $payment['created_at'];
                    $itemPayment['updated_at'] = $payment['updated_at'];
                    $itemPayment['invoice_id'] = $payment['invoice_id'];
                    $itemPayment['amount'] = $payment['amount'];
                    $itemPayment['reference'] = $payment['reference'];
                    $itemPayment['paid_at'] = $payment['paid_at'];
                    $itemPayment['payment_status'] = $payment['status'];
                    $itemPayment['exchange_rate'] = $payment['exchange_rate'];

                    $modelPayment = TradegeckoPayment::find()->where(['id' => $payment['id']])->one();
                    if ($modelPayment == null)
                        $modelPayment = new TradegeckoPayment();

                    $modelPayment->attributes = $itemPayment;
                    if ($modelPayment->save()) {
                        $storedArr['payment'] ++;
                    }
                    $realArr['payment'] ++;
                }
            }
        }

        self::logMessage($realArr['payment'].' Updates Found');
        self::logMessage($storedArr['payment'].' Updates were Applied to DB correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Payments\t".$realArr['payment']."\t".$storedArr['payment'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Payments from Tradegecko Done');

        return 0;
    }

    /**
     * This command updates all image data from tradegecko
     *
     * @return 0
     */
    public function actionSyncImage()
    {
        self::logMessage('Syncing Images from Tradegecko...');

        $realArr = [];
        $realArr['image'] = 0;

        $storedArr = [];
        $storedArr['image'] = 0;

        $startTime = microtime(true);

        try{
            $obj = self::getTradegeckoObject();
            if ($obj == null)
                return -1;

            $images = $obj->getImages();
            self::checkData($images, "Images");
        }
        catch(\Exception $ex)
        {
            echo 'Syncing Images Failed. API Request Error occured';
            self::logMessage('Syncing Images Failed. API Request Error occured');
            return -2;
        }

        if ($images)
        {
            foreach($images as $image)
            {
                if ($image['id'] != null)
                {
                    $itemImage = [];
                    $itemImage['id'] = $image['id'];
                    $itemImage['variant_id'] = $image['variant_id'];
                    $itemImage['uploader_id'] = $image['uploader_id'];
                    $itemImage['image_name'] = $image['name'];
                    $itemImage['position'] = $image['position'];
                    $itemImage['base_path'] = $image['base_path'];
                    $itemImage['file_name'] = $image['file_name'];
                    $itemImage['versions'] = serialize($image['versions']);
                    $itemImage['image_processing'] = $image['image_processing'] == 1 ? 'true' : 'false';

                    $modelImage = TradegeckoImage::find()->where(['id' => $image['id']])->one();
                    if ($modelImage == null)
                        $modelImage = new TradegeckoImage();

                    $modelImage->attributes = $itemImage;
                    if ($modelImage->save()) {
                        $storedArr['image'] ++;
                    }
                    $realArr['image'] ++;
                }
            }
        }

        self::logMessage($realArr['image'].' Updates Found');
        self::logMessage($storedArr['image'].' Updates were Applied to DB correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Images\t\t".$realArr['image']."\t".$storedArr['image'].PHP_EOL.PHP_EOL;

        $finishTime = microtime(true) - $startTime;

        echo $finishTime . ' seconds'."\n";

        self::logMessage('Syncing Images from Tradegecko Done');

        return 0;
    }

    /**
     * This command logs a message to a tradegecko.log file
     *
     * @param string $msg the message to be logged
     *
     */
    public function logMessage($msg)
    {
        Yii::info($msg, 'tradegecko');
    }

    /**
     * This command runs syncing with inventory2
     *
     * @return 0
     */
    public function actionSyncDb()
    {
        self::logMessage('Syncing Inventory2 from Inventory1...');

        $realArr = [];
        $realArr['synced_product'] = 0;
        $realArr['synced_variant'] = 0;
        $realArr['synced_image'] = 0;

        $storedArr = [];
        $storedArr['synced_product'] = 0;
        $storedArr['synced_variant'] = 0;
        $storedArr['synced_image'] = 0;

        //PRODUCTS
        $tradegeckoProducts = TradegeckoProduct::find()->all();
        foreach($tradegeckoProducts as $tradegeckoProduct)
        {
            $item = [];
            $item['tradegeckoId'] = $tradegeckoProduct['id'];
            $item['name'] = $tradegeckoProduct['product_name'];
            $item['description'] = $tradegeckoProduct['description'];
            $item['brand'] = $tradegeckoProduct['brand'];
            $item['productType'] = $tradegeckoProduct['product_type'];
            $item['supplier'] = $tradegeckoProduct['supplier'];
            $item['quantity'] = $tradegeckoProduct['quantity'];

            $item['status'] = $tradegeckoProduct['product_status'];
            if ($tradegeckoProduct['product_status'] == 'deleted')
                $item['status'] = 'inactive';

            $item['opt1'] = $tradegeckoProduct['opt1'];
            $item['opt2'] = $tradegeckoProduct['opt2'];
            $item['opt3'] = $tradegeckoProduct['opt3'];
            $item['imageUrl'] = $tradegeckoProduct['image_url'];
            $item['tradegeckoCreatedAt'] = $tradegeckoProduct['created_at'];
            $item['tradegeckoUpdatedAt'] = $tradegeckoProduct['updated_at'];
            $item['tradegeckoStatus'] = 'normal';
            $item['shop'] = $tradegeckoProduct['shop'];

            $model = Products::find()->where(['tradegeckoId' => $tradegeckoProduct['id']])->one();
            if ($model == null)
                $model = new Products();

            $model->attributes = $item;
            if ($model->save()) {
                $storedArr['synced_product'] ++;

                //VARIANTS
                $tradegeckoVariants = TradegeckoVariant::find()->where(['product_id'=>$tradegeckoProduct['id']])->all();
                foreach($tradegeckoVariants as $tradegeckoVariant)
                {
                    $itemVariant = [];
                    $itemVariant['tradegeckoId'] = $tradegeckoVariant['id'];
                    $itemVariant['productId'] = $model['id'];
                    $itemVariant['tradegeckoProductId'] = $tradegeckoVariant['product_id'];
                    $itemVariant['name'] = $tradegeckoVariant['variant_name'];
                    $itemVariant['sku'] = $tradegeckoVariant['sku'];
                    $itemVariant['description'] = $tradegeckoVariant['description'];
                    $itemVariant['supplierCode'] = $tradegeckoVariant['supplier_code'];
                    $itemVariant['upc'] = $tradegeckoVariant['upc'];
                    $itemVariant['buyPrice'] = $tradegeckoVariant['buy_price'];
                    $itemVariant['wholesalePrice'] = $tradegeckoVariant['wholesale_price'];
                    $itemVariant['retailPrice'] = $tradegeckoVariant['retail_price'];
                    $itemVariant['lastCostPrice'] = $tradegeckoVariant['last_cost_price'];
                    $itemVariant['movingAverageCost'] = $tradegeckoVariant['moving_average_cost'];
                    $itemVariant['availableStock'] = (float)$tradegeckoVariant['stock_on_hand'] + (float)$tradegeckoVariant['incoming_stock'] - (float)$tradegeckoVariant['committed_stock'];
                    $itemVariant['stockOnHand'] = $tradegeckoVariant['stock_on_hand'];
                    $itemVariant['committedStock'] = $tradegeckoVariant['committed_stock'];
                    $itemVariant['incomingStock'] = $tradegeckoVariant['incoming_stock'];
                    $itemVariant['opt1'] = $tradegeckoVariant['opt1'];
                    $itemVariant['opt2'] = $tradegeckoVariant['opt2'];
                    $itemVariant['opt3'] = $tradegeckoVariant['opt3'];

                    $itemVariant['status'] = $tradegeckoVariant['variant_status'];
                    if ($tradegeckoVariant['variant_status'] == 'deleted')
                        $itemVariant['status'] = 'inactive';
                    
                    $itemVariant['taxable'] = $tradegeckoVariant['taxable'] == 'true' ? 1 : 0;
                    $itemVariant['sellable'] = $tradegeckoVariant['sellable'] == 'true' ? 1 : 0;
                    $itemVariant['tradegeckoCreatedAt'] = $tradegeckoVariant['created_at'];
                    $itemVariant['tradegeckoUpdatedAt'] = $tradegeckoVariant['updated_at'];

                    $variantModel = ProductVariants::find()->where(['tradegeckoId' => $tradegeckoVariant['id']])->one();
                    if ($variantModel == null)
                        $variantModel = new ProductVariants();

                    $variantModel->attributes = $itemVariant;
                    if ($variantModel->save()) {
                        $storedArr['synced_variant'] ++;
                    }
                    $realArr['synced_variant'] ++;
                }

            }
            $realArr['synced_product'] ++;
        }

        self::logMessage($realArr['synced_product'].' Product Updates Found');
        self::logMessage($storedArr['synced_product'].' Product Updates were synced correctly');
        self::logMessage($realArr['synced_variant'].' Variant Updates Found');
        self::logMessage($storedArr['synced_variant'].' Variant Updates were synced correctly');
        self::logMessage($realArr['synced_image'].' Image Updates Found');
        self::logMessage($storedArr['synced_image'].' Image Updates were synced correctly');

        echo PHP_EOL."Type\t\tReal\tStored".PHP_EOL;
        echo "------------------------------".PHP_EOL;
        echo "Product\t\t".$realArr['synced_product']."\t".$storedArr['synced_product'].PHP_EOL;
        echo "Variant\t\t".$realArr['synced_variant']."\t".$storedArr['synced_variant'].PHP_EOL;
        echo "Image\t\t".$realArr['synced_image']."\t".$storedArr['synced_image'].PHP_EOL.PHP_EOL;

        self::logMessage('Syncing Inventory Done.');

        return 0;
    }

    public function getTradegeckoObject()
    {
        $obj = new Tradegecko;
        if ($obj->token == "")
        {
            echo 'Token was not specified in params.';
            self::logMessage('Token was not specified in params.');
            return null;
        }

        return $obj;
    }

    public function checkData($data, $label)
    {
        if (isset($data['type']))
        {
            echo 'Syncing '.$label.' : '.$data['message'].PHP_EOL;
            self::logMessage('Syncing '.$label.' : '.$data['message']);
            Yii::$app->end();
        }
        return 0;
    }
}

?>