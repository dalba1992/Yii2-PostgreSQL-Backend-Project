<?php
use app\models\CustomerSubscription;
use app\models\CustomerEntityInfo;
use app\models\CustomerEntityAddress;
use app\models\TbAttributeDetail;
use app\models\CustomerEntityAttribute;
use app\models\TradegeckoOrderLog;
use app\models\TradegeckoOrderLogSearch;
use app\models\TradegeckoInfo;
use app\models\TradegeckoOderItemsLog;
$this->title = 'Charged Users';
$date = date('Y-m');

?>
	<div id="content-header">
		<h1>Charged Users Order</h1>
		<div class="datepicker_widget">
			<form method="post" action="<?php echo Yii::$app->request->BaseUrl.'/index.php/displaychargedusers/getchargeduser'?>" id="daterangeform" name="date-range-form">
				<input type="text" name="first_date" id="first_date"
				   data-beatpicker="true"
				   data-beatpicker-position="['*','*']"
				   data-beatpicker-range="true" 
				   data-beatpicker-format="['YYYY','MM','DD'],separator:'-'" 
				   data-beatpicker-id="myDatePicker"
				/>
				<input type="hidden" id="fromdate" value="" name="fromdate"/>
				<input type="hidden" id="todate" value="" name="todate"/>
				<input type="button" class="btn btn-success" value="Submit" name="submit" onclick="date_range_fromSubmit();" />
			</form>
		</div>

	<?php 
			$nextpage = 2;
			$prevpage = 1;
			if(isset($_GET['page']) && $_GET['page']>0){
				$nextpage = $_GET['page'] + 1;
				$prevpage = $_GET['page'] - 1;
			}
		?>
	
		<div class="custom_pagination">
			<?php if(isset($_GET['from']) && isset($_GET['to'])){?>
					<?php if(isset($_GET['page']) && $prevpage >=1){?>	
						<a href="<?php echo Yii::$app->homeUrl.'displaychargedusers/fetchchargeduser/?from='.$_GET['from'].'&to='.$_GET['to'].'&page='.$prevpage ?>" class="btn btn-large tip-bottom">
							<i class="icon-arrow-left"></i> Previous
						</a>
					<?php } ?>
					<?php if(sizeof($charges->data)>0){?>
						<a href="<?php echo Yii::$app->homeUrl.'displaychargedusers/fetchchargeduser/?from='.$_GET['from'].'&to='.$_GET['to'].'&page='.$nextpage ?>" class="btn btn-large tip-bottom">
							Next<i class="icon-arrow-right"></i> 
						</a>
					<?php } ?>	
			<?php }else{?>
					<?php if(isset($_GET['page']) && $prevpage >=1){?>	
						<a href="<?php echo Yii::$app->homeUrl.'displaychargedusers/fetchchargeduser/?page='.$prevpage ?>" class="btn btn-large tip-bottom">
							<i class="icon-arrow-left"></i> Previous
						</a>
					<?php }
					//echo "nextpagfdfe->".$nextpage;die;
					?>
					<?php if(sizeof($charges->data)>0){?>
						<a href="<?php echo Yii::$app->homeUrl.'displaychargedusers/fetchchargeduser/?page='.$nextpage ?>" class="btn btn-large tip-bottom">
							Next<i class="icon-arrow-right"></i>
						</a>
					<?php } ?>	
				<?php } ?>
		</div>	
	</div>

	
<div class="container-fluid">
	
	<div class="row-fluid">
		<div class="span12">
			<div class="widget-box">
				<div class="widget-title">
					<h5>All Charged Members</h5>
				</div>
				<div class="widget-content nopadding charged_users">
					<table class="table table-striped table_sku">
						<thead>
							<tr>
								<th>ID</th>
								<th>Email</th>
								<th>First Name</th>
								<th>Last Name</th>
								<th>State</th>
								<?php if(sizeof($TbAttribute)>0){
										foreach($TbAttribute as $attributeinfo){
											if($attributeinfo->is_active){?>
												<th><?php echo $attributeinfo->tooltip_bar_title?></th>															
										<?php }
									}
								}?>
								<th>Date Sign Up</th>
								<th>Item 1 SKU</th>
								<th>Item 2 SKU</th>
								<th>Past SKUs</th>
								<th></th>

							</tr>
						</thead>
						<tbody>
						<?php 
							$count = 0;
							$status = "paid";
							if(isset($_GET['status']) && $_GET['status']!=null){
								$status = 'paid';
							}
							if(sizeof($charges->data)>0){
								foreach($charges->data as $charge)
								{
									if($status == $charge->status)
									{
										$custid=$charge->source->customer;
										$stripe_cust_id=CustomerSubscription::find()->where(['stripe_customer_id'=>$custid])->one();
										if(sizeof($stripe_cust_id)>0)
										{
											$customer_id=$stripe_cust_id->customer_id;
											$custinfo=CustomerEntityInfo::find()->where(['customer_id'=>$customer_id])->one();
											$custaddress=CustomerEntityAddress::find()->where(['customer_id'=>$customer_id,'type_id'=>1])->one();
										?>
										<tr>
											<td><?php echo $custinfo->customer_id;?></td>
											<td><?php echo $custinfo->email;?></td>
											<td><?php echo $custinfo->first_name;?></td>
											<td><?php echo $custinfo->last_name;?></td>
											<td><?php echo $custaddress->state;?></td>
										<?php 
												if(sizeof($TbAttribute)>0){
													foreach($TbAttribute as $attributeinfo){
														$style = '';
														if($attributeinfo->is_active){
															$customerAttribute = CustomerEntityAttribute::find()->where([
																														'customer_id' => $customer_id,
																														'attribute_id' => $attributeinfo->id
																														])->all();
																													
														?>
														<td>														
														<?php if($attributeinfo->is_multiselect){
															if(sizeof($customerAttribute)>0){
																foreach($customerAttribute as $attribute){
																	$TbAttributeDetail = TbAttributeDetail::findOne($attribute->value);
																	$style = $TbAttributeDetail->title.', '.$style;
																	
																}
															}
														  }else{																	
																if(sizeof($customerAttribute)>0){
																	foreach($customerAttribute as $attribute){
																		$TbAttributeDetail = TbAttributeDetail::findOne($attribute->value);
																		$style = $TbAttributeDetail->title;
																	}
																}															
															}
														  
														  ?>
														  <?php echo $style;?>
														</td>	
														<?php }
													 }
												}?>
											
											<td><?php echo date('Y/m/d',strtotime($stripe_cust_id->created_at));?></td>
											<td><input type="text" class="form-control span1" id="item1_<?php echo $custinfo->customer_id?>" placeholder="items1 sku"></td>
											<td><input type="text" class="form-control span1" id="item2_<?php echo $custinfo->customer_id?>" placeholder="items2 sku"></td>
											<td><small>
											<?php 
												$itemsArr = array();
												$itemsLog = TradegeckoOderItemsLog::find()->where(['customer_id' => $customer_id])->all();
												if(sizeof($itemsLog)>0){
													foreach($itemsLog as $items){
														$itemsArr[] = $items->item_id;
													}
												}
												echo implode(',',$itemsArr);
											?>
											</small></td>
											<td><?php $customer_status=TradegeckoOrderLog::find()->where(['customer_id' => $customer_id])->andWhere(['like', 'created_at', $date])->one();
												if(sizeof($customer_status)==0) { ?>										
													<button type="button" class="btn btn-success" onclick="submitorder(<?php echo $custinfo->customer_id;?>)">Submit</button> 
												<?php } ?>
											</td>
										</tr>
								<?php	} 
									}
								}
							}else{?>
								<tr>
									<td colspan="16">No Result Found</td>
								</tr>			
							<?php }

						?>
						
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

</div>

<script type="text/javascript">
function submitorder(id)
{
sku1=document.getElementById("item1_"+id).value;
sku2=document.getElementById("item2_"+id).value;

var loginurl = '<?php echo Yii::$app->homeUrl.'/displaychargedusers/fetchchargeduser/'?>';
	$.ajax({

	url : '<?php echo Yii::$app->homeUrl.'/chargedusersorder/ordersubmission/'?>',
	type : 'POST',
	data:{customer_id:id, sku1:sku1, sku2:sku2}, 
	//dataType:'json',
	success : function(data) {              
	
			if(data == 'OK')
			{
				alert('Order is successfully placed');
				window.location.href=loginurl;
			} else {
				alert(data);
				
			}
			
		},
	
	}); 

}

function filteruser()
{
	var id=jQuery("#cust_id").val();
	var url = '<?php echo Yii::$app->homeUrl.'/displaychargedusers/filteruserbyid'?>';
	url = url+'?customerid='+id;
	window.location.href = url;
}

function date_range_fromSubmit() {
 	jQuery("#fromdate").val(jQuery("#first_date").val());
	jQuery("#todate").val(jQuery(".beatpicker-input-to").val());
	var url = '<?php echo Yii::$app->homeUrl.'/displaychargedusers/fetchchargeduser'?>';
	url = url+'?from='+jQuery("#fromdate").val()+'&to='+jQuery("#todate").val();
	window.location.href = url;
 }
</script>