<?php

namespace app\components;

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
        $noOfPages = ceil($data['meta']['total'] / $limit);

        $data['pagination'] = [
            'limit' => $limit,
            'page' => $page,
            'no_of_pages' => $noOfPages
        ];

        return $data;
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

        $product = $data['product'];

        $variant_ids = $product['variant_ids'];
        if (count($variant_ids)) {
            foreach ($variant_ids as $variant_id) {
                $variant = $this->getVariant($variant_id);
                $product['variants'][] = $variant;
            }
        }

        return $product;
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

        $tempVariants = [];
        foreach($data['variants'] as $variant)
            $tempVariants[$variant['id']] = $variant;

        $data['variants'] = $tempVariants;

        $noOfPages = ceil($data['meta']['total'] / $limit);
        $data['pagination'] = [
            'limit' => $limit,
            'page' => $page,
            'no_of_pages' => $noOfPages
        ];

        return $data;
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

        if(isset($data['variant'])) {
            return $data['variant'];
        } else {
            return null;
        }
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

        if(isset($data['company'])) {
            return $data['company'];
        } else {
            return null;
        }
    }

    /**
     * Retrieve a specific company by id
     * @param integer $companyId
     * @return null|array
     */
    public function getCompany($companyId) {
        $response = $this->sendRequest("companies/$companyId");
        $data = Json::decode($response);

        if(isset($data['company'])) {
            return $data['company'];
        } else {
            return null;
        }
    }

    /**
     * Retrieve a list of all companies with 'active' status.
     * @return null|array
     */
    public function getCompanies() {
        $response = $this->sendRequest('companies');
        $data = Json::decode($response);

        if(isset($data['companies'])) {
            return $data['companies'];
        } else {
            return null;
        }
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

        if(isset($data['address'])) {
            return $data['address'];
        } else {
            return null;
        }
    }

    /**
     * Retrieve a particular address
     * @param integer $addressId TradeGecko address id
     * @return null|array
     */
    public function getAddress($addressId) {
        $response = $this->sendRequest("addresses/$addressId");
        $data = Json::decode($response);

        if(isset($data['address'])) {
            return $data['address'];
        } else {
            return null;
        }
    }

    /**
     * Retrieves list of addresses for all companies or addresses for a certain company.
     * @return null|array
     */
    public function getAddresses() {
        $response = $this->sendRequest("addresses");
        $data = Json::decode($response);

        if(isset($data['addresses'])) {
            return $data['addresses'];
        } else {
            return null;
        }
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

        if(isset($data['order'])) {
            return $data['order'];
        } else {
            return null;
        }
    }

    /**
     * Retrieve a specific order
     * @param integer $orderId TradeGecko order id
     * @return null|array
     */
    public function getOrder($orderId) {
        $response = $this->sendRequest("orders/$orderId");
        $data = Json::decode($response);

        if(isset($data['order'])) {
            return $data['order'];
        } else {
            return null;
        }
    }

    /**
     * Retrieve list of orders
     * @return null|array
     */
    public function getOrders() {
        $response = $this->sendRequest("orders");
        $data = Json::decode($response);

        if(isset($data['orders'])) {
            return $data['orders'];
        } else {
            return null;
        }
    }

    /**
     * Retrieve list of payments
     * @return null|array
     */
    public function getPayments() {
        $response = $this->sendRequest("payments");
        $data = Json::decode($response);
        
        if(isset($data['payments'])) {
            return $data['payments'];
        } else {
            return null;
        }
    }

    /**
     * Retrieve a specific order line item
     * @param $orderLineItemId TradeGecko order line item id
     * @return null|array
     */
    public function getOrderLineItem($orderLineItemId) {
        $response = $this->sendRequest("order_line_items/$orderLineItemId");
        $data = Json::decode($response);

        if(isset($data['order_line_item'])) {
            return $data['order_line_item'];
        } else {
            return null;
        }
    }

    /**
     * Retrieve list of images
     * @return null|array
     */
    public function getImages() {
        $response = $this->sendRequest('images');
        $data = Json::decode($response);

        if(isset($data['images'])) {
            return $data['images'];
        } else {
            return null;
        }
    }

    /**
     * Retrieve a specific image
     * @param int $imageId TradeGecko image id
     * @return null|array
     */
    public function getImage($imageId) {
        $response = $this->sendRequest("images/{$imageId}");
        $data = Json::decode($response);

        if(isset($data['image'])) {
            return $data['image'];
        } else {
            return null;
        }
    }

    /**
     * Send request to TradeGecko API
     * @param string $object TradeGecko object
     * @param string $method http method
     * @param array $params  Object parameters if has any
     * @return mixed|string
     */
    protected function sendRequest($object, $method = "GET", $params = []) {

        $url = $this->url . $object;

        $header = [
            "Accept: application/vnd.tradegecko.v1+json",
            "Content-type: application/json"
        ];
        if($this->token != '') {
            $header[] = "Authorization: Bearer " . $this->token;
        }

        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        if($method == "POST") {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        } elseif($method == "PUT" || $method == "DELETE") {
            if(!empty($params)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            }
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }

        $response = curl_exec($ch);

        if(curl_errno($ch)) {
            $response = curl_error($ch);
        } else {
            curl_close($ch);
        }

        $data = Json::decode($response);
        if(isset($data['type']) && isset($data['message'])) {
            echo 'TradeGecko Error: ' . "\n";
            echo VarDumper::dumpAsString($data) . "\n";

            exit;
        }
        return $response;
    }
}