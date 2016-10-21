<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\models\CustomerEntitystatus;
use app\models\CustomerEntityInfo;
use app\models\CustomerEntityAddress;
use app\models\Litevieworderlog;

use app\models\Register;

/**
 * PostController implements the CRUD actions for Post model.
 */
class LiteviewController extends \app\components\AController
{
    /**
     *
     */
    public function actionSendToLiteview(){
        if (Yii::$app->user->isGuest){
            $this->redirect(array('site/dashboard'));
        }else{
            if(isset($_GET['cmp_id']) && $_GET['cmp_id']!=null  && isset($_GET['o_id']) && $_GET['o_id']!=null){
                $customer_id = '';
                if(isset($_GET['cus_id'])){
                    $customer_id = $_GET['cus_id'];

                }
                $company_id = $_GET['cmp_id'];
                $order_id = $_GET['o_id'];
                $this->moveliteview($customer_id,$company_id,$order_id);
            }
        }
        $this->redirect(array('tradegecko/index'));
    }


    /**
     * @param $customer_id
     * @param $company_id
     * @param $tradegecko_order_id
     */
    public function moveliteview($customer_id, $company_id, $tradegecko_order_id){
        $ResponseAddress['addresses']['0'] = array();
        $headr = array();
        $accesstoken = "Bearer ".Yii::$app->params['tradegecko']['token'];
        $headr[] = 'Content-type: application/json';
        $headr[] = 'Authorization: '.$accesstoken;

        $url = "https://api.tradegecko.com/addresses?company_id=".$company_id;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $ResponseAddress = curl_exec($ch);
        curl_close($ch);
        $ResponseAddress = json_decode($ResponseAddress, TRUE);
        $ResponseAddress = $ResponseAddress['addresses']['0'];

        $date = date("Y-m-d");

        $billing_info = array();
        $shipping_info = array();
        $shipping_charge ='0';
        $shipping_charge = Yii::$app->params['tradegecko']['shipping_charge'];
        if($customer_id!=null){
            $customer_info = CustomerEntityInfo::find()->where(['customer_id'=>$customer_id,]
            )->orderBy(['id'=>SORT_DESC])->one();

            $BillingAddress = CustomerEntityAddress::find()->where(['customer_id'=>$customer_id,
                    'is_default' =>'1',
                    'type_id' => '1']
            )->orderBy(['id'=>SORT_DESC])->one();

            if(sizeof($BillingAddress)>0 && sizeof($customer_info)>0){
                $billing_info = array('firstname' =>$BillingAddress->first_name,
                    'lastname' => $BillingAddress->last_name,
                    'address1' => $BillingAddress->address,
                    'address2' => $BillingAddress->address2,
                    'city' => $BillingAddress->city,
                    'state' => $BillingAddress->state,
                    'country' => $BillingAddress->country,
                    'zipcode' => $BillingAddress->zipcode,
                    'phone' => $customer_info->phone,
                    'email' => $customer_info->email,
                );
            }
            $ShippingAddress = CustomerEntityAddress::find()->where(['customer_id'=>$customer_id,
                    'is_default' =>'1',
                    'type_id' => '2']
            )->orderBy(['id'=>SORT_DESC])->one();

            if(sizeof($ShippingAddress)>0 && sizeof($customer_info)>0){
                $shipping_info = array('firstname' =>$ShippingAddress->first_name,
                    'lastname' => $ShippingAddress->last_name,
                    'address1' => $ShippingAddress->address,
                    'address2' => $ShippingAddress->address2,
                    'city' => $ShippingAddress->city,
                    'state' => $ShippingAddress->state,
                    'country' => $ShippingAddress->country,
                    'zipcode' => $ShippingAddress->zipcode,
                    'phone' => $customer_info->phone,
                    'email' => $customer_info->email,
                );
                if($ShippingAddress->state == 'CA' || $ShippingAddress->state == 'NV' || $ShippingAddress->state == 'AZ' || $ShippingAddress->state == 'OR'){
                    $shipping_charge = 8.5;
                }

                //if($ShippingAddress->state == 'AK' || $ShippingAddress->state == 'HI' ){
                //	$ship_method = 8.5;
                //}
            }
        }
        if(sizeof($billing_info) == 0 && sizeof($ResponseAddress)>0){
            //print_r($ResponseAddress);die;
            if($ResponseAddress['country'] == 'USA'){
                $ResponseAddress['country'] ='US';
            }

            $billing_info = array('firstname' =>$ResponseAddress['label'],
                'lastname' => $ResponseAddress['label'],
                'address1' => $ResponseAddress['address1'],
                'address2' => $ResponseAddress['address2'],
                'city' => $ResponseAddress['city'],
                'state' => $ResponseAddress['state'],
                'country' => $ResponseAddress['country'],
                'zipcode' => $ResponseAddress['zip_code'],
                'phone' => $ResponseAddress['phone_number'],
                'email' => $ResponseAddress['email'],
            );
        }
        if(sizeof($shipping_info) == 0 && sizeof($ResponseAddress)>0){
            if($ResponseAddress['country'] == 'USA'){
                $ResponseAddress['country'] ='US';
            }
            if($ResponseAddress['state'] == 'CA' || $ResponseAddress['state'] == 'NV' || $ResponseAddress['state'] == 'AZ' || $ResponseAddress['state'] == 'OR'){
                $shipping_charge = 8.5;
            }
            $shipping_info = array('firstname' =>$ResponseAddress['label'],
                'lastname' => $ResponseAddress['label'],
                'address1' => $ResponseAddress['address1'],
                'address2' => $ResponseAddress['address2'],
                'city' => $ResponseAddress['city'],
                'state' => $ResponseAddress['state'],
                'country' => $ResponseAddress['country'],
                'zipcode' => $ResponseAddress['zip_code'],
                'phone' => $ResponseAddress['phone_number'],
                'email' => $ResponseAddress['email'],
            );

        }
        $url = "https://api.tradegecko.com/orders/".$tradegecko_order_id;
        $OrderResponse['order'] = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $OrderResponse = curl_exec($ch);
        curl_close($ch);
        $OrderResponse = json_decode($OrderResponse, TRUE);
        $OrderResponse = $OrderResponse['order'];
        //print_r($OrderResponse);die;
        $sub_total = 0;
        $grand_total = $OrderResponse['total'];
        $discount = '0';

        $tax = '0';
        $tradegecko_order_number = '';
        //print_r($OrderResponse);die;

        /****************** Set Order Information ************************************/
        if(sizeof($OrderResponse)>0 && isset($OrderResponse['order_line_item_ids'])){
            $tradegecko_order_number = $OrderResponse['order_number'];
            $user_id =  $OrderResponse['user_id'];
            /********************** get User name **************************/
            $url = "https://api.tradegecko.com/companies/".$company_id;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headr);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $userResponse = curl_exec($ch);
            curl_close($ch);
            $user  = json_decode($userResponse, TRUE);
            if(sizeof($user)>0){
                $name = $user['company']['name'];
            }
            //print_r($name);die;

            $billing_info['firstname'] = $name;
            $billing_info['lastname'] = $name;
            $shipping_info['firstname'] = $name;
            $shipping_info['lastname'] = $name;
            /********************** get User Name End **********************/
            foreach($OrderResponse['order_line_item_ids'] as $itemid){
                $url = "https://api.tradegecko.com/order_line_items/".$itemid;
                $OrderItemResponse['order_line_item'] = array();
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$url);
                curl_setopt($ch, CURLOPT_HTTPHEADER,$headr);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                $OrderItemResponse = curl_exec($ch);
                curl_close($ch);
                $OrderItemResponse = json_decode($OrderItemResponse, TRUE);
                //print_r($OrderItemResponse); die;
                if(sizeof($OrderItemResponse['order_line_item'])>0){
                    if($OrderItemResponse['order_line_item']['variant_id'] !=null){
                        $url = "https://api.tradegecko.com/variants/".$OrderItemResponse['order_line_item']['variant_id'];
                        $variantResponse = array();
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL,$url);
                        curl_setopt($ch, CURLOPT_HTTPHEADER,$headr);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                        $variantResponse = curl_exec($ch);
                        curl_close($ch);
                        $variantResponse = json_decode($variantResponse, TRUE);
                        //print_r($variantResponse);
                        //print_r($variantResponse['variant']);die;
                        if(isset($variantResponse['variant']) && sizeof($variantResponse['variant']) >0){
                            //print_r($variantResponse['variant']['sku']);die;
                            $item_name = $variantResponse['variant']['sku'].'-'.$variantResponse['variant']['product_name'].'-'.$variantResponse['variant']['name'];
                            $sub_total = $sub_total + $OrderItemResponse['order_line_item']['price'];
                            $item[] = array('name' => $item_name,
                                'price' => $OrderItemResponse['order_line_item']['price'],
                                'qty' => (int)$OrderItemResponse['order_line_item']['quantity'],
                                'sku' => $variantResponse['variant']['sku'],
                                'description' => $item_name,
                            );
                            //$tax = 	$OrderItemResponse['order_line_item']['tax_rate'];
                            //print_r($OrderItemResponse);die;
                        }
                    }
                }
            }
            $sub_total = $sub_total + $shipping_charge;
            if( $shipping_info['state'] == 'CA'){
                $tax = ($sub_total * 9)/100;
                $tax = number_format($tax,2,'.','');
            }
        }

        $this->sendToLiteview($customer_id,$company_id,$tradegecko_order_number,$billing_info,$shipping_info,$item,$sub_total,$tax,$shipping_charge,$discount,$grand_total,$tradegecko_order_id);
    }


    /**
     * @param $customer_id
     * @param $company_id
     * @param $tradegecko_order_number
     * @param $billing_info
     * @param $shipping_info
     * @param $item
     * @param $sub_total
     * @param $tax
     * @param $shipping_charge
     * @param $discount
     * @param $grand_total
     * @param $tradegecko_order_id
     */
    public function sendToLiteview($customer_id,$company_id,$tradegecko_order_number,$billing_info,$shipping_info,$item,$sub_total,$tax,$shipping_charge,$discount,$grand_total,$tradegecko_order_id){
        if(sizeof($billing_info) && sizeof($shipping_info)>0 && sizeof($item)>0){

            $date = date("Y-m-d");
            $nextweek = date("Y-m-d",strtotime("+1 week"));
            // '.$tradegecko_order_id.'
            $xml = '<?xml version="1.0" encoding="UTF-8"?>
				<toolkit>
				<submit_order>
					<order_info>
						<order_details>
							<order_status>Active</order_status>
							<order_date>'.$date.'</order_date>
							<order_number>'.$tradegecko_order_id.'</order_number> 
							<order_source>Web</order_source>
							<order_type>Regular</order_type>
							<catalog_name>Default</catalog_name>
							<gift_order>False</gift_order>
							<po_number >'.$tradegecko_order_id.'</po_number >
							<passthrough_field_01>passthrough information</passthrough_field_01>
							<passthrough_field_02>passthrough information</passthrough_field_02>
							<allocate_inventory_now>TRUE</allocate_inventory_now>
						</order_details>
						
						<billing_contact>
							<billto_prefix></billto_prefix>
							<billto_first_name>'.$billing_info['firstname'].'</billto_first_name>
							<billto_last_name></billto_last_name>
							<billto_suffix></billto_suffix>
							<billto_company_name></billto_company_name>
							<billto_address1>'.$billing_info['address1'].'</billto_address1>
							<billto_address2>'.$billing_info['address2'].'</billto_address2>
							<billto_address3></billto_address3>
							<billto_city>'.$billing_info['city'].'</billto_city>
							<billto_state>'.$billing_info['state'].'</billto_state>
							<billto_postal_code>'.$billing_info['zipcode'].'</billto_postal_code>
							<billto_country>US</billto_country>
							<billto_telephone_no>'.$billing_info['phone'].'</billto_telephone_no>
							<billto_email>'.$billing_info['email'].'</billto_email>
						</billing_contact>
						
						<shipping_contact>
							<shipto_prefix></shipto_prefix>
							<shipto_first_name>'.$shipping_info['firstname'].'</shipto_first_name>
							<shipto_last_name></shipto_last_name>
							<shipto_suffix></shipto_suffix>
							<shipto_company_name></shipto_company_name>
							<shipto_address1>'.$shipping_info['address1'].'</shipto_address1>
							<shipto_address2>'.$shipping_info['address2'].'</shipto_address2>
							<shipto_address3></shipto_address3>
							<shipto_city>'.$shipping_info['city'].'</shipto_city>
							<shipto_state>'.$shipping_info['state'].'</shipto_state>
							<shipto_postal_code>'.$shipping_info['zipcode'].'</shipto_postal_code>
							<shipto_country>'.$shipping_info['country'].'</shipto_country>
							<shipto_telephone_no>'.$shipping_info['phone'].'</shipto_telephone_no>
							<shipto_email>'.$shipping_info['email'].'</shipto_email>
							</shipping_contact>
							
						<billing_details>
							<sub_total>'.$sub_total.'</sub_total>
							<shipping_handling>'.$shipping_charge.'</shipping_handling>
							<sales_tax_total>'.$tax.'</sales_tax_total>
							<discount_total>'.$discount.'</discount_total>
							<grand_total>'.$grand_total.'</grand_total>
						</billing_details>
						
						<shipping_details>
							<ship_method>US Postal Service Parcel Select</ship_method>
							<ship_options>
							<signature_requested>FALSE</signature_requested>
							<insurance_requested>FALSE</insurance_requested>
							<insurance_value>60.00</insurance_value>
							<saturday_delivery_requested>FALSE</saturday_delivery_requested>
							<third_party_billing_requested>FALSE</third_party_billing_requested>
							<third_party_billing_account_no>AE999947</third_party_billing_account_no>
							<third_party_billing_zip>90638</third_party_billing_zip>
							<third_party_country>US</third_party_country>
							<general_description>Test items being shipped for testing</general_description>
							<content_description>sample items</content_description>
							</ship_options>
						</shipping_details>
						
						<order_notes>
							<note>
								<note_type>shipping</note_type>
								<note_description>Note: You were only charged $55.00. This invoice details the retail value of your order.
													Thank you, 
													Trendy Butler Team
								</note_description>
								<show_on_ps>True</show_on_ps>
							</note>
						</order_notes>
						
						<order_items>
							<total_line_items>'.sizeof($item).'</total_line_items>';
            $item_xml = '';

            foreach($item as $item_info){
                $item_xml = $item_xml.'<item>
												<inventory_item>'.$item_info['sku'].'</inventory_item>
												<inventory_item_sku>'.$item_info['sku'].'</inventory_item_sku>
												<inventory_item_description>'.$item_info['description'].'</inventory_item_description>
												<inventory_item_price>'.$item_info['price'].'</inventory_item_price>
												<inventory_item_qty>'.$item_info['qty'].'</inventory_item_qty>
												<inventory_item_ext_price>'.$item_info['price'].'</inventory_item_ext_price>
												<inventory_passthrough_01>non-related lv id</inventory_passthrough_01>
												<inventory_passthrough_02>non-related lv id</inventory_passthrough_02>
											</item>';

            }

            $xml = $xml.$item_xml.'</order_items>
					</order_info>
				</submit_order>
			</toolkit>';
            $username = Yii::$app->params['liteview']['username'];//Username Provided by IFS Account Rep
            $key = Yii::$app->params['liteview']['apikey'];//Application Key Provided by IFS Account Rep
            header('Content-type: text/xml');

            //echo $xml;die;

            $url = "https://liteviewapi.imaginefulfillment.com/order/submit/".$username;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-type: text/xml', 'appkey: ' .$key));
            //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:8.0) Gecko/20100101 Firefox/8.0');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);


            $response = curl_exec($ch);
            curl_close($ch);

            print($response);

            $created_at = $date;
            $this->liteviewlog($response,$customer_id,$company_id,$tradegecko_order_id,$tradegecko_order_number,$created_at);
            Yii::$app->end();
        }
    }


    /**
     * @param $response
     * @param $customer_id
     * @param $company_id
     * @param $tradegecko_order_id
     * @param $tradegecko_order_number
     * @param $created_at
     */
    public function liteviewlog($response,$customer_id,$company_id,$tradegecko_order_id,$tradegecko_order_number,$created_at){
        $liteviewSuccess = json_decode(json_encode((array)simplexml_load_string($response)),1);
        //print_r($liteviewSuccess);
        if(isset($liteviewSuccess['submit_order']) && isset($liteviewSuccess['submit_order']['order_information'])
            && isset($liteviewSuccess['submit_order']['order_information']['order_details'])
            && isset($liteviewSuccess['submit_order']['order_information']['order_details']['ifs_order_number'])
            && $liteviewSuccess['submit_order']['order_information']['order_details']['ifs_order_number'] !=null){

            $liteview_order_id = $liteviewSuccess['submit_order']['order_information']['order_details']['ifs_order_number'];
            $Litevieworderlog = new Litevieworderlog();
            $Litevieworderlog->customer_id = $customer_id;
            $Litevieworderlog->company_id = $company_id;
            $Litevieworderlog->tradegecko_order_id = $tradegecko_order_id;
            $Litevieworderlog->tradegecko_order_number = $tradegecko_order_number;
            $Litevieworderlog->liteview_order_id = $liteview_order_id;
            $Litevieworderlog->liteview_order_status = '1';
            $Litevieworderlog->liteview_order_created_at = $created_at;
            $Litevieworderlog->save();
            //echo 'Done'.$liteview_order_id;
        }
    }

    /**
     *
     */
    public function actionSend(){
        /*Sample submit_order POST request for the Liteview System, this script will POST Data and print out results once it receives
        *a response from liteview
        *Created On: 12/5/2012
        *Created By: Pablo Hernadez
        */
        $date = date("Y-m-d");

        $billing_info = array('firstname' =>'saurabh',
            'lastname' => 'seth',
            'address1' => 'billing LKO STREET 1',
            'address2' => 'billing LKO STREET 2',
            'city' => 'newyork',
            'state' => 'NY',
            'country' => 'US',
            'zipcode' => '10002',
            'phone' => '1234567891',
            'email' => 'saurabhseth@trendybutler.com',
        );

        $shipping_info = array('firstname' =>'saurabh',
            'lastname' => 'seth',
            'address1' => 'billing LKO STREET 1',
            'address2' => 'billing LKO STREET 2',
            'city' => 'newyork',
            'state' => 'NY',
            'country' => 'US',
            'zipcode' => '10002',
            'phone' => '1234567891',
            'email' => 'saurabhseth@trendybutler.com',
        );
        $sub_total = '55';
        $grand_total = '55';
        $discount = '0';
        $ship_charge = '0';
        $tax = '0';

        $item = array('name' =>'15-2002-L-C',
            'sku' => '15-2002-L-C',
            'description' => 'Test Item Description',
            'price' => '55',
            'qty' => '1',
        );
        //header('Content-type: text/xml');
        /*
        *Set Up the Liteview Credentials
        */
        $username = 'trendybutler';//Username Provided by IFS Account Rep
        $key = 'fb1a6ef987ca93ec2f7abb8fd4113f6f';//Application Key Provided by IFS Account Rep
        /*
        *Create your xml here somewhere
        */
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
				<toolkit>
				<submit_order>
					<order_info>
						<order_details>
							<order_status>Active</order_status>
							<order_date>'.$date.'</order_date>
							<order_number>11111-05</order_number>
							<order_source>Web</order_source>
							<order_type>Regular</order_type>
							<catalog_name>Default</catalog_name>
							<gift_order>False</gift_order>
							<po_number >PO#12131123121-12312321</po_number >
							<passthrough_field_01>passthrough information</passthrough_field_01>
							<passthrough_field_02>passthrough information</passthrough_field_02>
							<future_date_release>2015-04-10</future_date_release>
							<allocate_inventory_now>TRUE</allocate_inventory_now>
						</order_details>
						
						<billing_contact>
							<billto_prefix></billto_prefix>
							<billto_first_name>'.$billing_info['firstname'].'</billto_first_name>
							<billto_last_name>'.$billing_info['lastname'].'</billto_last_name>
							<billto_suffix></billto_suffix>
							<billto_company_name>Trendybutler.com</billto_company_name>
							<billto_address1>'.$billing_info['address1'].'</billto_address1>
							<billto_address2>'.$billing_info['address2'].'</billto_address2>
							<billto_address3></billto_address3>
							<billto_city>'.$billing_info['city'].'</billto_city>
							<billto_state>'.$billing_info['state'].'</billto_state>
							<billto_postal_code>'.$billing_info['zipcode'].'</billto_postal_code>
							<billto_country>US</billto_country>
							<billto_telephone_no>'.$billing_info['phone'].'</billto_telephone_no>
							<billto_email>'.$billing_info['email'].'</billto_email>
						</billing_contact>
						
						<shipping_contact>
							<shipto_prefix></shipto_prefix>
							<shipto_first_name>'.$shipping_info['firstname'].'</shipto_first_name>
							<shipto_last_name>'.$shipping_info['lastname'].'</shipto_last_name>
							<shipto_suffix></shipto_suffix>
							<shipto_company_name>Trendybutler.com</shipto_company_name>
							<shipto_address1>'.$shipping_info['address1'].'</shipto_address1>
							<shipto_address2>'.$shipping_info['address2'].'</shipto_address2>
							<shipto_address3></shipto_address3>
							<shipto_city>'.$shipping_info['city'].'</shipto_city>
							<shipto_state>'.$shipping_info['state'].'</shipto_state>
							<shipto_postal_code>'.$shipping_info['zipcode'].'</shipto_postal_code>
							<shipto_country>'.$shipping_info['country'].'</shipto_country>
							<shipto_telephone_no>'.$shipping_info['country'].'</shipto_telephone_no>
							<shipto_email>'.$shipping_info['email'].'</shipto_email>
							</shipping_contact>
							
						<billing_details>
							<sub_total>'.$sub_total.'</sub_total>
							<shipping_handling>'.$ship_charge.'</shipping_handling>
							<sales_tax_total>'.$tax.'</sales_tax_total>
							<discount_total>'.$discount.'</discount_total>
							<grand_total>'.$grand_total.'</grand_total>
						</billing_details>
						
						<shipping_details>
							<ship_method>US Postal Service Parcel Select</ship_method>
							<ship_options>
							<signature_requested>FALSE</signature_requested>
							<insurance_requested>FALSE</insurance_requested>
							<insurance_value>60.00</insurance_value>
							<saturday_delivery_requested>FALSE</saturday_delivery_requested>
							<third_party_billing_requested>FALSE</third_party_billing_requested>
							<third_party_billing_account_no>AE999947</third_party_billing_account_no>
							<third_party_billing_zip>90638</third_party_billing_zip>
							<third_party_country>US</third_party_country>
							<general_description>Test items being shipped for testing</general_description>
							<content_description>sample items</content_description>
							</ship_options>
						</shipping_details>
						
						<order_notes>
							<note>
								<note_type>shipping</note_type>
								<note_description>Note: You were only charged $55.00. This invoice details the retail value of your order.
													Thank you, 
													Trendy Butler Team
								</note_description>
								<show_on_ps>True</show_on_ps>
							</note>
						</order_notes>
						
						<order_items>
							<total_line_items>1</total_line_items>
							<item>
							<inventory_item>15-2002-L-C</inventory_item>
							<inventory_item_sku>15-2002-L-C</inventory_item_sku>
							<inventory_item_description>Test Item Description</inventory_item_description>
							<inventory_item_price>5.00</inventory_item_price>
							<inventory_item_qty>1</inventory_item_qty>
							<inventory_item_ext_price>5.00</inventory_item_ext_price>
							<inventory_passthrough_01>non-related lv id</inventory_passthrough_01>
							<inventory_passthrough_02>non-related lv id</inventory_passthrough_02>
							</item>
							</order_items>
						</order_info>
					</submit_order>
				</toolkit>';



        $url = "https://liteviewapi.imaginefulfillment.com/order/submit/".$username;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-type: text/xml', 'appkey: ' .$key));
        //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:8.0) Gecko/20100101 Firefox/8.0');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);


        $response = curl_exec($ch);
        curl_close($ch);
        print($response);
    }


    /**
     *
     */
    public function actionLiteviewlog(){
        $date = date("Y-m-d");
        $response = '<toolkit>
						<submit_order>
							<order_information><order_details><order_status>Active</order_status><ifs_order_number>TRB25166967</ifs_order_number><client_order_number>3633224</client_order_number><passthrough_field_01>passthrough information</passthrough_field_01><passthrough_field_02>passthrough information</passthrough_field_02></order_details><order_notes><note><note_status>Success</note_status><note_text>Note: You were only charged $55.00. This invoice details the retail value of your order.
								 Thank you, 
								 Trendy Butler Team
							</note_text></note></order_notes><order_items><item><inventory_item>JK882-X-B</inventory_item><inventory_sku>JK882-X-B</inventory_sku><qty_requested>1</qty_requested><inventory_passthrough_01/><inventory_passthrough_02/><import_status>Success</import_status><import_description>Your item was successfully received and allocated</import_description></item></order_items>
							</order_information>
						</submit_order><warnings/>
					</toolkit>';
        $customer_id = '1';
        $company_id = '123';
        $tradegecko_order_id = '1111';
        $tradegecko_order_number = '#sooo1';
        $created_at = $date;
        $this->liteviewlog($response,$customer_id,$company_id,$tradegecko_order_id,$tradegecko_order_number,$created_at);

    }
}