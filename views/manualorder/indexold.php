<?php 
use Yii;
use app\models\Statuslog;
use app\models\TradegeckoOrderLog;

	$this->title = 'Manual Order';	
	$current_day = date('d');
		
	if($current_day<14){
		$charge_start_date = strtotime( date('Y-m-14') . " -1 month " );
		$charge_start_date = date('Y-m-d',$charge_start_date);
	}else{
		$charge_start_date = date('Y-m-14');
	}
	
	$nextmonthDate = strtotime( $charge_start_date . " +1 month " );
	$charge_end_date = date("Y-m-d", $nextmonthDate);
	$Total_month_order = 0;
	$order_in_month = date('Y-m',strtotime($charge_start_date));
	$Total_created_order = 0;
	
	if(isset($search) && sizeof($search)>0){
		$Total_chargeUser = sizeof($chargeduser);
		if(sizeof($search) == 1){
			if(isset($_GET['state']) && $_GET['state']!='' && isset($_GET['mailid']) && $_GET['mailid']!=''){
				$query = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN (select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN(select customer_id from tradegecko_importcsv_order_log where created_at Like '%".$order_in_month."%' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."'))))";
				$Total_created_order = TradegeckoOrderLog::findBySql($query)->all();
				$Total_created_order = sizeof($Total_created_order);
			}else if(isset($_GET['state']) && $_GET['state']!=''){
				$query = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN(select customer_id from tradegecko_importcsv_order_log where created_at Like '%".$order_in_month."%' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."')))";
				$Total_created_order = TradegeckoOrderLog::findBySql($query)->all();
				$Total_created_order = sizeof($Total_created_order);
			}else if(isset($_GET['mailid']) && $_GET['mailid']!=''){
				$query = "select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN(select customer_id from tradegecko_importcsv_order_log where created_at Like '%".$order_in_month."%' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."')))";
				$Total_created_order = TradegeckoOrderLog::findBySql($query)->all();
				$Total_created_order = sizeof($Total_created_order);
			}else{
				$query = "select customer_id from tradegecko_importcsv_order_log where created_at Like '%".$order_in_month."%' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."')) ";
				$Total_created_order = TradegeckoOrderLog::findBySql($query)->all();
				$Total_created_order = sizeof($Total_created_order);
			}	
		}else if(sizeof($search) == 2){
			if(isset($_GET['state']) && $_GET['state']!='' && isset($_GET['mailid']) && $_GET['mailid']!=''){
				$query = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN (select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN(select customer_id from tradegecko_importcsv_order_log where created_at Like '%".$order_in_month."%' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."')))))";
				$Total_created_order = TradegeckoOrderLog::findBySql($query)->all();
				$Total_created_order = sizeof($Total_created_order);
			}else if(isset($_GET['state']) && $_GET['state']!=''){
				$query = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN(select customer_id from tradegecko_importcsv_order_log where created_at Like '%".$order_in_month."%' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."'))))";
				$Total_created_order = TradegeckoOrderLog::findBySql($query)->all();
				$Total_created_order = sizeof($Total_created_order);
			}else if(isset($_GET['mailid']) && $_GET['mailid']!=''){
				$query = "select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN(select customer_id from tradegecko_importcsv_order_log where created_at Like '%".$order_in_month."%' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."'))))";
				$Total_created_order = TradegeckoOrderLog::findBySql($query)->all();
				$Total_created_order = sizeof($Total_created_order);
			}else{
				$query = "select customer_id from tradegecko_importcsv_order_log where created_at Like '%".$order_in_month."%' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."'))) ";
				$Total_created_order = TradegeckoOrderLog::findBySql($query)->all();
				$Total_created_order = sizeof($Total_created_order); 
			}	
		}else if(sizeof($search) == 3){
			if(isset($_GET['state']) && $_GET['state']!='' && isset($_GET['mailid']) && $_GET['mailid']!=''){
				$query = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN (select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN(select customer_id from tradegecko_importcsv_order_log where created_at Like '%".$order_in_month."%' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."'))))))";
				$Total_created_order = TradegeckoOrderLog::findBySql($query)->all();
				$Total_created_order = sizeof($Total_created_order);
			}else if(isset($_GET['state']) && $_GET['state']!=''){
				$query = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN(select customer_id from tradegecko_importcsv_order_log where created_at Like '%".$order_in_month."%' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."')))))";
				$Total_created_order = TradegeckoOrderLog::findBySql($query)->all();
				$Total_created_order = sizeof($Total_created_order);
			}else if(isset($_GET['mailid']) && $_GET['mailid']!=''){
				$query = "select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN(select customer_id from tradegecko_importcsv_order_log where created_at Like '%".$order_in_month."%' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."')))))";
				$Total_created_order = TradegeckoOrderLog::findBySql($query)->all();
				$Total_created_order = sizeof($Total_created_order);
			}else{
				$query = "select customer_id from tradegecko_importcsv_order_log where created_at Like '%".$order_in_month."%' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."'))))";
				$Total_created_order = TradegeckoOrderLog::findBySql($query)->all();
				$Total_created_order = sizeof($Total_created_order);
			}	
		}else if(sizeof($search) == 4){
			if(isset($_GET['state']) && $_GET['state']!='' && isset($_GET['mailid']) && $_GET['mailid']!=''){
				$query = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN (select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN(select customer_id from tradegecko_importcsv_order_log where created_at Like '%".$order_in_month."%' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."' AND customer_id IN (select customer_id from customer_entity_attribute where value ='".$search[3]."')))))))";
				$Total_created_order = TradegeckoOrderLog::findBySql($query)->all();
				$Total_created_order = sizeof($Total_created_order);
			}else if(isset($_GET['state']) && $_GET['state']!=''){
				$query = "select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN(select customer_id from tradegecko_importcsv_order_log where created_at Like '%".$order_in_month."%' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."' AND customer_id IN (select customer_id from customer_entity_attribute where value ='".$search[3]."'))))))";
				$Total_created_order = TradegeckoOrderLog::findBySql($query)->all();
				$Total_created_order = sizeof($Total_created_order);
			}else if(isset($_GET['mailid']) && $_GET['mailid']!=''){
				$query = "select customer_id from customer_entity_info where email = '".$_GET['mailid']."' AND customer_id IN(select customer_id from tradegecko_importcsv_order_log where created_at Like '%".$order_in_month."%' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."' AND customer_id IN (select customer_id from customer_entity_attribute where value ='".$search[3]."'))))))";
				$Total_created_order = TradegeckoOrderLog::findBySql($query)->all();
				$Total_created_order = sizeof($Total_created_order);
			}else{
				$query = "select customer_id from tradegecko_importcsv_order_log where created_at Like '%".$order_in_month."%' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_attribute where value = '".$search[0]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[1]."' AND customer_id IN (select customer_id from customer_entity_attribute where value = '".$search[2]."' AND customer_id IN (select customer_id from customer_entity_attribute where value ='".$search[3]."')))))";
				$Total_created_order = TradegeckoOrderLog::findBySql($query)->all();
				$Total_created_order = sizeof($Total_created_order);
			}	
		}
		
	}else if(isset($_GET['state']) && $_GET['state']!='' && isset($_GET['mailid']) && $_GET['mailid']!=''){
		$Total_chargeUser = sizeof($chargeduser);
		$query = "select customer_id from tradegecko_importcsv_order_log where created_at Like '%".$order_in_month."%' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_address where state = '".$_GET['state']."' AND customer_id IN (select customer_id from customer_entity_info where email = '".$_GET['mailid']."')))";
		$Total_created_order = TradegeckoOrderLog::findBySql($query)->all();
		$Total_created_order = sizeof($Total_created_order);  
		
	}else if(isset($_GET['state']) && $_GET['state']!=''){
		$Total_chargeUser = sizeof($chargeduser);
		$query = "select customer_id from tradegecko_importcsv_order_log where created_at Like '%".$order_in_month."%' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_address where state = '".$_GET['state']."'))";
		$Total_created_order = TradegeckoOrderLog::findBySql($query)->all();
		$Total_created_order = sizeof($Total_created_order);  
		
	}else if(isset($_GET['mailid']) && $_GET['mailid']!=''){
		$Total_chargeUser = sizeof($chargeduser);
		$query = "select customer_id from tradegecko_importcsv_order_log where created_at Like '%".$order_in_month."%' AND customer_id IN(select customer_id from customer_status_log where status='active' AND (created_at BETWEEN '".$charge_start_date."' AND '".$charge_end_date."') AND customer_id IN(select customer_id from customer_entity_info where email = '".$_GET['mailid']."'))";
		$Total_created_order = TradegeckoOrderLog::findBySql($query)->all();
		$Total_created_order = sizeof($Total_created_order);  
	}else{
		$Total_chargeUser = Statuslog::find()->where(['status'=>'active'])
										->andWhere(['between', 'created_at', $charge_start_date, $charge_end_date])
										->count();

	
		$Total_created_order = TradegeckoOrderLog::find() ->where('created_at LIKE :query')
												->addParams([':query'=>'%'.$order_in_month.'%'])->count();
	}
	
	$page = 1;
	$next= $page+1;
	$prev = $page;
	if(isset($_GET['p']) && $_GET['p']>1 ){
		$next = $_GET['p']+1;
		$prev = $_GET['p']-1;
	}
	
	$increament_user = 1;
	if(isset($_GET['p'])){
		$increament_user = $_GET['p'];
		if(isset($_GET['allmember']) && isset($_GET['style']) && isset($_GET['tsize']) && 
		isset($_GET['bsize']) && isset($_GET['bfit']) && isset($_GET['state']) && isset($_GET['mailid'])){
			$Total_chargeUser = $countuser;
		}
	}
	$url = '';
	if(isset($_GET['allmember']) && isset($_GET['style']) && isset($_GET['tsize']) && 
		isset($_GET['bsize']) && isset($_GET['bfit']) && isset($_GET['state']) && isset($_GET['mailid'])){
		
		$url = "&allmember=".$_GET['allmember']."&style=".$_GET['style']."&tsize=".$_GET['tsize']."&bsize=".$_GET['bsize']."&bfit=".$_GET['bfit']."&state=".$_GET['state']."&mailid=".$_GET['mailid'];
		//echo '<br>'.$url ; die;
	}
?>
<div id="content-header">
	<h1>Manual Queue</h1>
	<div class="btn-group" style="width: auto;">
		<?php if(isset($_GET['p'])){?>
			<a title="" class="btn btn-large tip-bottom" href="<?php echo Yii::$app->homeurl.'manualorder?p='.$prev.$url ?>" data-original-title="Will not save your current changes">
				<i class="fa fa-arrow-left"></i> Previous
			</a>
			<a title="Manage Users" class="btn btn-large disabled" href="javascript:;">
				<i class="fa fa-user"></i> User
				<?php echo $increament_user ?> of <?php echo $Total_created_order ?> from <?php echo $Total_chargeUser; ?>
			</a>
			<a title="" class="btn btn-large tip-bottom" href="<?php echo Yii::$app->homeurl.'manualorder?p='.$next.$url ?>" data-original-title="Will not save your current changes">
				Next <i class="fa fa-arrow-right"></i>
			</a>
<?php 	}else{?>
			<a title="" class="btn btn-large tip-bottom" href="<?php echo Yii::$app->homeurl.'manualorder?p=1'.$url ?>" data-original-title="Will not save your current changes">
				<i class="fa fa-arrow-left"></i> Previous
			</a>
			<a title="Manage Users" class="btn btn-large disabled" href="javascript:;">
				<i class="fa fa-user"></i> User <?php echo $increament_user ?> of <?php echo $Total_created_order ?> from <?php echo $Total_chargeUser; ?>
			</a>
			<a title="" class="btn btn-large tip-bottom" href="<?php echo Yii::$app->homeurl.'manualorder?p=2'.$url ?>" data-original-title="Will not save your current changes">
				Next <i class="fa fa-arrow-right"></i>
			</a>
		<?php } ?>
	</div>
</div>

<div class="container-fluid">
	<!--Notifications-->
	<?php if(Yii::$app->session->getFlash('success')){?>
		<div class="alert alert-success">
			<button data-dismiss="alert" class="close">x</button>
			<strong>Success!</strong> <?php echo Yii::$app->session->getFlash('success'); ?>
		</div>
	<?php } ?>	
	
	<?php if(Yii::$app->session->getFlash('fail')){?>
		<div class="alert alert-error">
			<button data-dismiss="alert" class="close">x</button>
			<strong>Error!</strong> <?php echo Yii::$app->session->getFlash('fail'); ?>
		</div>
	<?php } ?>
	<div class="widget-box collapsible">
		<?php
			echo Yii::$app->view->renderFile('@app/views/manualorder/filter.php');
		?>
	</div>				
	<form action="<?php echo Yii::$app->homeUrl.'manualorder/submitorder'?>" method="post">
		<div class="widget-content nopadding">
			<?php echo Yii::$app->view->renderFile('@app/views/manualorder/info.php',['chargeduser' => $chargeduser]); ?>  							
		</div>
	</form>		
	<div class="row-fluid">
		<div class="span12">
			<?php echo Yii::$app->view->renderFile('@app/views/manualorder/previoussent.php',['chargeduser' => $chargeduser]); ?>  
		</div>
	</div>
</div>
