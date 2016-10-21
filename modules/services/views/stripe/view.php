<?php

/* @var $this \yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $model \app\models\Order */
/* @var $customerInfo \app\models\CustomerEntityInfo */
/* @var $customerAddress \app\models\CustomerEntityAddress */
/* @var $order integer */
/* @var $items \app\models\ProductVariants[] */
/*  */

$this->title = "Stripe Information";

?>

<style>
.head_td{
	font-weight: bold;
	text-align: center;
}
</style>

<div id="content-header">
    <h1>Stripe Information</h1>
</div>
<div class="row">
	<div class="col-xs-12">
		<div class="container-fluid">
			<div class="widget-box collapsible">
				<div class="widget-title">
					<a data-toggle="collapse" href="#collapseTwo">
						<span class="icon"><i class="fa fa-search"></i></span>
						<h5>Stripe Information of Customer</h5>
					</a>
					<a class="btn btn-xs btn-default sync" href="javascript:void(0);" data-id="<?php echo $customer['customerId']; ?>" style="float:right; margin-top:6px; margin-right:17px;"><i class="fa fa-refresh"></i> Sync</a>
				</div>
				<div id="collapseTwo" class="in">
					<div class="widget-content nopadding">
						<div id="stripe_data">
							<div class="widget-box">
								<div class="widget-title nopadding">
								    <ul class="nav nav-tabs">
								        <li class="active"><a href="#tab1" data-toggle="tab"> Customer</a></li>
								        <li><a href="#tab2" data-toggle="tab"> Subscription</a></li>
								        <li><a href="#tab3" data-toggle="tab"> Plan</a></li>
								        <li><a href="#tab4" data-toggle="tab"> Card</a></li>
								        <li><a href="#tab5" data-toggle="tab"> Charge</a></li>
								        <li><a href="#tab8" data-toggle="tab"> Invoice</a></li>
								        <li><a href="#tab9" data-toggle="tab"> Refund</a></li>
								        <!--<li><a href="#tab7" data-toggle="tab"> Transfer</a></li>
								        <li><a href="#tab10" data-toggle="tab"> Balance History</a></li>-->
								    </ul>
								</div>
								<div class="widget-content tab-content">
									<div class="tab-pane active" id="tab1">
										<table class="table table-striped table-hover table-bordered" style="font-size:10px">
											<tr>
												<td class="head_td" style="width:25%">Created At</td>
												<td style="width:25%"><?php echo gmdate("m/d/Y H:i:s", $customer['created']); ?></td>
												<td class="head_td" style="width:25%">ID</td>
												<td style="width:25%"><?php echo $customer['customerId']; ?></td>
											</tr>
											<tr>
												<td class="head_td" style="width:25%">Live Mode</td>
												<td style="width:25%"><?php echo $customer['livemode']; ?></td>
												<td class="head_td" style="width:25%">Email</td>
												<td style="width:25%"><?php echo $customer['email']; ?></td>
											</tr>
											<tr>
												<td class="head_td" style="width:25%">Account Balance</td>
												<td style="width:25%"><?php echo number_format(floatval($customer['account_balance']), 2); ?></td>
												<td class="head_td" style="width:25%">Currency</td>
												<td style="width:25%"><?php echo $customer['currency']; ?></td>
											</tr>
											<tr>
												<td class="head_td" style="width:25%">Default Card</td>
												<td style="width:25%"><?php echo $customer['default_card']; ?></td>
												<td class="head_td" style="width:25%">Default Source</td>
												<td style="width:25%"><?php echo $customer['default_source']; ?></td>
											</tr>
											<tr>
												<td class="head_td" style="width:25%">Description</td>
												<td colspan='3'><?php echo $customer['description']; ?></td>
											</tr>
										</table>
									</div>
									<div class="tab-pane" id="tab2">
										<table class="table table-striped table-hover table-bordered" style="font-size:10px">
											<tr>
												<td class="head_td">Subscription ID</td>
												<td class="head_td">Status</td>
												<td class="head_td">Start Date</td>
												<td class="head_td">End Date</td>
												<td class="head_td">Cancel Date</td>
												<td class="head_td">Current Period</td>
												<td class="head_td">Trial Period</td>
												<td class="head_td">Quantity</td>
												<td class="head_td">Tax %</td>
												<td class="head_td">Application Fee %</td>
											</tr>
											<?php if (count($subscriptions) == '0') { ?>
												<tr>
													<td style="text-align:center" colspan='8'>No Subscription</td>
												</tr>
											<?php } else {?>
												<?php foreach($subscriptions as $subscription) { ?>
												<tr>
													<td><?php echo $subscription['subscriptionId']; ?></td>
													<td><?php echo $subscription['status']; ?></td>
													<td><?php echo gmdate("m/d/Y", $subscription['start']); ?></td>
													<td><?php if ($subscription['ended_at'] != null) echo gmdate("m/d/Y", $subscription['ended_at']); ?></td>
													<td><?php if ($subscription['canceled_at'] != null) echo gmdate("m/d/Y", $subscription['canceled_at']); ?></td>
													<td>
														<?php echo gmdate("m/d/Y", $subscription['current_period_start']).' ~ '.gmdate("m/d/Y", $subscription['current_period_end']); ?>
													</td>
													<td>
														<?php echo gmdate("m/d/Y", $subscription['trial_start']).' ~ '.gmdate("m/d/Y", $subscription['trial_end']); ?>
													</td>
													<td><?php echo $subscription['quantity']; ?></td>
													<td><?php if ($subscription['tax_percent'] != null) echo number_format($subscription['tax_percent'], 1); ?></td>
													<td><?php if ($subscription['application_fee_percent'] != null) echo number_format($subscription['application_fee_percent'], 1); ?></td>
												</tr>
												<?php } ?>
											<?php } ?>
										</table>
									</div>
									<div class="tab-pane" id="tab3">
										<table class="table table-striped table-hover table-bordered" style="font-size:10px">
											<tr>
												<td class="head_td">Plan ID</td>
												<td class="head_td">Plan Title</td>
												<td class="head_td">Billing Interval</td>
												<td class="head_td">Created Date</td>
												<td class="head_td">Amount</td>
												<td class="head_td">Currency</td>
												<td class="head_td">Trial Period Days</td>
											</tr>
											<?php if (count($plans) == '0') { ?>
												<tr>
													<td style="text-align:center" colspan='8'>No Plans</td>
												</tr>
											<?php } else {?>
												<?php foreach($plans as $plan) { ?>
												<tr>
													<td><?php echo $plan['planId']; ?></td>
													<td><?php echo $plan['name']; ?></td>
													<td>
														<?php echo 'Every '.$plan['interval_count'].' '.$plan['interval']; ?>
							                            <?php if ($plan['interval_count'] > 1) echo 's'; ?>
													</td>
													<td><?php echo gmdate("m/d/Y", $plan['created']); ?></td>
													<td><?php echo number_format(floatval($plan['amount']) / 100, 2); ?></td>
													<td><?php echo $plan['currency']; ?></td>
													<td><?php echo $plan['trial_period_days']; ?></td>
												</tr>
												<?php } ?>
											<?php } ?>
										</table>
									</div>
									<div class="tab-pane" id="tab4">
										<table class="table table-striped table-hover table-bordered" style="font-size:10px">
											<tr>
												<td class="head_td">Card ID</td>
												<td class="head_td">Type</td>
												<td class="head_td">Funding</td>
												<td class="head_td">Expire Date</td>
												<td class="head_td">Origin</td>
												<td class="head_td">Name</td>
												<td class="head_td">Address</td>
												<td class="head_td">Finger Print</td>
												<td class="head_td">CVC Check</td>
												<td class="head_td">Address Line1 Check</td>
												<td class="head_td">Zipcode Check</td>
											</tr>
											<?php if (count($cards) == '0') { ?>
												<tr>
													<td style="text-align:center" colspan='11'>No Cards</td>
												</tr>
											<?php } else {?>
												<?php foreach($cards as $card) { ?>
												<tr>
													<td><?php echo $card['cardId']; ?></td>
													<td><?php echo $card['brand']; ?></td>
													<td><?php echo $card['funding']; ?></td>
													<td><?php echo $card['exp_month'].'/'.$card['exp_year']; ?></td>
													<td><?php echo $card['country']; ?></td>
													<td><?php echo $card['name']; ?></td>
													<td><?php echo $card['address_line1'].' '.$card['address_line2'].' '.$card['address_city'].' '.$card['address_state'].' '.$card['address_zip'].' '.$card['address_country']; ?></td>
													<td><?php echo $card['fingerprint']; ?></td>
													<td><?php echo $card['cvc_check'];  ?></td>
													<td><?php echo $card['address_line1_check'];  ?></td>
													<td><?php echo $card['address_zip_check'];  ?></td>
												</tr>
												<?php } ?>
											<?php } ?>
										</table>
									</div>
									<div class="tab-pane" id="tab5">
										<table class="table table-striped table-hover table-bordered" style="font-size:10px">
											<tr>
												<td class="head_td">Charge ID</td>
												<td class="head_td">Created Date</td>
												<td class="head_td">Status</td>
												<td class="head_td">Amount</td>
												<td class="head_td">Currency</td>
												<td class="head_td">Card ID</td>
												<td class="head_td">Source ID</td>
												<td class="head_td">Invoice</td>
												<td class="head_td">Refunded Amount</td>
											</tr>
											<?php if (count($charges) == 0) { ?>
												<tr>
													<td style="text-align:center" colspan='9'>No Charges</td>
												</tr>
											<?php } else {?>
												<?php foreach($charges as $charge) { ?>
												<tr>
													<td><?php echo $charge['chargeId']; ?></td>
													<td><?php echo gmdate("m/d/Y H:i:s", $charge['created']); ?></td>
													<td><?php echo $charge['status']; ?></td>
													<td><?php echo number_format(floatval($charge['amount']) / 100, 2); ?></td>
													<td><?php echo $charge['currency']; ?></td>
													<td><?php echo $charge['cardId']; ?></td>
													<td><?php echo $charge['sourceId']; ?></td>
													<td><?php echo $charge['invoice']; ?></td>
													<td><?php echo number_format(floatval($charge['amount_refunded']) / 100, 2); ?></td>
												</tr>
												<?php } ?>
											<?php } ?>
										</table>
									</div>
									<div class="tab-pane" id="tab8">
										<table class="table table-striped table-hover table-bordered" style="font-size:10px;">
											<tr>
												<td class="head_td">Invoice ID</td>
												<td class="head_td">Invoice Date</td>
												<td class="head_td">Invoice Period</td>
												<td class="head_td">Amount Due</td>
												<td class="head_td">SubTotal</td>
												<td class="head_td">Total</td>
												<td class="head_td">Charge ID</td>
												<td class="head_td">Tax Percent / Amount</td>
												<td class="head_td">Starting Balance</td>
												<td class="head_td">Ending Balance</td>
											</tr>
											<?php if (count($invoices) == 0) { ?>
												<tr>
													<td style="text-align:center" colspan='9'>No Invoices</td>
												</tr>
											<?php } else { ?>
												<?php foreach($invoices as $invoice) { ?>
												<tr>
													<td><?php echo $invoice['invoiceId']; ?></td>
													<td><?php echo gmdate('m/d/Y', $invoice['created']); ?></td>
													<td><?php echo gmdate('m/d/Y', $invoice['period_start']).' ~ '.gmdate('m/d/Y', $invoice['period_end']); ?></td>
													<td><?php echo number_format(floatval($invoice['amount_due'])/100, 2); ?></td>
													<td><?php echo number_format(floatval($invoice['subtotal'])/100, 2); ?></td>
													<td><?php echo number_format(floatval($invoice['total'])/100, 2); ?></td>
													<td><?php echo $invoice['chargeId']; ?></td>
													<td><?php if ($invoice['tax'] != null) echo $invoice['tax_percent'].' / '.number_format(floatval($invoice['tax']) / 100, 2); ?></td>
													<td><?php if ($invoice['starting_balance'] != null) echo number_format(floatval($invoice['starting_balance']) / 100, 2); ?></td>
													<td><?php if ($invoice['ending_balance'] != null) echo number_format(floatval($invoice['ending_balance']) / 100, 2); ?></td>
												</tr>
												<?php } ?>
											<?php } ?>
										</table>
									</div>
									<div class="tab-pane" id="tab9">
										<table class="table table-striped table-hover table-bordered" style="font-size:10px;">
											<tr>
												<td class="head_td">Refund ID</td>
												<td class="head_td">Created Date</td>
												<td class="head_td">Amount</td>
												<td class="head_td">Currency</td>
												<td class="head_td">Balance Transaction</td>
												<td class="head_td">Charge</td>
												<td class="head_td">Receipt Number</td>
												<td class="head_td">Reason</td>
											</tr>
											<?php if (count($refunds) == 0) { ?>
												<tr>
													<td style="text-align:center" colspan='8'>No Refunds</td>
												</tr>
											<?php } else { ?>
												<?php foreach($refunds as $refund) { ?>
												<tr>
													<td><?php echo $refund['refundId']; ?></td>
													<td><?php echo gmdate('m/d/Y', $refund['created']); ?></td>
													<td><?php echo number_format(floatval($refund['amount']) / 100, 2); ?></td>
													<td><?php echo $refund['currency']; ?></td>
													<td><?php echo $refund['balance_transaction']; ?></td>
													<td><?php echo $refund['chargeId']; ?></td>
													<td><?php echo $refund['receipt_number']; ?></td>
													<td><?php echo $refund['reason']; ?></td>
												</tr>
												<?php } ?>
											<?php } ?>
										</table>
									</div>
								</div>
							</div>
						</div>		
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="message" style="display: none;">
    <h4><i class="fa fa-spinner fa-pulse"></i> Syncing customer data...</h4>
</div><!-- /.modal -->

<?php
$search_script = <<<JS
    $(function(){
        $('.sync').click(function(){
            var customerId = $(this).attr('data-id');
            var i = $(this);
            $.blockUI({ message: $("#message") });
            $.ajax({
                url: '/services/stripe/sync',
                type: 'POST',
                data: {customerId: customerId},
                success: function(data) {},
                complete: function(data) {
                	window.location.href = '';
                }
            });
        });
    });
JS;
$this->registerJs($search_script, \yii\web\View::POS_END);
?>