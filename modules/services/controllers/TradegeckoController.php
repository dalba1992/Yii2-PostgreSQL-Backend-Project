<?php

namespace app\modules\services\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

use app\modules\services\components\TradeGecko;

use app\modules\services\models\TradegeckoProduct;
use app\modules\services\models\TradegeckoProductSearch;
use app\modules\services\models\TradegeckoAddress;
use app\modules\services\models\TradegeckoCompany;
use app\modules\services\models\TradegeckoCompanySearch;
use app\modules\services\models\TradegeckoContact;
use app\modules\services\models\TradegeckoCurrency;
use app\modules\services\models\TradegeckoCurrencySearch;
use app\modules\services\models\TradegeckoFulfillment;
use app\modules\services\models\TradegeckoFulfillmentLineItem;
use app\modules\services\models\TradegeckoFulfillmentSearch;
use app\modules\services\models\TradegeckoInvoice;
use app\modules\services\models\TradegeckoInvoiceLineItem;
use app\modules\services\models\TradegeckoInvoiceSearch;
use app\modules\services\models\TradegeckoOrder;
use app\modules\services\models\TradegeckoOrderSearch;
use app\modules\services\models\TradegeckoOrderLineItem;
use app\modules\services\models\TradegeckoVariant;
use app\modules\services\models\TradegeckoVariantSearch;
use app\modules\services\models\TradegeckoVariantLocation;
use app\modules\services\models\TradegeckoVariantStockLevel;
use app\modules\services\models\TradegeckoVariantIncomingStockLevel;
use app\modules\services\models\TradegeckoVariantCommittedStockLevel;
use app\modules\services\models\TradegeckoLocation;
use app\modules\services\models\TradegeckoLocationSearch;
use app\modules\services\models\TradegeckoPaymentTerm;
use app\modules\services\models\TradegeckoPaymentTermSearch;
use app\modules\services\models\TradegeckoPriceList;
use app\modules\services\models\TradegeckoPriceListSearch;
use app\modules\services\models\TradegeckoUser;
use app\modules\services\models\TradegeckoUserSearch;
use app\modules\services\models\TradegeckoTaxType;
use app\modules\services\models\TradegeckoTaxTypeSearch;
use app\modules\services\models\TradegeckoPayment;
use app\modules\services\models\TradegeckoPaymentSearch;
use app\modules\services\models\TradegeckoImage;

use app\models\Products;
use app\models\ProductVariants;

class TradegeckoController extends \app\modules\services\components\AController
{
    /**
     * This function loads index page.
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * This function loads product data from a database and displays on inventory page
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionInventory()
    {
        $searchModel = new TradegeckoProductSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams)->dataProvider;
        $brandArray = TradegeckoProduct::find()->select(['brand'])->groupBy(['brand'])->all();
        $arr = [];
        foreach($brandArray as $brands)
            $arr[$brands['brand']] = $brands['brand'];

        return $this->render('inventory', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'status' => 'all',
            'brandArray' => $arr,
        ]);
    }

    /**
     * This function loads variant data of a product from a database and displays on inventoryview page
     *
     * @param integer $id id of a product
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionInventoryview($id)
    {
        $tradegeckoProduct = TradegeckoProduct::find()->where(['id' => $id])->one();
        if ($tradegeckoProduct != null)
        {
            $variantQuery = TradegeckoVariant::find()->joinWith(['tradegeckoImage'])->where(['product_id' => $id])->orderBy('id asc');
            $variants = $variantQuery->all();

            $variantDataProvider = new ActiveDataProvider([
                'query' => $variantQuery,
                'sort' => [
                    'defaultOrder' => [
                        'id' => SORT_DESC
                    ]
                ]
            ]);

            return $this->render('inventoryview', [
                'model' => $tradegeckoProduct,
                'variantDataProvider' => $variantDataProvider,
                'variants' => $variants,
            ]);
        }
        else
            throw new NotFoundHttpException('The requested product not exist.'); 
    }

    /**
     * This function deletes a product and its variants
     *
     * @param integer $id id of a product
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionInventorydelete($id)
    {
        $tradegeckoProduct = TradegeckoProduct::find()->where(['id' => $id])->one();
        if($tradegeckoProduct->delete()) {
            $variants = TradegeckoVariant::findAll(["product_id" => $id]);
            if(count($variants) > 0) {
                foreach($variants as $variant) {
                    $variantId = $variant['id'];
                    $variant->delete();

                    $variantLocations = TradegeckoVariantLocation::find()->where(['variant_id' => $variantId])->all();
                    if ($variantLocations != null)
                    {
                        foreach($variantLocations as $variantLocation)
                            $variantLocation->delete();
                    }

                    $variantStockLevels = TradegeckoVariantStockLevel::find()->where(['variant_id' => $variantId])->all();
                    if ($variantStockLevels != null)
                    {
                        foreach($variantStockLevels as $variantStockLevel)
                            $variantStockLevel->delete();
                    }

                    $variantIncomingStockLevels = TradegeckoVariantIncomingStockLevel::find()->where(['variant_id' => $variantId])->all();
                    if ($variantIncomingStockLevels != null)
                    {
                        foreach($variantIncomingStockLevels as $variantIncomingStockLevel)
                            $variantIncomingStockLevel->delete();
                    }

                    $variantCommittedStockLevels = TradegeckoVariantCommittedStockLevel::find()->where(['variant_id' => $variantId])->all();
                    if ($variantCommittedStockLevels != null)
                    {
                        foreach($variantCommittedStockLevels as $variantCommittedStockLevel)
                            $variantCommittedStockLevel->delete();
                    }
                }
            }
            $this->redirect('inventory');
        }
    }

    /**
     * This function deletes a variant
     *
     * @param integer $id id of a variant
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionVariantdelete($variantId)
    {
        $variant = TradegeckoVariant::find()->where(['id' => $variantId])->one();

        $productId = $variant['product_id'];
        $availableNum = $variant['stock_on_hand'] - $variant['committed_stock'];

        $variant->delete();

        $productModel = TradegeckoProduct::find()->where(['id' => $productId])->one();
        $productModel['quantity'] = intval($productModel['quantity']) - intval($availableNum);
        $productModel['variant_num'] = intval($productModel['variant_num']) - 1;
        $productModel->save();

        $variantLocations = TradegeckoVariantLocation::find()->where(['variant_id' => $variantId])->all();
        if ($variantLocations != null)
        {
            foreach($variantLocations as $variantLocation)
                $variantLocation->delete();
        }

        $variantStockLevels = TradegeckoVariantStockLevel::find()->where(['variant_id' => $variantId])->all();
        if ($variantStockLevels != null)
        {
            foreach($variantStockLevels as $variantStockLevel)
                $variantStockLevel->delete();
        }

        $variantIncomingStockLevels = TradegeckoVariantIncomingStockLevel::find()->where(['variant_id' => $variantId])->all();
        if ($variantIncomingStockLevels != null)
        {
            foreach($variantIncomingStockLevels as $variantIncomingStockLevel)
                $variantIncomingStockLevel->delete();
        }

        $variantCommittedStockLevels = TradegeckoVariantCommittedStockLevel::find()->where(['variant_id' => $variantId])->all();
        if ($variantCommittedStockLevels != null)
        {
            foreach($variantCommittedStockLevels as $variantCommittedStockLevel)
                $variantCommittedStockLevel->delete();
        }
        $this->redirect(['inventoryview', 'id' => $productId]);
    }

    /**
     * This function loads order data from a database and displays on order page
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionOrder()
    {
        $searchModel = new TradegeckoOrderSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams)->dataProvider;

        return $this->render('order', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * This function loads an order from a database and displays on orderview page
     *
     * @param integer $id id of an order
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionOrderview($id)
    {
        $tradegeckoOrder = TradegeckoOrder::find()->where(['id' => $id])->one();
        if ($tradegeckoOrder != null)
        {
            $orderData = [];
            $orderData = $tradegeckoOrder->attributes;

            $billTo = TradegeckoAddress::find()->where(['id' => $orderData['billing_address_id']])->one();
            $orderData['billTo'] = $billTo['label'];

            $shipTo = TradegeckoAddress::find()->where(['id' => $orderData['shipping_address_id']])->one();
            $orderData['shipTo'] = $shipTo['label'];

            $currency = TradegeckoCurrency::find()->where(['id' => $orderData['currency_id']])->one();
            $orderData['currency'] = $currency['currency_name'].' ('.$currency['iso'].')';
            $orderData['currencySymbol'] = $currency['symbol'];

            $shipFrom = TradegeckoLocation::find()->where(['id' => $orderData['stock_location_id']])->one();
            $orderData['shipFrom'] = $shipFrom['label'];

            $tradegeckoOrderLineItemQuery = tradegeckoOrderLineItem::find()->joinWith(['tradegeckoVariant'])->where(['order_id' => $id])->orderBy('position');

            $orderLineItemDataProvider = new ActiveDataProvider([
                'query' => $tradegeckoOrderLineItemQuery,
                'sort' => [
                    'defaultOrder' => [
                        'id' => SORT_DESC
                    ]
                ]
            ]);

            return $this->render('orderview', [
                'orderData' => $orderData,
                'model' => $tradegeckoOrder,
                'orderLineItemDataProvider' => $orderLineItemDataProvider
            ]);
        }
        else
            throw new NotFoundHttpException('The requested product not exist.'); 
    }

    /**
     * This function deletes an order and its line items
     *
     * @param integer $id id of an order
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionOrderdelete($id)
    {
        $tradegeckoOrder = TradegeckoOrder::find()->where(['id' => $id])->one();
        if($tradegeckoOrder->delete()) {
            $tradegeckoOrderLineItems = TradegeckoOrderLineItem::find()->where(['order_id' => $id])->all();
            if ($tradegeckoOrderLineItems != null)
            {
                foreach($tradegeckoOrderLineItems as $tradegeckoOrderLineItem)
                    $tradegeckoOrderLineItem->delete();
            }
            $this->redirect('order');
        }
    }

    /**
     * This function loads invoice data from a database and displays on invoice page
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionInvoice()
    {
        $searchModel = new TradegeckoInvoiceSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams)->dataProvider;

        return $this->render('invoice', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * This function loads an invoice from a database and displays on invoiceview page
     *
     * @param integer $id id of an invoice
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionInvoiceview($id)
    {
        $tradegeckoInvoice = TradegeckoInvoice::find()->where(['id' => $id])->one();
        if ($tradegeckoInvoice != null)
        {
            $invoiceData = [];
            $invoiceData = $tradegeckoInvoice->attributes;

            $billTo = TradegeckoAddress::find()->where(['id' => $invoiceData['billing_address_id']])->one();
            $invoiceData['billTo'] = $billTo['label'];

            $shipTo = TradegeckoAddress::find()->where(['id' => $invoiceData['shipping_address_id']])->one();
            $invoiceData['shipTo'] = $shipTo['label'];

            $issuedAt = TradegeckoOrder::find()->where(['id' => $invoiceData['order_id']])->one();
            $invoiceData['issuedAt'] = $issuedAt['issued_at'];

            $currency = TradegeckoCurrency::find()->where(['id' => $invoiceData['currency_id']])->one();
            $invoiceData['currencySymbol'] = $currency['symbol'];

            $orderData = TradegeckoOrder::find()->where(['id' => $invoiceData['order_id']])->one();
            $shipFrom = TradegeckoLocation::find()->where(['id' => $orderData['stock_location_id']])->one();
            $invoiceData['shipFrom'] = $shipFrom['label'];

            $tradegeckoInvoiceLineItemQuery = TradegeckoInvoiceLineItem::find()->joinWith(['tradegeckoOrderLineItem'])->where(['invoice_id' => $id])->orderBy('position');

            $invoiceLineItemDataProvider = new ActiveDataProvider([
                'query' => $tradegeckoInvoiceLineItemQuery,
                'sort' => [
                    'defaultOrder' => [
                        'id' => SORT_DESC
                    ]
                ]
            ]);

            return $this->render('invoiceview', [
                'invoiceData' => $invoiceData,
                'model' => $tradegeckoInvoice,
                'invoiceLineItemDataProvider' => $invoiceLineItemDataProvider
            ]);
        }
        else
            throw new NotFoundHttpException('The requested product not exist.'); 
    }

    /**
     * This function deletes an invoice and its line items
     *
     * @param integer $id id of an invoice
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionInvoicedelete($id)
    {
        $tradegeckoInvoice = TradegeckoInvoice::find()->where(['id' => $id])->one();
        if($tradegeckoInvoice->delete()) {
            $tradegeckoInvoiceLineItems = TradegeckoInvoiceLineItem::find()->where(['invoice_id' => $id])->all();
            if ($tradegeckoInvoiceLineItems != null)
            {
                foreach($tradegeckoInvoiceLineItems as $tradegeckoInvoiceLineItem)
                    $tradegeckoInvoiceLineItem->delete();
            }
            $this->redirect('invoice');
        }
    }

    /**
     * This function loads fulfillment data from a database and displays on fulfillment page
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionFulfillment()
    {
        $searchModel = new TradegeckoFulfillmentSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams)->dataProvider;

        return $this->render('fulfillment', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * This function loads a fulfillment from a database and displays on fulfillmentview page
     *
     * @param integer $id id of a fulfillment
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionFulfillmentview($id)
    {
        $tradegeckoFulfillment = TradegeckoFulfillment::find()->where(['id' => $id])->one();
        if ($tradegeckoFulfillment != null)
        {
            $fulfillmentData = [];
            $fulfillmentData = $tradegeckoFulfillment->attributes;

            $billTo = TradegeckoAddress::find()->where(['id' => $fulfillmentData['billing_address_id']])->one();
            $fulfillmentData['billTo'] = $billTo['label'];

            $shipTo = TradegeckoAddress::find()->where(['id' => $fulfillmentData['shipping_address_id']])->one();
            $fulfillmentData['shipTo'] = $shipTo['label'];

            $orderData = TradegeckoOrder::find()->where(['id' => $fulfillmentData['order_id']])->one();
            $currency = TradegeckoCurrency::find()->where(['id' => $orderData['currency_id']])->one();
            $fulfillmentData['currencySymbol'] = $currency['symbol'];

            $shipFrom = TradegeckoLocation::find()->where(['id' => $fulfillmentData['stock_location_id']])->one();
            $fulfillmentData['shipFrom'] = $shipFrom['label'];

            $tradegeckoFulfillmentLineItemQuery = TradegeckoFulfillmentLineItem::find()->joinWith(['tradegeckoOrderLineItem'])->where(['fulfillment_id' => $id])->orderBy('position');

            $fulfillmentLineItemDataProvider = new ActiveDataProvider([
                'query' => $tradegeckoFulfillmentLineItemQuery,
                'sort' => [
                    'defaultOrder' => [
                        'id' => SORT_DESC
                    ]
                ]
            ]);

            return $this->render('fulfillmentview', [
                'fulfillmentData' => $fulfillmentData,
                'model' => $tradegeckoFulfillment,
                'fulfillmentLineItemDataProvider' => $fulfillmentLineItemDataProvider
            ]);
        }
        else
            throw new NotFoundHttpException('The requested product not exist.'); 
    }

    /**
     * This function deletes a fulfillment and its line items
     *
     * @param integer $id id of a fulfillment
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionFulfillmentdelete($id)
    {
        $tradegeckoFulfillment = TradegeckoFulfillment::find()->where(['id' => $id])->one();
        if($tradegeckoFulfillment->delete()) {
            $tradegeckoFulfillmentLineItems = TradegeckoFulfillmentLineItem::find()->where(['fulfillment_id' => $id])->all();
            if ($tradegeckoFulfillmentLineItems != null)
            {
                foreach($tradegeckoFulfillmentLineItems as $tradegeckoFulfillmentLineItem)
                    $tradegeckoFulfillmentLineItem->delete();
            }
            $this->redirect('fulfillment');
        }
    }

    /**
     * This function loads company data from a database and shows on a company page
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCompany()
    {
        $searchModel = new TradegeckoCompanySearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams)->dataProvider;

        return $this->render('company', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * This function loads a company data from a database and displays on companyview page
     *
     * @param integer $id id of a company
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCompanyview($id)
    {
        $tradegeckoCompany = TradegeckoCompany::find()->where(['id' => $id])->one();
        if ($tradegeckoCompany != null)
        {
            $tradegeckoAddressQuery = TradegeckoAddress::find()->where(['company_id' => $id]);

            $addressDataProvider = new ActiveDataProvider([
                'query' => $tradegeckoAddressQuery,
                'sort' => [
                    'defaultOrder' => [
                        'id' => SORT_DESC
                    ]
                ]
            ]);

            $tradegeckoContactQuery = TradegeckoContact::find()->where(['company_id' => $id]);

            $contactDataProvider = new ActiveDataProvider([
                'query' => $tradegeckoContactQuery,
                'sort' => [
                    'defaultOrder' => [
                        'id' => SORT_DESC
                    ]
                ]
            ]);

            return $this->render('companyview', [
                'companyData' => $tradegeckoCompany->attributes,
                'addressDataProvider' => $addressDataProvider,
                'contactDataProvider' => $contactDataProvider,
            ]);
        }
        else
            throw new NotFoundHttpException('The requested product not exist.'); 
    }

    /**
     * This function deletes a company and its related data
     *
     * @param integer $id id of a company
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCompanydelete($id)
    {
        $tradegeckoCompany = TradegeckoCompany::find()->where(['id' => $id])->one();
        if($tradegeckoCompany->delete()) {
            $tradegeckoAddresses = TradegeckoAddress::find()->where(['company_id' => $id])->all();
            if ($tradegeckoAddresses != null)
            {
                foreach($tradegeckoAddresses as $tradegeckoAddress)
                    $tradegeckoAddress->delete();
            }

            $tradegeckoContacts = TradegeckoContact::find()->where(['company_id' => $id])->all();
            if ($tradegeckoContacts != null)
            {
                foreach($tradegeckoContacts as $tradegeckoContact)
                    $tradegeckoContact->delete();
            }
            $this->redirect('company');
        }
    }

    /**
     * This function loads currency, location, payment term, price list, tax type and user data from a database and shows on a data page
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionData()
    {
        $taxTypeSearchModel = new TradegeckoTaxTypeSearch();
        $taxTypeDataProvider = $taxTypeSearchModel->search(Yii::$app->request->queryParams)->dataProvider;

        $currencySearchModel = new TradegeckoCurrencySearch();
        $currencyDataProvider = $currencySearchModel->search(Yii::$app->request->queryParams)->dataProvider;

        $locationSearchModel = new TradegeckoLocationSearch();
        $locationDataProvider = $locationSearchModel->search(Yii::$app->request->queryParams)->dataProvider;

        $paymentTermSearchModel = new TradegeckoPaymentTermSearch();
        $paymentTermDataProvider = $paymentTermSearchModel->search(Yii::$app->request->queryParams)->dataProvider;

        $priceListSearchModel = new TradegeckoPriceListSearch();
        $priceListDataProvider = $priceListSearchModel->search(Yii::$app->request->queryParams)->dataProvider;

        $userSearchModel = new TradegeckoUserSearch();
        $userDataProvider = $userSearchModel->search(Yii::$app->request->queryParams)->dataProvider;

        return $this->render('data', [
            'taxTypeDataProvider' => $taxTypeDataProvider,
            'currencyDataProvider' => $currencyDataProvider,
            'locationDataProvider' => $locationDataProvider,
            'paymentTermDataProvider' => $paymentTermDataProvider,
            'priceListDataProvider' => $priceListDataProvider,
            'userDataProvider' => $userDataProvider,
        ]);
    }

    /**
     * This function updates a shop flag of an inventory
     *
     * @param integer $inventoryId the id of an inventory
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionUpdateshop($inventoryId)
    {
        $model = TradegeckoProduct::find()->where(['id' => $inventoryId])->one();
        if ($model->shop == '0' || $model->shop == NULL)
            $model->shop = '1';
        else
            $model->shop = '0';
        $model->save();

        $inven1 = Products::find()->where(['tradegeckoId' => $inventoryId])->one();
        if ($inven1 != null)
        {
            $inven1->shop = $model->shop;
            $inven1->save();
        }
    }

    /**
     * This function updates inventory data of an inventory from tradegecko
     *
     * @param integer $inventoryId the id of an inventory
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSyncOne($inventoryId)
    {
        $ret = [];

        set_time_limit(0);
        $shopVal = '0';
        $oldProduct = TradegeckoProduct::find()->where(['id' => $inventoryId])->one();
        if ($oldProduct != null)
        {
            $shopVal = $oldProduct->shop;
            $oldProduct->delete();
        }

        //PRODUCT UPDATING
        try{
            $obj = new TradeGecko();
            if ($obj->token == "")
            {
                $ret['code'] = '1';
                $ret['message'] = 'Token was not specified in params.';
                echo json_encode($ret);
                Yii::$app->end();
            }

            $product = $obj->getProduct($inventoryId);
            if (isset($product['type']))
            {
                $ret['code'] = '2';
                $ret['message'] = $product['message'];
                echo json_encode($ret);
                Yii::$app->end();
            }
        }
        catch(\Exception $ex)
        {
            $ret['code'] = '3';
            $ret['message'] = 'Syncing Product Failed.';
            echo json_encode($ret);
            Yii::$app->end();
        }

        if ($product != null)
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
            $itemProduct['shop'] = $shopVal;

            $modelProduct = TradegeckoProduct::find()->where(['id' => $product['id']])->one();
            if ($modelProduct == null)
                $modelProduct = new TradegeckoProduct();

            $modelProduct->attributes = $itemProduct;
            if ($modelProduct->save()) {
            }

            //VARIANT UPDATE
            $oldVariants = TradegeckoVariant::find()->where(['product_id' => $product['id']])->all();
            if ($oldVariants != null)
            {
                foreach($oldVariants as $old)
                    $old->delete();
            }
            foreach($product['variant_ids'] as $variantId)
            {
                try{
                    $obj1 = new TradeGecko();
                    if ($obj1->token == "")
                    {
                        $ret['code'] = '1';
                        $ret['message'] = 'Token was not specified in params.';
                        echo json_encode($ret);
                        Yii::$app->end();
                    }

                    $variant = $obj1->getVariant($variantId);
                    if (isset($variant['type']))
                    {
                        $ret['code'] = '2';
                        $ret['message'] = $variant['message'];
                        echo json_encode($ret);
                        Yii::$app->end();
                    }
                }
                catch(\Exception $ex)
                {
                    $ret['code'] = '3';
                    $ret['message'] = 'Syncing Variants Failed.';
                    echo json_encode($ret);
                    Yii::$app->end();
                }

                if ($variant != null)
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
                    $modelVariant->save();

                    // locations
                    foreach($variant['locations'] as $variantLocation)
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
                        $modelVariantLocation->save();
                    }

                    // stock levels
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
                        $modelVariantStockLevel->save();
                    }

                    // committed stock levels
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
                        $modelVariantCommittedStockLevel->save();
                    }

                    // incoming stock levels
                    if(isset($variant['incoming_stock_levels'])) {
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
                            $modelVariantIncomingStockLevel->save();
                        }
                    }
                }
            }
        }

        //IMAGE UPDATE
        try{
            $obj2 = new Tradegecko;
            if ($obj2->token == "")
            {
                $ret['code'] = '1';
                $ret['message'] = 'Token was not specified in params.';
                echo json_encode($ret);
                Yii::$app->end();
            }

            $images = $obj2->getImages();
            if (isset($images['type']))
            {
                $ret['code'] = '2';
                $ret['message'] = $images['message'];
                echo json_encode($ret);
                Yii::$app->end();
            }
        }
        catch(\Exception $ex)
        {
            $ret['code'] = '3';
            $ret['message'] = 'Syncing Images Failed.';
            echo json_encode($ret);
            Yii::$app->end();
        }

        foreach($images as $image)
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
            $modelImage->save();
        }

        //SYNCING INVENTORY2
        $tradegeckoProduct = TradegeckoProduct::find()->where(['id' => $inventoryId])->one();
        if ($tradegeckoProduct != null) 
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
                    $variantModel->save();
                }
            }
        }

        $ret['code'] = '0';
        $ret['message'] = 'Successfully Done.';
        echo json_encode($ret);
        Yii::$app->end();
    }

    /**
     * This function updates inventory data from tradegecko
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSyncAll()
    {
        $ret = [];
        set_time_limit(0);

        //PRODUCT UPDATING
        try{
            $obj = new TradeGecko();
            if ($obj->token == "")
            {
                $ret['code'] = '1';
                $ret['message'] = 'Token was not specified in params.';
                echo json_encode($ret);
                Yii::$app->end();
            }

            $products = $obj->getProducts();
            if (isset($products['type']))
            {
                $ret['code'] = '2';
                $ret['message'] = $products['message'];
                echo json_encode($ret);
                Yii::$app->end();
            }
        }
        catch(\Exception $ex)
        {
            $ret['code'] = '3';
            $ret['message'] = 'Syncing Products Failed.';
            echo json_encode($ret);
            Yii::$app->end();
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

                $modelProduct = TradegeckoProduct::find()->where(['id' => $product['id']])->one();
                if ($modelProduct == null)
                    $modelProduct = new TradegeckoProduct();

                $modelProduct->attributes = $itemProduct;
                $modelProduct->save();
            }

            // check if a product was deleted from TradeGecko
            $deletableProductIds = array_diff($localProductIds, $tradeGeckoProductIds);

            foreach($deletableProductIds as $productId) {
                $model = TradegeckoProduct::find()->where(['id' => $productId])->one();
                if($model) {
                    $model->attributes = [
                        'product_status' => 'deleted'
                    ];
                    $model->save();
                }
            }
        }

        //VARIANT UPDATE
        try{
            $obj = new Tradegecko;
            if ($obj->token == "")
            {
                $ret['code'] = '1';
                $ret['message'] = 'Token was not specified in params.';
                echo json_encode($ret);
                Yii::$app->end();
            }

            $variants = $obj->getVariants();
            if (isset($variants['type']))
            {
                $ret['code'] = '2';
                $ret['message'] = $variants['message'];
                echo json_encode($ret);
                Yii::$app->end();
            }
        }
        catch(\Exception $ex)
        {
            $ret['code'] = '3';
            $ret['message'] = 'Syncing Variants Failed.';
            echo json_encode($ret);
            Yii::$app->end();
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
                $modelVariant->save();

                // locations
                foreach($variant['locations'] as $variantLocation)
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
                    $modelVariantLocation->save();
                }

                // stock levels
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
                    $modelVariantStockLevel->save();
                }

                // committed stock levels
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
                    $modelVariantCommittedStockLevel->save();
                }

                // incoming stock levels
                if(isset($variant['incoming_stock_levels'])) {
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
                        $modelVariantIncomingStockLevel->save();
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
                    $model->save();
                }
            }
        }

        //IMAGE UPDATE
        try{
            $obj2 = new Tradegecko;
            if ($obj2->token == "")
            {
                $ret['code'] = '1';
                $ret['message'] = 'Token was not specified in params.';
                echo json_encode($ret);
                Yii::$app->end();
            }

            $images = $obj2->getImages();
            if (isset($images['type']))
            {
                $ret['code'] = '2';
                $ret['message'] = $images['message'];
                echo json_encode($ret);
                Yii::$app->end();
            }
        }
        catch(\Exception $ex)
        {
            $ret['code'] = '3';
            $ret['message'] = 'Syncing Images Failed.';
            echo json_encode($ret);
            Yii::$app->end();
        }

        foreach($images as $image)
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
            $modelImage->save();
        }

        //SYNCING WITH INVENTORY2
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
                    $variantModel->save();
                }
            }
        }

        $ret['code'] = '0';
        $ret['message'] = 'Successfully Done.';
        echo json_encode($ret);
        Yii::$app->end();
    }

    /**
     * List Inventory
     * @return mixed
     */
    public function actionList($status = null)
    {
        $searchModel = new TradegeckoProductSearch();

        if ($status == 'shop')
            $searchModel->shop = '1';
        else if ($status == 'concierge')
            $searchModel->shop = '0';

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams)->dataProvider;
        $brandArray = TradegeckoProduct::find()->select(['brand'])->groupBy(['brand'])->all();
        $arr = [];
        foreach($brandArray as $brands)
            $arr[$brands['brand']] = $brands['brand'];

        return $this->render('inventory', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'status' => $status,
            'brandArray' => $arr,
        ]);
    }
}
