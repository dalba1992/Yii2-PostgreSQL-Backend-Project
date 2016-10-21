<?php
$this->title = "Tradegecko Order's";
use app\models\TradegeckoInfo;
use app\models\Litevieworderlog;
?>
<div id="content-header">
	<h1>All Tradegecko Order's</h1>
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
				<h5>Information About All Tradegecko Finallized Order's</h5>
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
				<?php if(isset($_GET['p']) && $_GET['p']> $prevpage ) {?> 
					<a class="btn" href="<?php echo Yii::$app->homeUrl.'/tradegecko/?p='.$prevpage.'&limit=100' ?>">prev</a>
				<?php } ?>
				<?php  if(sizeof($order['orders'])>0){?>
					<a class="btn" href="<?php echo Yii::$app->homeUrl.'/tradegecko/?p='.$nextpage.'&limit=100' ?>">next</a>
				<?php } ?>
			</span>
		</div>
		<div id="collapseTwo" class="collapse in">
			<div class="widget-content nopadding">
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th></th>
							<th>Customer Id</th>
							<th>Company Id</th>
							<th>Tradegecko Order Number</th>
							<th>Tradegecko Order Id</th>
							<th>Tradegecko Order Status</th>
							<th>Created_at</th>
							<th>Liteview Status</th>
							<th>Action</th>
						</tr>
					</thead>
					
					<tbody>
						<?php
						 if(sizeof($order['orders'])>0){
							$count = 0;
							if(isset($_GET['p'])){
								$count = $_GET['p'] *100;
							}
							foreach($order['orders'] as $Tradegecko_orderinfo){
								++$count;
					 		#-----------------------------------------------------------------------
							# Show Only Not sent orders
							#-----------------------------------------------------------------------		
					 		$Litevieworderlog = Litevieworderlog::find()->where(['tradegecko_order_id'=>$Tradegecko_orderinfo['id']])
																				  ->orderBy(['id'=>SORT_DESC])->one();	

					 		if(sizeof($Litevieworderlog)==0){
							#-----------------------------------------------------------------------
							# Show Only Not sent orders
							#-----------------------------------------------------------------------	

							?> 
							<tr>
								<td><?php echo $count; ?> </td>
								<?php 	
									$customer = TradegeckoInfo::find()->where(['company_id'=>$Tradegecko_orderinfo['company_id']])->one();
								?>
								<td>
									<?php   $customer_id = '';
										if(sizeof($customer) > 0){ 
											$customer_id = $customer->customer_id;
											echo $customer->customer_id;
									 }?>
								</td>
								<td><?php echo $Tradegecko_orderinfo['company_id'] ?></td>
								<td><?php echo "#".$Tradegecko_orderinfo['order_number'] ?></td>
								<td><?php echo $Tradegecko_orderinfo['id'] ?></td>
								<td><?php echo $Tradegecko_orderinfo['status'] ?></td>
								
								<td><?php 
										$created_at = explode('T',$Tradegecko_orderinfo['created_at']);
										echo $created_at[0]; ?>
								</td>
								<td>
								<?php //Moved to the top: $Litevieworderlog = Litevieworderlog::find()->where(['tradegecko_order_id'=>$Tradegecko_orderinfo['id']])
									  //											  ->orderBy(['id'=>SORT_DESC])->one();
									  if(sizeof($Litevieworderlog)>0){
										if($Litevieworderlog->liteview_order_status){
											echo "Yes";
										}else{
											echo 'Not';
										}	
									  }else{
											echo 'Not';
									  }											  
										
								?>
									
								</td>
								<td>
									<?php 
										if(sizeof($Litevieworderlog)>0){
											if(!$Litevieworderlog->liteview_order_status){ ?>
												<a href="<?php echo Yii::$app->homeUrl.'/liteview/sendtoliteview?cus_id='.$customer_id.'&cmp_id='.$Tradegecko_orderinfo['company_id'].'&o_id='.$Tradegecko_orderinfo['id']?>" class="btn">Send to Liteview</a>
									<?php   }
									}else{ ?> 
											<a href="<?php echo Yii::$app->homeUrl.'/liteview/sendtoliteview?cus_id='.$customer_id.'&cmp_id='.$Tradegecko_orderinfo['company_id'].'&o_id='.$Tradegecko_orderinfo['id']?>" class="btn">Send to Liteview</a>	
										<?php }	?>
								</td>
							</tr>
						<?php 
							#-----------------------------------------------------------------------
							# Show Only Not sent orders
							#-----------------------------------------------------------------------	
							}
							#-----------------------------------------------------------------------
							# Show Only Not sent orders
							#-----------------------------------------------------------------------		
							} 
						}else{ ?> 
								<tr>
									<td class="no_order" colspan="9">No Tradegecko Order In Queue..</td>
								</tr>
								
						<?php } 
						?>
					</tbody>
				
				</table>
			</div>
		</div>
	</div>  
</div>
