<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\helpers\VarDumper;
use yii\web\HttpException;


class Liteview extends Component {

    /**
     * Liteview API Endpoint
     * @var string
     */
    public $url = 'https://liteviewapi.imaginefulfillment.com';

    /**
     * Liteview username provided by IFS
     * @var string
     */
    public $username = '';

    /**
     * Liteview application key provided by IFS
     * @var string
     */
    public $appKey = '';

    /**
     * For testing purposes
     * @var bool
     */
    public $debug = true;

    /**
     * Provides the list of available shipping methods for your account in the Liteview System
     * @return array
     */
    public function getOrderMethods() {

        if(!$this->debug) {
            $responseXML = $this->sendRequest('order', 'methods');

            $xml = simplexml_load_string($responseXML);
            foreach($xml->shipping_methods->shipping_method as $method) {
                $response[] = (string) $method;
            }

            return $response;

        } else {

            $responseXML = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<toolkit>
    <shipping_methods>
        <shipping_method>Canada Post Commercial Expedited Parcel-USA</shipping_method>
        <shipping_method>Canada Post Expedited Parcel</shipping_method>
        <shipping_method>Canada Post Expedited Parcel-USA</shipping_method>
        <shipping_method>Canada Post International Air Parcel</shipping_method>
        <shipping_method>Canada Post International Surface Parcel</shipping_method>
        <shipping_method>Canada Post Light Packet-International</shipping_method>
        <shipping_method>Canada Post Light Packet-USA</shipping_method>
        <shipping_method>Canada Post Priority Courier</shipping_method>
        <shipping_method>Canada Post Purolator International</shipping_method>
        <shipping_method>Canada Post Regular Parcel</shipping_method>
        <shipping_method>Canada Post Small Packets-International</shipping_method>
        <shipping_method>Canada Post Small Packets-USA</shipping_method>
    </shipping_methods>
</toolkit>
XML;
            $xml = simplexml_load_string($responseXML);
            $response = [];

            foreach($xml->shipping_methods->shipping_method as $method) {
                $response[] = (string) $method;
            }

            return $response;
        }

    }

    /**
     * Provides a list of valid country names in the Liteview System
     * @return array
     */
    public function getOrderCountries() {

        if(!$this->debug) {
            $responseXML = $this->sendRequest('order', 'countrylist');

            $xml = simplexml_load_string($responseXML);
            $response = [];

            foreach($xml->country_list->country as $country) {
                $response[(string)$country->country_code] = (string)$country->country_name;
            }

            return $response;

        } else {

            $responseXML = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<toolkit>
    <country_list>
        <country>
            <country_code>AD</country_code>
            <country_name>Andorra</country_name>
        </country>
        <country>
            <country_code>AE</country_code>
            <country_name>U.A.E.</country_name>
        </country>
        <country>
            <country_code>AF</country_code>
            <country_name>Afghanistan</country_name>
        </country>
    </country_list>
</toolkit>
XML;

            $xml = simplexml_load_string($responseXML);
            $response = [];

            foreach($xml->country_list->country as $country) {
                $response[(string)$country->country_code] = (string)$country->country_name;
            }

            return $response;

        }

    }

    /**
     * Submits an order into Liteview and returns a confirmation of import results
     * @param array $params
     * @return array
     */
    public function submitOrder($params) {

        if(!$this->debug) {

            $responseXML = $this->sendRequest('order', 'submit', 'POST', $params);
            $xml = simplexml_load_string($responseXML);
            $response = [];

            $order = $xml->submit_order->order_information;

            $details = $order->order_details;
            $response['details'] = [
                'status' => (string)$details->order_status,
                'ifs_order_number' => (string)$details->ifs_order_number,
                'client_order_number' => (string)$details->client_order_number,
                'passthrough_field_01' => (string)$details->passthrough_field_01,
                'passthrough_field_02' => (string)$details->passthrough_field_02,
            ];

            $notes = $order->order_notes;
            $response['notes'] = [];
            if(count($notes->note) > 1) {
                foreach($notes->note as $note) {
                    $response['notes'][] = [
                        'status' => (string)$note->note_status,
                        'text' => (string)$note->note_text
                    ];
                }
            } else {
                $response['notes'][] = [
                    'status' => (string)$notes->note->note_status,
                    'text' => (string)$notes->note->note_text
                ];
            }

            $items = $order->order_items;
            $response['items'] = [];
            if(count($items->item) > 1) {
                foreach($items->item as $item) {
                    $response['items'][] = [
                        'name' => (string)$item->inventory_item,
                        'sku' => (string)$item->inventory_sku,
                        'qty_requested' => (int)$item->qty_requested,
                        'inventory_passthrough_01' => (string)$item->inventory_passthrough_01,
                        'inventory_passthrough_02' => (string)$item->inventory_passthrough_02,
                        'import_status' => (string)$item->import_status,
                        'import_description' => (string)$item->import_description
                    ];
                }
            } else {
                $response['items'][] = [
                    'name' => (string)$items->item->inventory_item,
                    'sku' => (string)$items->item->inventory_sku,
                    'qty_requested' => (int)$items->item->qty_requested,
                    'inventory_passthrough_01' => (string)$items->item->inventory_passthrough_01,
                    'inventory_passthrough_02' => (string)$items->item->inventory_passthrough_02,
                    'import_status' => (string)$items->item->import_status,
                    'import_description' => (string)$items->item->import_description
                ];
            }

            if(isset($xml->warnings->warning) && !empty($xml->warnings->warning)) {
                $response['warnings'] = [];
                if(count($xml->warnings->warning) > 1) {
                    foreach($xml->warnings->warning as $warning) {
                        $response['warnings'][] = (string)$warning;
                    }
                } else {
                    $response['warnings'][] = (string)$xml->warnings->warning;
                }
            }

            return $response;

        } else {

            $params = $this->arrayToXML($params);
            VarDumper::dump($params, 10, true);

            $responseXML = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<toolkit>
    <submit_order>
        <order_information>
            <order_details>
                <order_status>Active</order_status>
                <ifs_order_number>TST22200340</ifs_order_number>
                <client_order_number>199901-01</client_order_number>
                <passthrough_field_01>passthrough information</passthrough_field_01>
                <passthrough_field_02>passthrough information</passthrough_field_02>
            </order_details>
            <order_notes>
                <note>
                    <note_status>Success</note_status>
                    <note_text>
                        Please make sure to pack order
                        well
                    </note_text>
                </note>
            </order_notes>
            <order_items>
                <item>
                    <inventory_item>TEST1001</inventory_item>
                    <inventory_sku>TEST1001</inventory_sku>
                    <qty_requested>1</qty_requested>
                    <inventory_passthrough_01>non-related lv id</inventory_passthrough_01>
                    <inventory_passthrough_02>non-related lv id</inventory_passthrough_02>
                    <import_status>Success</import_status>
                    <import_description>
                        Your item was successfully
                        received and allocated
                    </import_description>
                </item>
                <item>
                    <inventory_item>TEST1002</inventory_item>
                    <inventory_sku>TEST1002</inventory_sku>
                    <qty_requested>1</qty_requested>
                    <inventory_passthrough_01>non-related lv id</inventory_passthrough_01>
                    <inventory_passthrough_02>non-related lv id</inventory_passthrough_02>
                    <import_status>Success</import_status>
                    <import_description>
                        Your item was successfully
                        received and allocated
                    </import_description>
                </item>
            </order_items>
        </order_information>
    </submit_order>
    <warnings>
        <warning>
            Saturday Delivery is not available for all
            destinations you may be notified if it fails to
            qualifiy for this service
        </warning>
        <warning>
            The third party option was accepted but we
            dont show a waiver to ship third party on the account,
            please submit so order can be processed promptly
        </warning>
        <warning>If Inventory Item Price is passed as zero then by default liteview will convert it to $1.00</warning>
    </warnings>
</toolkit>
XML;
            $xml = simplexml_load_string($responseXML);
            $response = [];

            $order = $xml->submit_order->order_information;

            $details = $order->order_details;
            $response['details'] = [
                'status' => (string)$details->order_status,
                'ifs_order_number' => (string)$details->ifs_order_number,
                'client_order_number' => (string)$details->client_order_number,
                'passthrough_field_01' => (string)$details->passthrough_field_01,
                'passthrough_field_02' => (string)$details->passthrough_field_02,
            ];

            $notes = $order->order_notes;
            $response['notes'] = [];
            if(count($notes->note) > 1) {
                foreach($notes->note as $note) {
                    $response['notes'][] = [
                        'status' => (string)$note->note_status,
                        'text' => (string)$note->note_text
                    ];
                }
            } else {
                $response['notes'][] = [
                    'status' => (string)$notes->note->note_status,
                    'text' => (string)$notes->note->note_text
                ];
            }

            $items = $order->order_items;
            $response['items'] = [];
            if(count($items->item) > 1) {
                foreach($items->item as $item) {
                    $response['items'][] = [
                        'name' => (string)$item->inventory_item,
                        'sku' => (string)$item->inventory_sku,
                        'qty_requested' => (int)$item->qty_requested,
                        'inventory_passthrough_01' => (string)$item->inventory_passthrough_01,
                        'inventory_passthrough_02' => (string)$item->inventory_passthrough_02,
                        'import_status' => (string)$item->import_status,
                        'import_description' => (string)$item->import_description
                    ];
                }
            } else {
                $response['items'][] = [
                    'name' => (string)$items->item->inventory_item,
                    'sku' => (string)$items->item->inventory_sku,
                    'qty_requested' => (int)$items->item->qty_requested,
                    'inventory_passthrough_01' => (string)$items->item->inventory_passthrough_01,
                    'inventory_passthrough_02' => (string)$items->item->inventory_passthrough_02,
                    'import_status' => (string)$items->item->import_status,
                    'import_description' => (string)$items->item->import_description
                ];
            }

            if(isset($xml->warnings->warning) && !empty($xml->warnings->warning)) {
                $response['warnings'] = [];
                if(count($xml->warnings->warning) > 1) {
                    foreach($xml->warnings->warning as $warning) {
                        $response['warnings'][] = (string)$warning;
                    }
                } else {
                    $response['warnings'][] = (string)$xml->warnings->warning;
                }
            }

            return $response;

        }

    }

    /**
     * Resubmits or adds items to an order that already exists in Liteview and has not been batched to ship, returns a confirmation of import results.
     * @param array $params
     * @return array
     */
    public function resubmitOrder($params) {
        if(!$this->debug) {

            $responseXML = $this->sendRequest('order', 're_submit', 'POST', $params);
            $xml = simplexml_load_string($responseXML);
            $response = [];

            $order = $xml->submit_order->order_information;

            $details = $order->order_details;
            $response['details'] = [
                'status' => (string)$details->order_status,
                'ifs_order_number' => (string)$details->ifs_order_number,
                'client_order_number' => (string)$details->client_order_number,
                'passthrough_field_01' => (string)$details->passthrough_field_01,
                'passthrough_field_02' => (string)$details->passthrough_field_02,
            ];

            $notes = $order->order_notes;
            $response['notes'] = [];
            if(count($notes->note) > 1) {
                foreach($notes->note as $note) {
                    $response['notes'][] = [
                        'status' => (string)$note->note_status,
                        'text' => (string)$note->note_text
                    ];
                }
            } else {
                $response['notes'][] = [
                    'status' => (string)$notes->note->note_status,
                    'text' => (string)$notes->note->note_text
                ];
            }

            $items = $order->order_items;
            $response['items'] = [];
            if(count($items->item) > 1) {
                foreach($items->item as $item) {
                    $response['items'][] = [
                        'name' => (string)$item->inventory_item,
                        'sku' => (string)$item->inventory_sku,
                        'qty_requested' => (int)$item->qty_requested,
                        'inventory_passthrough_01' => (string)$item->inventory_passthrough_01,
                        'inventory_passthrough_02' => (string)$item->inventory_passthrough_02,
                        'import_status' => (string)$item->import_status,
                        'import_description' => (string)$item->import_description
                    ];
                }
            } else {
                $response['items'][] = [
                    'name' => (string)$items->item->inventory_item,
                    'sku' => (string)$items->item->inventory_sku,
                    'qty_requested' => (int)$items->item->qty_requested,
                    'inventory_passthrough_01' => (string)$items->item->inventory_passthrough_01,
                    'inventory_passthrough_02' => (string)$items->item->inventory_passthrough_02,
                    'import_status' => (string)$items->item->import_status,
                    'import_description' => (string)$items->item->import_description
                ];
            }

            if(isset($xml->warnings->warning) && !empty($xml->warnings->warning)) {
                $response['warnings'] = [];
                if(count($xml->warnings->warning) > 1) {
                    foreach($xml->warnings->warning as $warning) {
                        $response['warnings'][] = (string)$warning;
                    }
                } else {
                    $response['warnings'][] = (string)$xml->warnings->warning;
                }
            }

            return $response;

        } else {

            $params = $this->arrayToXML($params);
            VarDumper::dump($params, 10, true);

            $responseXML = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<toolkit>
    <submit_order>
        <order_information>
            <order_details>
                <order_status>Active</order_status>
                <ifs_order_number>TST22200340</ifs_order_number>
                <client_order_number>199901-01</client_order_number>
                <passthrough_field_01>passthrough information</passthrough_field_01>
                <passthrough_field_02>passthrough information</passthrough_field_02>
            </order_details>
            <order_notes>
                <note>
                    <note_status>Success</note_status>
                    <note_text>
                        Please make sure to pack order
                        well
                    </note_text>
                </note>
            </order_notes>
            <order_items>
                <item>
                    <inventory_item>TEST1001</inventory_item>
                    <inventory_sku>TEST1001</inventory_sku>
                    <qty_requested>1</qty_requested>
                    <inventory_passthrough_01>non-related lv id</inventory_passthrough_01>
                    <inventory_passthrough_02>non-related lv id</inventory_passthrough_02>
                    <import_status>Success</import_status>
                    <import_description>
                        Your item was successfully
                        received and allocated
                    </import_description>
                </item>
                <item>
                    <inventory_item>TEST1002</inventory_item>
                    <inventory_sku>TEST1002</inventory_sku>
                    <qty_requested>1</qty_requested>
                    <inventory_passthrough_01>non-related lv id</inventory_passthrough_01>
                    <inventory_passthrough_02>non-related lv id</inventory_passthrough_02>
                    <import_status>Success</import_status>
                    <import_description>
                        Your item was successfully
                        received and allocated
                    </import_description>
                </item>
            </order_items>
        </order_information>
    </submit_order>
    <warnings>
        <warning>
            Saturday Delivery is not available for all
            destinations you may be notified if it fails to
            qualifiy for this service
        </warning>
        <warning>
            The third party option was accepted but we
            dont show a waiver to ship third party on the account,
            please submit so order can be processed promptly
        </warning>
        <warning>If Inventory Item Price is passed as zero then by default liteview will convert it to $1.00</warning>
    </warnings>
</toolkit>
XML;
            $xml = simplexml_load_string($responseXML);
            $response = [];

            $order = $xml->submit_order->order_information;

            $details = $order->order_details;
            $response['details'] = [
                'status' => (string)$details->order_status,
                'ifs_order_number' => (string)$details->ifs_order_number,
                'client_order_number' => (string)$details->client_order_number,
                'passthrough_field_01' => (string)$details->passthrough_field_01,
                'passthrough_field_02' => (string)$details->passthrough_field_02,
            ];

            $notes = $order->order_notes;
            $response['notes'] = [];
            if(count($notes->note) > 1) {
                foreach($notes->note as $note) {
                    $response['notes'][] = [
                        'status' => (string)$note->note_status,
                        'text' => (string)$note->note_text
                    ];
                }
            } else {
                $response['notes'][] = [
                    'status' => (string)$notes->note->note_status,
                    'text' => (string)$notes->note->note_text
                ];
            }

            $items = $order->order_items;
            $response['items'] = [];
            if(count($items->item) > 1) {
                foreach($items->item as $item) {
                    $response['items'][] = [
                        'name' => (string)$item->inventory_item,
                        'sku' => (string)$item->inventory_sku,
                        'qty_requested' => (int)$item->qty_requested,
                        'inventory_passthrough_01' => (string)$item->inventory_passthrough_01,
                        'inventory_passthrough_02' => (string)$item->inventory_passthrough_02,
                        'import_status' => (string)$item->import_status,
                        'import_description' => (string)$item->import_description
                    ];
                }
            } else {
                $response['items'][] = [
                    'name' => (string)$items->item->inventory_item,
                    'sku' => (string)$items->item->inventory_sku,
                    'qty_requested' => (int)$items->item->qty_requested,
                    'inventory_passthrough_01' => (string)$items->item->inventory_passthrough_01,
                    'inventory_passthrough_02' => (string)$items->item->inventory_passthrough_02,
                    'import_status' => (string)$items->item->import_status,
                    'import_description' => (string)$items->item->import_description
                ];
            }

            if(isset($xml->warnings->warning) && !empty($xml->warnings->warning)) {
                $response['warnings'] = [];
                if(count($xml->warnings->warning) > 1) {
                    foreach($xml->warnings->warning as $warning) {
                        $response['warnings'][] = (string)$warning;
                    }
                } else {
                    $response['warnings'][] = (string)$xml->warnings->warning;
                }
            }

            return $response;

        }
    }

    /**
     * Cancels an order into the Liteview only if order is not batched otherwise order cannot be cancelled!
     * @param array $params
     * @return array
     */
    public function cancelOrder($params) {

        if(!$this->debug) {

            $responseXML = $this->sendRequest('order', 'cancel', 'POST', $params);
            $xml = simplexml_load_string($responseXML);
            $response = [];

            $details = $xml->cancel_order->order_information->order_details;
            $response['details'] = [
                'status' => (string)$details->order_status,
                'client_order_number' => (string)$details->client_order_number,
                'ifs_order_number' => (string)$details->ifs_order_number
            ];

            if(isset($xml->warnings->warning) && !empty($xml->warnings->warning)) {
                $response['warnings'] = [];
                if(count($xml->warnings->warning) > 1) {
                    foreach($xml->warnings->warning as $warning) {
                        $response['warnings'][] = (string)$warning;
                    }
                } else {
                    $response['warnings'][] = (string)$xml->warnings->warning;
                }
            }

            return $response;

        } else {

            $params = $this->arrayToXML($params);
            VarDumper::dump($params, 10, true);

            $responseXML = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<toolkit>
    <cancel_order>
        <order_information>
            <order_details>
                <order_status>Cancelled</order_status>
                <client_order_number>199901-01</client_order_number>
                <ifs_order_number>TST22200340</ifs_order_number>
            </order_details>
        </order_information>
    </cancel_order>
    <warnings>
        <warning>This order cannot be cancelled since it is already batched.</warning>
    </warnings>
</toolkit>
XML;
            $xml = simplexml_load_string($responseXML);
            $response = [];

            $details = $xml->cancel_order->order_information->order_details;
            $response['details'] = [
                'status' => (string)$details->order_status,
                'client_order_number' => (string)$details->client_order_number,
                'ifs_order_number' => (string)$details->ifs_order_number
            ];

            if(isset($xml->warnings->warning) && !empty($xml->warnings->warning)) {
                $response['warnings'] = [];
                if(count($xml->warnings->warning) > 1) {
                    foreach($xml->warnings->warning as $warning) {
                        $response['warnings'][] = (string)$warning;
                    }
                } else {
                    $response['warnings'][] = (string)$xml->warnings->warning;
                }
            }

            return $response;
        }

    }

    /**
     * Provides a list of tracking numbers for a specific order
     * @param string $order_number Liteview Order Number or Order Number you used to submit the order
     * @return array
     */
    public function orderTracking($order_number) {

        if(!$this->debug) {
            $parameters[] = $order_number;
            $responseXML = $this->sendRequest('order', 'tracking', 'GET', $parameters);

            $xml = simplexml_load_string($responseXML);

            $shipment = $xml->order_tracking->shipment;
            $response = [
                'tracking_no' => (string)$shipment->tracking_no,
                'ifs_order_no' => (string)$shipment->ifs_order_no,
                'client_no' => (string)$shipment->client_no,
                'ship_date' => (string)$shipment->ship_date,
                'ship_method' => (string)$shipment->ship_method,
                'service_level' => (string)$shipment->service_level
            ];

            return $response;

        } else {
            $parameters[] = $order_number;
            VarDumper::dump($parameters, 10, true);

            $responseXML = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<toolkit>
    <order_tracking>
        <shipment>
            <tracking_no>0241231351513132</tracking_no>
            <ifs_order_no>TST22200340</ifs_order_no>
            <client_no>TST22200340</client_no>
            <ship_date>2011-09-12</ship_date>
            <ship_method>FedEx</ship_method>
            <service_level>Ground</service_level>
        </shipment>
        <shipment>
            <tracking_no>0241231351513132</tracking_no>
            <ifs_order_no>TST22200340</ifs_order_no>
            <client_no>TST22200340</client_no>
            <ship_date>2011-09-12</ship_date>
            <ship_method>FedEx</ship_method>
            <service_level>Ground</service_level>
        </shipment>
    </order_tracking>
</toolkit>
XML;

            $xml = simplexml_load_string($responseXML);
            $response = [];

            $shipments = $xml->order_tracking->shipment;
            if(count($shipments) > 1) {
                foreach($shipments as $shipment) {
                    $response[] = [
                        'tracking_no' => (string)$shipment->tracking_no,
                        'ifs_order_no' => (string)$shipment->ifs_order_no,
                        'client_no' => (string)$shipment->client_no,
                        'ship_date' => (string)$shipment->ship_date,
                        'ship_method' => (string)$shipment->ship_method,
                        'service_level' => (string)$shipment->service_level
                    ];
                }
            } else {
                $response[] = [
                    'tracking_no' => (string)$xml->order_tracking->shipment->tracking_no,
                    'ifs_order_no' => (string)$xml->order_tracking->shipment->ifs_order_no,
                    'client_no' => (string)$xml->order_tracking->shipment->client_no,
                    'ship_date' => (string)$xml->order_tracking->shipment->ship_date,
                    'ship_method' => (string)$xml->order_tracking->shipment->ship_method,
                    'service_level' => (string)$xml->order_tracking->shipment->service_level
                ];
            }


            return $response;
        }

    }

    /**
     * Provides a list of tracking numbers for a specific date range format, it is the same as the single tracking request but returns a group of shipments.
     * Query is based on shipment date, this call is paginated meaning it will return a max of 500 results at a time, you must skip results to fully download the entire response.
     * @param string $start_date format date as yyyy-mm-dd, the beginning of the date range request, response will include this date starting at 0:00 hours
     * @param string $end_date format date as yyyy-mm-dd, the end of the date range request, response will include this date up 23:59 hours
     * @param int $skip 0 is the default, if you receive more than 500 results you will need to use this parameter to skip previously downloaded results
     * @return array
     */
    public function orderTrackingRange($start_date, $end_date, $skip = 0) {

        if(!$this->debug) {

            $params = [
                $start_date,
                $end_date,
                $skip
            ];
            $responseXML = $this->sendRequest('order', 'tracking/range', 'GET', $params);

            $xml = simplexml_load_string($responseXML);
            $response = [];

            $shipments = $xml->order_tracking->shipment;
            if(count($shipments) > 1) {
                foreach($shipments as $shipment) {
                    $response['shipments'][] = [
                        'tracking_no' => (string)$shipment->tracking_no,
                        'ifs_order_no' => (string)$shipment->ifs_order_no,
                        'client_no' => (string)$shipment->client_no,
                        'ship_date' => (string)$shipment->ship_date,
                        'ship_method' => (string)$shipment->ship_method,
                        'service_level' => (string)$shipment->service_level
                    ];
                }
            } else {
                $response['shipments'][] = [
                    'tracking_no' => (string)$xml->order_tracking->shipment->tracking_no,
                    'ifs_order_no' => (string)$xml->order_tracking->shipment->ifs_order_no,
                    'client_no' => (string)$xml->order_tracking->shipment->client_no,
                    'ship_date' => (string)$xml->order_tracking->shipment->ship_date,
                    'ship_method' => (string)$xml->order_tracking->shipment->ship_method,
                    'service_level' => (string)$xml->order_tracking->shipment->service_level
                ];
            }

            $info = $xml->query_information;
            $response['info'] = [
                'found_shipments' => (int)$info->found_shipments,
                'shown_first' => (int)$info->shown_first,
                'shown_last' => (int)$info->shown_last,
                'remaining' => (int)$info->remaining
            ];

            return $response;

        } else {

            $responseXML = <<<XML
<?xml version="1.0"?>
<toolkit>
    <query_information>
        <found_shipments>250</found_shipments>
        <shown_first>1</shown_first>
        <shown_last>250</shown_last>
        <remaining>0</remaining>
    </query_information>
    <order_tracking>
        <shipment>
            <tracking_no>9405510200883600279801</tracking_no>
            <ifs_order_no>TRB25312558</ifs_order_no>
            <client_no>3964336</client_no>
            <ship_date>2015-04-06</ship_date>
            <ship_method>US Postal Service</ship_method>
            <service_level>Priority Mail</service_level>
        </shipment>
        <shipment>
            <tracking_no>9405510200829568734242</tracking_no>
            <ifs_order_no>TRB25312560</ifs_order_no>
            <client_no>3962784</client_no>
            <ship_date>2015-04-06</ship_date>
            <ship_method>US Postal Service</ship_method>
            <service_level>Priority Mail</service_level>
        </shipment>
        <shipment>
            <tracking_no>9405510200829568734259</tracking_no>
            <ifs_order_no>TRB25312561</ifs_order_no>
            <client_no>3962781</client_no>
            <ship_date>2015-04-06</ship_date>
            <ship_method>US Postal Service</ship_method>
            <service_level>Priority Mail</service_level>
        </shipment>
    </order_tracking>
</toolkit>
XML;
            $xml = simplexml_load_string($responseXML);
            $response = [];

            $shipments = $xml->order_tracking->shipment;
            if(count($shipments) > 1) {
                foreach($shipments as $shipment) {
                    $response['shipments'][] = [
                        'tracking_no' => (string)$shipment->tracking_no,
                        'ifs_order_no' => (string)$shipment->ifs_order_no,
                        'client_no' => (string)$shipment->client_no,
                        'ship_date' => (string)$shipment->ship_date,
                        'ship_method' => (string)$shipment->ship_method,
                        'service_level' => (string)$shipment->service_level
                    ];
                }
            } else {
                $response['shipments'][] = [
                    'tracking_no' => (string)$xml->order_tracking->shipment->tracking_no,
                    'ifs_order_no' => (string)$xml->order_tracking->shipment->ifs_order_no,
                    'client_no' => (string)$xml->order_tracking->shipment->client_no,
                    'ship_date' => (string)$xml->order_tracking->shipment->ship_date,
                    'ship_method' => (string)$xml->order_tracking->shipment->ship_method,
                    'service_level' => (string)$xml->order_tracking->shipment->service_level
                ];
            }

            $info = $xml->query_information;
            $response['info'] = [
                'found_shipments' => (int)$info->found_shipments,
                'shown_first' => (int)$info->shown_first,
                'shown_last' => (int)$info->shown_last,
                'remaining' => (int)$info->remaining
            ];

            return $response;
        }

    }

    /**
     * Provides a list of updates for a spcific order including tracking information and history of the order
     * @param string $order_number Liteview Order Number or Order Number you used to submit the order
     * @return array
     */
    public function orderStatus($order_number) {

        if(!$this->debug) {
            $params[] = $order_number;
            $responseXML = $this->sendRequest('order', 'ship_status', $params);

            $xml = simplexml_load_string($responseXML);
            $response = [];

            $order = $xml->order_status->order;

            $response['status'] = (string)$order->status;
            $response['shipment_status'] = (string)$order->shipment_status;
            $response['fulfillment_status'] = (string)$order->fulfillment_status;
            $response['dispatch_status'] = (string)$order->dispatch_status;
            $response['ifs_order_no'] = (string)$order->ifs_order_no;
            $response['client_no'] = (string)$order->client_no;
            $response['po_number'] = (string)$order->po_number;
            $response['order_date'] = (string)$order->order_date;
            $response['received_date'] = (string)$order->received_date;
            $response['batch_date'] = (string)$order->batch_date;
            $response['fulfill_date'] = (string)$order->fulfill_date;
            $response['dispatch_date'] = (string)$order->dispatch_date;
            $response['parent_service_level'] = (string)$order->parent_service_level;
            $response['liteview_status'] = (string)$order->liteview_status;
            $response['items_count'] = (int)$order->order_items->item_count;
            if($response['items_count'] > 0) {
                $response['items'] = [];
                if(count($order->order_items->item) > 1) {
                    foreach($order->order_items->item as $item) {
                        $xmlItem = [];
                        $xmlItem['name'] = (string)$item->inventory_item;
                        $xmlItem['sku'] = (string)$item->inventory_item_sku;
                        $xmlItem['description'] = (string)$item->inventory_item_description;
                        $xmlItem['price'] = (float)$item->inventory_item_price;
                        $xmlItem['quantity'] = (int)$item->inventory_item_qty;
                        $xmlItem['extended_price'] = (float)$item->inventory_item_ext_price;
                        $xmlItem['status'] = (string)$item->inventory_item_status;
                        $xmlItem['inventory_passthrough_01'] = (string)$item->inventory_passthrough_01;
                        $xmlItem['inventory_passthrough_02'] = (string)$item->inventory_passthrough_02;

                        $xmlItem['serial_information'] = [];
                        $xmlItem['serial_information']['serial_count'] = (int)$item->serial_information->serial_count;
                        if($xmlItem['serial_information']['serial_count'] > 0) {
                            $xmlItem['serial_information']['serial_numbers'] = [];
                            if (count($item->serial_information->serial_no) > 1) {
                                foreach ($item->serial_information->serial_no as $sNo) {
                                    $xmlItem['serial_information']['serial_numbers'][] = (string)$sNo;
                                }
                            } else {
                                $xmlItem['serial_information']['serial_numbers'][] = (string)$item->serial_information->serial_no;
                            }
                        }

                        $response['items'][] = $xmlItem;
                    }
                } else {
                    $item = $order->order_items->item;
                    $xmlItem = [];
                    $xmlItem['name'] = (string)$item->inventory_item;
                    $xmlItem['sku'] = (string)$item->inventory_item_sku;
                    $xmlItem['description'] = (string)$item->inventory_item_description;
                    $xmlItem['price'] = (float)$item->inventory_item_price;
                    $xmlItem['quantity'] = (int)$item->inventory_item_qty;
                    $xmlItem['extended_price'] = (float)$item->inventory_item_ext_price;
                    $xmlItem['status'] = (string)$item->inventory_item_status;
                    $xmlItem['inventory_passthrough_01'] = (string)$item->inventory_passthrough_01;
                    $xmlItem['inventory_passthrough_02'] = (string)$item->inventory_passthrough_02;

                    $xmlItem['serial_information'] = [];
                    $xmlItem['serial_information']['serial_count'] = (int)$item->serial_information->serial_count;
                    if($xmlItem['serial_information']['serial_count'] > 0) {
                        $xmlItem['serial_information']['serial_numbers'] = [];
                        if (count($item->serial_information->serial_no) > 1) {
                            foreach ($item->serial_information->serial_no as $sNo) {
                                $xmlItem['serial_information']['serial_numbers'][] = (string)$sNo;
                            }
                        } else {
                            $xmlItem['serial_information']['serial_numbers'][] = (string)$item->serial_information->serial_no;
                        }
                    }

                    $response['items'][] = $xmlItem;
                }
            }
            $response['tracking_numbers_count'] = (int)$order->tracking_numbers->tracking_count;
            if($response['tracking_numbers_count'] > 0) {
                $response['tracking_numbers'] = [];
                if(count($order->tracking_numbers->shipment) > 1) {
                    foreach($order->tracking_numbers->shipment as $shipment) {
                        $response['tracking_numbers'][] = [
                            'ship_date' => (string)$shipment->ship_date,
                            'tracking_number' => (string)$shipment->tracking_number,
                            'ship_method' => (string)$shipment->ship_method,
                            'service_level' => (string)$shipment->service_level
                        ];
                    }
                } else {
                    $shipment = $order->tracking_numbers->shipment;
                    $response['tracking_numbers'][] = [
                        'ship_date' => (string)$shipment->ship_date,
                        'tracking_number' => (string)$shipment->tracking_number,
                        'ship_method' => (string)$shipment->ship_method,
                        'service_level' => (string)$shipment->service_level
                    ];
                }
            }


            return $response;

        } else {

            $responseXML = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<toolkit>
    <order_status>
        <order>
            <status>Active</status>
            <shipment_status/>
            <fulfillment_status/>
            <dispatch_status/>
            <ifs_order_no>TES23621495</ifs_order_no>
            <client_no>49057</client_no>
            <po_number>po12345</po_number>
            <order_date>2013-05-09 00:00:00-07</order_date>
            <received_date>2013-08-05 13:39:04.004718-07</received_date>
            <batch_date/>
            <fulfill_date/>
            <dispatch_date/>
            <parent_service_level>UPS Ground</parent_service_level>
            <liteview_status>Active</liteview_status>
            <order_items>
                <item_count>1</item_count>
                <item>
                    <inventory_item>80288</inventory_item>
                    <inventory_item_sku>80288</inventory_item_sku>
                    <inventory_item_description>
                        Yoda with Brown
                        Snake Premier Guild Exclusive Kenner
                        Jumbo Figure
                    </inventory_item_description>
                    <inventory_item_price>0</inventory_item_price>
                    <inventory_item_qty>1</inventory_item_qty>
                    <inventory_item_ext_price>0</inventory_item_ext_price>
                    <inventory_item_status>Backorder</inventory_item_status>
                    <inventory_passthrough_01/>
                    <inventory_passthrough_02/>
                    <serial_information>
                        <serial_count>1</serial_count>
                        <serial_no>101.Serial(1)</serial_no>
                    </serial_information>
                </item>
            </order_items>
            <tracking_numbers>
                <tracking_count>3</tracking_count>
                <shipment>
                    <ship_date>2015-05-15</ship_date>
                    <tracking_number>ABC123</tracking_number>
                    <ship_method>UPS</ship_method>
                    <service_level>Ground</service_level>
                </shipment>
                <shipment>
                    <ship_date>2015-05-15</ship_date>
                    <tracking_number>ABC123</tracking_number>
                    <ship_method>UPS</ship_method>
                    <service_level>Ground</service_level>
                </shipment>
                <shipment>
                    <ship_date>2015-05-15</ship_date>
                    <tracking_number>ABC123</tracking_number>
                    <ship_method>UPS</ship_method>
                    <service_level>Ground</service_level>
                </shipment>
            </tracking_numbers>
        </order>
    </order_status>
</toolkit>

XML;

            $xml = simplexml_load_string($responseXML);
            $response = [];

            $order = $xml->order_status->order;

            $response['status'] = (string)$order->status;
            $response['shipment_status'] = (string)$order->shipment_status;
            $response['fulfillment_status'] = (string)$order->fulfillment_status;
            $response['dispatch_status'] = (string)$order->dispatch_status;
            $response['ifs_order_no'] = (string)$order->ifs_order_no;
            $response['client_no'] = (string)$order->client_no;
            $response['po_number'] = (string)$order->po_number;
            $response['order_date'] = (string)$order->order_date;
            $response['received_date'] = (string)$order->received_date;
            $response['batch_date'] = (string)$order->batch_date;
            $response['fulfill_date'] = (string)$order->fulfill_date;
            $response['dispatch_date'] = (string)$order->dispatch_date;
            $response['parent_service_level'] = (string)$order->parent_service_level;
            $response['liteview_status'] = (string)$order->liteview_status;
            $response['items_count'] = (int)$order->order_items->item_count;
            if($response['items_count'] > 0) {
                $response['items'] = [];
                if(count($order->order_items->item) > 1) {
                    foreach($order->order_items->item as $item) {
                        $xmlItem = [];
                        $xmlItem['name'] = (string)$item->inventory_item;
                        $xmlItem['sku'] = (string)$item->inventory_item_sku;
                        $xmlItem['description'] = (string)$item->inventory_item_description;
                        $xmlItem['price'] = (float)$item->inventory_item_price;
                        $xmlItem['quantity'] = (int)$item->inventory_item_qty;
                        $xmlItem['extended_price'] = (float)$item->inventory_item_ext_price;
                        $xmlItem['status'] = (string)$item->inventory_item_status;
                        $xmlItem['inventory_passthrough_01'] = (string)$item->inventory_passthrough_01;
                        $xmlItem['inventory_passthrough_02'] = (string)$item->inventory_passthrough_02;

                        $xmlItem['serial_information'] = [];
                        $xmlItem['serial_information']['serial_count'] = (int)$item->serial_information->serial_count;
                        if($xmlItem['serial_information']['serial_count'] > 0) {
                            $xmlItem['serial_information']['serial_numbers'] = [];
                            if (count($item->serial_information->serial_no) > 1) {
                                foreach ($item->serial_information->serial_no as $sNo) {
                                    $xmlItem['serial_information']['serial_numbers'][] = (string)$sNo;
                                }
                            } else {
                                $xmlItem['serial_information']['serial_numbers'][] = (string)$item->serial_information->serial_no;
                            }
                        }

                        $response['items'][] = $xmlItem;
                    }
                } else {
                    $item = $order->order_items->item;
                    $xmlItem = [];
                    $xmlItem['name'] = (string)$item->inventory_item;
                    $xmlItem['sku'] = (string)$item->inventory_item_sku;
                    $xmlItem['description'] = (string)$item->inventory_item_description;
                    $xmlItem['price'] = (float)$item->inventory_item_price;
                    $xmlItem['quantity'] = (int)$item->inventory_item_qty;
                    $xmlItem['extended_price'] = (float)$item->inventory_item_ext_price;
                    $xmlItem['status'] = (string)$item->inventory_item_status;
                    $xmlItem['inventory_passthrough_01'] = (string)$item->inventory_passthrough_01;
                    $xmlItem['inventory_passthrough_02'] = (string)$item->inventory_passthrough_02;

                    $xmlItem['serial_information'] = [];
                    $xmlItem['serial_information']['serial_count'] = (int)$item->serial_information->serial_count;
                    if($xmlItem['serial_information']['serial_count'] > 0) {
                        $xmlItem['serial_information']['serial_numbers'] = [];
                        if (count($item->serial_information->serial_no) > 1) {
                            foreach ($item->serial_information->serial_no as $sNo) {
                                $xmlItem['serial_information']['serial_numbers'][] = (string)$sNo;
                            }
                        } else {
                            $xmlItem['serial_information']['serial_numbers'][] = (string)$item->serial_information->serial_no;
                        }
                    }

                    $response['items'][] = $xmlItem;
                }
            }
            $response['tracking_numbers_count'] = (int)$order->tracking_numbers->tracking_count;
            if($response['tracking_numbers_count'] > 0) {
                $response['tracking_numbers'] = [];
                if(count($order->tracking_numbers->shipment) > 1) {
                    foreach($order->tracking_numbers->shipment as $shipment) {
                        $response['tracking_numbers'][] = [
                            'ship_date' => (string)$shipment->ship_date,
                            'tracking_number' => (string)$shipment->tracking_number,
                            'ship_method' => (string)$shipment->ship_method,
                            'service_level' => (string)$shipment->service_level
                        ];
                    }
                } else {
                    $shipment = $order->tracking_numbers->shipment;
                    $response['tracking_numbers'][] = [
                        'ship_date' => (string)$shipment->ship_date,
                        'tracking_number' => (string)$shipment->tracking_number,
                        'ship_method' => (string)$shipment->ship_method,
                        'service_level' => (string)$shipment->service_level
                    ];
                }
            }


            return $response;
        }

    }

    /**
     * Provides a list of active orders for a specific date range and tracking numbers for these orders if applicable
     * Non trackable methods will not return tracking info, only dispatch date
     * Query is based on dispatch date
     * @param string $start_date format date as yyyy-mm-dd, the begining of the date range request, response will include this date starting at 0:00 hours
     * @param string $end_date format date as yyyy-mm-dd, the end of the date range request, response will include this date up 23:59 hours
     * @param int $skip 0 is the default, if you receive more than 500 results you will need to use this parameter to skip previously downloaded results
     * @param string $status optional parameter, either 'Active', 'Cancelled', or 'OnHold' respectively.
     * @return array
     */
    public function orderStatusRange($start_date, $end_date, $skip = 0, $status = '') {

        if(!$this->debug) {
            $params = [
                $start_date,
                $end_date,
                $skip,
                $status
            ];
            $responseXML = $this->sendRequest('order', 'ship_status/range', 'GET', $params);

            $xml = simplexml_load_string($responseXML);
            $response = [];

            $info = $xml->query_information;
            $response['information'] = [
                'found_orders' => (int)$info->found_orders,
                'shown_first' => (int)$info->shown_first,
                'shown_last' => (int)$info->shown_last,
                'remaining' => (int)$info->remaining
            ];

            $response['orders'] = [];
            if(isset($xml->order_status->order)) {
                if(count($xml->order_status->order) > 1) {
                    foreach($xml->order_status->order as $order) {
                        $item = [];
                        $item['status'] = (string)$order->status;
                        $item['shipment_status'] = (string)$order->shipment_status;
                        $item['fulfillment_status'] = (string)$order->fulfillment_status;
                        $item['dispatch_status'] = (string)$order->dispatch_status;
                        $item['ifs_order_no'] = (string)$order->ifs_order_no;
                        $item['client_no'] = (string)$order->client_no;
                        $item['batch_date'] = (string)$order->batch_date;
                        $item['fulfill_date'] = (string)$order->fulfill_date;
                        $item['dispatch_date'] = (string)$order->dispatch_date;
                        $item['parent_service_level'] = (string)$order->parent_service_level;
                        $item['tracking_numbers_count'] = (int)$order->tracking_numbers->tracking_count;
                        if($item['tracking_numbers_count'] > 0) {
                            $item['tracking_numbers'] = [];
                            if(count($order->tracking_numbers->shipment) > 1) {
                                foreach($order->tracking_numbers->shipment as $shipment) {
                                    $item['tracking_numbers'][] = [
                                        'ship_date' => (string)$shipment->ship_date,
                                        'tracking_number' => (string)$shipment->tracking_number,
                                        'ship_method' => (string)$shipment->ship_method,
                                        'service_level' => (string)$shipment->service_level
                                    ];
                                }
                            } else {
                                $shipment = $order->tracking_numbers->shipment;
                                $item['tracking_numbers'][] = [
                                    'ship_date' => (string)$shipment->ship_date,
                                    'tracking_number' => (string)$shipment->tracking_number,
                                    'ship_method' => (string)$shipment->ship_method,
                                    'service_level' => (string)$shipment->service_level
                                ];
                            }
                        }

                        $response['orders'][] = $item;
                    }
                }
            }

            return $response;

        } else {

            $responseXML = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<toolkit>
    <query_information>
        <found_orders>28</found_orders>
        <shown_first>1</shown_first>
        <shown_last>28</shown_last>
        <remaining>0</remaining>
    </query_information>
    <order_status>
        <order>
            <status>Active</status>
            <shipment_status/>
            <fulfillment_status/>
            <dispatch_status/>
            <ifs_order_no>TES22208124</ifs_order_no>
            <client_no>769219</client_no>
            <batch_date>2011-10-26 18:58:41.014104-07</batch_date>
            <fulfill_date>2011-10-27 09:35:48.188177-07</fulfill_date>
            <dispatch_date>2011-10-27 09:35:48.188177-07</dispatch_date>
            <parent_service_level>FedEx Ground</parent_service_level>
            <tracking_numbers>
                <tracking_count>0</tracking_count>
            </tracking_numbers>
        </order>
        <order>
            <status>Active</status>
            <shipment_status/>
            <fulfillment_status/>
            <dispatch_status/>
            <ifs_order_no>TES22208124</ifs_order_no>
            <client_no>769219</client_no>
            <batch_date>2011-10-26 18:58:41.014104-07</batch_date>
            <fulfill_date>2011-10-27 09:35:48.188177-07</fulfill_date>
            <dispatch_date>2011-10-27 09:35:48.188177-07</dispatch_date>
            <parent_service_level>FedEx Ground</parent_service_level>
            <tracking_numbers>
                <tracking_count>2</tracking_count>
                <shipment>
                    <ship_date>2015-04-04</ship_date>
                    <tracking_number>ABC123</tracking_number>
                    <ship_method>UPS</ship_method>
                    <service_level>Ground</service_level>
                </shipment>
                <shipment>
                    <ship_date>2015-04-04</ship_date>
                    <tracking_number>ABC123</tracking_number>
                    <ship_method>UPS</ship_method>
                    <service_level>Ground</service_level>
                </shipment>
            </tracking_numbers>
        </order>
    </order_status>
</toolkit>
XML;
            $xml = simplexml_load_string($responseXML);
            $response = [];

            $info = $xml->query_information;
            $response['information'] = [
                'found_orders' => (int)$info->found_orders,
                'shown_first' => (int)$info->shown_first,
                'shown_last' => (int)$info->shown_last,
                'remaining' => (int)$info->remaining
            ];

            $response['orders'] = [];
            if(isset($xml->order_status->order)) {
                if(count($xml->order_status->order) > 1) {
                    foreach($xml->order_status->order as $order) {
                        $item = [];
                        $item['status'] = (string)$order->status;
                        $item['shipment_status'] = (string)$order->shipment_status;
                        $item['fulfillment_status'] = (string)$order->fulfillment_status;
                        $item['dispatch_status'] = (string)$order->dispatch_status;
                        $item['ifs_order_no'] = (string)$order->ifs_order_no;
                        $item['client_no'] = (string)$order->client_no;
                        $item['batch_date'] = (string)$order->batch_date;
                        $item['fulfill_date'] = (string)$order->fulfill_date;
                        $item['dispatch_date'] = (string)$order->dispatch_date;
                        $item['parent_service_level'] = (string)$order->parent_service_level;
                        $item['tracking_numbers_count'] = (int)$order->tracking_numbers->tracking_count;
                        if($item['tracking_numbers_count'] > 0) {
                            $item['tracking_numbers'] = [];
                            if(count($order->tracking_numbers->shipment) > 1) {
                                foreach($order->tracking_numbers->shipment as $shipment) {
                                    $item['tracking_numbers'][] = [
                                        'ship_date' => (string)$shipment->ship_date,
                                        'tracking_number' => (string)$shipment->tracking_number,
                                        'ship_method' => (string)$shipment->ship_method,
                                        'service_level' => (string)$shipment->service_level
                                    ];
                                }
                            } else {
                                $shipment = $order->tracking_numbers->shipment;
                                $item['tracking_numbers'][] = [
                                    'ship_date' => (string)$shipment->ship_date,
                                    'tracking_number' => (string)$shipment->tracking_number,
                                    'ship_method' => (string)$shipment->ship_method,
                                    'service_level' => (string)$shipment->service_level
                                ];
                            }
                        }

                        $response['orders'][] = $item;
                    }
                }
            }

            return $response;
        }

    }

    /**
     * Provides a list of returns entered on a specific date range, query is based on the date the return was created in liteview.
     * This date may be different from what you receive in the return date,
     * for example a return is received today but it’s processed tomorrow, it will have the entry of tomorrow but with the return date of yesterday.
     * This is a paginated call limit of 500 per call, if results are over 500 you will need to use the skip parameter to skip previously downloaded results.
     * @param string $start_date format date as yyyy-mm-dd, the begining of the date range request, response will include this date starting at 0:00 hours
     * @param string $end_date format date as yyyy-mm-dd, the end of the date range request, response will include this date up 23:59 hours
     * @param int $skip 0 is the default, if you receive more than 500 results you will need to use this parameter to skip previously downloaded results
     * @return array
     */
    public function orderRetunsList($start_date, $end_date, $skip = 0) {

        if(!$this->debug) {

            $params = [
                $start_date,
                $end_date,
                $skip
            ];
            $responseXML = $this->sendRequest('order', 'returns/list', $params);

            $xml = simplexml_load_string($responseXML);
            $response = [];

            $info = $xml->query_information;
            $response['information'] = [
                'found_returns' => (int)$info->found_returns,
                'shown_first' => (int)$info->shown_first,
                'shown_last' => (int)$info->shown_last,
                'remaining' => (int)$info->remaining
            ];

            $returns = $xml->order_returns->return;
            $response['returns'] = [];
            if(count($returns) > 1) {
                foreach($returns as $return) {
                    $returnItem = [];
                    $returnItem['order'] = [
                        'return_date' => (string)$return->order_information->return_date,
                        'liteview_order_number' => (string)$return->order_information->liteview_order_number,
                        'client_order_number' => (string)$return->order_information->client_order_number,
                        'rma_no' => (string)$return->order_information->rma_no,
                        'original_ship_method' => (string)$return->order_information->original_ship_method,
                        'return_ship_method' => (string)$return->order_information->return_ship_method,
                        'return_code' => (string)$return->order_information->return_code,
                        'return_code_description' => (string)$return->order_information->return_code_description,
                        'return_additional_notes' => (string)$return->order_information->return_additional_notes,
                    ];

                    $returnItem['contact'] = [
                        'first_name' => (string)$return->contact_information->first_name,
                        'last_name' => (string)$return->contact_information->last_name,
                        'company_name' => (string)$return->contact_information->company_name,
                        'address_1' => (string)$return->contact_information->address_1,
                        'address_2' => (string)$return->contact_information->address_2,
                        'city' => (string)$return->contact_information->city,
                        'state' => (string)$return->contact_information->state,
                        'postal_code' => (string)$return->contact_information->postal_code,
                        'country' => (string)$return->contact_information->country,
                        'phone_number' => (string)$return->contact_information->phone_number,
                        'email' => (string)$return->contact_information->email,
                    ];

                    $return_items = $return->item_information->return_item;
                    $returnItem['items'] = [];
                    if(count($return_items) > 1) {
                        foreach($return_items as $return_item) {
                            $item = [];
                            $item['name'] = (string)$return_item->inventory_item;
                            $item['sku'] = (string)$return_item->inventory_sku;
                            $item['description'] = (string)$return_item->inventory_descrption;
                            $item['quantity_ordered'] = (int)$return_item->quantity_ordered;
                            $item['quantity_returned_good'] = (int)$return_item->quantity_returned_good;
                            $item['quantity_returned_bad'] = (int)$return_item->quantity_returned_bad;
                            $item['quantity_returned_rework'] = (int)$return_item->quantity_returned_rework;

                            $returnItem['items'][] = $item;
                        }
                    } else {
                        $return_item = $return_items;
                        $item = [];
                        $item['name'] = (string)$return_item->inventory_item;
                        $item['sku'] = (string)$return_item->inventory_sku;
                        $item['description'] = (string)$return_item->inventory_descrption;
                        $item['quantity_ordered'] = (int)$return_item->quantity_ordered;
                        $item['quantity_returned_good'] = (int)$return_item->quantity_returned_good;
                        $item['quantity_returned_bad'] = (int)$return_item->quantity_returned_bad;
                        $item['quantity_returned_rework'] = (int)$return_item->quantity_returned_rework;

                        $returnItem['items'][] = $item;
                    }

                    $response['returns'][] = $returnItem;
                }
            } else {
                $return = $returns;
                $returnItem = [];
                $returnItem['order'] = [
                    'return_date' => (string)$return->order_information->return_date,
                    'liteview_order_number' => (string)$return->order_information->liteview_order_number,
                    'client_order_number' => (string)$return->order_information->client_order_number,
                    'rma_no' => (string)$return->order_information->rma_no,
                    'original_ship_method' => (string)$return->order_information->original_ship_method,
                    'return_ship_method' => (string)$return->order_information->return_ship_method,
                    'return_code' => (string)$return->order_information->return_code,
                    'return_code_description' => (string)$return->order_information->return_code_description,
                    'return_additional_notes' => (string)$return->order_information->return_additional_notes,
                ];

                $returnItem['contact'] = [
                    'first_name' => (string)$return->contact_information->first_name,
                    'last_name' => (string)$return->contact_information->last_name,
                    'company_name' => (string)$return->contact_information->company_name,
                    'address_1' => (string)$return->contact_information->address_1,
                    'address_2' => (string)$return->contact_information->address_2,
                    'city' => (string)$return->contact_information->city,
                    'state' => (string)$return->contact_information->state,
                    'postal_code' => (string)$return->contact_information->postal_code,
                    'country' => (string)$return->contact_information->country,
                    'phone_number' => (string)$return->contact_information->phone_number,
                    'email' => (string)$return->contact_information->email,
                ];

                $return_items = $return->item_information->return_item;
                $returnItem['items'] = [];
                if(count($return_items) > 1) {
                    foreach($return_items as $return_item) {
                        $item = [];
                        $item['name'] = (string)$return_item->inventory_item;
                        $item['sku'] = (string)$return_item->inventory_sku;
                        $item['description'] = (string)$return_item->inventory_descrption;
                        $item['quantity_ordered'] = (int)$return_item->quantity_ordered;
                        $item['quantity_returned_good'] = (int)$return_item->quantity_returned_good;
                        $item['quantity_returned_bad'] = (int)$return_item->quantity_returned_bad;
                        $item['quantity_returned_rework'] = (int)$return_item->quantity_returned_rework;

                        $returnItem['items'][] = $item;
                    }
                } else {
                    $return_item = $return_items;
                    $item = [];
                    $item['name'] = (string)$return_item->inventory_item;
                    $item['sku'] = (string)$return_item->inventory_sku;
                    $item['description'] = (string)$return_item->inventory_descrption;
                    $item['quantity_ordered'] = (int)$return_item->quantity_ordered;
                    $item['quantity_returned_good'] = (int)$return_item->quantity_returned_good;
                    $item['quantity_returned_bad'] = (int)$return_item->quantity_returned_bad;
                    $item['quantity_returned_rework'] = (int)$return_item->quantity_returned_rework;

                    $returnItem['items'][] = $item;
                }

                $response['returns'][] = $returnItem;
            }

            return $response;

        } else {

            $responseXML = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<toolkit>
    <query_information>
        <found_returns>9</found_returns>
        <shown_first>1</shown_first>
        <shown_last>9</shown_last>
        <remaining>0</remaining>
    </query_information>
    <order_returns>
        <return>
            <order_information>
                <return_date>2012-01-05</return_date>
                <liteview_order_number>TES22200347</liteview_order_number>
                <client_order_number>199901-07</client_order_number>
                <rma_no/>
                <original_ship_method>FedEx Ground</original_ship_method>
                <return_ship_method/>
                <return_code>UNKN-UOP</return_code>
                <return_code_description>
                    UKNOWN REASON/WAS NOT
                    SPECIFIED Unopened Package
                </return_code_description>
                <return_additional_notes/>
            </order_information>
            <contact_information>
                <first_name>Johnny</first_name>
                <last_name>Appleseed</last_name>
                <company_name/>
                <address_1>20100 S vermont ave</address_1>
                <address_2>Ste 2</address_2>
                <address_3/>
                <city>Lawndale</city>
                <state/>
                <postal_code>90260</postal_code>
                <country>US</country>
                <phone_number>3102924500</phone_number>
                <email>pabloh@imaginefulfillment.com</email>
            </contact_information>
            <item_information>
                <return_item>
                    <inventory_item>TEST1001</inventory_item>
                    <inventory_sku>TEST1001</inventory_sku>
                    <inventory_descrption>Test Item Description</inventory_descrption>
                    <quantity_ordered/>
                    <quantity_returned_good/>
                    <quantity_returned_bad/>
                    <quantity_returned_rework/>
                </return_item>
            </item_information>
        </return>
        <return>
            <order_information>
                <return_date>2012-01-05</return_date>
                <liteview_order_number>TES22200347</liteview_order_number>
                <client_order_number>199901-07</client_order_number>
                <rma_no/>
                <original_ship_method>FedEx Ground</original_ship_method>
                <return_ship_method/>
                <return_code>UNKN-UOP</return_code>
                <return_code_description>
                    UKNOWN REASON/WAS NOT
                    SPECIFIED Unopened Package
                </return_code_description>
                <return_additional_notes/>
            </order_information>
            <contact_information>
                <first_name>Johnny</first_name>
                <last_name>Appleseed</last_name>
                <company_name/>
                <address_1>20100 S vermont ave</address_1>
                <address_2>Ste 2</address_2>
                <address_3/>
                <city>Lawndale</city>
                <state/>
                <postal_code>90260</postal_code>
                <country>US</country>
                <phone_number>3102924500</phone_number>
                <email>pabloh@imaginefulfillment.com</email>
            </contact_information>
            <item_information>
                <return_item>
                    <inventory_item>TEST1001</inventory_item>
                    <inventory_sku>TEST1001</inventory_sku>
                    <inventory_descrption>Test Item Description</inventory_descrption>
                    <quantity_ordered/>
                    <quantity_returned_good/>
                    <quantity_returned_bad/>
                    <quantity_returned_rework/>
                </return_item>
                <return_item>
                    <inventory_item>TEST1002</inventory_item>
                    <inventory_sku>TEST1002</inventory_sku>
                    <inventory_descrption>Test Item 2 Description</inventory_descrption>
                    <quantity_ordered/>
                    <quantity_returned_good/>
                    <quantity_returned_bad/>
                    <quantity_returned_rework/>
                </return_item>
            </item_information>
        </return>
    </order_returns>
</toolkit>
XML;

            $xml = simplexml_load_string($responseXML);
            $response = [];

            $info = $xml->query_information;
            $response['information'] = [
                'found_returns' => (int)$info->found_returns,
                'shown_first' => (int)$info->shown_first,
                'shown_last' => (int)$info->shown_last,
                'remaining' => (int)$info->remaining
            ];

            $returns = $xml->order_returns->return;
            $response['returns'] = [];
            if(count($returns) > 1) {
                foreach($returns as $return) {
                    $returnItem = [];
                    $returnItem['order'] = [
                        'return_date' => (string)$return->order_information->return_date,
                        'liteview_order_number' => (string)$return->order_information->liteview_order_number,
                        'client_order_number' => (string)$return->order_information->client_order_number,
                        'rma_no' => (string)$return->order_information->rma_no,
                        'original_ship_method' => (string)$return->order_information->original_ship_method,
                        'return_ship_method' => (string)$return->order_information->return_ship_method,
                        'return_code' => (string)$return->order_information->return_code,
                        'return_code_description' => (string)$return->order_information->return_code_description,
                        'return_additional_notes' => (string)$return->order_information->return_additional_notes,
                    ];

                    $returnItem['contact'] = [
                        'first_name' => (string)$return->contact_information->first_name,
                        'last_name' => (string)$return->contact_information->last_name,
                        'company_name' => (string)$return->contact_information->company_name,
                        'address_1' => (string)$return->contact_information->address_1,
                        'address_2' => (string)$return->contact_information->address_2,
                        'city' => (string)$return->contact_information->city,
                        'state' => (string)$return->contact_information->state,
                        'postal_code' => (string)$return->contact_information->postal_code,
                        'country' => (string)$return->contact_information->country,
                        'phone_number' => (string)$return->contact_information->phone_number,
                        'email' => (string)$return->contact_information->email,
                    ];

                    $return_items = $return->item_information->return_item;
                    $returnItem['items'] = [];
                    if(count($return_items) > 1) {
                        foreach($return_items as $return_item) {
                            $item = [];
                            $item['name'] = (string)$return_item->inventory_item;
                            $item['sku'] = (string)$return_item->inventory_sku;
                            $item['description'] = (string)$return_item->inventory_descrption;
                            $item['quantity_ordered'] = (int)$return_item->quantity_ordered;
                            $item['quantity_returned_good'] = (int)$return_item->quantity_returned_good;
                            $item['quantity_returned_bad'] = (int)$return_item->quantity_returned_bad;
                            $item['quantity_returned_rework'] = (int)$return_item->quantity_returned_rework;

                            $returnItem['items'][] = $item;
                        }
                    } else {
                        $return_item = $return_items;
                        $item = [];
                        $item['name'] = (string)$return_item->inventory_item;
                        $item['sku'] = (string)$return_item->inventory_sku;
                        $item['description'] = (string)$return_item->inventory_descrption;
                        $item['quantity_ordered'] = (int)$return_item->quantity_ordered;
                        $item['quantity_returned_good'] = (int)$return_item->quantity_returned_good;
                        $item['quantity_returned_bad'] = (int)$return_item->quantity_returned_bad;
                        $item['quantity_returned_rework'] = (int)$return_item->quantity_returned_rework;

                        $returnItem['items'][] = $item;
                    }

                    $response['returns'][] = $returnItem;
                }
            } else {
                $return = $returns;
                $returnItem = [];
                $returnItem['order'] = [
                    'return_date' => (string)$return->order_information->return_date,
                    'liteview_order_number' => (string)$return->order_information->liteview_order_number,
                    'client_order_number' => (string)$return->order_information->client_order_number,
                    'rma_no' => (string)$return->order_information->rma_no,
                    'original_ship_method' => (string)$return->order_information->original_ship_method,
                    'return_ship_method' => (string)$return->order_information->return_ship_method,
                    'return_code' => (string)$return->order_information->return_code,
                    'return_code_description' => (string)$return->order_information->return_code_description,
                    'return_additional_notes' => (string)$return->order_information->return_additional_notes,
                ];

                $returnItem['contact'] = [
                    'first_name' => (string)$return->contact_information->first_name,
                    'last_name' => (string)$return->contact_information->last_name,
                    'company_name' => (string)$return->contact_information->company_name,
                    'address_1' => (string)$return->contact_information->address_1,
                    'address_2' => (string)$return->contact_information->address_2,
                    'city' => (string)$return->contact_information->city,
                    'state' => (string)$return->contact_information->state,
                    'postal_code' => (string)$return->contact_information->postal_code,
                    'country' => (string)$return->contact_information->country,
                    'phone_number' => (string)$return->contact_information->phone_number,
                    'email' => (string)$return->contact_information->email,
                ];

                $return_items = $return->item_information->return_item;
                $returnItem['items'] = [];
                if(count($return_items) > 1) {
                    foreach($return_items as $return_item) {
                        $item = [];
                        $item['name'] = (string)$return_item->inventory_item;
                        $item['sku'] = (string)$return_item->inventory_sku;
                        $item['description'] = (string)$return_item->inventory_descrption;
                        $item['quantity_ordered'] = (int)$return_item->quantity_ordered;
                        $item['quantity_returned_good'] = (int)$return_item->quantity_returned_good;
                        $item['quantity_returned_bad'] = (int)$return_item->quantity_returned_bad;
                        $item['quantity_returned_rework'] = (int)$return_item->quantity_returned_rework;

                        $returnItem['items'][] = $item;
                    }
                } else {
                    $return_item = $return_items;
                    $item = [];
                    $item['name'] = (string)$return_item->inventory_item;
                    $item['sku'] = (string)$return_item->inventory_sku;
                    $item['description'] = (string)$return_item->inventory_descrption;
                    $item['quantity_ordered'] = (int)$return_item->quantity_ordered;
                    $item['quantity_returned_good'] = (int)$return_item->quantity_returned_good;
                    $item['quantity_returned_bad'] = (int)$return_item->quantity_returned_bad;
                    $item['quantity_returned_rework'] = (int)$return_item->quantity_returned_rework;

                    $returnItem['items'][] = $item;
                }

                $response['returns'][] = $returnItem;
            }

            return $response;

        }

    }

    /**
     * Provides a list of current inventory for the user’s account
     * @param int $skip 0 is the default, if you receive more than 500 results you will need to use this parameter to skip previously downloaded results
     * @return array
     */
    public function getInventoryList($skip = 0) {

        if(!$this->debug) {

            $params[] = $skip;
            $responseXML = $this->sendRequest('inventory', 'list', $params);

            $xml = simplexml_load_string($responseXML);
            $response = [];

            $info = $xml->query_information;
            $response['information'] = [
                'shown_first' => (int)$info->shown_first,
                'shown_last' => (int)$info->shown_last,
                'found_inventory' => (int)$info->found_inventory,
                'remaining_inventory' => (int)$info->remaining_inventory,
            ];

            $items = $xml->inventory_list->item;
            $response['items'] = [];
            if(count($items) > 1) {
                foreach($items as $item) {
                    $rItem = [];
                    $rItem['name'] = (string)$item->inventory_item;
                    $rItem['sku'] = (string)$item->inventory_sku;
                    $rItem['catalog_name'] = (string)$item->catalog_name;
                    $rItem['description'] = (string)$item->inventory_description;
                    $rItem['subsets'] = [];
                    if(count($item->inventory_subsets->subset) > 1){
                        foreach($item->inventory_subsets->subset as $subset) {
                            $sitem = [];
                            $sitem['name'] = (string)$subset->subset_name;
                            $sitem['quantity_on_hand'] = (int)$subset->quantity_on_hand;
                            $sitem['quantity_shipped'] = (int)$subset->quantity_shipped;
                            $sitem['quantity_received'] = (int)$subset->quantity_received;
                            $sitem['quantity_adjusted'] = (int)$subset->quantity_adjusted;

                            $rItem['subsets'][] = $sitem;
                        }
                    } else {
                        $subset = $item->inventory_subsets->subset;

                        $sitem = [];
                        $sitem['name'] = (string)$subset->subset_name;
                        $sitem['quantity_on_hand'] = (int)$subset->quantity_on_hand;
                        $sitem['quantity_shipped'] = (int)$subset->quantity_shipped;
                        $sitem['quantity_received'] = (int)$subset->quantity_received;
                        $sitem['quantity_adjusted'] = (int)$subset->quantity_adjusted;

                        $rItem['subsets'][] = $sitem;
                    }

                    $response['items'][] = $rItem;
                }
            } else {
                $item = $xml->inventory_list->item;

                $rItem = [];
                $rItem['name'] = (string)$item->inventory_item;
                $rItem['sku'] = (string)$item->inventory_sku;
                $rItem['catalog_name'] = (string)$item->catalog_name;
                $rItem['description'] = (string)$item->inventory_description;
                $rItem['subsets'] = [];
                if(count($item->inventory_subsets->subset) > 1){
                    foreach($item->inventory_subsets->subset as $subset) {
                        $sitem = [];
                        $sitem['name'] = (string)$subset->subset_name;
                        $sitem['quantity_on_hand'] = (int)$subset->quantity_on_hand;
                        $sitem['quantity_shipped'] = (int)$subset->quantity_shipped;
                        $sitem['quantity_received'] = (int)$subset->quantity_received;
                        $sitem['quantity_adjusted'] = (int)$subset->quantity_adjusted;

                        $rItem['subsets'][] = $sitem;
                    }
                } else {
                    $subset = $item->inventory_subsets->subset;

                    $sitem = [];
                    $sitem['name'] = (string)$subset->subset_name;
                    $sitem['quantity_on_hand'] = (int)$subset->quantity_on_hand;
                    $sitem['quantity_shipped'] = (int)$subset->quantity_shipped;
                    $sitem['quantity_received'] = (int)$subset->quantity_received;
                    $sitem['quantity_adjusted'] = (int)$subset->quantity_adjusted;

                    $rItem['subsets'][] = $sitem;
                }

                $response['items'][] = $rItem;
            }

            return $response;


        } else {

            $responseXML = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<toolkit>
    <query_information>
        <shown_first>939</shown_first>
        <shown_last>939</shown_last>
        <found_inventory>939</found_inventory>
        <remaining_inventory>0</remaining_inventory>
    </query_information>
    <inventory_list>
        <item>
            <inventory_item>9000</inventory_item>
            <inventory_sku>9000</inventory_sku>
            <catalog_name><![CDATA[Shirts]]></catalog_name>
            <inventory_description><![CDATA[Test 9000]]></inventory_description>
            <inventory_subsets>
                <subset>
                    <subset_name>Active</subset_name>
                    <quantity_on_hand>13</quantity_on_hand>
                    <quantity_shipped>0</quantity_shipped>
                    <quantity_received>0</quantity_received>
                    <quantity_adjusted>0</quantity_adjusted>
                </subset>
            </inventory_subsets>
        </item>
    </inventory_list>
</toolkit>
XML;

            $xml = simplexml_load_string($responseXML);
            $response = [];

            $info = $xml->query_information;
            $response['information'] = [
                'shown_first' => (int)$info->shown_first,
                'shown_last' => (int)$info->shown_last,
                'found_inventory' => (int)$info->found_inventory,
                'remaining_inventory' => (int)$info->remaining_inventory,
            ];

            $items = $xml->inventory_list->item;
            $response['items'] = [];
            if(count($items) > 1) {
                foreach($items as $item) {
                    $rItem = [];
                    $rItem['name'] = (string)$item->inventory_item;
                    $rItem['sku'] = (string)$item->inventory_sku;
                    $rItem['catalog_name'] = (string)$item->catalog_name;
                    $rItem['description'] = (string)$item->inventory_description;
                    $rItem['subsets'] = [];
                    if(count($item->inventory_subsets->subset) > 1){
                        foreach($item->inventory_subsets->subset as $subset) {
                            $sitem = [];
                            $sitem['name'] = (string)$subset->subset_name;
                            $sitem['quantity_on_hand'] = (int)$subset->quantity_on_hand;
                            $sitem['quantity_shipped'] = (int)$subset->quantity_shipped;
                            $sitem['quantity_received'] = (int)$subset->quantity_received;
                            $sitem['quantity_adjusted'] = (int)$subset->quantity_adjusted;

                            $rItem['subsets'][] = $sitem;
                        }
                    } else {
                        $subset = $item->inventory_subsets->subset;

                        $sitem = [];
                        $sitem['name'] = (string)$subset->subset_name;
                        $sitem['quantity_on_hand'] = (int)$subset->quantity_on_hand;
                        $sitem['quantity_shipped'] = (int)$subset->quantity_shipped;
                        $sitem['quantity_received'] = (int)$subset->quantity_received;
                        $sitem['quantity_adjusted'] = (int)$subset->quantity_adjusted;

                        $rItem['subsets'][] = $sitem;
                    }

                    $response['items'][] = $rItem;
                }
            } else {
                $item = $xml->inventory_list->item;

                $rItem = [];
                $rItem['name'] = (string)$item->inventory_item;
                $rItem['sku'] = (string)$item->inventory_sku;
                $rItem['catalog_name'] = (string)$item->catalog_name;
                $rItem['description'] = (string)$item->inventory_description;
                $rItem['subsets'] = [];
                if(count($item->inventory_subsets->subset) > 1){
                    foreach($item->inventory_subsets->subset as $subset) {
                        $sitem = [];
                        $sitem['name'] = (string)$subset->subset_name;
                        $sitem['quantity_on_hand'] = (int)$subset->quantity_on_hand;
                        $sitem['quantity_shipped'] = (int)$subset->quantity_shipped;
                        $sitem['quantity_received'] = (int)$subset->quantity_received;
                        $sitem['quantity_adjusted'] = (int)$subset->quantity_adjusted;

                        $rItem['subsets'][] = $sitem;
                    }
                } else {
                    $subset = $item->inventory_subsets->subset;

                    $sitem = [];
                    $sitem['name'] = (string)$subset->subset_name;
                    $sitem['quantity_on_hand'] = (int)$subset->quantity_on_hand;
                    $sitem['quantity_shipped'] = (int)$subset->quantity_shipped;
                    $sitem['quantity_received'] = (int)$subset->quantity_received;
                    $sitem['quantity_adjusted'] = (int)$subset->quantity_adjusted;

                    $rItem['subsets'][] = $sitem;
                }

                $response['items'][] = $rItem;
            }

            return $response;
        }

    }

    /**
     * Provides detail information for a specific inventory item including transactions and subset quantity on hand.
     * @param string $item This field is optional however it must be included in the request path with null value. Either item or sku must passed.
     * @param string $sku This field is optional however it must be included in the request path with null value. Either item or sku must be passed.
     * @return array
     * @throws HttpException
     */
    public function getInventoryItem($item = null, $sku = null) {

        if(!$this->debug) {

            if($item == null && $sku == null) {
                throw new HttpException(403, "Invalid request");
            }

            $params = [
                $item,
                $sku
            ];
            $responseXML = $this->sendRequest('inventory', 'item', 'GET', $params);

            $xml = simplexml_load_string($responseXML);
            $response = [];

            $details = $xml->inventory_item->item_details;
            $response['details'] = [
                'number' => (string)$details->item_no,
                'sku' => (string)$details->item_sku,
                'description' => (string)$details->item_desscription,
                'inventory_status' => (string)$details->item_inventory_status,
                'catalog' => (string)$details->item_catalog,
                'type' => (string)$details->item_type,
                'country_of_manufacture' => (string)$details->item_country_of_manufacture,
                'upc_number' => (string)$details->item_upc_number,
                'retail_cost' => (float)$details->item_retail_cost,
                'net_cost' => (float)$details->item_net_cost,
                'weight' => (float)$details->item_weight,
                'weight_type' => (string)$details->item_weight_type,
                'length' => (float)$details->item_length,
                'width' => (float)$details->item_width,
                'height' => (float)$details->item_height,
                'size_type' => (string)$details->item_size_type
            ];

            if(isset($xml->inventory_item->inventory_subsets)) {
                $response['inventory_subsets'] = [];
                if(count($xml->inventory_item->inventory_subsets->subset) > 1) {
                    foreach($xml->inventory_item->inventory_subsets->subset as $subset) {
                        $item = [];
                        $item['subset_name'] = (string)$subset->subset_name;
                        $item['quantity_on_hand'] = (int)$subset->quantity_on_hand;
                        $item['quantity_shipped'] = (int)$subset->quantity_shipped;
                        $item['quantity_received'] = (int)$subset->quantity_received;
                        $item['quantity_adjusted'] = (int)$subset->quantity_adjusted;
                        $response['inventory_subsets'][] = $item;
                    }
                } else {
                    $subset = $xml->inventory_item->inventory_subsets->subset;
                    $item = [];
                    $item['name'] = (string)$subset->subset_name;
                    $item['quantity_on_hand'] = (int)$subset->quantity_on_hand;
                    $item['quantity_shipped'] = (int)$subset->quantity_shipped;
                    $item['quantity_received'] = (int)$subset->quantity_received;
                    $item['quantity_adjusted'] = (int)$subset->quantity_adjusted;
                    $response['inventory_subsets'][] = $item;
                }
            }

            if(isset($xml->inventory_item->inventory_min_max)) {
                $response['inventory_min_max'] = [];
                if(count($xml->inventory_item->inventory_min_max->subset) > 1) {
                    foreach($xml->inventory_item->inventory_min_max->subset as $subset) {
                        $item = [];
                        $item['subset_name'] = (string)$subset->subset_name;
                        $item['minimum_level'] = (int)$subset->minimum_level;
                        $item['minimum_alert_send'] = (int)$subset->minimum_alert_send;
                        $item['maximum_level'] = (int)$subset->maximum_level;
                        $item['maximum_alert_send'] = (int)$subset->maximum_alert_send;

                        $response['inventory_min_max'][] = $item;
                    }
                } else {
                    $subset = $xml->inventory_item->inventory_min_max->subset;
                    $item = [];
                    $item['subset_name'] = (string)$subset->subset_name;
                    $item['minimum_level'] = (int)$subset->minimum_level;
                    $item['minimum_alert_send'] = (int)$subset->minimum_alert_send;
                    $item['maximum_level'] = (int)$subset->maximum_level;
                    $item['maximum_alert_send'] = (int)$subset->maximum_alert_send;

                    $response['inventory_min_max'][] = $item;
                }
            }

            if(isset($xml->inventory_item->inventory_apparel)) {
                $apparel = $xml->inventory_item->inventory_apparel;
                $response['inventory_apparel'] = [
                    'apparel_text_size' => (string)$apparel->apparel_text_size,
                    'apparel_text_color' => (string)$apparel->apparel_text_color,
                    'apparel_composition' => (string)$apparel->apparel_composition,
                    'apparel_style' => (string)$apparel->apparel_style,
                    'apparel_season' => (string)$apparel->apparel_season,
                    'apparel_shoe_width' => (string)$apparel->apparel_shoe_width,
                    'apparel_number_size' => (int)$apparel->apparel_number_size,
                    'apparel_gender' => (string)$apparel->apparel_gender,
                ];
            }

            if(isset($xml->inventory_item->inventory_kit_components)) {
                $response['kit_components'] = [];
                if(count($xml->inventory_item->inventory_kit_components->component) > 1) {
                    foreach($xml->inventory_item->inventory_kit_components->component as $component) {
                        $item = [];
                        $item['item_number'] = (string)$component->item_no;
                        $item['item_sku'] = (string)$component->item_sku;
                        $item['quantity'] = (int)$component->qty;

                        $response['kit_components'][] = $item;
                    }
                } else {
                    $component = $xml->inventory_item->inventory_kit_components->component;
                    $item = [];
                    $item['item_number'] = (string)$component->item_no;
                    $item['item_sku'] = (string)$component->item_sku;
                    $item['quantity'] = (int)$component->qty;

                    $response['kit_components'][] = $item;
                }
            }

            if(isset($xml->inventory_item->inventory_transactions)) {
                $response['inventory_transactions'] = [];
                if(count($xml->inventory_item->inventory_transactions->transaction) > 1) {
                    foreach($xml->inventory_item->inventory_transactions->transaction as $transaction) {
                        $item = [];
                        $item['type'] = (string)$transaction->type;
                        $item['date_time'] = (string)$transaction->date_time;
                        $item['subset'] = (string)$transaction->subset;
                        $item['description'] = (string)$transaction->description;
                        $item['quantity'] = (int)$transaction->quantity;
                        $item['performed_by'] = (string)$transaction->performed_by;
                        $item['quantity_on_hand_before'] = (int)$transaction->quantity_on_hand_before;
                        $item['quantity_on_hand_after'] = (int)$transaction->quantity_on_hand_after;

                        $response['inventory_transactions'][] = $item;
                    }
                } else {
                    $transaction = $xml->inventory_item->inventory_transactions->transaction;
                    $item = [];
                    $item['type'] = (string)$transaction->type;
                    $item['date_time'] = (string)$transaction->date_time;
                    $item['subset'] = (string)$transaction->subset;
                    $item['description'] = (string)$transaction->description;
                    $item['quantity'] = (int)$transaction->quantity;
                    $item['performed_by'] = (string)$transaction->performed_by;
                    $item['quantity_on_hand_before'] = (int)$transaction->quantity_on_hand_before;
                    $item['quantity_on_hand_after'] = (int)$transaction->quantity_on_hand_after;

                    $response['inventory_transactions'][] = $item;
                }
            }

            return $response;

        } else {

            $responseXML = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<toolkit>
    <inventory_item>
        <item_details>
            <item_no>68</item_no>
            <item_sku>68</item_sku>
            <item_description><![CDATA[Clinique 7 Day Scrub
                Cream Rinse Off Formula]]></item_description>
            <item_inventory_status><![CDATA[Active]]></item_inventory_status>
            <item_catalog><![CDATA[default]]></item_catalog>
            <item_type><![CDATA[STD]]></item_type>
            <item_country_of_manufacture/>
            <item_upc_number/>
            <item_retail_cost>0</item_retail_cost>
            <item_net_cost>0</item_net_cost>
            <item_weight>0.10625</item_weight>
            <item_weight_type><![CDATA[Pounds]]></item_weight_type>
            <item_length>0</item_length>
            <item_width>0</item_width>
            <item_height>0</item_height>
            <item_size_type><![CDATA[Inches]]></item_size_type>
        </item_details>
        <inventory_subsets>
            <subset>
                <subset_name>Bad/Damaged</subset_name>
                <quantity_on_hand>0</quantity_on_hand>
                <quantity_shipped>0</quantity_shipped>
                <quantity_received>0</quantity_received>
                <quantity_adjusted>0</quantity_adjusted>
            </subset>
            <subset>
                <subset_name>Active</subset_name>
                <quantity_on_hand>5</quantity_on_hand>
                <quantity_shipped>0</quantity_shipped>
                <quantity_received>5</quantity_received>
                <quantity_adjusted>0</quantity_adjusted>
            </subset>
            <subset>
                <subset_name>Quality Control Hold</subset_name>
                <quantity_on_hand>0</quantity_on_hand>
                <quantity_shipped>0</quantity_shipped>
                <quantity_received>0</quantity_received>
                <quantity_adjusted>0</quantity_adjusted>
            </subset>
            <subset>
                <subset_name>Reworkables</subset_name>
                <quantity_on_hand>0</quantity_on_hand>
                <quantity_shipped>0</quantity_shipped>
                <quantity_received>0</quantity_received>
                <quantity_adjusted>0</quantity_adjusted>
            </subset>
            <subset>
                <subset_name>On Hold</subset_name>
                <quantity_on_hand>0</quantity_on_hand>
                <quantity_shipped>0</quantity_shipped>
                <quantity_received>0</quantity_received>
                <quantity_adjusted>0</quantity_adjusted>
            </subset>
        </inventory_subsets>
        <inventory_min_max>
            <subset>
                <subset_name>Bad/Damaged</subset_name>
                <minimum_level>0</minimum_level>
                <minimum_alert_send/>
                <maximum_level>0</maximum_level>
                <maximum_alert_send/>
            </subset>
            <subset>
                <subset_name>Active</subset_name>
                <minimum_level>50</minimum_level>
                <minimum_alert_send>1</minimum_alert_send>
                <maximum_level>500</maximum_level>
                <maximum_alert_send>1</maximum_alert_send>
            </subset>
            <subset>
                <subset_name>Quality Control Hold</subset_name>
                <minimum_level>0</minimum_level>
                <minimum_alert_send/>
                <maximum_level>0</maximum_level>
                <maximum_alert_send/>
            </subset>
            <subset>
                <subset_name>Reworkables</subset_name>
                <minimum_level>0</minimum_level>
                <minimum_alert_send/>
                <maximum_level>0</maximum_level>
                <maximum_alert_send/>
            </subset>
            <subset>
                <subset_name>On Hold</subset_name>
                <minimum_level>0</minimum_level>
                <minimum_alert_send/>
                <maximum_level>0</maximum_level>
                <maximum_alert_send/>
            </subset>
        </inventory_min_max>
        <inventory_apparel>
            <apparel_text_size>XXL</apparel_text_size>
            <apparel_text_color/>
            <apparel_composition/>
            <apparel_style/>
            <apparel_season/>
            <apparel_shoe_width/>
            <apparel_number_size>0</apparel_number_size>
            <apparel_gender/>
        </inventory_apparel>
        <inventory_transactions>
            <transaction>
                <type>ADD</type>
                <date_time>2012-02-03 13:01:27.396142-08</date_time>
                <subset>Active</subset>
                <description><![CDATA[ADD]]></description>
                <quantity>5</quantity>
                <performed_by>dannys</performed_by>
                <quantity_on_hand_before>0</quantity_on_hand_before>
                <quantity_on_hand_after>5</quantity_on_hand_after>
            </transaction>
        </inventory_transactions>
    </inventory_item>
</toolkit>
XML;

            $xml = simplexml_load_string($responseXML);
            $response = [];

            $details = $xml->inventory_item->item_details;
            $response['details'] = [
                'number' => (string)$details->item_no,
                'sku' => (string)$details->item_sku,
                'description' => (string)$details->item_desscription,
                'inventory_status' => (string)$details->item_inventory_status,
                'catalog' => (string)$details->item_catalog,
                'type' => (string)$details->item_type,
                'country_of_manufacture' => (string)$details->item_country_of_manufacture,
                'upc_number' => (string)$details->item_upc_number,
                'retail_cost' => (float)$details->item_retail_cost,
                'net_cost' => (float)$details->item_net_cost,
                'weight' => (float)$details->item_weight,
                'weight_type' => (string)$details->item_weight_type,
                'length' => (float)$details->item_length,
                'width' => (float)$details->item_width,
                'height' => (float)$details->item_height,
                'size_type' => (string)$details->item_size_type
            ];

            if(isset($xml->inventory_item->inventory_subsets)) {
                $response['inventory_subsets'] = [];
                if(count($xml->inventory_item->inventory_subsets->subset) > 1) {
                    foreach($xml->inventory_item->inventory_subsets->subset as $subset) {
                        $item = [];
                        $item['subset_name'] = (string)$subset->subset_name;
                        $item['quantity_on_hand'] = (int)$subset->quantity_on_hand;
                        $item['quantity_shipped'] = (int)$subset->quantity_shipped;
                        $item['quantity_received'] = (int)$subset->quantity_received;
                        $item['quantity_adjusted'] = (int)$subset->quantity_adjusted;
                        $response['inventory_subsets'][] = $item;
                    }
                } else {
                    $subset = $xml->inventory_item->inventory_subsets->subset;
                    $item = [];
                    $item['name'] = (string)$subset->subset_name;
                    $item['quantity_on_hand'] = (int)$subset->quantity_on_hand;
                    $item['quantity_shipped'] = (int)$subset->quantity_shipped;
                    $item['quantity_received'] = (int)$subset->quantity_received;
                    $item['quantity_adjusted'] = (int)$subset->quantity_adjusted;
                    $response['inventory_subsets'][] = $item;
                }
            }

            if(isset($xml->inventory_item->inventory_min_max)) {
                $response['inventory_min_max'] = [];
                if(count($xml->inventory_item->inventory_min_max->subset) > 1) {
                    foreach($xml->inventory_item->inventory_min_max->subset as $subset) {
                        $item = [];
                        $item['subset_name'] = (string)$subset->subset_name;
                        $item['minimum_level'] = (int)$subset->minimum_level;
                        $item['minimum_alert_send'] = (int)$subset->minimum_alert_send;
                        $item['maximum_level'] = (int)$subset->maximum_level;
                        $item['maximum_alert_send'] = (int)$subset->maximum_alert_send;

                        $response['inventory_min_max'][] = $item;
                    }
                } else {
                    $subset = $xml->inventory_item->inventory_min_max->subset;
                    $item = [];
                    $item['subset_name'] = (string)$subset->subset_name;
                    $item['minimum_level'] = (int)$subset->minimum_level;
                    $item['minimum_alert_send'] = (int)$subset->minimum_alert_send;
                    $item['maximum_level'] = (int)$subset->maximum_level;
                    $item['maximum_alert_send'] = (int)$subset->maximum_alert_send;

                    $response['inventory_min_max'][] = $item;
                }
            }

            if(isset($xml->inventory_item->inventory_apparel)) {
                $apparel = $xml->inventory_item->inventory_apparel;
                $response['inventory_apparel'] = [
                    'apparel_text_size' => (string)$apparel->apparel_text_size,
                    'apparel_text_color' => (string)$apparel->apparel_text_color,
                    'apparel_composition' => (string)$apparel->apparel_composition,
                    'apparel_style' => (string)$apparel->apparel_style,
                    'apparel_season' => (string)$apparel->apparel_season,
                    'apparel_shoe_width' => (string)$apparel->apparel_shoe_width,
                    'apparel_number_size' => (int)$apparel->apparel_number_size,
                    'apparel_gender' => (string)$apparel->apparel_gender,
                ];
            }

            if(isset($xml->inventory_item->inventory_kit_components)) {
                $response['kit_components'] = [];
                if(count($xml->inventory_item->inventory_kit_components->component) > 1) {
                    foreach($xml->inventory_item->inventory_kit_components->component as $component) {
                        $item = [];
                        $item['item_number'] = (string)$component->item_no;
                        $item['item_sku'] = (string)$component->item_sku;
                        $item['quantity'] = (int)$component->qty;

                        $response['kit_components'][] = $item;
                    }
                } else {
                    $component = $xml->inventory_item->inventory_kit_components->component;
                    $item = [];
                    $item['item_number'] = (string)$component->item_no;
                    $item['item_sku'] = (string)$component->item_sku;
                    $item['quantity'] = (int)$component->qty;

                    $response['kit_components'][] = $item;
                }
            }

            if(isset($xml->inventory_item->inventory_transactions)) {
                $response['inventory_transactions'] = [];
                if(count($xml->inventory_item->inventory_transactions->transaction) > 1) {
                    foreach($xml->inventory_item->inventory_transactions->transaction as $transaction) {
                        $item = [];
                        $item['type'] = (string)$transaction->type;
                        $item['date_time'] = (string)$transaction->date_time;
                        $item['subset'] = (string)$transaction->subset;
                        $item['description'] = (string)$transaction->description;
                        $item['quantity'] = (int)$transaction->quantity;
                        $item['performed_by'] = (string)$transaction->performed_by;
                        $item['quantity_on_hand_before'] = (int)$transaction->quantity_on_hand_before;
                        $item['quantity_on_hand_after'] = (int)$transaction->quantity_on_hand_after;

                        $response['inventory_transactions'][] = $item;
                    }
                } else {
                    $transaction = $xml->inventory_item->inventory_transactions->transaction;
                    $item = [];
                    $item['type'] = (string)$transaction->type;
                    $item['date_time'] = (string)$transaction->date_time;
                    $item['subset'] = (string)$transaction->subset;
                    $item['description'] = (string)$transaction->description;
                    $item['quantity'] = (int)$transaction->quantity;
                    $item['performed_by'] = (string)$transaction->performed_by;
                    $item['quantity_on_hand_before'] = (int)$transaction->quantity_on_hand_before;
                    $item['quantity_on_hand_after'] = (int)$transaction->quantity_on_hand_after;

                    $response['inventory_transactions'][] = $item;
                }
            }

            return $response;
        }

    }

    /**
     * Submits and creates new images for an existing inventory item.
     * array['fields']
     *          ['fieldName']
     *              ['item'] string Inventory Item for the item in the purchase order, must exist in liteview
     *              ['item_sku'] string Inventory SKU for the item in the purchase order, must exist in liteview
     *              ['image_default'] boolean true/false or 1/0 make sure lowercase, validation will fail any other entry
     *              ['image_filename'] string Filename of the image, this is what liteview will save the image as in its internal server after decoding the image.
     *              ['image'] base64Binary Encoded image to upload as string. Liteview will convert and save to its server from the base64 code.
     * @param array $params
     * @return string
     */
    public function uploadInventoryImage($params) {

        if(!$this->debug) {
            $params = [
                'submit_inventory_image' => [
                    'inventory' => [
                        'item' => [
                            'inventory_item' => $params['item'],
                            'inventory_sku' => $params['item_sku'],
                            'image_default' => $params['image_default'],
                            'image_filename' => $params['image_filename'],
                            'image_picture' => $params['image']
                        ]
                    ]
                ]
            ];
            $responseXML = $this->sendRequest('inventory', 'image', 'POST', $params);
            $xml = simplexml_load_string($responseXML);
            $status = (string)$xml->submit_inventory_image->inventory->item->submit_status;

            return $status;
        } else {
            $params = [
                'submit_inventory_image' => [
                    'inventory' => [
                        'item' => [
                            'inventory_item' => $params['item'],
                            'inventory_sku' => $params['item_sku'],
                            'image_default' => $params['image_default'],
                            'image_filename' => $params['image_filename'],
                            'image_picture' => $params['image']
                        ]
                    ]
                ]
            ];

            $responseXML = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<toolkit>
    <submit_inventory_image>
        <inventory>
            <item>
                <submit_status>Successfully uploaded image to liteview. preview in the Liteview Web UI</submit_status>
            </item>
        </inventory>
    </submit_inventory_image>
</toolkit>
XML;

            $xml = simplexml_load_string($responseXML);
            $status = (string)$xml->submit_inventory_image->inventory->item->submit_status;

            return $status;
        }

    }

    /**
     * Submits and creates a new vendor in Liteview Address Book
     * array['fields']
     *          ['fieldName']
     *              ['lookup_id'] string Lookup id or code that will be used to identify this vendor in liteview uniquely, This code will be used when submitting a purchase order into liteview
     *              ['company_name'] string Company name of the vendor (Misc Graphics)
     *              ['contact'] Contact name at the vendor for questions about delivery appointments etc..(john doe)
     *              ['address1'] string Vendor address 1
     *              ['address2'] string Vendor address 2
     *              ['city'] string Vendor city
     *              ['state'] string Vendor state
     *              ['zip'] string Vendor zip code
     *              ['country'] string Vendor country code, 2 letter code that can be obtaind by doing country list call. Throws an error if is not valid.
     *              ['phone'] string Vendor phone
     *              ['email'] string Vendor email
     * @param arrray $params
     * @return string
     */
    public function addVendor($params) {

        if(!$this->debug) {
            $params = [
                'submit_po_vendor' => [
                    'vendor_information' => [
                        'vendor' => [
                            'lookup_id' => $params['lookup_id'],
                            'company_name' => $params['company_name'],
                            'contact' => $params['contact'],
                            'address1' => $params['address1'],
                            'address2' => $params['address2'],
                            'city' => $params['city'],
                            'state' => $params['state'],
                            'zip' => $params['zip'],
                            'country' => $params['country'],
                            'phone' => $params['phone'],
                            'email' => $params['email']
                        ]
                    ]
                ]
            ];
            $responseXML = $this->sendRequest('purchase/order', 'vendor/submit', 'POST', $params);
            $xml = simplexml_load_string($responseXML);
            $status = (string)$xml->submit_po_vendor->vendor_information->vendor->submit_status;

            return $status;

        } else {

            $params = [
                'submit_po_vendor' => [
                    'vendor_information' => [
                        'vendor' => [
                            'lookup_id' => $params['lookup_id'],
                            'company_name' => $params['company_name'],
                            'contact' => $params['contact'],
                            'address1' => $params['address1'],
                            'address2' => $params['address2'],
                            'city' => $params['city'],
                            'state' => $params['state'],
                            'zip' => $params['zip'],
                            'country' => $params['country'],
                            'phone' => $params['phone'],
                            'email' => $params['email']
                        ]
                    ]
                ]
            ];

            $responseXML = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<toolkit>
    <submit_po_vendor>
        <vendor_information>
            <vendor>
                <submit_status>Successfully created vendor in Liteview PO System</submit_status>
            </vendor>
        </vendor_information>
    </submit_po_vendor>
</toolkit>
XML;
            $xml = simplexml_load_string($responseXML);
            $status = (string)$xml->submit_po_vendor->vendor_information->vendor->submit_status;

            return $status;
        }

    }

    /**
     * Provides a list of the vendors from the Liteview address book, that can be used to create purchase orders
     * @return array
     */
    public function getVendorList() {

        if(!$this->debug) {

            $responseXML = $this->sendRequest('purchase/order', 'vendor/list', 'GET');

            $xml = simplexml_load_string($responseXML);
            $response = [];

            if(isset($xml->po_vendor_list->vendor)) {
                if(count($xml->po_vendor_list->vendor) > 1) {
                    foreach($xml->po_vendor_list->vendor as $vendor) {
                        $response[] = [
                            'lookup_id' => (string)$vendor->lookup_id,
                            'company_name' => (string)$vendor->company_name,
                            'contact' => (string)$vendor->contact,
                            'address1' => (string)$vendor->address1,
                            'address2' => (string)$vendor->address2,
                            'city' => (string)$vendor->city,
                            'state' => (string)$vendor->state,
                            'zip' => (string)$vendor->zip,
                            'country' => (string)$vendor->country,
                            'phone' => (string)$vendor->phone,
                            'email' => (string)$vendor->email,
                        ];
                    }
                } else {
                    $vendor = $xml->po_vendor_list->vendor;
                    $response[] = [
                        'lookup_id' => (string)$vendor->lookup_id,
                        'company_name' => (string)$vendor->company_name,
                        'contact' => (string)$vendor->contact,
                        'address1' => (string)$vendor->address1,
                        'address2' => (string)$vendor->address2,
                        'city' => (string)$vendor->city,
                        'state' => (string)$vendor->state,
                        'zip' => (string)$vendor->zip,
                        'country' => (string)$vendor->country,
                        'phone' => (string)$vendor->phone,
                        'email' => (string)$vendor->email,
                    ];
                }
            }

            return $response;
        } else {

            $responseXML = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<toolkit>
    <po_vendor_list>
        <vendor>
            <lookup_id>1002</lookup_id>
            <company_name>test</company_name>
            <contact/>
            <address1>20100 S Vermont</address1>
            <address2/>
            <city>Torrance</city>
            <state>CA</state>
            <zip>90502</zip>
            <country>US</country>
            <phone>310-217-4600</phone>
            <email>dannys@imaginefulfillment.com</email>
        </vendor>
        <vendor>
            <lookup_id>1001</lookup_id>
            <company_name>Doe Pharmacuticals</company_name>
            <contact/>
            <address1>1234 Anywhere Street</address1>
            <address2/>
            <city>Torrance</city>
            <state>CA</state>
            <zip>90260</zip>
            <country>US</country>
            <phone>3102174600</phone>
            <email>pabloh@imaginefulfillment.com</email>
        </vendor>
        <vendor>
            <lookup_id>test1001</lookup_id>
            <company_name>No Pharmacuticals</company_name>
            <contact/>
            <address1>20100 S Vermont Ave</address1>
            <address2/>
            <city>Torrance</city>
            <state>CA</state>
            <zip>90260</zip>
            <country>US</country>
            <phone>3102174600</phone>
            <email>pabloh@imaginefulfillment.com</email>
        </vendor>
    </po_vendor_list>
</toolkit>

XML;
            $xml = simplexml_load_string($responseXML);
            $response = [];

            if(isset($xml->po_vendor_list->vendor)) {
                if(count($xml->po_vendor_list->vendor) > 1) {
                    foreach($xml->po_vendor_list->vendor as $vendor) {
                        $response[] = [
                            'lookup_id' => (string)$vendor->lookup_id,
                            'company_name' => (string)$vendor->company_name,
                            'contact' => (string)$vendor->contact,
                            'address1' => (string)$vendor->address1,
                            'address2' => (string)$vendor->address2,
                            'city' => (string)$vendor->city,
                            'state' => (string)$vendor->state,
                            'zip' => (string)$vendor->zip,
                            'country' => (string)$vendor->country,
                            'phone' => (string)$vendor->phone,
                            'email' => (string)$vendor->email,
                        ];
                    }
                } else {
                    $vendor = $xml->po_vendor_list->vendor;
                    $response[] = [
                        'lookup_id' => (string)$vendor->lookup_id,
                        'company_name' => (string)$vendor->company_name,
                        'contact' => (string)$vendor->contact,
                        'address1' => (string)$vendor->address1,
                        'address2' => (string)$vendor->address2,
                        'city' => (string)$vendor->city,
                        'state' => (string)$vendor->state,
                        'zip' => (string)$vendor->zip,
                        'country' => (string)$vendor->country,
                        'phone' => (string)$vendor->phone,
                        'email' => (string)$vendor->email,
                    ];
                }
            }

            return $response;
        }

    }

    /**
     * Submits and creates a new vendor in Liteview Address Book, it now also sends e-mail confirmation of creation to liteview user and also subscribes to alerts default for the account
     * array['fields']
     *          ['fieldName']
     *              ['po_number'] string Purchase order for the po, this number must be unique. It can only be used once per account
     *              ['po_status'] string PO Status, can either Open or Closed, if closed it can later be opened from web user interface
     *              ['confirmed_total_pallet_count'] integer If known and be confirmed the total number of pallets for the purchase order
     *              ['estimated_total_pallet_count'] integer If not known and can be estimated the total number of pallets for the purchase order
     *              ['confirmed_arrival_date'] date If known and confirmed the arrival date for the entire purchase order Format: yyyy-mm-dd i.e. 2012-09-19
     *              ['estimated_arrival_date'] date If not known and can be estimated arrival date for the entire purchase order Format: yyyy-mm-dd i.e. 2012-09-19
     *              ['liteview_user'] string The liteview user that will be the contact for the purchase order, this needs to be a valid liteview user, Liteview will send e-mails to this user when items are received and other events are triggered
     *              ['vendor_lookup_id'] string The vendor lookup id for the vendor this purchase order belongs to. The vendor must already be created before it can be used here.
     *              ['items'] array Contains the purchase order items
     *                  array['fields']
     *                          ['fieldName']
     *                              ['inventory_item'] string Inventory Item for the item in the purchase order, must exist in liteview
     *                              ['inventory_sku'] string Inventory SKU for the item in the purchase order, must exist in liteview
     *                              ['po_qty'] integer Item quantity. Must be greater than 1.
     *                              ['confirmed_carton_count'] integer If known and confirmed the total carton count for the sku
     *                              ['estimated_carton_count'] integer If not known and can be estimated the carton count for the sku
     *                              ['confirmed_pallet_count'] integer If known and confirmed the total number of pallets for the sku
     *                              ['estimated_pallet_count'] integer If not known and can be estimated the number of pallets for the sku
     *                              ['confirmed_arrival_date'] date If known and confirmed the arrival date for this sku, if your po will be received across different dates use this field Format: yyyy-mm-dd i.e. 2012-09-19
     *                              ['estimated_arrival_date'] date If not known and can be estimated the arrival date for this sku, if your po will be received across different dates use this field. Format: yyyy-mm-dd i.e. 2012-09-19
     * @param $params
     * @return array
     */
    public function submitPurchaseOrder($params) {

        if(!$this->debug) {

            $paramsXML = [
                'submit_po' => [
                    'purchase_order' => [
                        'information' => [
                            'po_number' => $params['po_number'],
                            'po_status' => $params['po_status'],
                            'confirmed_total_pallet_count' => $params['confirmed_total_pallet_count'],
                            'estimated_total_pallet_count' => $params['estimated_total_pallet_count'],
                            'confirmed_arrival_date' => $params['confirmed_arrival_date'],
                            'estimated_arrival_date' => $params['estimated_arrival_date'],
                            'liteview_user' => $params['liteview_user'],
                            'vendor_lookup_id' => $params['vendor_lookup_id'],
                        ],
                        'items' => []
                    ]
                ]
            ];

            if(count($params['items'])) {
                foreach($params['items'] as $item) {
                    $paramsXML['submit_po']['purchase_order']['items'][] = [
                        'inventory_item' => $item['inventory_item'],
                        'inventory_sku' => $item['inventory_sku'],
                        'po_qty' => $item['po_qty'],
                        'confirmed_carton_count' => $item['confirmed_carton_count'],
                        'estimated_carton_count' => $item['estimated_carton_count'],
                        'confirmed_pallet_count' => $item['confirmed_pallet_count'],
                        'estimated_pallet_count' => $item['estimated_pallet_count'],
                        'confirmed_arrival_date' => $item['confirmed_arrival_date'],
                        'estimated_arrival_date' => $item['estimated_arrival_date'],
                    ];
                }
            }

            $responseXML = $this->sendRequest('', '', 'POST', $paramsXML);
            $xml = simplexml_load_string($responseXML);
            $info = $xml->submit_po->purchase_order->information;
            $response = [
                'liteview_asn_id' => (string)$info->liteview_asn_id,
                'po_number' => (string)$info->po_number,
                'po_status' => (string)$info->po_status,
                'vendor_lookup_id' => (string)$info->vendor_lookup_id,
                'po_document_pdf' => (string)$info->po_document_pdf
            ];

            return $response;

        } else {

            $paramsXML = [
                'submit_po' => [
                    'purchase_order' => [
                        'information' => [
                            'po_number' => $params['po_number'],
                            'po_status' => $params['po_status'],
                            'confirmed_total_pallet_count' => $params['confirmed_total_pallet_count'],
                            'estimated_total_pallet_count' => $params['estimated_total_pallet_count'],
                            'confirmed_arrival_date' => $params['confirmed_arrival_date'],
                            'estimated_arrival_date' => $params['estimated_arrival_date'],
                            'liteview_user' => $params['liteview_user'],
                            'vendor_lookup_id' => $params['vendor_lookup_id'],
                        ],
                        'items' => []
                    ]
                ]
            ];

            if(count($params['items'])) {
                foreach($params['items'] as $item) {
                    $paramsXML['submit_po']['purchase_order']['items'][] = [
                        'inventory_item' => $item['inventory_item'],
                        'inventory_sku' => $item['inventory_sku'],
                        'po_qty' => $item['po_qty'],
                        'confirmed_carton_count' => $item['confirmed_carton_count'],
                        'estimated_carton_count' => $item['estimated_carton_count'],
                        'confirmed_pallet_count' => $item['confirmed_pallet_count'],
                        'estimated_pallet_count' => $item['estimated_pallet_count'],
                        'confirmed_arrival_date' => $item['confirmed_arrival_date'],
                        'estimated_arrival_date' => $item['estimated_arrival_date'],
                    ];
                }
            }

            $responseXML = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<toolkit>
    <submit_po>
        <purchase_order>
            <information>
                <liteview_asn_id>TES1059</liteview_asn_id>
                <po_number>test10001_abc2</po_number>
                <po_status>Open</po_status>
                <vendor_lookup_id>test1001</vendor_lookup_id>
                <po_document_pdf>JVBERi0xLjQNCiWTjIueIFJlcG9ydExhYiBHZW5lcmF0ZWQgUERGIGRvY3VtZW50IGh0dHA6Ly93d3cucmVwb3J0bGFiLmNvbQ0KJSAnQmFzaWNGb250cyc6IGNsYXNzIFBERkRpY3Rpb25hcnkgDQoxIDAgb2JqDQolIFRoZSBzdGFuZGFyZCBmb250cyBkaWN0aW9uYXJ5DQo8PCAvRjEgMiAwIFINCiAvRjIrMCA5IDAgUg0KIC9GMyA0IDAgUiA+Pg0KZW5kb2JqDQolICdGMSc6IGNsYXNzIFBERlR5cGUxRm9udCANCjIgMCBvYmoNCiUgRm9udCBIZWx2ZXRpY2ENCjw8IC9CYXNlRm9udCAvSGVsdmV0aWNhDQogL0VuY29kaW5nIC9XaW5BbnNpRW5jb2RpbmcNCiAvTmFtZSAvRjENCiAvU3VidHlwZSAvVHlwZTENCiAvVHlwZSAvRm9udCA+Pg0KZW5kb2JqDQolICdGb3JtW.....PDF Base64 Data PLEASE note this is not a complete base 64 string example..</po_document_pdf>
            </information>
        </purchase_order>
    </submit_po>
</toolkit>

XML;
            $xml = simplexml_load_string($responseXML);
            $info = $xml->submit_po->purchase_order->information;
            $response = [
                'liteview_asn_id' => (string)$info->liteview_asn_id,
                'po_number' => (string)$info->po_number,
                'po_status' => (string)$info->po_status,
                'vendor_lookup_id' => (string)$info->vendor_lookup_id,
                'po_document_pdf' => (string)$info->po_document_pdf
            ];

            return $response;
        }

    }

    /**
     * Provides detail about a current purchase order in the liteview system, including status, notes, and items status for the purchase order
     * @param string $po_number Purchase order number
     * @return array
     */
    public function getPurchaseOrderDetails($po_number) {

        if(!$this->debug) {
            $params = [$po_number];
            $responseXML = $this->sendRequest('purchase/order', 'detail/information', 'GET', $params);

            $xml = simplexml_load_string($responseXML);
            $response = [];

            $info = $xml->purchase_order->information;
            $response['details'] = [
                'liteview_asn_id' => (string)$info->liteview_asn_id,
                'po_number' => (string)$info->po_number,
                'po_status' => (string)$info->po_status,
                'liteview_user' => (string)$info->liteview_user
            ];

            $vendor = $xml->purchase_order->vendor;
            $response['vendor'] = [
                'lookup_id' => (string)$vendor->vendor_lookup_id,
                'company_name' => (string)$vendor->company_name,
                'contact' => (string)$vendor->contact,
                'address1' => (string)$vendor->address1,
                'address2' => (string)$vendor->address2,
                'city' => (string)$vendor->city,
                'state' => (string)$vendor->state,
                'zip' => (string)$vendor->zip,
                'country' => (string)$vendor->country,
                'phone' => (string)$vendor->phone,
                'email' => (string)$vendor->email,
            ];

            if(isset($xml->purchase_order->notes)) {
                $response['notes'] = [];
                if(count($xml->purchase_order->notes->note) > 1) {
                    foreach($xml->purchase_order->notes->note as $note) {
                        $response['notes'][] = [
                            'comment' => (string)$note->comment,
                            'added_by' => (string)$note->added_by,
                            'added_on' => (string)$note->added_on
                        ];
                    }
                } else {
                    $note = $xml->purchase_order->notes->note;
                    $response['notes'][] = [
                        'comment' => (string)$note->comment,
                        'added_by' => (string)$note->added_by,
                        'added_on' => (string)$note->added_on
                    ];
                }
            }

            if(isset($xml->purchase_order->items)) {
                $response['items'] = [];
                if(count($xml->purchase_order->items->item) > 1) {
                    foreach($xml->purchase_order->items->item as $item) {
                        $ret = [];
                        $ret['inventory_item'] = (string)$item->inventory_item;
                        $ret['inventory_sku'] = (string)$item->inventory_sku;
                        $ret['inventory_description'] = (string)$item->inventory_description;
                        $ret['po_qty'] = (int)$item->po_qty;
                        $ret['remaining_qty_to_receive'] = (int)$item->remaining_qty_to_receive;
                        $ret['variance'] = (int)$item->variance;
                        $ret['status'] = (string)$item->inventory_item;
                        $ret['receiving_transactions'] = [];
                        if(isset($item->receiving_transactions)) {
                            if(count($item->receiving_transactions->transaction) > 1) {
                                foreach($item->receiving_transactions->transaction as $transaction) {
                                    $ret['receiving_transactions'][] = [
                                        'transaction_id' => (int)$transaction->transaction_id,
                                        'qty_received' => (int)$transaction->qty_received,
                                        'receive_date' => (string)$transaction->receive_date
                                    ];
                                }
                            } else {
                                $transaction = $item->receiving_transactions->transaction;
                                $ret['receiving_transactions'][] = [
                                    'transaction_id' => (int)$transaction->transaction_id,
                                    'qty_received' => (int)$transaction->qty_received,
                                    'receive_date' => (string)$transaction->receive_date
                                ];
                            }
                        }

                        $response['items'][] = $ret;
                    }
                } else {
                    $item = $xml->purchase_order->items->item;
                    $ret = [];
                    $ret['inventory_item'] = (string)$item->inventory_item;
                    $ret['inventory_sku'] = (string)$item->inventory_sku;
                    $ret['inventory_description'] = (string)$item->inventory_description;
                    $ret['po_qty'] = (int)$item->po_qty;
                    $ret['remaining_qty_to_receive'] = (int)$item->remaining_qty_to_receive;
                    $ret['variance'] = (int)$item->variance;
                    $ret['status'] = (string)$item->inventory_item;
                    $ret['receiving_transactions'] = [];
                    if(isset($item->receiving_transactions)) {
                        if(count($item->receiving_transactions->transaction) > 1) {
                            foreach($item->receiving_transactions->transaction as $transaction) {
                                $ret['receiving_transactions'][] = [
                                    'transaction_id' => (int)$transaction->transaction_id,
                                    'qty_received' => (int)$transaction->qty_received,
                                    'receive_date' => (string)$transaction->receive_date
                                ];
                            }
                        } else {
                            $transaction = $item->receiving_transactions->transaction;
                            $ret['receiving_transactions'][] = [
                                'transaction_id' => (int)$transaction->transaction_id,
                                'qty_received' => (int)$transaction->qty_received,
                                'receive_date' => (string)$transaction->receive_date
                            ];
                        }
                    }

                    $response['items'][] = $ret;
                }
            }

            return $response;

        } else {

            $responseXML = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<toolkit>
    <purchase_order>
        <information>
            <liteview_asn_id>TES1017</liteview_asn_id>
            <po_number>TES1</po_number>
            <po_status>Open</po_status>
            <liteview_user>yuevshs</liteview_user>
        </information>
        <vendor>
            <vendor_lookup_id>1002</vendor_lookup_id>
            <company_name>test</company_name>
            <contact>Jane Doe</contact>
            <address1>20100 S Vermont</address1>
            <address2/>
            <city>Torrance</city>
            <state>CA</state>
            <zip>90502</zip>
            <country>US</country>
            <phone>310-217-4600</phone>
            <email>dannys@imaginefulfillment.com</email>
        </vendor>
        <notes>
            <note>
                <comment>This po is partially received</comment>
                <added_by>pabloh007</added_by>
                <added_on>2012-09-12 17:00:09.800111-07</added_on>
            </note>
            <note>
                <comment>
                    PO Item:TEST1001 had its Status changed
                    from : to Open
                </comment>
                <added_by>pabloh007</added_by>
                <added_on>2012-09-13 11:48:30.427993-07</added_on>
            </note>
        </notes>
        <items>
            <item>
                <inventory_item>TEST1002</inventory_item>
                <inventory_sku>TEST1002</inventory_sku>
                <inventory_description>Test Item Number 2</inventory_description>
                <po_qty>1000</po_qty>
                <remaining_qty_to_receive/>
                <variance>0</variance>
                <status>Closed</status>
                <receiving_transactions>
                    <transaction>
                        <qty_received>1000</qty_received>
                        <receive_date>2012-09-17</receive_date>
                    </transaction>
                </receiving_transactions>
            </item>
            <item>
                <inventory_item>TEST1001</inventory_item>
                <inventory_sku>TEST1001</inventory_sku>
                <inventory_description>Test Item Number 1</inventory_description>
                <po_qty>5000</po_qty>
                <remaining_qty_to_receive/>
                <variance>-2747</variance>
                <status>Closed</status>
                <receiving_transactions>
                    <transaction>
                        <transaction_id>1001</transaction_id>
                        <qty_received>498</qty_received>
                        <receive_date>2012-09-20</receive_date>
                    </transaction>
                    <transaction>
                        <transaction_id>1002</transaction_id>
                        <qty_received>490</qty_received>
                        <receive_date>2012-09-20</receive_date>
                    </transaction>
                </receiving_transactions>
            </item>
        </items>
    </purchase_order>
</toolkit>

XML;

            $xml = simplexml_load_string($responseXML);
            $response = [];

            $info = $xml->purchase_order->information;
            $response['details'] = [
                'liteview_asn_id' => (string)$info->liteview_asn_id,
                'po_number' => (string)$info->po_number,
                'po_status' => (string)$info->po_status,
                'liteview_user' => (string)$info->liteview_user
            ];

            $vendor = $xml->purchase_order->vendor;
            $response['vendor'] = [
                'lookup_id' => (string)$vendor->vendor_lookup_id,
                'company_name' => (string)$vendor->company_name,
                'contact' => (string)$vendor->contact,
                'address1' => (string)$vendor->address1,
                'address2' => (string)$vendor->address2,
                'city' => (string)$vendor->city,
                'state' => (string)$vendor->state,
                'zip' => (string)$vendor->zip,
                'country' => (string)$vendor->country,
                'phone' => (string)$vendor->phone,
                'email' => (string)$vendor->email,
            ];

            if(isset($xml->purchase_order->notes)) {
                $response['notes'] = [];
                if(count($xml->purchase_order->notes->note) > 1) {
                    foreach($xml->purchase_order->notes->note as $note) {
                        $response['notes'][] = [
                            'comment' => (string)$note->comment,
                            'added_by' => (string)$note->added_by,
                            'added_on' => (string)$note->added_on
                        ];
                    }
                } else {
                    $note = $xml->purchase_order->notes->note;
                    $response['notes'][] = [
                        'comment' => (string)$note->comment,
                        'added_by' => (string)$note->added_by,
                        'added_on' => (string)$note->added_on
                    ];
                }
            }

            if(isset($xml->purchase_order->items)) {
                $response['items'] = [];
                if(count($xml->purchase_order->items->item) > 1) {
                    foreach($xml->purchase_order->items->item as $item) {
                        $ret = [];
                        $ret['inventory_item'] = (string)$item->inventory_item;
                        $ret['inventory_sku'] = (string)$item->inventory_sku;
                        $ret['inventory_description'] = (string)$item->inventory_description;
                        $ret['po_qty'] = (int)$item->po_qty;
                        $ret['remaining_qty_to_receive'] = (int)$item->remaining_qty_to_receive;
                        $ret['variance'] = (int)$item->variance;
                        $ret['status'] = (string)$item->inventory_item;
                        $ret['receiving_transactions'] = [];
                        if(isset($item->receiving_transactions)) {
                            if(count($item->receiving_transactions->transaction) > 1) {
                                foreach($item->receiving_transactions->transaction as $transaction) {
                                    $ret['receiving_transactions'][] = [
                                        'transaction_id' => (int)$transaction->transaction_id,
                                        'qty_received' => (int)$transaction->qty_received,
                                        'receive_date' => (string)$transaction->receive_date
                                    ];
                                }
                            } else {
                                $transaction = $item->receiving_transactions->transaction;
                                $ret['receiving_transactions'][] = [
                                    'transaction_id' => (int)$transaction->transaction_id,
                                    'qty_received' => (int)$transaction->qty_received,
                                    'receive_date' => (string)$transaction->receive_date
                                ];
                            }
                        }

                        $response['items'][] = $ret;
                    }
                } else {
                    $item = $xml->purchase_order->items->item;
                    $ret = [];
                    $ret['inventory_item'] = (string)$item->inventory_item;
                    $ret['inventory_sku'] = (string)$item->inventory_sku;
                    $ret['inventory_description'] = (string)$item->inventory_description;
                    $ret['po_qty'] = (int)$item->po_qty;
                    $ret['remaining_qty_to_receive'] = (int)$item->remaining_qty_to_receive;
                    $ret['variance'] = (int)$item->variance;
                    $ret['status'] = (string)$item->inventory_item;
                    $ret['receiving_transactions'] = [];
                    if(isset($item->receiving_transactions)) {
                        if(count($item->receiving_transactions->transaction) > 1) {
                            foreach($item->receiving_transactions->transaction as $transaction) {
                                $ret['receiving_transactions'][] = [
                                    'transaction_id' => (int)$transaction->transaction_id,
                                    'qty_received' => (int)$transaction->qty_received,
                                    'receive_date' => (string)$transaction->receive_date
                                ];
                            }
                        } else {
                            $transaction = $item->receiving_transactions->transaction;
                            $ret['receiving_transactions'][] = [
                                'transaction_id' => (int)$transaction->transaction_id,
                                'qty_received' => (int)$transaction->qty_received,
                                'receive_date' => (string)$transaction->receive_date
                            ];
                        }
                    }

                    $response['items'][] = $ret;
                }
            }

            return $response;
        }

    }

    /**
     * Send request to Liteview API
     * @param string $call
     * @param string $func
     * @param string $method
     * @param array $params
     * @return mixed
     */
    protected function sendRequest($call, $func, $method = "GET", $params = []) {

        $url = $this->url . '/' . $call . '/' . $func . '/' . $this->username;
        if($method == 'GET' && !empty($params) && is_array($params)) {
            $url .= "/" . implode('/', $params);
        } elseif($method == 'POST' && !empty($params) && is_array($params)) {
            $params = $this->arrayToXML($params);
        } elseif(!empty($params) && is_string($params)) {
            $url .= '/' . $params;
        }

        VarDumper::dump($url, 10, true); exit;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-type: text/xml', 'appkey: ' . $this->appKey));

        if($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }

        $responseXml = curl_exec($ch);
        curl_close($ch);



        return $responseXml;
    }

    /**
     * Convert an array to xml string
     * @param array $data
     * @return string
     */
    protected function arrayToXML($data) {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $root = new \DOMElement("toolkit");
        $dom->appendChild($root);
        $this->buildXML($root, $data);

        $dom->formatOutput = true;
        $xml = $dom->saveXML();

        return $xml;
    }

    /**
     * @param \DOMElement $element
     * @param mixed $data
     */
    protected function buildXML($element, $data) {
        if(is_array($data)) {
            foreach($data as $name => $value) {
                if(is_array($value)) {
                    $child = new \DOMElement(is_int($name) ? 'item' : $name);
                    $element->appendChild($child);
                    $this->buildXML($child, $value);
                } else {
                    $child = new \DOMElement(is_int($name) ? 'item' : $name);
                    $element->appendChild($child);
                    $child->appendChild(new \DOMText((string) $value));
                }
            }
        } else {
            $element->appendChild(new \DOMText((string) $data));
        }
    }
}