<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\components\Helper;
use yii\widgets\LinkPager;

$this->title = 'All Member';
?>
<script type="text/javascript">
	function editmember(memberid){
		// var url = '<?php echo Yii::$app->request->BaseUrl."/index.php/members/profile/?member="?>'+memberid;
		window.location='<?php echo Yii::$app->request->BaseUrl."/index.php/members/profile/?member="?>' +memberid;
	}
</script>
<div class="row">
	<div class="col-md-12">
		<div class="widget-box">
			<div class="widget-title">
				<h5>All Members</h5>
				<form id="page-changer" name="form1" method="GET" action="">
					<div class="filter">
						<label>Show
							<select  class="pagination" onchange="<?php if(isset($_GET['page'])){?>window.location.href='?per-page='+this.options[this.selectedIndex].text+'&page=<?php echo $_GET['page']; ?>'<?php }else { ?>window.location.href='?per-page='+this.options[this.selectedIndex].text<?php } ?>">
								<option value="1" <?php if(isset($_GET['per-page']) && ($_GET['per-page']==10)) {?> selected="selected" <?php } ?>>10</option>
								<option value="2" <?php if(isset($_GET['per-page']) && ($_GET['per-page']==15)) {?> selected="selected" <?php } ?>>15</option>
								<option value="3" <?php if(isset($_GET['per-page']) && ($_GET['per-page']==20)) { echo 'selected="selected"'; }?>>20</option>
								<option value="4" <?php if(isset($_GET['per-page']) && ($_GET['per-page']==25)) { echo 'selected="selected"'; }?>>25</option>
							</select>&nbsp;entries
						</label>
					</div>
				</form>
			</div>

			<div class="container-fluid">
				<div class="row-fluid">
					<div class="span12">
						<div class="widget-box">
							<div class="widget-title">
								<h5>All Members</h5>
								<form id="page-changer" name="form1" method="GET" action="">
									<div class="filter"><label>Show
											<select  class="pagination" onchange="<?php if(isset($_GET['page'])){?>window.location.href='?per-page='+this.options[this.selectedIndex].text+'&page=<?php echo $_GET['page']; ?>'<?php }else { ?>window.location.href='?per-page='+this.options[this.selectedIndex].text<?php } ?>">
												<option value="1" <?php if(isset($_GET['per-page']) && ($_GET['per-page']==10)) {?> selected="selected" <?php } ?>>10</option>
												<option value="2" <?php if(isset($_GET['per-page']) && ($_GET['per-page']==15)) {?> selected="selected" <?php } ?>>15</option>
												<option value="3" <?php if(isset($_GET['per-page']) && ($_GET['per-page']==20)) { echo 'selected="selected"'; }?>>20</option>
												<option value="4" <?php if(isset($_GET['per-page']) && ($_GET['per-page']==25)) { echo 'selected="selected"'; }?>>25</option>
											</select>&nbsp;entries</label>
									</div>
								</form>
							</div>


							<?php //$limit = isset($_GET['per-page'])?$_GET['per-page']:10;?>


							<div class="widget-content nopadding">

								<?= GridView::widget([
									'dataProvider' => $dataProvider,
									'filterModel' => $searchModel,
									'options' => [
										'class' => 'ascending',
									],

									'columns' => [
										//        ['class' => 'yii\grid\SerialColumn'],

										['attribute' => 'is_active',
											'label'=>'eStatus',
											'format' => 'raw',
											'filter' =>false,
											'value'=>function ($data) {
												return ($data->is_active==\app\models\CustomerEntity::STATUS_ACTIVE)? '<span class="label label-success">Active</span>':'<span class="label">Not active</span>';
											},
										],

										['attribute' => 'customerEntityStatuses.status',
											'label'=>'vStatus',
											'format' => 'raw',
											'value' => function($data){
												return Yii::$app->message->status($data->id);
											}
										],
										['attribute' => 'id',
											'label'=>'Member ID',
										],

										'customerEntityInfos.email',

										['attribute' => 'customerEntityInfos.first_name',
											'label'=>'First Name',

										],

										['attribute' =>  'customerEntityInfos.last_name',
											'label'=>'Last Name',
										],


										[
											'attribute' => 'customerSubscription.stripe_customer_id',
											'label' => 'ARB ID',
											'format' => 'raw',
											'value' => function($data){
												return Yii::$app->message->arbid($data->id);
											}
										],

										['attribute' => 'customerEntityAddresses.zipcode',
											'format' => 'raw',
											'value' => function($data){
												return Yii::$app->message->zipcode($data->id);
											}
										],

										['attribute' => 'created_at',
											'filter' =>false,
										],

										['attribute' => 'customerSubscriptionOrders.id',
											'label'=>'Orders',
											'format' => 'raw',
											'value' => function ($data) {
												return 'N/A';
											},
										],

										[
											'format' => 'raw',
											'label' => 'Manage',
											'value' => function ($data) {
												return Yii::$app->message->manage($data->id);
											},
										],
										//     ['class' => 'yii\grid\ActionColumn'],
									],
								]); ?>

								<div class="widget-content nopadding oTableLite">
									<?= GridView::widget([
										'dataProvider' => $dataProvider,
										'filterModel' => $searchModel,
										'options' => [
											'class' => 'ascending',
										],
										'columns' => [
											//['class' => 'yii\grid\SerialColumn'],
											['attribute' => 'is_active', 'label'=>'eStatus',  'format' => 'raw', 'filter' =>false,
												'value'=>function ($data) {
													return ($data->is_active==1)? '<span class="label label-success">Active</span>':'<span class="label">Not active</span>';
												},
											],
											['attribute' => 'customerEntityStatuses.status', 'label'=>'vStatus',  'format' => 'raw',
												'value' => function($data){
													return Yii::$app->message->status($data->id);
												}
											],
											['attribute' => 'id', 'label'=>'Member ID'],
											'customerEntityInfos.email',
											['attribute' => 'customerEntityInfos.first_name', 'label'=>'First Name'],
											['attribute' =>  'customerEntityInfos.last_name',  'label'=>'Last Name'],
											['attribute' => 'customerSubscription.stripe_customer_id', 'label' => 'ARB ID', 'format' => 'raw',
												'value' => function($data){
													return Yii::$app->message->arbid($data->id);
												}
											],
											['attribute' => 'customerEntityAddresses.zipcode', 'format' => 'raw',
												'value' => function($data){
													return Yii::$app->message->zipcode($data->id);
												}
											],
											['attribute' => 'created_at', 'filter' =>false],
											['attribute' => 'customerSubscriptionOrders.id', 'label'=>'Orders', 'format' => 'raw',
												'value' => function ($data) {
													return 'N/A';
												},
											],
											['format' => 'raw', 'label' => 'Manage',
												'value' => function ($data) {
													return Yii::$app->message->manage($data->id);
												},
											],
										],
									]); ?>
								</div>
							</div>
						</div>
					</div>
					<style>
						.oTableLite table{
							table-layout: fixed !important;
							word-wrap: break-word !important;
						}
						.oTableLite table tbody td{ font-size: 87% !important;}
						.label-default {  background-color: #777;  }
					</style>
