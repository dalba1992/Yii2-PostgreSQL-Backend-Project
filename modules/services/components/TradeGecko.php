<?php

namespace app\modules\services\components;

use Yii;
use yii\base\Component;
use yii\data\ArrayDataProvider;
use yii\helpers\Json;
use yii\helpers\VarDumper;

class TradeGecko extends Component {

    /**
     * TradeGecko API Endpoint
     * @var string
     */
    public $url = 'https://api.tradegecko.com/';

    /**
     * TradeGecko application id
     * @var string
     */
    public $clientId = '';

    /**
     * TradeGecko application secret
     * @var string
     */
    public $clientSecret = '';

    /**
     * The user will be redirected to this url with authorization code
     * as a query parameter after the application is successfully authorized
     * @var string
     */
    public $redirectUri = '';

    /**
     * Is the key used to retrieve the access token.
     * @var string
     */
    public $code = '';

    /**
     * The access token is a unique key used to make requests to the API
     * @var string
     */
    public $token = '';

    static public $variantQueryLimit = 5000;

    public function __construct($config = array()) {
        try{
            $this->token = Yii::$app->params['tradegecko']['token'];
        }catch(\Exception $ex){
            $this->token = '';
        }

        parent::__construct($config);
        $this->init();
    }

    public function init() {

    }

    /**
     * Get all products from TradeGecko
     * @param int $limit number of items displayed on page
     * @param int $page current page number
     * @return array
     */
    public function getProducts($limit = 500, $page = 0) {

        $response = $this->sendRequest("products?limit=$limit&page=$page");
        $data = Json::decode($response, true);
        return $this->retData($data, 'products');
    }

    /**
     * Returns a data provider with all products from TradeGecko
     * @param int $pageSize number of items displayed on page
     * @param array $sortAttributes sort attributes
     * @return ArrayDataProvider
     */
    public function getProductsDataProvider($pageSize = 10, $sortAttributes = []) {

        $data = $this->getProducts();
        $completed = false;
        $products = [];

        if(isset($data['products']) && count($data['products'])) {
            for($pageNo = 1; $pageNo <= $data['pagination']['no_of_pages']; $pageNo++) {
                $data = $this->getProducts($data['pagination']['limit'], $pageNo);
                if(count($data['products'])) {
                    foreach($data['products'] as $product) {
                        $products[] = $product;
                    }
                }
                if($pageNo == $data['pagination']['no_of_pages']) $completed = true;
            }
        }

        if($completed) {
            $config = [
                'allModels' => $products,
                'pagination' => [
                    'totalCount' => count($products),
                    'pageSize' => $pageSize,
                ]
            ];
            if (!empty($sortAttributes)) {
                $config['sort'] = [
                    'attributes' => $sortAttributes
                ];
            }

            return new ArrayDataProvider($config);
        }
    }


    /**
     * Get a product from TradeGecko API by id
     * @param $productId Product id
     * @return array
     */
    public function getProduct($productId) {
        $response = $this->sendRequest("products/{$productId}");
        $data = Json::decode($response);
        return $this->retData($data, 'product');
    }

    /**
     * Get all variants from TradeGecko API
     * @param int $limit number of items displayed
     * @param int $page current page
     * @return mixed
     */
    public function getVariants($limit = 5000, $page = 0) {
        $response = $this->sendRequest("variants?limit=$limit&page=$page");
        $data = Json::decode($response);
        return $this->retData($data, 'variants');
    }

    /**
     * Returns a data provider with all variants from TradeGecko
     * @param int $pageSize number of items displayed on page
     * @param array $sortAttributes sort attributes
     * @return ArrayDataProvider
     */
    public function getVariantsDataProvider($raw = false, $pageSize = 10, $sortAttributes = []) {
        $currentPage = 0;
        $limit = self::$variantQueryLimit;

        // get the page 0 of variants
        $data[$currentPage] = $this->getVariants($limit, $currentPage);
        $totalPages = $data[$currentPage]['pagination']['no_of_pages'];
        $variants = $data[$currentPage]['variants'];

        $currentPage++;

        $completed = false;


        while($currentPage < ($totalPages - 1)){
            if(!empty($data[$currentPage]['variants'])) {
                $variants = \yii\helpers\ArrayHelper::merge($variants, $data[$currentPage]['variants']);
            }
            $currentPage++;
            if($currentPage <= $totalPages) {
                $data[$currentPage] = $this->getVariants($limit, $currentPage);
            }
        }

        if($raw) // return just the raw array of variants, no data provider
            return $variants;
        else{   // return the variants nicely wrapped inside an array data provider
            $config = [
                'allModels' => $variants,
                'pagination' => [
                    'totalCount' => count($variants),
                    'pageSize' => $pageSize,
                ]
            ];

            if (!empty($sortAttributes)) {
                $config['sort'] = [
                    'attributes' => $sortAttributes
                ];
            }

            return new ArrayDataProvider($config);
        }
    }

    /**
     * Get a variant from TradeGecko by id
     * @param $variantId
     * @return array|null
     */
    public function getVariant($variantId) {
        $response = $this->sendRequest("variants/{$variantId}");
        $data = Json::decode($response);
        return $this->retData($data, 'variant');
    }

    /**
     * Create a new company
     * array['fields']
     *          ['fieldName']
     *              ['name'] string required
     *              ['company_type'] string required. Can be 'business', 'supplier' or 'consumer'.
     *              ['email'] string optional
     *              ['phone_number'] string optional
     *              ['website'] string optional
     *              ['online_id'] string optional
     *              ['company_code'] string optional
     *              ['fax'] string optional
     *              ['default_tax_rate'] string optional
     *              ['default_discount_rate'] string optional
     *              ['default_price_list_id'] string optional
     *              ['default_payment_term_id'] string optional
     *              ['default_stock_location_id'] string optional
     * @param array $params
     * @return null|array
     */
    public function createCompany($params) {
        $response = $this->sendRequest('companies', 'POST', $params);
        $data = Json::decode($response);
        return $this->retData($data, 'company');
    }

    /**
     * Retrieve a specific company by id
     * @param integer $companyId
     * @return null|array
     */
    public function getCompany($companyId) {
        $response = $this->sendRequest("companies/$companyId");
        $data = Json::decode($response);
        return $this->retData($data, 'company');
    }

    /**
     * Retrieve a list of all companies with 'active' status.
     * @return null|array
     */
    public function getCompanies() {
        $response = $this->sendRequest('companies');
        $data = Json::decode($response);
        return $this->retData($data, 'companies');
    }

    /**
     * Create a new address
     * array['fields']
     *          ['fieldName']
     *              ['company_id'] integer required. The TradeGecko ID of the company to which the address belongs.
     *              ['label'] string required. A unique (per company) label for the address e.g. Shipping
     *              ['company_name'] string optional. The name of the company or contact of the recipient.
     *              ['address1'] string required.
     *              ['address2'] string optional.
     *              ['city'] string optional.
     *              ['country'] string optional.
     *              ['zip_code'] string optional.
     *              ['suburb'] string optional.
     *              ['state'] string optional.
     *              ['phone_number'] string optional.
     *              ['email'] string optional
     * @param array $params
     * @return null|array
     */
    public function createAddress($params) {
        $response = $this->sendRequest('addresses', 'POST', $params);
        $data = Json::decode($response);
        return $this->retData($data, 'address');
    }

    /**
     * Retrieve a particular address
     * @param integer $addressId TradeGecko address id
     * @return null|array
     */
    public function getAddress($addressId) {
        $response = $this->sendRequest("addresses/$addressId");
        $data = Json::decode($response);
        return $this->retData($data, 'address');
    }

    /**
     * Retrieves list of addresses for all companies or addresses for a certain company.
     * @return null|array
     */
    public function getAddresses() {
        $response = $this->sendRequest("addresses");
        $data = Json::decode($response);
        return $this->retData($data, 'addresses');
    }

    /**
     * Create a new order
     * array['fields']
     *          ['fieldName']
     *              ['billing_address_id'] integer required. The id of the address of the company to be used for billing.
     *              ['shipping_address_id'] integer required. The id of the address of the company to be used for shipping.
     *              ['company_id'] integer required. The id of the company the order is for
     *              ['email'] string required. Company email address
     *              ['issued_at'] date required. Invoice issue date. Format: yyyy-mm-dd
     *              ['tax_type'] string required. Whether the order should include tax. Must be one of "inclusive" or "exclusive".
     *              ['order_line_items'] array optional. You can optionally include the initial order line items when creating an order
     *                      ['variant_id'] integer required.
     *                      ['quantity'] integer required.
     *                      ['price'] decimal required. i.e. 129.99
     *                      ['discount'] integer optional.
     *                      ['tax_rate_override'] integer optional.
     *                      ['freeform'] integer optional.
     *              ['order_number'] string optional. Supply a specific order_number, leave this blank and we will assign the next available order number
     *              ['ship_at'] date optional. Format: yyyy-mm-dd
     *              ['fulfillment_status'] string optional. Must be one of "shipped", "partial" or "unshipped".
     *              ['notes'] string optional.
     *              ['status'] string optional. Must be one of "active", "draft" when creating an invoice
     *              ['payment_status'] string optional. Must be one of "paid" or "unpaid".
     *              ['phone_number'] string optional.
     *              ['reference_number'] string optional.
     *              ['source'] string optional. The name of your integration. i.e. "magikart"
     *              ['stock_location_id'] integer optional. The id of the Account location to which the stock should come from. Leave blank if not using multiple locations.
     *              ['source_url'] string optional. URL link to a source location. i.e. "https://open.magikart.com/orders/12345"
     * @param array $params
     * @return null|array
     */
    public function createOrder($params) {
        $response = $this->sendRequest('orders', 'POST', $params);
        $data = Json::decode($response);
        return $this->retData($data, 'order');
    }

    /**
     * Retrieve a specific order
     * @param integer $orderId TradeGecko order id
     * @return null|array
     */
    public function getOrder($orderId) {
        $response = $this->sendRequest("orders/$orderId");
        $data = Json::decode($response);
        return $this->retData($data, 'order');
    }

    /**
     * Retrieve list of orders
     * @return null|array
     */
    public function getOrders() {
        $response = $this->sendRequest("orders");
        $data = Json::decode($response);
        return $this->retData($data, 'orders');
    }

    /**
     * Retrieve a specific payment by id
     * @param integer $paymentId
     * @return null|array
     */
    public function getPayment($paymentId) {
        $response = $this->sendRequest("payments/$paymentId");
        $data = Json::decode($response);
        return $this->retData($data, 'payment');
    }

    /**
     * Retrieve list of payments
     * @return null|array
     */
    public function getPayments() {
        $response = $this->sendRequest("payments");
        $data = Json::decode($response);
        return $this->retData($data, 'payments');
    }

    /**
     * Retrieve a specific order line item
     * @param $orderLineItemId TradeGecko order line item id
     * @return null|array
     */
    public function getOrderLineItem($orderLineItemId) {
        $response = $this->sendRequest("order_line_items/$orderLineItemId");
        $data = Json::decode($response);
        return $this->retData($data, 'order_line_item');
    }

    /**
     * Retrieve a specific user by id
     * @param integer $userId
     * @return null|array
     */
    public function getUser($userId) {
        $response = $this->sendRequest("users/$userId");
        $data = Json::decode($response);
        return $this->retData($data, 'user');
    }

    /**
     * Retrieve list of users
     * @return null|array
     */
    public function getUsers() {
        $response = $this->sendRequest("users");
        $data = Json::decode($response);
        return $this->retData($data, 'users');
    }

    /**
     * Retrieve a specific invoice by id
     * @param integer $invoiceId
     * @return null|array
     */
    public function getInvoice($invoiceId) {
        $response = $this->sendRequest("invoices/$invoiceId");
        $data = Json::decode($response);
        return $this->retData($data, 'invoice');
    }

    /**
     * Retrieve list of invoices
     * @return null|array
     */
    public function getInvoices() {
        $response = $this->sendRequest("invoices");
        $data = Json::decode($response);
        return $this->retData($data, 'invoices');
    }

    /**
     * Retrieve a specific contact by id
     * @param integer $contactId
     * @return null|array
     */
    public function getContact($contactId) {
        $response = $this->sendRequest("contacts/$contactId");
        $data = Json::decode($response);
        return $this->retData($data, 'contact');
    }

    /**
     * Retrieve list of contacts
     * @return null|array
     */
    public function getContacts() {
        $response = $this->sendRequest("contacts");
        $data = Json::decode($response);
        return $this->retData($data, 'contacts');
    }

    /**
     * Retrieve a specific currency by id
     * @param integer $currencyId
     * @return null|array
     */
    public function getCurrency($currencyId) {
        $response = $this->sendRequest("currencies/$currencyId");
        $data = Json::decode($response);
        return $this->retData($data, 'currency');
    }

    /**
     * Retrieve a list of all currencies
     * @return null|array
     */
    public function getCurrencies() {
        $response = $this->sendRequest('currencies');
        $data = Json::decode($response);
        return $this->retData($data, 'currencies');
    }

    /**
     * Retrieve a specific fulfillment by id
     * @param integer $fulfillmentId
     * @return null|array
     */
    public function getFulfillment($fulfillmentId) {
        $response = $this->sendRequest("fulfillments/$fulfillmentId");
        $data = Json::decode($response);
        return $this->retData($data, 'fulfillment');
    }

    /**
     * Retrieve a list of all fulfillments
     * @return null|array
     */
    public function getFulfillments() {
        $response = $this->sendRequest('fulfillments');
        $data = Json::decode($response);
        return $this->retData($data, 'fulfillments');
    }

    /**
     * Retrieve a specific fulfillment line item by fulfillment line item id
     * @param integer $fulfillmentLineItemId
     * @return null|array
     */
    public function getFulfillmentLineItem($fulfillmentLineItemId) {
        $response = $this->sendRequest("fulfillment_line_items/$fulfillmentLineItemId");
        $data = Json::decode($response);
        return $this->retData($data, 'fulfillment_line_item');
    }

    /**
     * Retrieve a specific invoice line item by invoice line item id
     * @param integer $invoiceLineItemId
     * @return null|array
     */
    public function getInvoiceLineItem($invoiceLineItemId) {
        $response = $this->sendRequest("invoice_line_items/$invoiceLineItemId");
        $data = Json::decode($response);
        return $this->retData($data, 'invoice_line_item');
    }

    /**
     * Retrieve a specific location by id
     * @param integer $locationId
     * @return null|array
     */
    public function getLocation($locationId) {
        $response = $this->sendRequest("locations/$locationId");
        $data = Json::decode($response);
        return $this->retData($data, 'location');
    }

    /**
     * Retrieve a list of all locations
     * @return null|array
     */
    public function getLocations() {
        $response = $this->sendRequest('locations');
        $data = Json::decode($response);
        return $this->retData($data, 'locations');
    }

    /**
     * Retrieve a specific payment term by id
     * @param integer $paymentTermId
     * @return null|array
     */
    public function getPaymentTerm($paymentTermId) {
        $response = $this->sendRequest("payment_terms/$paymentTermId");
        $data = Json::decode($response);
        return $this->retData($data, 'payment_term');
    }

    /**
     * Retrieve a list of all locations
     * @return null|array
     */
    public function getPaymentTerms() {
        $response = $this->sendRequest('payment_terms');
        $data = Json::decode($response);
        return $this->retData($data, 'payment_terms');
    }

    /**
     * Retrieve a specific price list by id
     * @param integer $priceListId
     * @return null|array
     */
    public function getPriceList($priceListId) {
        $response = $this->sendRequest("price_lists/$priceListId");
        $data = Json::decode($response);
        return $this->retData($data, 'price_list');
    }

    /**
     * Retrieve a list of all price lists
     * @return null|array
     */
    public function getPriceLists() {
        $response = $this->sendRequest('price_lists');
        $data = Json::decode($response);
        return $this->retData($data, 'price_lists');
    }

    /**
     * Retrieve a specific procurement by id
     * @param integer $procurementId
     * @return null|array
     */
    public function getProcurement($procurementId) {
        $response = $this->sendRequest("procurements/$procurementId");
        $data = Json::decode($response);
        return $this->retData($data, 'procurement');
    }

    /**
     * Retrieve a list of all procurements
     * @return null|array
     */
    public function getProcurements() {
        $response = $this->sendRequest('procurements');
        $data = Json::decode($response);
        return $this->retData($data, 'procurements');
    }

    /**
     * Retrieve a specific purchase order by id
     * @param integer $purchaseOrderId
     * @return null|array
     */
    public function getPurchaseOrder($purchaseOrderId) {
        $response = $this->sendRequest("purchase_orders/$purchaseOrderId");
        $data = Json::decode($response);
        return $this->retData($data, 'purchase_order');
    }

    /**
     * Retrieve a list of all purchase orders
     * @return null|array
     */
    public function getPurchaseOrders() {
        $response = $this->sendRequest('purchase_orders');
        $data = Json::decode($response);
        return $this->retData($data, 'purchase_orders');
    }

    /**
     * Retrieve a specific purchase order line item by id
     * @param integer $purchaseOrderLineItemId
     * @return null|array
     */
    public function getPurchaseOrderLineItem($purchaseOrderLineItemId) {
        $response = $this->sendRequest("purchase_order_line_items/$purchaseOrderLineItemId");
        $data = Json::decode($response);
        return $this->retData($data, 'purchase_order_line_item');
    }

    /**
     * Retrieve a list of all purchase order line items
     * @return null|array
     */
    public function getPurchaseOrderLineItems() {
        $response = $this->sendRequest('purchase_order_line_items');
        $data = Json::decode($response);
        return $this->retData($data, 'purchase_order_line_items');
    }

    /**
     * Retrieve a specific tax component by id
     * @param integer $purchaseOrderId
     * @return null|array
     */
    public function getTaxComponent($taxComponentId) {
        $response = $this->sendRequest("tax_components/$taxComponentId");
        $data = Json::decode($response);
        return $this->retData($data, 'tax_component');
    }

    /**
     * Retrieve a list of all tax components
     * @return null|array
     */
    public function getTaxComponents() {
        $response = $this->sendRequest('tax_components');
        $data = Json::decode($response);
        return $this->retData($data, 'tax_components');
    }

    /**
     * Retrieve a specific tax type by id
     * @param integer $taxTypeId
     * @return null|array
     */
    public function getTaxType($taxTypeId) {
        $response = $this->sendRequest("tax_types/$taxTypeId");
        $data = Json::decode($response);
        return $this->retData($data, 'tax_type');
    }

    /**
     * Retrieve a list of all tax types
     * @return null|array
     */
    public function getTaxTypes() {
        $response = $this->sendRequest('tax_types');
        $data = Json::decode($response);
        return $this->retData($data, 'tax_types');
    }

    /**
     * Retrieve a specific image by id
     * @param integer $imageId
     * @return null|array
     */
    public function getImage($imageId) {
        $response = $this->sendRequest("images/$imageId");
        $data = Json::decode($response);
        return $this->retData($data, 'image');
    }

    /**
     * Retrieve list of images
     * @return null|array
     */
    public function getImages() {
        $response = $this->sendRequest("images");
        $data = Json::decode($response);
        return $this->retData($data, 'images');
    }

    /**
     * Send request to TradeGecko API
     * @param string $object TradeGecko object
     * @param string $method http method
     * @param array $params  Object parameters if has any
     * @return mixed|string
     */

    protected function sendRequest($object, $method = "GET", $params = [])
    {
        $url = $this->url . $object;
            
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
        ));

        $header = [
            "Accept: application/vnd.tradegecko.v1+json",
            "Content-type: application/json",
            "Authorization: Bearer ".$this->token,
        ];
        
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        if($method == "POST") {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        } elseif($method == "PUT" || $method == "DELETE") {
            if(!empty($params)) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
            }
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        }

        $data = curl_exec($curl);
        curl_close($curl);

        return $data;
    }

    protected function retData($data, $key)
    {
        if (isset($data['type']))
            return $data;
        else
        {
            if(isset($data[$key])) {
                return $data[$key];
            } else {
                return null;
            }
        }
    }
}