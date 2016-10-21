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

?>

<?php 
	$current_day = date('d');
	if($current_day<14){
		$charge_start_date = strtotime( date('Y-m-14') . " -1 month " );
		$charge_start_date = date('Y-m-d',$charge_start_date);
	}else{
		$charge_start_date = date('Y-m-14');
	}
	$order_in_month = date('Y-m',strtotime($charge_start_date));
?>
<table class="table table-bordered table-striped">
<thead>
	<tr>
		<th>Info</th>
		<th>Item 1</th>
		<th>Item 2</th>
		<th>Item 3</th>
		<th>Item 4</th>
		<th>Item 5</th>
		<th>Item 6</th>
		<th>Total</th>
	</tr>
</thead>
<tbody>
	<tr>
		<td>
		<?php 
		$customer_id = '';
		if(sizeof($chargeduser)>0){ ?>
			<?php foreach($chargeduser as $customer){
					$customer_id = $customer->customer_id;
					$custinfo = CustomerEntityInfo::find()->where(['customer_id'=>$customer_id])->one();
					$custaddress = CustomerEntityAddress::find()->where(['customer_id'=>$customer_id,'type_id'=>2])->one();
			?>
					<b>Member Id: <h4><?php echo $custinfo->customer_id; ?></h4>
					<?php 
					echo $custinfo->first_name.' '.$custinfo->last_name ;?>
							<input type="hidden" value="<?php echo $customer_id ?>" name="manualorder[customer_id]" ></input>
							</b>
							<br><?php echo $custaddress->address ?><br><?php echo $custaddress->city.','.$custaddress->state.','.$custaddress->country.','.$custaddress->zipcode ?><br><?php echo $custinfo->email?>
							<br><br>
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
													<?php if($TbAttributeDetail->tb_attribute_id == 4){ ?>
															<span class="size_title"> <?php echo $TbAttributeDetail->title; ?> </span>
													<?php }else{ 
															echo $TbAttributeDetail->title; 
													 } ?>
													
													<br><br>
													<?php
												}
											}															
									}
								}
							}?>
							<br><b>Member Since: </b><br><?php echo date('Y-m-d', strtotime($custinfo->created_at)); ?>
					<?php 
					break;
					} 
			}else{?>
				No Customer Found!
		<?php } ?>	
		</td>
		<?php echo Yii::$app->view->renderFile('@app/views/manualorder/product.php'); ?>
				
		<td>
			<center>
				<div class="form-actions">
					<div class="">Retail Pricing: <b>$120.00</b></div>
					<br>
					<?php 
					
					if($customer_id !=''){
						$Order = TradegeckoOrderLog::find()->where(['customer_id'=>$customer_id])
															->andWhere('created_at LIKE :query')
															->addParams([':query'=>'%'.$order_in_month.'%'])->one();
						if(sizeof($Order) >0 ){?>
							Order Has Been Created For this Month.
						<?php }else{ ?>
							<button class="btn btn-large btn-success" type="submit">Submit Order</button>
						<?php } ?>
					<?php } ?>	
				</div>
			</center> 
		</td>
	</tr>
</tbody>

</table>