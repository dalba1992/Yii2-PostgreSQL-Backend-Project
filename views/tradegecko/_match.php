<?php
use yii\helpers\Url;
$this->title = "Customer Order Details";

?>
<div id="content-header">
	<h1>Customer Order Mismatch Details</h1>

						

		<div style="width: auto;" class="btn-group">
				<?php if($next > 25){ ?>	
			<a class="btn btn-large tip-bottom" data-original-title="Previous 25 Orders" href="<?php echo Yii::$app->homeUrl.'tradegecko/checkorder?offset='.$prev; ?>" title=""> <i class="icon-arrow-left"></i> Previous </a>
				<?php } ?>
			<a href="javascript:;" class="btn btn-large disabled" > <i class="icon-user"></i> 
			<?php echo "Log Orders from <b>". $from ."</b> to <b>". $to ."</b>"; ?> </a> 
				<a data-original-title="Next 25 Orders" href="<?php echo Yii::$app->homeUrl.'tradegecko/checkorder?offset='.$next; ?>" class="btn btn-large tip-bottom" title=""> Next <i class="icon-arrow-right"></i></a>
		</div>
</div>
<div class="container-fluid">
	<div class="widget-box collapsible ">
		<div class="widget-title tradegecko_wrap">
			<a data-toggle="collapse" href="#collapseTwo">
				<span class="icon"><i class="icon-signal"></i></span>
				<h5>Information About Different Tradegecko Order's</h5>
					
			</a>
			
			<span class=pagination>
					<form method="post" action="<?= Url::to(['tradegecko/updateorder']) ?>">
								<input type="hidden" value="<?php echo htmlspecialchars(json_encode($DataProvider)); ?>" name="order_data">
								<input type="hidden" name="keys" id="val" value="" />
								<input type="hidden" name="offset" id="offset" value="<?= $offset ?>" />
								<input type="submit" class="btn" onclick="javascript:return checkselect();" id="submit" name="post" value="Update" />
						
						</form>
			</span>
		</div>
		<div id="collapseTwo" class="collapse in">
			<div class="widget-content nopadding">
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th><input type="checkbox" id="checkall" name="select"></th>
							<th>Sr</th>
							<th>Email</th>
							<th>Customer Name</th>
							<th>Order ID</th>
							<th>Created at</th>
							<th>Order Details in DB</th>
							<th>Order Details at Tradegecko</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$all_keys='';
							if(sizeof($DataProvider)>0){
								$i=1;
								$all_keys='';
								foreach($DataProvider as $key=>$value) { 
									$all_keys .=$i.',';
									?>
								<tr>
									<td style="text-align:center;">
										<input type="checkbox" class="checkbox" data-key="<?= $i ?>" id="select<?= $i ?>">
									</td>
									<td><?= $i++ ?></td>
									<td><?= $value['EMAIL'] ?></td>
									<td><?= $value['CUSTOMER'] ?></td>
									<td><?= $value['ORDER_ID'] ?></td>
									<td><?= $value['DATE'] ?></td>
									<td><?php foreach($value['DB'] as $prd){
											echo $prd."<br>";
										} ?></td>
									<td><?php foreach($value['Tradegecko'] as $prd){
											echo $prd."<br>";
										} ?></td>
								</tr>
							<?php } }else{?>
										<tr>
											<td colspan="6">No mismatch of Records found from Log Order ID <b><?= $from ?></b> to <b><?= $to ?></b></td>	
										</tr>
							<?php } ?>
					</tbody>
				
				</table>
			</div>
		</div>
	</div>  
</div>

<script>
function checkselect()
{
	if($('#val').val() == "")
	{
		alert('please select atleast one records');
		return false;
	}else{
		return true;
	}
}
$(document).ready(function(){
	
	$('.checkbox').click(function(){
		data = $(this).data("key");
	    if ($(this).prop('checked')) {
	       	$("#val").val($("#val").val()+data+",");
		}else{
	    	$("#val").val($("#val").val().replace(data+',',''));
	    	$('#checkall').prop("checked","");
	    }
	});

	$("#checkall").click(function(){
		if($(this).prop('checked')){
			$('.checkbox').prop("checked","true");
			$("#val").val("<?= $all_keys ?>");
		}else{
			$('.checkbox').prop("checked","");
			$("#val").val("");
		}
	});
	
});
</script>
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