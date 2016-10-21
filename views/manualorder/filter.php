<?php
 
use app\models\TbAttribute;
use app\models\TbAttributeDetail;
$AttrdetailCollection = TbAttributeDetail::find();
$state = '';
$email = '';
if(!isset($_GET['Filter']) && isset(Yii::$app->session['Filter'])) {
    $_GET['Filter'] = Yii::$app->session['Filter'];
}

if(isset($_GET['Filter']['state']))
{
	$state = $_GET['Filter']['state'];
}
if(isset($_GET['Filter']['mailid']))
{
	$email = $_GET['Filter']['mailid'];
}

?>
<div class="widget-title">
	<a data-toggle="collapse" href="#collapseTwo">
		<span class="icon"><i class="fa fa-filter"></i></span>
		<h5>Filter Members in Queue</h5>
	</a>
</div>
<div id="collapseTwo" class="<?php echo (isset($_GET['Filter']) || isset(Yii::$app->session['Filter'])) ? 'in' : 'collapse'; ?>">
	<div class="widget-content nopadding">
	   <table class="table table-bordered table-striped table-hover">
		<thead>
			<tr>
				<th>Style Profile</th>
				<th>Top Size</th>
				<th>Bottom Size</th>
				<th>Bottom Fit</th>
				<th>State</th>
				<th>e-Mail</th>
			</tr>
		</thead>
		<form method="get">
			<tbody>
				<tr>
					<td>
						<div class="control-group">
							<div class="controls">
								<select name="Filter[style]" style="width:100%;max-width:90%;">
									<option value=''>Filter user style</option>
									<?php 
									$TbAttributeDetail = $AttrdetailCollection->where(['tb_attribute_id'=>'1'])->all();
									foreach($TbAttributeDetail as $attribue){
									?>
									<option value="<?php echo $attribue->id ?>" <?php if(isset($_GET['Filter']['style']) && $_GET['Filter']['style'] == $attribue->id){ ?> selected='selected' <?php } ?>><?php echo $attribue->title ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</td>
					<td>
						<div class="control-group">
							<div class="controls">
								<select name = "Filter[tsize]" style="width:100%;max-width:90%;">
									<option value=''>Filter Top Size</option>
								<?php
									$TbAttributeDetail = $AttrdetailCollection->where(['tb_attribute_id'=>'4'])->all();
									foreach($TbAttributeDetail as $attribue){
								?>
									<option value="<?php echo $attribue->id ?>" <?php if(isset($_GET['Filter']['tsize']) && $_GET['Filter']['tsize'] == $attribue->id){ ?> selected='selected' <?php } ?>><?php echo $attribue->title ?></option>
									
									<?php } ?>									
								</select>
							</div>
						</div>
					</td>
					<td>
						<div class="control-group">
							<div class="controls">
								<select name = "Filter[bsize]" style="width:100%;max-width:90%;">
									<option value=''>Filter Bottom Size</option>
									<?php
									$TbAttributeDetail = $AttrdetailCollection->where(['tb_attribute_id'=>'5'])->all();
									foreach($TbAttributeDetail as $attribue){
								?>
									<option value="<?php echo $attribue->id ?>" <?php if(isset($_GET['Filter']['bsize']) && $_GET['Filter']['bsize'] == $attribue->id){ ?> selected='selected' <?php } ?>><?php echo $attribue->title ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</td>
					<td>
						<div class="control-group">
							<div class="controls">
								<select name = "Filter[bfit]" style="width:100%;max-width:90%;">
									<option value=''>Filter Bottom Fit</option>
									<?php
									$TbAttributeDetail = $AttrdetailCollection->where(['tb_attribute_id'=>'6'])->all();
									foreach($TbAttributeDetail as $attribue){
								?>
									<option value="<?php echo $attribue->id ?>" <?php if(isset($_GET['Filter']['bfit']) && $_GET['Filter']['bfit'] == $attribue->id){ ?> selected='selected' <?php } ?>><?php echo $attribue->title ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</td>
					<td>
						<div class="control-group">
							<div class="controls">
								<select name = "Filter[state]" style="width:100%;max-width:90%;">
									<option value=''>Filter State</option>
									<option value="AL" <?php if($state == 'AL'){?> selected='selected' <?php }?> >Alabama</option>
									<option value="AK" <?php if($state == 'AK'){?> selected='selected' <?php }?>>Alaska</option>
									<option value="AZ" <?php if($state == 'AZ'){?> selected='selected' <?php }?>>Arizona</option>
									<option value="AR" <?php if($state == 'AR'){?> selected='selected' <?php }?>>Arkansas</option>
									<option value="CA" <?php if($state == 'CA'){?> selected='selected' <?php }?>>California</option>
									<option value="CO" <?php if($state == 'CO'){?> selected='selected' <?php }?>>Colorado</option>
									<option value="CT" <?php if($state == 'CT'){?> selected='selected' <?php }?>>Connecticut</option>
									<option value="DE" <?php if($state == 'DE'){?> selected='selected' <?php }?>>Delaware</option>
									<option value="FL" <?php if($state == 'FL'){?> selected='selected' <?php }?>>Florida</option>
									<option value="GA" <?php if($state == 'GA'){?> selected='selected' <?php }?>>Georgia</option>
									<option value="HI" <?php if($state == 'HI'){?> selected='selected' <?php }?>>Hawaii</option>
									<option value="ID" <?php if($state == 'ID'){?> selected='selected' <?php }?>>Idaho</option>
									<option value="IL" <?php if($state == 'IL'){?> selected='selected' <?php }?>>Illinios</option>
									<option value="IN" <?php if($state == 'IN'){?> selected='selected' <?php }?>>Indiana</option>
									<option value="IA" <?php if($state == 'IS'){?> selected='selected' <?php }?>>Iowa</option>
									<option value="KS" <?php if($state == 'KS'){?> selected='selected' <?php }?>>Kansas</option>
									<option value="KY" <?php if($state == 'KY'){?> selected='selected' <?php }?>>Kentucky</option>
									<option value="LA" <?php if($state == 'LA'){?> selected='selected' <?php }?>>Louisiana</option>
									<option value="ME" <?php if($state == 'ME'){?> selected='selected' <?php }?>>Maine</option>
									<option value="MD" <?php if($state == 'MD'){?> selected='selected' <?php }?>>Maryland</option>
									<option value="MA" <?php if($state == 'MA'){?> selected='selected' <?php }?>>Massachusetts</option>
									<option value="MI" <?php if($state == 'MI'){?> selected='selected' <?php }?>>Michigan</option>
									<option value="MN" <?php if($state == 'MN'){?> selected='selected' <?php }?>>Minnesota</option>
									<option value="MS" <?php if($state == 'MS'){?> selected='selected' <?php }?>>Mississippi</option>
									<option value="MO" <?php if($state == 'MO'){?> selected='selected' <?php }?>>Missouri</option>
									<option value="MT" <?php if($state == 'MT'){?> selected='selected' <?php }?>>Montana</option>
									<option value="NE" <?php if($state == 'NE'){?> selected='selected' <?php }?>>Nebraska</option>
									<option value="NV" <?php if($state == 'NV'){?> selected='selected' <?php }?>>Nevada</option>
									<option value="NH" <?php if($state == 'NH'){?> selected='selected' <?php }?>>New Hampshire</option>
									<option value="NJ" <?php if($state == 'NJ'){?> selected='selected' <?php }?>>New Jersey</option>
									<option value="NM" <?php if($state == 'NM'){?> selected='selected' <?php }?>>New Mexico</option>
									<option value="NY" <?php if($state == 'NY'){?> selected='selected' <?php }?>>New York</option>
									<option value="NC" <?php if($state == 'NC'){?> selected='selected' <?php }?>>North Carolina</option>
									<option value="ND" <?php if($state == 'ND'){?> selected='selected' <?php }?>>North Dakota</option>
									<option value="OH" <?php if($state == 'OH'){?> selected='selected' <?php }?>>Ohio</option>
									<option value="OK" <?php if($state == 'OK'){?> selected='selected' <?php }?>>Oklahoma</option>
									<option value="OR" <?php if($state == 'OR'){?> selected='selected' <?php }?>>Oregon</option>
									<option value="PA" <?php if($state == 'PA'){?> selected='selected' <?php }?>>Pennsylvania</option>
									<option value="RI" <?php if($state == 'RI'){?> selected='selected' <?php }?>>Rhode Island</option>
									<option value="SC" <?php if($state == 'SC'){?> selected='selected' <?php }?>>South Carolina</option>
									<option value="SD" <?php if($state == 'SD'){?> selected='selected' <?php }?>>South Dakota</option>
									<option value="TN" <?php if($state == 'TN'){?> selected='selected' <?php }?>>Tennessee</option>
									<option value="TX" <?php if($state == 'TX'){?> selected='selected' <?php }?>>Texas</option>
									<option value="UT" <?php if($state == 'UT'){?> selected='selected' <?php }?>>Utah</option>
									<option value="VT" <?php if($state == 'VT'){?> selected='selected' <?php }?>>Vermont</option>
									<option value="VA" <?php if($state == 'VA'){?> selected='selected' <?php }?>>Virginia</option>
									<option value="WA" <?php if($state == 'WA'){?> selected='selected' <?php }?>>Washington</option>
									<option value="DC" <?php if($state == 'DC'){?> selected='selected' <?php }?>>Washington, DC</option>
									<option value="WV" <?php if($state == 'WV'){?> selected='selected' <?php }?>>West Virginia</option>
									<option value="WI" <?php if($state == 'WI'){?> selected='selected' <?php }?>>Wisconsin</option>
									<option value="WY" <?php if($state == 'WY'){?> selected='selected' <?php }?>>Wyoming</option>
								</select>
							</div>
						</div>
					</td>
					<td>
						<center>
							<div class="control-group">
								<div class="controls">
									<input type="text" value="<?php echo $email ?>" id="email" name="Filter[mailid]"><br>
									<button class="btn btn-success">Submit</button> or <?php echo \yii\helpers\Html::a('reset filters', ['reset-filters']); ?>
								</div>
							</div>
						</center>
					</td>			
				</tr>
			</tbody>
		</form>
	</table>
	</div>
</div>