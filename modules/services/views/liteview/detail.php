<?php
    use app\modules\services\models\TradegeckoCurrency;

    $orderDetails = $liteviewOrder['submit_order']['order_info']['order_details'];
    $billingContact = $liteviewOrder['submit_order']['order_info']['billing_contact'];
    $shippingContact = $liteviewOrder['submit_order']['order_info']['shipping_contact'];
    $billingDetails = $liteviewOrder['submit_order']['order_info']['billing_details'];
    $shippingDetails = $liteviewOrder['submit_order']['order_info']['shipping_details'];
    $orderNotes = $liteviewOrder['submit_order']['order_info']['order_notes'];
    $orderItems = $liteviewOrder['submit_order']['order_info']['order_items'];
    $symbol = '$';
?>

<style>
.head_td{
    font-weight: bold;
    text-align: center;
}
</style>

<div class="widget-box collapsible detailWidget">
    <div class="widget-title">
        <a data-toggle="collapse" href="#collapseTwo">
            <span class="icon"><i class="fa fa-search"></i></span>
            <h5>Liteview Order Details</h5>
        </a>
    </div>
    <div id="collapseTwo" class="in">
        <div class="widget-content nopadding">
            <div id="stripe_data">
                <div class="widget-box">
                    <div class="widget-title nopadding">
                        <ul class="nav nav-tabs">
                            <input type="hidden" name="liteview_order_id" value="<?php echo $liteviewOrder['id']; ?>">
                            <li class="active"><a href="#tab1" data-toggle="tab"> Order Details</a></li>
                            <li><a href="#tab2" data-toggle="tab"> Billing Contacts</a></li>
                            <li><a href="#tab3" data-toggle="tab"> Shipping Contacts</a></li>
                            <li><a href="#tab5" data-toggle="tab"> Shipping Details</a></li>
                            <li><a href="#tab6" data-toggle="tab"> Order Notes</a></li>
                            <li><a href="#tab7" data-toggle="tab"> Order Items</a></li>
                        </ul>
                    </div>
                    <div class="widget-content tab-content">
                        <div class="tab-pane active" id="tab1">
                            <table class="table table-striped table-hover table-bordered" style="font-size:12px">
                                <tr>
                                    <td class="head_td" style="width:25%">Order Status</td>
                                    <td style="width:25%"><?php echo $status; ?></td>
                                    <td class="head_td" style="width:25%">Order Date</td>
                                    <td style="width:25%"><?php echo $orderDetails['order_date']; ?></td>
                                </tr>
                                <tr>
                                    <td class="head_td" style="width:25%">Order Number</td>
                                    <td style="width:25%"><?php echo $orderDetails['order_number']; ?></td>
                                    <td class="head_td" style="width:25%">Order Source</td>
                                    <td style="width:25%"><?php echo $orderDetails['order_source']; ?></td>
                                </tr>
                                <tr>
                                    <td class="head_td" style="width:25%">Order Type</td>
                                    <td style="width:25%"><?php echo $orderDetails['order_type']; ?></td>
                                    <td class="head_td" style="width:25%">Catalog Name</td>
                                    <td style="width:25%"><?php echo $orderDetails['catalog_name']; ?></td>
                                </tr>
                                <tr>
                                    <td class="head_td" style="width:25%">Gift Order</td>
                                    <td style="width:25%"><?php echo $orderDetails['gift_order']; ?></td>
                                    <td class="head_td" style="width:25%">PO #</td>
                                    <td style="width:25%"><?php echo $orderDetails['po_number']; ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="tab-pane" id="tab2">
                            <table class="table table-striped table-hover table-bordered" style="font-size:12px">
                                <tr>
                                    <td class="head_td" style="width:25%">Prefix</td>
                                    <td style="width:25%"><?php echo $billingContact['billto_prefix']; ?></td>
                                    <td class="head_td" style="width:25%">Suffix</td>
                                    <td style="width:25%"><?php echo $billingContact['billto_suffix']; ?></td>
                                </tr>
                                <tr>
                                    <td class="head_td" style="width:25%">First Name</td>
                                    <td style="width:25%"><?php echo $billingContact['billto_first_name']; ?></td>
                                    <td class="head_td" style="width:25%">Last Name</td>
                                    <td style="width:25%"><?php echo $billingContact['billto_last_name']; ?></td>
                                </tr>
                                <tr>
                                    <td class="head_td" style="width:25%">Company Name</td>
                                    <td style="width:25%"><?php echo $billingContact['billto_company_name']; ?></td>
                                    <td class="head_td" style="width:25%">Address</td>
                                    <td style="width:25%"><?php echo $billingContact['billto_address1'].PHP_EOL.$billingContact['billto_address2'].PHP_EOL.$billingContact['billto_address3']; ?></td>
                                </tr>
                                <tr>
                                    <td class="head_td" style="width:25%">City</td>
                                    <td style="width:25%"><?php echo $billingContact['billto_city']; ?></td>
                                    <td class="head_td" style="width:25%">State</td>
                                    <td style="width:25%"><?php echo $billingContact['billto_state']; ?></td>
                                </tr>
                                <tr>
                                    <td class="head_td" style="width:25%">Postal Code</td>
                                    <td style="width:25%"><?php echo $billingContact['billto_postal_code']; ?></td>
                                    <td class="head_td" style="width:25%">Country</td>
                                    <td style="width:25%"><?php echo $billingContact['billto_country']; ?></td>
                                </tr>
                                <tr>
                                    <td class="head_td" style="width:25%">Telehone Number</td>
                                    <td style="width:25%"><?php echo $billingContact['billto_telephone_no']; ?></td>
                                    <td class="head_td" style="width:25%">E-mail</td>
                                    <td style="width:25%"><?php echo $billingContact['billto_email']; ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="tab-pane" id="tab3">
                            <table class="table table-striped table-hover table-bordered" style="font-size:12px">
                                <tr>
                                    <td class="head_td" style="width:25%">Prefix</td>
                                    <td style="width:25%"><?php echo $shippingContact['shipto_prefix']; ?></td>
                                    <td class="head_td" style="width:25%">Suffix</td>
                                    <td style="width:25%"><?php echo $shippingContact['shipto_suffix']; ?></td>
                                </tr>
                                <tr>
                                    <td class="head_td" style="width:25%">First Name</td>
                                    <td style="width:25%"><?php echo $shippingContact['shipto_first_name']; ?></td>
                                    <td class="head_td" style="width:25%">Last Name</td>
                                    <td style="width:25%"><?php echo $shippingContact['shipto_last_name']; ?></td>
                                </tr>
                                <tr>
                                    <td class="head_td" style="width:25%">Company Name</td>
                                    <td style="width:25%"><?php echo $shippingContact['shipto_company_name']; ?></td>
                                    <td class="head_td" style="width:25%">Address</td>
                                    <td style="width:25%"><?php echo $shippingContact['shipto_address1'].PHP_EOL.$shippingContact['shipto_address2'].PHP_EOL.$shippingContact['shipto_address3']; ?></td>
                                </tr>
                                <tr>
                                    <td class="head_td" style="width:25%">City</td>
                                    <td style="width:25%"><?php echo $shippingContact['shipto_city']; ?></td>
                                    <td class="head_td" style="width:25%">State</td>
                                    <td style="width:25%"><?php echo $shippingContact['shipto_state']; ?></td>
                                </tr>
                                <tr>
                                    <td class="head_td" style="width:25%">Postal Code</td>
                                    <td style="width:25%"><?php echo $shippingContact['shipto_postal_code']; ?></td>
                                    <td class="head_td" style="width:25%">Country</td>
                                    <td style="width:25%"><?php echo $shippingContact['shipto_country']; ?></td>
                                </tr>
                                <tr>
                                    <td class="head_td" style="width:25%">Telehone Number</td>
                                    <td style="width:25%"><?php echo $shippingContact['shipto_telephone_no']; ?></td>
                                    <td class="head_td" style="width:25%">E-mail</td>
                                    <td style="width:25%"><?php echo $shippingContact['shipto_email']; ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="tab-pane" id="tab5">
                            <table class="table table-striped table-hover table-bordered" style="font-size:12px">
                                <tr>
                                    <td class="head_td" style="width:25%">Ship Method</td>
                                    <td style="width:25%"><?php echo $shippingDetails['ship_method']; ?></td>
                                    <td class="head_td" style="width:25%">Signature Requested</td>
                                    <td style="width:25%"><?php echo $shippingDetails['signature_requested']; ?></td>
                                </tr>
                                <tr>
                                    <td class="head_td" style="width:25%">Insurance Requested</td>
                                    <td style="width:25%"><?php echo $shippingDetails['insurance_requested']; ?></td>
                                    <td class="head_td" style="width:25%">Insurance Value</td>
                                    <td style="width:25%"><?php echo $shippingDetails['insurance_value']; ?></td>
                                </tr>
                                <tr>
                                    <td class="head_td" style="width:25%">Saturday Delivery Requested</td>
                                    <td style="width:25%"><?php echo $shippingDetails['saturday_delivery_requested']; ?></td>
                                    <td class="head_td" style="width:25%">3rd Party Billing Requested</td>
                                    <td style="width:25%"><?php echo $shippingDetails['third_party_billing_requested']; ?></td>
                                </tr>
                                <tr>
                                    <td class="head_td" style="width:25%">3rd Party Billing Account #</td>
                                    <td style="width:25%"><?php echo $shippingDetails['third_party_billing_account_no']; ?></td>
                                    <td class="head_td" style="width:25%">3rd Party Billing Zip</td>
                                    <td style="width:25%"><?php echo $shippingDetails['third_party_billing_zip']; ?></td>
                                </tr>
                                <tr>
                                    <td class="head_td" style="width:25%">3rd Party Country</td>
                                    <td style="width:25%"><?php echo $shippingDetails['third_party_country']; ?></td>
                                    <td class="head_td" style="width:25%">General Description</td>
                                    <td style="width:25%"><?php echo $shippingDetails['general_description']; ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="tab-pane" id="tab6">
                            <table class="table table-striped table-hover table-bordered" style="font-size:12px">
                                <tr>
                                    <td class="head_td" style="width:25%">Note Type</td>
                                    <td style="width:75%"><?php echo $orderNotes['note_type']; ?></td>
                                </tr>
                                <tr>
                                    <td class="head_td" style="width:25%">Note Description</td>
                                    <td style="width:75%; white-space:pre"><?php echo $orderNotes['note_description']; ?></td>
                                </tr>
                                <tr>
                                    <td class="head_td" style="width:25%">Show on PS</td>
                                    <td style="width:75%;"><?php echo $orderNotes['show_on_ps']; ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="tab-pane" id="tab7">
                            <table class="table table-striped table-hover table-bordered" style="font-size:12px">
                                <tr>
                                    <td class='head_td'>Inventory Item</td>
                                    <td class='head_td'>Inventory Item SKU</td>
                                    <td class='head_td'>Description</td>
                                    <td class='head_td'>Price</td>
                                    <td class='head_td'>Qty</td>
                                    <td class='head_td'>Total</td>
                                    <td class='head_td'>Inventory Passthrough 01</td>
                                    <td class='head_td'>Inventory Passthrough 02</td>
                                </tr>
                                <?php foreach($orderItems as $orderItem) { ?>
                                    <?php
                                        $symbol = '$';
                                        $currencyModel = TradegeckoCurrency::find()->where(['id' => $orderItem['tradegecko_currency_id']])->one();
                                        if ($currencyModel != null)
                                            $symbol = $currencyModel->symbol;
                                    ?>
                                    <tr>
                                        <td><?php echo $orderItem['inventory_item']; ?></td>
                                        <td><?php echo $orderItem['inventory_item_sku']; ?></td>
                                        <td><?php echo $orderItem['inventory_item_description']; ?></td>
                                        <td><?php echo $symbol.number_format(floatval($orderItem['inventory_item_price']), 2); ?></td>
                                        <td><?php echo $orderItem['inventory_item_qty']; ?></td>
                                        <td><?php echo $symbol.number_format(floatval($orderItem['inventory_item_ext_price']), 2); ?></td>
                                        <td><?php echo $orderItem['inventory_passthrough_01']; ?></td>
                                        <td><?php echo $orderItem['inventory_passthrough_02']; ?></td>
                                    </tr>
                                <?php } ?>
                            </table>

                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-9">
                                        </div>
                                        <div class="col-md-3" style='float:right;'>
                                            <dl class="dl-horizontal" style="border-bottom:1px solid lightgray; font-size:12px;">
                                                <dt style="width:135px;">Sub Total</dt>
                                                <dd><?php echo $symbol.number_format($billingDetails['sub_total'], 2); ?></dd>
                                            </dl>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-9">
                                        </div>
                                        <div class="col-md-3" style='float:right;'>
                                            <dl class="dl-horizontal" style="border-bottom:1px solid lightgray; font-size:12px;">
                                                <dt style="width:135px;">Shipping Handling</dt>
                                                <dd><?php echo $symbol.number_format($billingDetails['shipping_handling'] ,2); ?></dd>
                                            </dl>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-9">
                                        </div>
                                        <div class="col-md-3" style='float:right;'>
                                            <dl class="dl-horizontal" style="border-bottom:1px solid lightgray; font-size:12px;">
                                                <dt style="width:135px;">Sales Tax Total</dt>
                                                <dd><?php echo $symbol.number_format($billingDetails['sales_tax_total'] ,2); ?></dd>
                                            </dl>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-9">
                                        </div>
                                        <div class="col-md-3" style='float:right;'>
                                            <dl class="dl-horizontal" style="border-bottom:1px solid lightgray; font-size:12px;">
                                                <dt style="width:135px;">Discount Total</dt>
                                                <dd><?php echo $symbol.number_format($billingDetails['discount_total'] ,2); ?></dd>
                                            </dl>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-9">
                                        </div>
                                        <div class="col-md-3" style='float:right;'>
                                            <dl class="dl-horizontal" style="border-bottom:1px solid lightgray; font-size:12px;">
                                                <dt style="width:135px;">Grand Total</dt>
                                                <dd><?php echo $symbol.number_format($billingDetails['grand_total'] ,2); ?></dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="buttons" style="margin:15px; text-align:right;">
                        <?php if ($flag != '2') { ?>
                            <button type="submit" class="btn btn-small btn-success" id="editOrder" style="padding: 3px 8px; font-size:12.5px;" onclick="editOrder();">Make Changes</button>
                            &nbsp;
                            <?php if ($flag == '0') { ?>
                            <button type="submit" class="btn btn-small btn-primary" id="submitOrder" style="padding: 3px 8px; font-size:12.5px;" onclick="submitOrder();">Submit</button>
                            <?php } else { ?>
                            <button type="submit" class="btn btn-small btn-warning" id="cancelOrder" style="padding: 3px 8px; font-size:12.5px;" onclick="cancelOrder();">Cancel</button>
                            &nbsp;
                            <button type="submit" class="btn btn-small btn-info" id="resubmitOrder" style="padding: 3px 8px; font-size:12.5px;" onclick="resubmitOrder();">Re-submit</button>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>      
        </div>
    </div>
</div>