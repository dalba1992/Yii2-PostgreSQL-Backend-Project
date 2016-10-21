<?php
use app\models\CustomerEntityAddress;
$customership = CustomerEntityAddress::find()->where(['customer_id'=>$_GET['member'],'type_id'=>2])->one();
?>
	<!--Shipping Info-->
	<div id="tab4" class="tab-pane">
		<div class="widget-box">
			<div class="widget-title">
			<span class="icon">
				<i class="fa fa-road"></i>
			</span>
				<h5>Shipping Info</h5>
			</div>
			<?php if(count($customership)>0){?>
				<div class="widget-content nopadding">
					<form id="shippingForm" class="form-horizontal" method="post" action="<?php echo Yii::$app->homeUrl.'/members/update-shipping?member='.$_GET['member']?>">
						<div class="form-group">
							<label class="col-sm-2 control-label">First Name</label>
							<div class="col-sm-10">
								<input type="text" value="<?php echo $customership->first_name;?>" name="shipping[first_name]" id="output" class="form-control" required />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Last Name</label>
							<div class="col-sm-10">
								<input type="text" value="<?php echo $customership->last_name;?>" name="shipping[last_name]" id="output" class="form-control" required />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Address 1</label>
							<div class="col-sm-10">
								<input type="text" value="<?php echo $customership->address;?>" name="shipping[address]" id="output" class="form-control" required />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Address 2</label>
							<div class="col-sm-10">
								<input type="text" value="<?php echo $customership->address2;?>" name="shipping[address2]" id="output" class="form-control" required />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">City</label>
							<div class="col-sm-10">
								<input type="text" value="<?php echo $customership->city;?>" name="shipping[city]" id="output" class="form-control" required />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">State</label>
							<div class="col-sm-10">
								<select required id="shipping_state" class="custom_select form-control" name="shipping[state]">
									<option value="">State</option>
									<option value="AL" <?php if($customership->state =='AL'){?> selected="selected" <?php }?>>AL</option>
									<option value="AK" <?php if($customership->state =='AK'){?> selected="selected" <?php }?>>AK</option>
									<option value="AZ" <?php if($customership->state =='AZ'){?> selected="selected" <?php }?>>AZ</option>
									<option value="AR" <?php if($customership->state =='AR'){?> selected="selected" <?php }?>>AR</option>
									<option value="CA" <?php if($customership->state =='CA'){?> selected="selected" <?php }?>>CA</option>
									<option value="CO" <?php if($customership->state =='CO'){?> selected="selected" <?php }?>>CO</option>
									<option value="CT" <?php if($customership->state =='CT'){?> selected="selected" <?php }?>>CT</option>
									<option value="DE" <?php if($customership->state =='DE'){?> selected="selected" <?php }?>>DE</option>
									<option value="FL" <?php if($customership->state =='FL'){?> selected="selected" <?php }?>>FL</option>
									<option value="GA" <?php if($customership->state =='GA'){?> selected="selected" <?php }?>>GA</option>
									<option value="HI" <?php if($customership->state =='HI'){?> selected="selected" <?php }?>>HI</option>
									<option value="ID" <?php if($customership->state =='ID'){?> selected="selected" <?php }?>>ID</option>
									<option value="IL" <?php if($customership->state =='IL'){?> selected="selected" <?php }?>>IL</option>
									<option value="IN" <?php if($customership->state =='IN'){?> selected="selected" <?php }?>>IN</option>
									<option value="IA" <?php if($customership->state =='IA'){?> selected="selected" <?php }?>>IA</option>
									<option value="KS" <?php if($customership->state =='KS'){?> selected="selected" <?php }?>>KS</option>
									<option value="KY" <?php if($customership->state =='KY'){?> selected="selected" <?php }?>>KY</option>
									<option value="LA" <?php if($customership->state =='LA'){?> selected="selected" <?php }?>>LA</option>
									<option value="ME" <?php if($customership->state =='ME'){?> selected="selected" <?php }?>>ME</option>
									<option value="MD" <?php if($customership->state =='MD'){?> selected="selected" <?php }?>>MD</option>
									<option value="MA" <?php if($customership->state =='MA'){?> selected="selected" <?php }?>>MA</option>
									<option value="MI" <?php if($customership->state =='MI'){?> selected="selected" <?php }?>>MI</option>
									<option value="MN" <?php if($customership->state =='MN'){?> selected="selected" <?php }?>>MN</option>
									<option value="MS" <?php if($customership->state =='MS'){?> selected="selected" <?php }?>>MS</option>
									<option value="MO" <?php if($customership->state =='MO'){?> selected="selected" <?php }?>>MO</option>
									<option value="MT" <?php if($customership->state =='MT'){?> selected="selected" <?php }?>>MT</option>
									<option value="NE" <?php if($customership->state =='NE'){?> selected="selected" <?php }?>>NE</option>
									<option value="NV" <?php if($customership->state =='NV'){?> selected="selected" <?php }?>>NV</option>
									<option value="NH" <?php if($customership->state =='NH'){?> selected="selected" <?php }?>>NH</option>
									<option value="NJ" <?php if($customership->state =='NJ'){?> selected="selected" <?php }?>>NJ</option>
									<option value="NM" <?php if($customership->state =='NM'){?> selected="selected" <?php }?>>NM</option>
									<option value="NY" <?php if($customership->state =='NY'){?> selected="selected" <?php }?>>NY</option>
									<option value="NC" <?php if($customership->state =='NC'){?> selected="selected" <?php }?>>NC</option>
									<option value="ND" <?php if($customership->state =='ND'){?> selected="selected" <?php }?>>ND</option>
									<option value="OH" <?php if($customership->state =='OH'){?> selected="selected" <?php }?>>OH</option>
									<option value="OK" <?php if($customership->state =='OK'){?> selected="selected" <?php }?>>OK</option>
									<option value="OR" <?php if($customership->state =='OR'){?> selected="selected" <?php }?>>OR</option>
									<option value="PA" <?php if($customership->state =='PA'){?> selected="selected" <?php }?>>PA</option>
									<option value="RI" <?php if($customership->state =='RI'){?> selected="selected" <?php }?>>RI</option>
									<option value="SC" <?php if($customership->state =='SC'){?> selected="selected" <?php }?>>SC</option>
									<option value="SD" <?php if($customership->state =='SD'){?> selected="selected" <?php }?>>SD</option>
									<option value="TN" <?php if($customership->state =='TN'){?> selected="selected" <?php }?>>TN</option>
									<option value="TX" <?php if($customership->state =='TX'){?> selected="selected" <?php }?>>TX</option>
									<option value="UT" <?php if($customership->state =='UT'){?> selected="selected" <?php }?>>UT</option>
									<option value="VT" <?php if($customership->state =='VT'){?> selected="selected" <?php }?>>VT</option>
									<option value="VA" <?php if($customership->state =='VA'){?> selected="selected" <?php }?>>VA</option>
									<option value="WA" <?php if($customership->state =='WA'){?> selected="selected" <?php }?>>WA</option>
									<option value="WV" <?php if($customership->state =='WV'){?> selected="selected" <?php }?>>WV</option>
									<option value="WI" <?php if($customership->state =='WI'){?> selected="selected" <?php }?>>WI</option>
									<option value="WY" <?php if($customership->state =='WY'){?> selected="selected" <?php }?>>WY</option>
									<option value="AA" <?php if($customership->state =='AA'){?> selected="selected" <?php }?>>AA</option>
									<option value="AE" <?php if($customership->state =='AE'){?> selected="selected" <?php }?>>AE</option>
									<option value="AP" <?php if($customership->state =='AP'){?> selected="selected" <?php }?>>AP</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Country</label>
							<div class="col-sm-10">
								<select  id="shipping_country" class="custom_select_country form-control" name="shipping[country]" required>
									<option value="">Country</option>
									<option value="US" <?php if($customership->country =='US'){?> selected="selected" <?php }?>>USA</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Zip Code</label>
							<div class="col-sm-10">
								<input type="text" value="<?php echo $customership->zipcode;?>" name="shipping[zipcode]" id="output" class="form-control" required />
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<button class="btn btn-success btn-lg" id="shipping_submit" type="submit">Save Shipping Changes</button>
								<button type="submit" class="btn btn-danger btn-lg">Discard Shipping Changes</button>
							</div>
						</div>
					</form>
				</div>
			<?php }?>
		</div>
	</div>

<?php
$script = <<< JS
$(function(){
    jQuery( ".shipping_submit" ).click(function() {
		var form = jQuery( "#shipping_form" );
		form.validate();
	});
});
JS;
$this->registerJs($script, yii\web\View::POS_END);