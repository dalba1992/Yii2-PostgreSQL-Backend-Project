<?php
/**
 * @var CustomerEntity $model
 * @var CustomerSubscriptionState $customerSubscriptionState
 */

use yii\helpers\Html;
use app\models\TbAttribute;
use app\models\TbAttributeDetail;
use app\models\TbAttributeDetailImage;
use app\models\CustomerEntityAttribute;
use app\models\CustomerEntityInfo;

$customerEntityInfo = $model->customerEntityInfo;
?>

<div class="row">
    <div class="widget-box" id="orders-section">
        <div class="widget-title">
            <span class="icon"><i class="fa fa-plane"></i></span>
            <h5>Order History</h5>
        </div>
        <div id="collapse-group" class="accordion">
            <div class="accordion-group widget-box">
                <!--Order e-Commerce-->
                <div class="accordion-heading">
                    <div class="widget-title">
                        <a data-toggle="collapse" href="#collapseGeComm" data-parent="#collapse-group">
                            <span class="icon">#4</span>
									<span><h5>02-22-14</h5>
										<span class="label label-success">Shipped</span>
										<h5>#97770</h5>
									</span>
                            <span class="label label-inverse">e-Commerce Order</span>
                        </a>
                    </div>
                </div>
                <div id="collapseGeComm" class="collapse accordion-body">
                    <div class="widget-content">
                        <h5>Order #97770 Info</h5>
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th>Status</th>
                                <th>Tracking</th>
                                <th>Ship Date</th>
                                <th>Order Number</th>
                                <th>Order Date</th>
                                <th>Subtotal</th>
                                <th>Tax</th>
                                <th>Shipping</th>
                                <th>Total</th>
                                <th>Manage</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><span class="badge badge-success">Shipped</span></td>
                                <td>Fedex <a target="_blank" href="https://www.fedex.com/fedextrack/?tracknumbers=599489318347&amp;cntry_code=us&amp;language=en">599489318347</a></td>
                                <td>03-04-14</td>
                                <td>97767</td>
                                <td>02-21-14</td>
                                <td>$130.00</td>
                                <td>$0.00</td>
                                <td>$14.25</td>
                                <td>$144.25</td>
                                <td>
                                    <a data-toggle="modal" data-original-title="Please create an internal order if you need to send an extra package!" class="btn tip-bottom btn-danger" href="#cancelOrder">Cancel Order</a>
                                    <a href="tb-profile.html"><button data-original-title="Please use old Admin system to process a return" class="btn btn-warning disabled tip-bottom">Return Order</button></a>
                                    <br>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <h5>Items Received</h5>
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th>Image</th>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>QTY</th>
                                <th>Price</th>
                                <th>Total Price</th>
                                <th>Size</th>
                                <th>Color</th>
                                <th>Manage</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><?php echo Html::img('/web/img/demo/img1.jpg') ?></td>
                                <td>Torrance NVY</td>
                                <td>Fashion Tee</td>
                                <td>1</td>
                                <td>$38.00</td>
                                <td>$38.00</td>
                                <td>LRG</td>
                                <td>Black</td>
                                <td>
                                    <a data-toggle="modal" data-original-title="Will only work if the order has not shipped" class="btn tip-bottom disabled" href="#changeSize"><i class="icon-pencil"></i> Change Size</a>
                                </td>
                            </tr>
                            <tr>
                                <td><?php echo Html::img('/web/img/demo/img1.jpg') ?></td>
                                <td>Torrance NVY</td>
                                <td>Fashion Tee</td>
                                <td>1</td>
                                <td>$38.00</td>
                                <td>$38.00</td>
                                <td>LRG</td>
                                <td>Black</td>
                                <td>
                                    <a data-toggle="modal" data-original-title="Will only work if the order has not shipped" class="btn tip-bottom disabled" href="#changeSize"><i class="icon-pencil"></i> Change Size</a>
                                </td>
                            </tr>
                            <tr>
                                <td><?php echo Html::img('/web/img/demo/img1.jpg') ?></td>
                                <td>Torrance NVY</td>
                                <td>Fashion Tee</td>
                                <td>1</td>
                                <td>$38.00</td>
                                <td>$38.00</td>
                                <td>LRG</td>
                                <td>Black</td>
                                <td>
                                    <a data-toggle="modal" data-original-title="Will only work if the order has not shipped" class="btn tip-bottom disabled" href="#changeSize"><i class="icon-pencil"></i> Change Size</a>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <!--Buttons to manage order-->
                        <a href="tb-profile.html"><button class="btn btn-info">View Packing Sheet</button></a>
                        <a href="tb-profile.html"><button class="btn btn-info">View Receipt</button></a>
                        <div class="btn-group">
                            <div></div>
                        </div>
                    </div>
                </div>
                <div class="accordion-group widget-box">
                    <!--Order Three-->
                    <div class="accordion-heading">
                        <div class="widget-title">
                            <a data-toggle="collapse" href="#collapseG-3" data-parent="#collapse-group">
                                <span class="icon">#3</span> <span><h5>02-21-14</h5><span class="label label-warning">Authorized</span><h5>#97769</h5></span> <span class="label label-info">Club Order</span>
                            </a>
                        </div>
                    </div>
                    <div id="collapseG-3" class="collapse accordion-body">
                        <div class="widget-content">
                            <h5>Order #97769 Info</h5>
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Tracking</th>
                                    <th>Ship Date</th>
                                    <th>Order Number</th>
                                    <th>Order Date</th>
                                    <th>Subtotal</th>
                                    <th>Tax</th>
                                    <th>Shipping</th>
                                    <th>Total</th>
                                    <th>Manage</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><span class="badge badge-warning">Authorized</span></td>
                                    <td>Fedex <a target="_blank" href="https://www.fedex.com/fedextrack/?tracknumbers=599489318347&amp;cntry_code=us&amp;language=en">599489318347</a></td>
                                    <td>03-04-14</td>
                                    <td>97767</td>
                                    <td>02-21-14</td>
                                    <td>$130.00</td>
                                    <td>$0.00</td>
                                    <td>$14.25</td>
                                    <td>$144.25</td>
                                    <td>
                                        <a data-toggle="modal" data-original-title="Please create an internal order if you need to send an extra package!" class="btn tip-bottom btn-danger" href="#cancelOrder">Cancel Order</a>
                                        <a href="tb-profile.html"><button data-original-title="Please use old Admin system to process a return" class="btn btn-warning disabled tip-bottom">Return Order</button></a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                            <h5>Items Received</h5>
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Item Name</th>
                                    <th>Category</th>
                                    <th>QTY</th>
                                    <th>Price</th>
                                    <th>Total Price</th>
                                    <th>Size</th>
                                    <th>Color</th>
                                    <th>Manage</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><?php echo Html::img('/web/img/demo/img1.jpg') ?></td>
                                    <td>Torrance NVY</td>
                                    <td>Fashion Tee</td>
                                    <td>1</td>
                                    <td>$38.00</td>
                                    <td>$38.00</td>
                                    <td>LRG</td>
                                    <td>Black</td>
                                    <td>
                                        <a data-toggle="modal" data-original-title="Will only work if the order has not shipped" class="btn tip-bottom" href="#changeSize"><i class="icon-pencil"></i> Change Size</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo Html::img('/web/img/demo/img1.jpg') ?></td>
                                    <td>Torrance NVY</td>
                                    <td>Fashion Tee</td>
                                    <td>1</td>
                                    <td>$38.00</td>
                                    <td>$38.00</td>
                                    <td>LRG</td>
                                    <td>Black</td>
                                    <td>
                                        <a data-toggle="modal" data-original-title="Will only work if the order has not shipped" class="btn tip-bottom" href="#changeSize"><i class="icon-pencil"></i> Change Size</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo Html::img('/web/img/demo/av1.jpg') ?></td>
                                    <td>Torrance NVY</td>
                                    <td>Fashion Tee</td>
                                    <td>1</td>
                                    <td>$38.00</td>
                                    <td>$38.00</td>
                                    <td>LRG</td>
                                    <td>Black</td>
                                    <td>
                                        <a data-toggle="modal" data-original-title="Will only work if the order has not shipped" class="btn tip-bottom" href="#changeSize"><i class="fa fa-pencil"></i> Change Size</a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                            <!--Buttons to manage order-->
                            <a href="tb-profile.html"><button class="btn btn-info">View Packing Sheet</button></a>
                            <a href="tb-profile.html"><button class="btn btn-info">View Receipt</button></a>
                            <div class="btn-group">
                                <div></div>
                            </div></div>
                    </div>
                </div>

                <div class="accordion-group widget-box">
                    <!--Order Two-->
                    <div class="accordion-heading">
                        <div class="widget-title">
                            <a data-toggle="collapse" href="#collapseGTwo" data-parent="#collapse-group">
                                <span class="icon">#2</span> <span><h5>01-21-14</h5><span class="label label-success">Shipped</span><h5>#97768</h5></span>
                                <span class="label label-default">Internal Order</span>
                            </a>
                        </div>
                    </div>
                    <div id="collapseGTwo" class="collapse accordion-body">
                        <div class="widget-content">
                            <h5>Order #97768 Info</h5>
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Tracking</th>
                                    <th>Ship Date</th>
                                    <th>Order Number</th>
                                    <th>Order Date</th>
                                    <th>Subtotal</th>
                                    <th>Tax</th>
                                    <th>Shipping</th>
                                    <th>Total</th>
                                    <th>Manage</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><span class="badge badge-success">Shipped</span></td>
                                    <td>Fedex <a target="_blank" href="https://www.fedex.com/fedextrack/?tracknumbers=599489318347&amp;cntry_code=us&amp;language=en">599489318347</a></td>
                                    <td>03-04-14</td>
                                    <td>97767</td>
                                    <td>02-21-14</td>
                                    <td>$130.00</td>
                                    <td>$0.00</td>
                                    <td>$14.25</td>
                                    <td>$144.25</td>
                                    <td>
                                        <a data-toggle="modal" data-original-title="Please create an internal order if you need to send an extra package!" class="btn tip-bottom btn-danger" href="#cancelOrder">Cancel Order</a>
                                        <a href="tb-profile.html"><button data-original-title="Please use old Admin system to process a return" class="btn btn-warning disabled tip-bottom">Return Order</button></a>
                                        <br>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                <tr><th>Notes</th></tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <span>Reason for Internal Order goes here by Admin #67</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                            <h5>Items Received</h5>
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Item Name</th>
                                    <th>Category</th>
                                    <th>QTY</th>
                                    <th>Price</th>
                                    <th>Total Price</th>
                                    <th>Size</th>
                                    <th>Color</th>
                                    <th>Manage</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <?php echo Html::img('/web/img/demo/img1.jpg') ?>
                                    </td>
                                    <td>Torrance NVY</td>
                                    <td>Fashion Tee</td>
                                    <td>1</td>
                                    <td>$38.00</td>
                                    <td>$38.00</td>
                                    <td>LRG</td>
                                    <td>Black</td>
                                    <td>
                                        <a data-toggle="modal" data-original-title="Will only work if the order has not shipped" class="btn tip-bottom disabled" href="#changeSize"><i class="icon-pencil"></i> Change Size</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php echo Html::img('/web/img/demo/img1.jpg') ?>
                                    </td>
                                    <td>Torrance NVY</td>
                                    <td>Fashion Tee</td>
                                    <td>1</td>
                                    <td>$38.00</td>
                                    <td>$38.00</td>
                                    <td>LRG</td>
                                    <td>Black</td>
                                    <td>
                                        <a data-toggle="modal" data-original-title="Will only work if the order has not shipped" class="btn tip-bottom disabled" href="#changeSize"><i class="icon-pencil"></i> Change Size</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo Html::img('/web/img/demo/img1.jpg') ?></td>
                                    <td>Torrance NVY</td>
                                    <td>Fashion Tee</td>
                                    <td>1</td>
                                    <td>$38.00</td>
                                    <td>$38.00</td>
                                    <td>LRG</td>
                                    <td>Black</td>
                                    <td>
                                        <a data-toggle="modal" data-original-title="Will only work if the order has not shipped" class="btn tip-bottom disabled" href="#changeSize"><i class="icon-pencil"></i> Change Size</a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                            <!--Buttons to manage order-->
                            <a href="tb-profile.html"><button class="btn btn-info">View Packing Sheet</button></a>
                            <a href="tb-profile.html"><button class="btn btn-info">View Receipt</button></a>
                            <div class="btn-group">
                                <div></div>
                            </div>
                        </div>
                    </div></div>

                <div class="accordion-group widget-box">
                    <!--Order one-->
                    <div class="accordion-heading">
                        <div class="widget-title">
                            <a data-toggle="collapse" href="#collapseGOne" data-parent="#collapse-group">
                                <span class="icon">#1</span> <span><h5>12-21-14</h5><span class="label label-danger">Cancelled</span><h5>#97767</h5></span> <span class="label label-info pull-right">Club Order</span>
                            </a>
                        </div>
                    </div>
                    <div id="collapseGOne" class="collapse accordion-body">
                        <div class="widget-content">
                            <h5>Order #97767 Info</h5>
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Tracking</th>
                                    <th>Ship Date</th>
                                    <th>Order Number</th>
                                    <th>Order Date</th>
                                    <th>Subtotal</th>
                                    <th>Tax</th>
                                    <th>Shipping</th>
                                    <th>Total</th>
                                    <th>Notes</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><span class="badge badge-important">Cancelled</span></td>
                                    <td>Fedex <a target="_blank" href="https://www.fedex.com/fedextrack/?tracknumbers=599489318347&amp;cntry_code=us&amp;language=en">599489318347</a></td>
                                    <td>03-04-14</td>
                                    <td>97767</td>
                                    <td>02-21-14</td>
                                    <td>$130.00</td>
                                    <td>$0.00</td>
                                    <td>$14.25</td>
                                    <td>$144.25</td>
                                    <td>
                                        <a data-toggle="modal" data-original-title="Please create an internal order if you need to send an extra package!" class="btn tip-bottom btn-danger disabled" href="#cancelOrder">Cancel Order</a>
                                        <a href="tb-profile.html"><button data-original-title="Please use old Admin system to process a return" class="btn btn-warning disabled tip-bottom">Return Order</button></a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>Notes</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <span>Reason for the cancelled order goes here by Admin #67</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <h5>Items Received</h5>
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Item Name</th>
                                    <th>Category</th>
                                    <th>QTY</th>
                                    <th>Price</th>
                                    <th>Total Price</th>
                                    <th>Size</th>
                                    <th>Color</th>
                                    <th>Manage</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><?php echo Html::img('/web/img/demo/img1.jpg') ?></td>
                                    <td>Torrance NVY</td>
                                    <td>Fashion Tee</td>
                                    <td>1</td>
                                    <td>$38.00</td>
                                    <td>$38.00</td>
                                    <td>LRG</td>
                                    <td>Black</td>
                                    <td>
                                        <a data-toggle="modal" data-original-title="Will only work if the order has not shipped" class="btn tip-bottom disabled" href="#changeSize"><i class="icon-pencil"></i> Change Size</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo Html::img('/web/img/demo/img1.jpg') ?></td>
                                    <td>Torrance NVY</td>
                                    <td>Fashion Tee</td>
                                    <td>1</td>
                                    <td>$38.00</td>
                                    <td>$38.00</td>
                                    <td>LRG</td>
                                    <td>Black</td>
                                    <td>
                                        <a data-toggle="modal" data-original-title="Will only work if the order has not shipped" class="btn tip-bottom disabled" href="#changeSize"><i class="icon-pencil"></i> Change Size</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo Html::img('/web/img/demo/img1.jpg') ?></td>
                                    <td>Torrance NVY</td>
                                    <td>Fashion Tee</td>
                                    <td>1</td>
                                    <td>$38.00</td>
                                    <td>$38.00</td>
                                    <td>LRG</td>
                                    <td>Black</td>
                                    <td>
                                        <a data-toggle="modal" data-original-title="Will only work if the order has not shipped" class="btn tip-bottom disabled" href="#changeSize"><i class="icon-pencil"></i> Change Size</a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <!--Buttons to manage order-->
                            <a href="tb-profile.html"><button class="btn btn-info">View Packing Sheet</button></a>
                            <a href="tb-profile.html"><button class="btn btn-info">View Receipt</button></a>
                            <div class="btn-group">
                                <div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>