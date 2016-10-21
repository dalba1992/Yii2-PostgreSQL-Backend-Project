<?php 
	use yii\helpers\Html;
	use yii\grid\GridView;
	use app\components\Helper;
	use yii\widgets\LinkPager;
	$this->title = "Create Tradegecko Order";
?>
<div id="content-header">
	<h1>Create an Internal Order on Tradegecko</h1>
	<!--<div class="btn-group">
		<a href="tb-manual-order.html" class="btn btn-large tip-bottom" title="Will not save your current changes"><i class="icon-arrow-left"></i> Previous</a>
		<a href="#" class="btn btn-large disabled" title="Manage Users"><i class="icon-user"></i> User 2 of 4,655 from 15,221</a>
		<a href="tb-manual-order.html" class="btn btn-large tip-bottom" title="Will not save your current changes">Next <i class="icon-arrow-right"></i></a>
	</div>-->
</div>

<div class="container-fluid">
						<!--Notifications-->
			<div style="display:none" class="alert alert-success">
				<button class="close" data-dismiss="alert">x</button>
				<strong>Success!</strong> internal order created for Nadir Hyder.
			</div>
			<div style="display:none" class="alert alert-error">
				<button class="close" data-dismiss="alert">x</button>
				<strong>Error!</strong> Internal order has failed, please try again.
			</div>

			<div class="widget-box collapsible">
				<div class="widget-title">
					<a href="#collapseTwo" data-toggle="collapse">
						<span class="icon"><i class="icon-signal"></i></span>
						<h5>Create Order At Tradegecko by Import Csv </h5>
					</a>
				</div>
				<div class="collapse in" id="collapseTwo">
					<div class="widget-content nopadding">
					   <table class="table table-bordered table-striped table-hover">
						<thead>
							<tr>
								<th>Browse the Order Csv</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<div class="control-group ">
										<div class="controls">
											<form enctype ='multipart/form-data' method="post" name="tradegecko_order" action="<?php echo Yii::$app->homeUrl.'/order/submitorder'?>">
												<input required type="file" name="file" id="csvfile" placeholder="Import Order Csv"><br>
												<button type="submit" name="submit_csv" class="btn btn-success">Create Order</button>
											</form>
										</div>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
					</div>
				</div>
			</div>   
	<div class="row-fluid">
		<div class="span12">
			<div class="widget-box">
				<div class="widget-title">
					<span class="icon">
						<i class="icon-th-list"></i>
					</span>
					<h5>All Order's</h5>
				</div>
				<div class="widget-content">
					Order Log Grid	
				</div>
			</div>
		</div>
	</div>
</div>