<?php
/**
 * @var $this yii\web\View
 * @var $totalOrders int
 */

use app\models\CustomerSubscriptionOrder;

$this->title = 'Dashboard';
$orders = CustomerSubscriptionOrder::find()->indexBy('id')->count();
?>

<div id="content-header">
    <h1>Dashboard</h1>
</div>
<div id="breadcrumb">
    <a class="tip-bottom" title="Dashboard" href="javscript:;"><i class="fa fa-home"></i> Home</a>
</div>

<div class="row">
    <div class="col-md-12">
        <?php   echo Yii::$app->controller->renderPartial
        		(
        			'//site/dashboard/stats', 
	        		[
	        			'totalOrders' => $totalOrders,
		                'activeOrders' => $activeOrders,
		                'draftOrders' => $draftOrders,
		                'finalizedOrders' => $finalizedOrders,
		                'fulfilledOrders' => $fulfilledOrders,
		                'returnedOrders' => $returnedOrders,
		                'paidOrders' => $paidOrders,
		                'unpaidOrders' => $unpaidOrders,
		                'sentOrders' => $sentOrders,
	        		]
	        	); 
        ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?php   echo Yii::$app->controller->renderPartial
        		(
        			'//site/dashboard/charts', 
        			[
        				'totalOrders' => $totalOrders,
		                'activeOrders' => $activeOrders,
		                'draftOrders' => $draftOrders,
		                'finalizedOrders' => $finalizedOrders,
		                'fulfilledOrders' => $fulfilledOrders,
		                'returnedOrders' => $returnedOrders,
		                'paidOrders' => $paidOrders,
		                'unpaidOrders' => $unpaidOrders,
		                'sentOrders' => $sentOrders,
		                'stats' => $stats,
		                'monday' => $monday,
		                'sunday' => $sunday,
        			]
        		); ?>
    </div>
</div>