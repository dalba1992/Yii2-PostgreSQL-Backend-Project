<?php 
use app\models\CustomerEntityInfo;
use app\models\TradegeckoOrderLog;
use app\models\TradegeckoInfo;
use app\models\TradegeckoOderItemsLog;

?>

<div class="widget-box">
	<div class="widget-title">
		<span class="icon">
			<i class="fa fa-th-list"></i>
		</span>
		<h5>Previously Sent</h5>
	</div>
	
	<div class="widget-content">
		<?php 
			if(sizeof($chargeduser)>0){ ?>
			<?php foreach($chargeduser as $customer){
					$customer_id = $customer->customer_id;
					$custinfo = CustomerEntityInfo::find()->where(['customer_id'=>$customer_id])->one();
					$TradegeckoOrderLog = TradegeckoOrderLog::find()->where(['customer_id'=>$customer_id])
																	->orderBy(['id'=>SORT_DESC])->all();
					
					//print_r($TradegeckoOrderLog); die;														
					if(sizeof($TradegeckoOrderLog>0)){	
						foreach($TradegeckoOrderLog as $orders){
					?>		
						<span class="order_info"><span class="order_title">Order:<?php echo $orders->tradegecko_order_number ?></span>
							<?php 
								$TradegeckoOderItemsLog = TradegeckoOderItemsLog::find()->where(['log_order_id' => $orders->id])->all();
								foreach($TradegeckoOderItemsLog as $item){ ?>
									<span class="sku"> SKU: <?php echo $item->item_id?></span>
								<?php }
							?>
							<span><b><?php echo $orders->created_at ?></b></span>
						</span>
						
						<!--<img width="100" height="100" data-original-title="Zakon" class="tip-top" alt="Zakon" src="<?php echo Yii::$app->request->BaseUrl ?>/demo/square.jpg">-->
					
					<?php } ?>
					
					<div style="clear:both"></div>
			<?php	}else { ?>
						<span class="no_order"> No Order Found Yet!</span>
					<?php } ?>
		<?php 	}
			} ?>
	</div>
</div>