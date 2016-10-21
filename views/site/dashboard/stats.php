<?php
/**
 * @var $this yii\web\View
 */

use app\models\CustomerSubscriptionOrder;
?>
<div class="widget-box">
    <div class="widget-title"><span class="icon"><i class="fa fa-line-chart"></i></span><h5>Statistics</h5></div>
    <div class="widget-content">
        <div class="row">
            <div class="col-md-4">
                <ul class="site-stats">
                    <li><i class="icon-plane"></i> <strong><?php echo $totalOrders;?></strong> <small>Total Orders</small></li>
                    <li><i class="icon-arrow-right"></i> <strong><?php echo $activeOrders;?></strong> <small>Active Orders</small></li>
                    <li class="divider"></li>
                    <li><i class="icon-ok-sign"></i> <strong><?php echo $draftOrders;?></strong> <small>Draft Orders</small></li>
                    <!--<li><i class="icon-remove"></i> <strong>4,786</strong> <small>Cancelled Orders</small></li>-->

                </ul>
            </div>
            <div class="col-md-4">
                <ul class="site-stats">
                    <li><i class="icon-backward"></i> <strong><?php echo $finalizedOrders;?></strong> <small>Finalized Orders</small></li>
                    <li><i class="icon-warning-sign"></i> <strong><?php echo $fulfilledOrders;?></strong> <small>Fulfilled Orders</small></li>
                    <li class="divider"></li>
                    <li><i class="icon-exclamation-sign"></i> <strong><?php echo $returnedOrders;?></strong> <small>Returned Orders</small></li>
                    <!--<li><i class="icon-pause"></i> <strong>53</strong> <small>Queued Orders</small></li>-->

                </ul>
            </div>
            <div class="col-md-4">
                <ul class="site-stats">
                    <li><i class="icon-remove"></i> <strong><?php echo $paidOrders;?></strong> <small>Paid Orders</small></li>
                    <li><i class="icon-flag"></i> <strong><?php echo $unpaidOrders;?></strong> <small>Unpaid Orders</small></li>
                    <li class="divider"></li>
                    <li><i class="icon-arrow-left"></i> <strong><?php echo $sentOrders;?></strong> <small>Sent Orders</small></li>
                    <!--<li><i class="icon-exclamation-sign"></i> <strong>93</strong> <small>Fraudulent Orders</small></li>-->
                </ul>
            </div>
        </div>
    </div>
</div>