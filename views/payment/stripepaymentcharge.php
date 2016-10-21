<?php
$this->title = "Customer Payment Information";
print_r($payment);die;
?>
<div id="content-header">
	<h1>Stipe Payment Information</h1>
</div>
<div class="container-fluid">
						<!--Notifications-->
	<div style="display:none" class="alert alert-success">
		<button data-dismiss="alert" class="close">x</button>
		<strong>Success!</strong> internal order created for TB.
	</div>
	<div style="display:none" class="alert alert-error">
		<button data-dismiss="alert" class="close">x</button>
		<strong>Error!</strong> Internal order has failed, please try again.
	</div>
			
	<div class="widget-box collapsible ">
		<div class="widget-title tradegecko_wrap">
			<a data-toggle="collapse" href="#collapseTwo">
				<span class="icon"><i class="icon-signal"></i></span>
				<h5>Information About All Payment's for Customer At Stripe</h5>
			</a>
			
			<span class=pagination>
				<?php 
					$page = 1;
					$nextpage = 2;
					$prevpage = 1;
					if(isset($_GET['p']) && $_GET['p']!=null){
						$nextpage = $_GET['p'] + 1;
						if($_GET['p']>=2){
							$prevpage = $_GET['p'] - 1;
						}
					}
				?>
				<?php /* if(isset($_GET['p']) && $_GET['p']> $prevpage ) {?> 
					<a class="btn" href="<?php echo Yii::$app->homeUrl.'/tradegecko/?p='.$prevpage.'&limit=100' ?>">prev</a>
				<?php } ?>
				<?php  if(sizeof($order['orders'])>0){?>
					<a class="btn" href="<?php echo Yii::$app->homeUrl.'/tradegecko/?p='.$nextpage.'&limit=100' ?>">next</a>
				<?php } */ ?>
			</span>
		</div>
		<div id="collapseTwo" class="collapse in">
			<div class="widget-content nopadding">
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th></th>
							<th>Customer Id</th>
							<th>email</th>
							<th>Charge</th>
							<th>Charge Created At</th>
						</tr>
					</thead>
					
					<tbody>
						<tr>
							<td>1</td>
							<td>1</td>
							<td>email</td>
							<td>paid</td>
							<td>2015</td>
						</tr>
					</tbody>
				
				</table>
			</div>
		</div>
	</div>  
</div>