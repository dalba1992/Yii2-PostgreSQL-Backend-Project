<?php
$this->title = "Tradegecko Order's";
use app\models\TradegeckoInfo;
use app\models\Litevieworderlog;
?>
<div id="content-header">
	<h1>Tradegecko Duplicate Orders</h1>
	<div style="width: auto;" class="btn-group">
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
				<?php if(isset($_GET['p']) && $_GET['p']> $prevpage ) {?> 
			<a class="btn btn-large tip-bottom" data-original-title="Previous 100 Orders" href="<?php echo Yii::$app->homeUrl.'tradegecko/duplicateorders?p='.$prevpage; ?>" title=""> <i class="icon-arrow-left"></i> Previous </a>
				<?php } ?>
			<a href="javascript:;" class="btn btn-large disabled" > <i class="icon-user"></i> 
			<?php echo "TG Orders from <b>". $from ."</b> to <b>". $to ."</b>"; ?> </a> 
				<a data-original-title="Next 100 Orders" href="<?php echo Yii::$app->homeUrl.'tradegecko/duplicateorders?p='.$nextpage; ?>" class="btn btn-large tip-bottom" title=""> Next <i class="icon-arrow-right"></i></a>
		</div>
</div>
<div class="container-fluid">
		<div class="widget-box collapsible ">
		<div class="widget-title tradegecko_wrap">
			<a data-toggle="collapse" href="#collapseTwo">
				<span class="icon"><i class="icon-signal"></i></span>
				<h5>All Tradegecko Duplicate Finallized Order's</h5>
			</a>
		</div>
		<div id="collapseTwo" class="collapse in">
			<div class="widget-content nopadding">
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th></th>
							<th>Customer Id</th>
							<th>Company Id</th>
							<th>TG Email</th>
							<th>TG Order Number</th>
							<th>TG Order Id</th>
							<th>TG Order Status</th>
							<th>Dupicate TG Order Number</th>
							<th>Dupicate TG Order Id</th>
							<th>Dupicate TG Order Status</th>
						</tr>
					</thead>
					
					<tbody>
						<?php
						 if(sizeof($order['orders'])>0){
							$count = 0;
							if(isset($_GET['p']) && $_GET['p']>1){
								$count = $_GET['p'] *100;
							}
							foreach($order['orders'] as $company_id=>$Tradegecko_orderinfo){
								++$count;
					 		?> 
							<tr>
								<td><?php echo $count; ?> </td>
								<?php 	
									$customer = TradegeckoInfo::find()->where(['company_id'=>$company_id])->one();
								?>
								<td>
									<?php   $customer_id = '';
										if(sizeof($customer) > 0){ 
											$customer_id = $customer->customer_id;
											echo $customer->customer_id;
									 }?>
								</td>
								<td><?php echo $company_id ?></td>

								<?php
									$email=null;
								 foreach($Tradegecko_orderinfo as $order_id=>$order_detail) { 
								 	if($email==null){
								 		$email = $order_detail['email'];
								 	?>
										<td><?php echo $email; ?></td>
									<?php } ?>
								<td><?php echo "#".$order_detail['order_number']; ?></td>
								<td><?php echo $order_detail['id']; ?></td>
								<td><?php echo $order_detail['status']; ?></td>
								<?php } ?>
							</tr>
						<?php 
							
							} 
						}else{ ?> 
								<tr>
									<td class="no_order" colspan="9">No duplicate Orders In Queue.Try next..</td>
								</tr>
								
						<?php } 
						?>
					</tbody>
				
				</table>
			</div>
		</div>
	</div>  
</div>
<style>
.pagination .btn 
{
    margin-right: 12px;
    margin-top: 3px;
}
.widget-title.tradegecko_wrap .pagination
{
	margin-right: 0px;
}
@media screen and (max-width:1120px) and (min-width:300px)
{
	#content-header .btn-group 
	{
    	position: static;
	}
}
</style>