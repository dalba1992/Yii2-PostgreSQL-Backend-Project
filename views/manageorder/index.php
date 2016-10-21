<?php 
use app\models\CustomerSubscription;
use app\models\CustomerEntityInfo;
use app\models\CustomerEntityAddress;
use app\models\TbAttribute;
use app\models\TbAttributeDetail;
use app\models\CustomerEntityAttribute;
use app\models\TradegeckoOrderLog;
use app\models\TradegeckoInfo;
use app\models\TradegeckoOderItemsLog;
$date = date('Y-m');
$this->title = "Manual Order Queue";
?>
<script src="<?php echo Yii::$app->request->BaseUrl.'/js/jquery-ui.js'?>"></script>
<style type="text/css">
	.charge_month{
		margin-left:20px;
	}
	.not-active {
	   pointer-events: none;
	   cursor: default;
	}
</style>
	<div id="content-header">
		<h1>Manual Queue</h1>
		<?php 
			$count_user=0;
			$total_count = 0;
			$total_charged_customer = 0;
			if(isset($_GET['total_charge'])){
				$total_charged_customer = $_GET['total_charge'];
			}
			
			$count=0;
			if(isset($_GET['offset']) && $_GET['offset']>0){
				$count = $_GET['offset'];
				$count_user = $count+1;
			}
			$chargeduser = null;
			$customer_id = 0;
			$stripe_customer_id = '';
			if(isset($_GET['from']) && $_GET['from']>0){
				if(Yii::$app->session->get('chargeduser')){
					$chargeduser = Yii::$app->session->get('chargeduser');
					foreach($chargeduser as $chargecustomer){
						$stripe_customer_id = $chargecustomer->source->customer;
						if($stripe_customer_id !=null){
							++$count;
							$total_count = $count;
							$customer_info = CustomerSubscription::find()->where(['stripe_customer_id'=>$stripe_customer_id])->one();
							if(sizeof($customer_info)>0){
								$customer_id = $customer_info->customer->id;
								break;
							}
						}
					}
				}
			}else{
				if(Yii::$app->session->get('chargeduser')){
					unset($_SESSION['chargeduser']);
				}
			}
		?>
		<div class="charge_month">
				<div class="charge_info">
				<script>
					$.noConflict();
					jQuery(document).ready(function($){
						$( "#from_datepicker" ).datepicker();
					});
					
				</script>
				<form action="<?php echo Yii::$app->homeurl.'/manageorder/submitdate/' ?>" method="post" name="chaged_user">
					<span class="charge_input">Charged Date: <input type="text" required="" id="from_datepicker" name="from"></span>
					<span class="charge_submit"><input type="submit" title="" data-original-title="" value="Fetch User" class="btn btn-large tip-bottom"></span>		
				</form>	
			</div>
		</div>
		<div class="btn-group">
			<?php 
				$prevurl = null;
				$nexturl = null;
				
				if($count>0 && $_GET['from']>0){
					//echo $count;die;
					$prev = --$count;
					$next = $prev+1; 
					$prevurl = Yii::$app->homeurl.'/manageorder/submitdate/?prev=1&offset='.$prev .'&from='.$_GET['from'].'&total_charge='.$total_charged_customer;;
					$nexturl = Yii::$app->homeurl.'/manageorder/submitdate/?offset='.$next.'&from='.$_GET['from'].'&total_charge='.$total_charged_customer;
				} ?>
			<a href="<?php if($prevurl!=null){ echo $prevurl; } else{ ?> javascript:; <?php } ?>" class="btn btn-large tip-bottom" title="Will not save your current changes"><i class="icon-arrow-left"></i> Previous</a>
			<a href="#" class="btn btn-large disabled" title="Manage Users"><i class="icon-user"></i> User <?php echo $total_count ?> of <?php echo $total_charged_customer ?> from <?php echo $total_charged_customer ?></a>
			<a href="<?php if($nexturl!=null){ echo $nexturl; } else{ ?> javascript:; <?php } ?>" class="btn btn-large tip-bottom" title="Will not save your current changes">Next <i class="icon-arrow-right"></i></a>
		</div>
	</div>
	 		
	<div class="container-fluid">
		<!--Notifications-->
		<?php if(Yii::$app->session->hasFlash('success')) { ?>
				<div class="alert alert-success">
					<button class="close" data-dismiss="alert">x</button>
					<strong>Success!</strong> <?php echo Yii::$app->session->get('success'); ?>
				</div>
		<?php } ?>
		<?php if(Yii::$app->session->hasFlash('error')) { ?>
				<div class="alert alert-error">
					<button class="close" data-dismiss="alert">x</button>
					<strong>Error!</strong> <?php echo Yii::$app->session->get('error'); ?>
				</div>
		<?php } ?>
		<div class="widget-box collapsible">
			<?php echo Yii::$app->view->renderFile('@app/views/manageorder/filter.php'); ?>  
		</div>	
		<?php 
		if(isset($chargeduser) && sizeof($chargeduser)>0 && $customer_id >0){
			$custinfo = CustomerEntityInfo::find()->where(['customer_id'=>$customer_id])->one();
			$custaddress = CustomerEntityAddress::find()->where(['customer_id'=>$customer_id,'type_id'=>2])->one();
		
	?>			

	<div class="widget-content nopadding">
		<form method = "post" action="<?php echo Yii::$app->homeUrl.'/manageorder/createmanualorder/'?>">
			<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>Info </th>
						<th>Item 1</th>
						<th>Item 2</th>
						<th>Item 3 </th>
						<th>Item 4 </th>
						<th>Item 5 </th>
						<th>Item 6 </th>
						<th>Total</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<b><?php echo $custinfo->first_name.' '.$custinfo->last_name ;?>
							<input type="hidden" value="<?php echo $customer_id ?>" name="manualorder[customer_id]" ></input>
							</b>
							<br><?php echo $custaddress->address ?><br><?php echo $custaddress->city.','.$custaddress->state.','.$custaddress->country.','.$custaddress->zipcode ?><br><?php echo $custinfo->email?>
							<br>
							<?php 
							$TbAttribute = 	TbAttribute::find()->orderBy(['id' => SORT_ASC,])->all();
							if(sizeof($TbAttribute)>0){
								foreach($TbAttribute as $attributeinfo){
									$style = '';
									if($attributeinfo->is_active){
										$customerAttribute = CustomerEntityAttribute::find()->where([
																									'customer_id' => $customer_id,
																									'attribute_id' => $attributeinfo->id
																									])->all();
																								
								
										if($attributeinfo->is_multiselect){
											if(sizeof($customerAttribute)>0){
												foreach($customerAttribute as $attribute){
													$TbAttributeDetail = TbAttributeDetail::findOne($attribute->value);
													$style = $TbAttributeDetail->title.', '.$style;
													
												}
												?>
												<b><?php echo $attributeinfo->tooltip_bar_title ?></b>
												<br>
												<?php echo $style ?>
												<br>
												<?php
											}
										}else if(sizeof($customerAttribute)>0){																
											
												foreach($customerAttribute as $attribute){
													$TbAttributeDetail = TbAttributeDetail::findOne($attribute->value);
													?>
													<b><?php echo $attributeinfo->tooltip_bar_title?></b>
													<br>
													<?php echo $TbAttributeDetail->title; ?>
													<br>
													<?php
												}
											}															
									}
								}
							}?>
							<b>Member Since: </b><br><?php echo date('Y-m-d', strtotime($customer_info->created_at)); ?>
						</td>
					<?php for($item_count=1;$item_count<=6;$item_count++){ ?>	
						<td>
							
							<div class="control-group">
								<div class="controls">
								<?php $products = Yii::$app->tradeGeckoHelper->getTradegeckoProduct() ?>
																
									<select id="product<?php echo $item_count ?>" name="manualorder[product<?php echo $item_count ?>][productid]" onchange="getproduct(<?php echo $item_count ?>);" style="width:100%;max-width:90%;">
										<option rel='' value="0">Please Select Product</option>
										<?php foreach($products as $key=> $product){?>
										<option rel='<?php echo $key ?>' value ='<?php echo $product['id'] ?>'><?php echo $product['name'] ?></option>
										<?php }?>
									</select>
									
								</div>
								<div class="controls">
									<div class="category_wrap">
										<span class="cat_info" id="category<?php echo $item_count; ?>" class="category">
											Category:
										</span>
									</div>
								</div>
							</div>
							<center class="image_center">
								<img id="product_img<?php echo $item_count ?>" src="<?php echo Yii::$app->request->BaseUrl ?>/web/img/noimage.gif" alt="" width="130">
								<br><br>
								<a title="Remove this Product" href="#" class="tip-top btn-small btn btn-danger"><i class="icon-trash icon-white"></i> Remove</a>
								<br><br>
								<p >$<span id="retail_price_info<?php echo $item_count ?>">120.00</span> MSRP</p>
							</center>
							<div class="control-group">
								<div class="controls">
									<span class="size_color_wrap">Variation(Size/Color)</span>
									<select id="variation<?php echo $item_count ?>" name="manualorder[product<?php echo $item_count ?>][sku]" onchange="changeimage(<?php echo $item_count ?>);" style="width:100%;max-width:90%;">
										<option image_url="" value="">No Variation Found!</option>
									</select>
									<input type="hidden" id="variation_id<?php echo $item_count ?>" name="manualorder[product<?php echo $item_count ?>][variation_id]" value=""></input>
									<input type="hidden" id="retail_price<?php echo $item_count ?>" name="manualorder[product<?php echo $item_count ?>][retail_price]" value=""></input>
									
								</div>							
							</div>
							<center><h4>14 Left</h4></center>
						</td>
					<?php } ?>	
						<td>
							<center>
								<div class="form-actions">
									<div class="">Retail Pricing: <b>$120.00</b></div>
									<br>
									<?php $picked_order_status = TradegeckoOrderLog::find()->where(['customer_id' => $customer_id])->andWhere(['like', 'created_at', $date])->one(); 
										if(sizeof($picked_order_status) == 0) { ?>		
											<button type="submit" class="btn btn-large btn-success">Submit Order</button>
										<?php }else{ ?>
											<span>Order Has Been Picked For This Month. </span>
										<?php } ?>
								</div>
							</center> 
						</td>
					</tr>
				</tbody>
			</table>							
		</form>
	</div>
	
	<script type="text/javascript">
	function changeimage(item){
		var image_url = jQuery('#variation'+item).find('option:selected').attr('image_url');
		var variant_id = jQuery('#variation'+item).find('option:selected').attr('variant_id');
		var retail_price = jQuery('#variation'+item).find('option:selected').attr('retail_price');
		if(image_url!= ''){
			jQuery('#product_img'+item).attr('src',image_url);
		}else{
			var image_url = "<?php echo Yii::$app->request->BaseUrl.'/web/img/noimage.gif' ?>";
			jQuery('#product_img'+item).attr('src',image_url);
		}
		if(variant_id!=''){
			jQuery("#variation_id"+item).val(variant_id);
		}else{
			jQuery("#variation_id"+item).val('');
		}
		if(retail_price!=''){
			jQuery("#retail_price"+item).val(retail_price);
			jQuery("#retail_price_info"+item).html(retail_price);
		}else{
			jQuery("#retail_price"+item).val('');
			jQuery("#retail_price_info"+item).html('0.00');
		}
	}
	function getproduct(item){
		var products = <?php echo json_encode($products) ?>;
		var rel = jQuery('#product'+item).find('option:selected').attr('rel');
		if(rel!=''){
			var category = products[rel]['category'];
			var image_url = products[rel]['image_url'];
			jQuery('#category'+item).html('Category:'+category);
			jQuery('#product_img'+item).attr('src',image_url);
			
			var option = '<option value="">Please select Variations!</option>';
			for(var i=0;i<products[rel]['variant_detail'].length; i++){
				option = option+'<option retail_price="'+products[rel]['variant_detail'][i]['retail_price']+'" variant_id="'+products[rel]['variant_detail'][i]['variant_id']+'" image_url="'+products[rel]['variant_detail'][i]['image_url']+'" value="'+products[rel]['variant_detail'][i]['sku']+'" >'+products[rel]['variant_detail'][i]['size_color']+'</option>';
			}
			
			jQuery("#variation"+item).empty().append(option);
			
		}else{
			var image_url = "<?php echo Yii::$app->request->BaseUrl.'/web/img/noimage.gif' ?>";
			jQuery('#category'+item).html('Category:');
			jQuery('#product_img'+item).attr('src',image_url);
			jQuery("#variation"+item).empty().append('<option value="">Please select Variations!</option>');
		}
			
	}
</script>
	<?php }else{ ?>
		<div class="widget-content nopadding">
			<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>Info</th>								
						</tr>
				</thead>
				<tbody>
				<tr>
					<td><?php echo "No Result found!".$stripe_customer_id; ?></td>
					</tr>
				</tbody>
			</table>
		</div>			
			
	<?php } ?>
			
		<div class="row-fluid">
			<div class="span12">
				<div class="widget-box">
					<div class="widget-title">
						<span class="icon">
							<i class="icon-th-list"></i>
						</span>
						<h5>Previously Sent</h5>
					</div>
					<div class="widget-content">
						<img src="<?php echo Yii::$app->request->BaseUrl ?>/demo/square.jpg" alt="Zakon" width="100" height="100" class="tip-top" data-original-title="Zakon">
						<img src="<?php echo Yii::$app->request->BaseUrl ?>/demo/square.jpg" alt="Product Title" width="100" height="100" class="tip-top" data-original-title="Product Title">
						<img src="<?php echo Yii::$app->request->BaseUrl ?>/demo/square.jpg" alt="Product Title" width="100" height="100" class="tip-top" data-original-title="Product Title">
						<img src="<?php echo Yii::$app->request->BaseUrl ?>/demo/square.jpg" alt="Product Title" width="100" height="100" class="tip-top" data-original-title="Product Title">
						<img src="<?php echo Yii::$app->request->BaseUrl ?>/demo/square.jpg" alt="Product Title" width="100" height="100" class="tip-top" data-original-title="Product Title">
						<img src="<?php echo Yii::$app->request->BaseUrl ?>/demo/square.jpg" alt="Product Title" width="100" height="100" class="tip-top" data-original-title="Product Title">
						<img src="<?php echo Yii::$app->request->BaseUrl ?>/demo/square.jpg" alt="Product Title" width="100" height="100" class="tip-top" data-original-title="Product Title">
						<img src="<?php echo Yii::$app->request->BaseUrl ?>/demo/square.jpg" alt="Product Title" width="100" height="100" class="tip-top" data-original-title="Product Title">
						<img src="<?php echo Yii::$app->request->BaseUrl ?>/demo/square.jpg" alt="Product Title" width="100" height="100" class="tip-top" data-original-title="Product Title">
						<img src="<?php echo Yii::$app->request->BaseUrl ?>/demo/square.jpg" alt="Product Title" width="100" height="100" class="tip-top" data-original-title="Product Title">
						<img src="<?php echo Yii::$app->request->BaseUrl ?>/demo/square.jpg" alt="Product Title" width="100" height="100" class="tip-top" data-original-title="Product Title">
						<img src="<?php echo Yii::$app->request->BaseUrl ?>/demo/square.jpg" alt="Product Title" width="100" height="100" class="tip-top" data-original-title="Product Title">
						<img src="<?php echo Yii::$app->request->BaseUrl ?>/demo/square.jpg" alt="Product Title" width="100" height="100" class="tip-top" data-original-title="Product Title">
						<img src="<?php echo Yii::$app->request->BaseUrl ?>/demo/square.jpg" alt="Product Title" width="100" height="100" class="tip-top" data-original-title="Product Title">
						<img src="<?php echo Yii::$app->request->BaseUrl ?>/demo/square.jpg" alt="Product Title" width="100" height="100" class="tip-top" data-original-title="Product Title">
						<img src="<?php echo Yii::$app->request->BaseUrl ?>/demo/square.jpg" alt="Product Title" width="100" height="100" class="tip-top" data-original-title="Product Title">
						<img src="<?php echo Yii::$app->request->BaseUrl ?>/demo/square.jpg" alt="Product Title" width="100" height="100" class="tip-top" data-original-title="Product Title">
						<img src="<?php echo Yii::$app->request->BaseUrl ?>/demo/square.jpg" alt="Product Title" width="100" height="100" class="tip-top" data-original-title="Product Title">
					</div>
				</div>
			</div>
		</div>
	</div>
