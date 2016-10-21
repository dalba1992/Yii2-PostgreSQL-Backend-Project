<?php

namespace app\components;
use Yii;
use app\models\CustomerEntityStatus;
use yii\base\Component;

use app\models\CustomerEntity;
use app\models\CustomerSubscription;
use app\models\CustomerEntityAddress;
use app\models\Product;
use app\models\Brand;
use app\models\Category;
use app\models\Productattribute;
use app\models\Productattributeoption;
use app\models\Productattributevalue;
use app\models\Variants;
use app\models\Variantsimage;

class TradeGeckoHelper extends Component
{
    const TG_API_COMPANIES_URL = 'https://api.tradegecko.com/companies';
    const TG_API_ADDRESSES_URL = 'https://api.tradegecko.com/addresses';
    const TG_API_VARIANTS_URL = 'https://api.tradegecko.com/variants';
    const TG_API_ORDERS_URL = 'https://api.tradegecko.com/orders';

    /**
     * @param $id
     * @return mixed|string
     */
    public function zipcode($id){

        $query=CustomerEntityAddress::find()->where(['customer_id'=>$id,'type_id'=>2])->one();
        if(!is_object($query))
            return '(not set)';

        return $query->zipcode;

    }

    /**
     * @param $id
     * @return string
     */
    public function arbid($id){
        $query = (new \yii\db\Query());
        $query->select('stripe_customer_id')->from('customer_subscription')->where('customer_id=:idd', [':idd' => $id])->orderBy(['id' => SORT_DESC])->limit(1);
        $command = $query->createCommand();
        $rows = $command->queryAll();

        $var = isset($rows[0]['stripe_customer_id']) ? $rows[0]['stripe_customer_id']: "NULL";

        return $var;
    }

    /**
     * @param integer $rowId
     * @param integer $customerId
     * @param array $products
     * @return array
     */
    public function tradegeckoordercreate($rowId, $customerId=0, $products=array()) {
        $tradeGeckoProducts = array();
        $companyId = 0;

        if($customerId == 0)
            return array('error'=>true,'errmsg'=>'No customer ID provided for this customer. Customer Id : '.$customerId);

        if(Yii::$app->params['tgDevMode'] && $customerId == 58677) $customerId = 37;//@todo remove line
        $customerStatus = CustomerSubscription::find()->where(['customer_id' => $customerId])->orderBy(['id'=>SORT_DESC])->one();
        if(!$customerStatus)
            return array('error'=>true,'errmsg'=>'No subscription found for this user!');
        if(Yii::$app->params['tgDevMode'] && $customerId == 37) $customerId = 58677;//@todo remove line

        $issued_date = date('M d Y',strtotime($customerStatus->created_at));

        $ship_date = strtotime($issued_date);
        $ship_date = strtotime("+7 day", $ship_date);
        $ship_date = date('M d Y', $ship_date);

        $data = '';
        $accesstoken = "Bearer ".Yii::$app->params['tradegecko']['token'];

        $header = array();
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization: '.$accesstoken;

        $url = self::TG_API_COMPANIES_URL.'?company_code='.$customerId;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $ResponseCompany = curl_exec($ch);
        curl_close($ch);
        $ResponseCompany = json_decode($ResponseCompany, TRUE);

        if(!isset($ResponseCompany['companies']) || !isset($ResponseCompany['companies'][0]))
            return array('error'=>true,'errmsg'=>'No company(company code) on tradegecko exist for customer Id : '.$customerId);

        $companyId = $ResponseCompany['companies'][0]['id'];
        if($companyId==0)
            return array('error'=>true,'errmsg'=>'No company ID exist for customer - '.$customerId);

        $url = self::TG_API_ADDRESSES_URL.'?company_id='.$companyId;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $ResponseAddress = curl_exec($ch);
        curl_close($ch);
        $ResponseAddress = json_decode($ResponseAddress, TRUE);

        if(!isset($ResponseAddress['addresses']) || !isset($ResponseAddress['addresses'][0]))
            return array('error'=>true,'errmsg'=>'No address exist for customer Id : '.$customerId);

        $shipping_address_id = $ResponseAddress['addresses'][0]['id'];
        $billing_address_id = $ResponseAddress['addresses'][0]['id'];
        $email_id = $ResponseAddress['addresses'][0]['email'];

        foreach($ResponseAddress['addresses'] as $key => $address){
            if($address['label']=='Shipping')
                $shipping_address_id = $address['id'];

            if($address['label']=='Billing')
                $billing_address_id = $address['id'];
        }

        if(count($products)<=0)
            return array('error'=>true,'errmsg'=>'No products exist to place order for customer Id : '.$customerId);

        $order_line_items = array();
        $variant_ids = array();

        foreach($products as $keyprod => $valproduct){
            $sku_id = $valproduct['sku_id'];

            $url = self::TG_API_VARIANTS_URL.'?sku='.$sku_id;
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $ResponseVariant = curl_exec($ch);
            curl_close($ch);
            $ResponseVariant = json_decode($ResponseVariant, TRUE);

            if(!$ResponseVariant  || !is_array($ResponseVariant) || !isset($ResponseVariant['variants']))
                continue;

            if(!isset($ResponseVariant['variants'][0]))
                return array('error'=>true,'errmsg'=>'Please Check the Variant on TradeGecko, not found for the SKU Id : '.$sku_id);

            $variant = $ResponseVariant['variants'][0];
            $variant_id = $variant['id'];

            if(!in_array($variant_id,$variant_ids))
                $variant_ids[] = $variant_id;
            else
                continue;

            $tradeGeckoProducts[] = array('sku_id'=>$sku_id);

            if($ResponseAddress['addresses'][0]['state']=='CA')
                $order_line_items[] = array('quantity'=>1,'price'=>$variant['retail_price'], 'variant_id'=>$variant['id'], "tax_rate_override"=> "9.0");
            else
                $order_line_items[] = array('quantity'=>1,'price'=>$variant['retail_price'], 'variant_id'=>$variant['id']);
        }

        if($ResponseAddress['addresses'][0]['state']=='CA' || $ResponseAddress['addresses'][0]['state']=='AZ' || $ResponseAddress['addresses'][0]['state']=='NV' || $ResponseAddress['addresses'][0]['state']=='OR')
            $order_line_items[] = array('quantity'=>1,'price'=>8.5, 'label'=>'Shipping', 'freeform'=>true);
        else
            $order_line_items[] = array('quantity'=>1,'price'=>13.5, 'label'=>'Shipping', 'freeform'=>true);

        $req = array('order' => array(
            'company_id' => $companyId,
            'billing_address_id' => $billing_address_id,
            'shipping_address_id' => $shipping_address_id,
            'email'=> $email_id,
            'issued_at' => $issued_date,
            'ship_at' => $ship_date,
            'tax_type' => 'exclusive',
            'tax_label'  => 'SALES',
            'payment_status' => 'unpaid',
            'status'=> 'active',
            'order_line_items' => $order_line_items,
        ));

        $req = json_encode($req);

        $url = self::TG_API_ORDERS_URL;
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$req);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $Response = curl_exec($ch);
        curl_close($ch);

        $Response = json_decode($Response);

        if(isset($Response->order->id))
            return array('error'=>false,'order_id'=>$Response->order->id,'order_number'=>$Response->order->order_number,'productsTradegecko'=>$tradeGeckoProducts);

        return array('error'=>true,'errmsg'=>'Order not created for the customer - '.$customerId);
    }

    public function getTradegeckoProduct(){
        # ------------------------------------------------------------------------------------------------
        # TradeGeko  API
        # ------------------------------------------------------------------------------------------------

        $accesstoken = "Bearer ".$this->Randomtradegeckoapi();
        # ------------------------------------------------------------------------------------------------
        # Create Request Header
        # ------------------------------------------------------------------------------------------------
        $header = array();
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization: '.$accesstoken;
        # ------------------------------------------------------------------------------------------------
        # Initiate cURL, Set Option for URL, Header, Return data and then close cUrl
        # ------------------------------------------------------------------------------------------------
        $url = "https://api.tradegecko.com/products?limit=10000";
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $Response = curl_exec($ch);
        curl_close($ch);
        $ProductResponse = json_decode($Response);
        //print_r($ProductResponse);die;
        $products = array();
        if(isset($ProductResponse->products) && sizeof($ProductResponse->products)>0){
            $count=0;
            foreach($ProductResponse->products as $product){
                $url = "https://api.tradegecko.com/variants/?product_id=".$product->id;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$url);
                curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                $variant_detail = curl_exec($ch);
                curl_close($ch);
                $variant_detail = json_decode($variant_detail,TRUE);
                $Allvariants = array();
                if(isset($variant_detail['variants'])){
                    $variant_detail = $variant_detail['variants'];
                    if(sizeof($variant_detail)>0){
                        foreach($variant_detail as $key => $variant_info){
                            $size_color = 'No Size|Color Found!';
                            $image_id = '';
                            if(isset($variant_info['opt1']) || isset($variant_info['opt2'])){
                                $size_color = $variant_info['opt1'].'/'.$variant_info['opt2'];
                            }
                            $sku = $variant_info['sku'];
                            $retail_price = $variant_info['retail_price'];
                            $variant_id = $variant_info['id'];
                            if(isset($variant_info['image_ids'][0])){
                                $image_id = $variant_info['image_ids'][0];
                            }
                            $image_url = Yii::$app->request->BaseUrl.'/web/img/noimage.gif';
                            $url = "https://api.tradegecko.com/images/".$image_id;
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL,$url);
                            curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                            $variant_image = curl_exec($ch);
                            curl_close($ch);
                            $variant_image = json_decode($variant_image,TRUE);

                            if(isset($variant_image['images']) && sizeof($variant_image['images'])>0 && isset($variant_image['images']['base_path'])){
                                $image_url = $variant_image['images']['base_path'].'/'.$variant_image['images']['file_name'];
                            }

                            $Allvariants[] =  array('variant_id'=> $variant_id,
                                'sku'=> $sku,
                                'retail_price'=> $retail_price,
                                'size_color'=> $size_color,
                                'image_id' => $image_id,
                                'image_url' => $image_url
                            );
                        }
                    }
                }
                $product_image = $product->image_url;
                if($product_image == ''){
                    $product_image = Yii::$app->request->BaseUrl.'/web/img/noimage.gif';
                }
                $products[] = array('id'=>$product->id,
                    'name'=>$product->name,
                    'category'=>$product->product_type,
                    'image_url'=>$product_image,
                    'variant_detail'=>$Allvariants
                );

            }
        }
        //die;
        return $products;
    }

    /**
     * @return array
     */
    public function Randomtradegeckoapi(){
        $random = array('236c41d1c88f9d4a1b814c1db9669c8294c43db944f49af7de3bee0b9a25581f',
            'c98c9e5098a57d4601a24fec718cd8967586b11f03e31fd0e7835876c0bdc737',
            '0e2aeba42311646706550a622ba8327dee6bf1d18b30743aa2a9c94664bb7fb5',
            '2d6d22d28589159055f5f53beeb325e336907f608f761e12d403534d9ba2db2a',
            'a05b05acc15594df6e8c7764f285eff4af09c40d94fb3878dffb1d7d1af616f5',
            '6d89130532823ca8835782f977a2d3dcada3de47387cf31297ccf3947c848149',
            '4e1660749bf2055e015500f9fb933dce508bf4e62f6a246dc09fe1959cfe75b7',
            '9a38884332094b1ffcd7f7f88113bce33f824de7de17cfcac4c4a677732dff92',
            'dc343f5821e28b8f284450189c840822bd3a18ff2fcafac0ffbdbbee60cc1f45',
            'd2dd9106aa3c699acffd6af4c3ce7f9b5eb2d988beb8111db5a6c8637aeabcdd'
        );
        $random = $random [mt_rand ( 0, count ( $random ) - 1 )];
        return $random;
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getInventory() {
        $connection = Yii::$app->db;
        $command = $connection->createCommand ( 'SELECT distinct(product_entity_info_id) FROM tb_product_variants where qty>0' );
        $model = $command->queryAll ();
        if($model && !empty($model)){
            $product_ids= implode(', ', array_map(function ($product) {
                return $product['product_entity_info_id'];
            }, $model));
        }
        else{
            $product_ids = '0';
        }

        $Product = Product::find()->where('id IN('.$product_ids.')')->orderby('name')->all();

        return $Product;
    }
}