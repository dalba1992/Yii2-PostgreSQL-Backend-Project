<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Coupon */

$this->title = 'Update Coupon: ' . ' ' . $model->coupon_code;
//$this->params['breadcrumbs'][] = ['label' => 'Coupons', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
//$this->params['breadcrumbs'][] = 'Update';
?>
<div class="coupon-update">
	<div id="content-header">
		<h1>Update : <?php echo $model->coupon_code ?> </h1>
	</div>
	<?php if(Yii::$app->session->hasFlash('success')) { ?>
			<div class="alert alert-success">
				<button data-dismiss="alert" class="close">x</button>
				<strong>Success!</strong><?php echo Yii::$app->session->getFlash('success'); ?>
			</div>
	<?php } ?>
	<?php if(Yii::$app->session->hasFlash('fail')) { ?>
			<div class="alert alert-error">
				<button data-dismiss="alert" class="close">x</button>
				<strong>Error!</strong> <?php echo Yii::$app->session->getFlash('fail'); ?>
			</div>
	<?php } ?>
    

<div class="row">
    <div class="col-xs-12">
        <div class="widget-box">
             <div class="widget-title">
                <span class="icon">
                    <i class="fa fa-ticket"></i>
                </span>
                <h5>Coupon Info</h5>
             </div>
            <div id="coupon_form_wrap" class="widget-content nopadding">
                <?php
                $url = Yii::$app->homeUrl.'/coupon/update?id='.$model->id;
                ?>
                <form id="coupon_form" class="form-horizontal" method="post" action="<?php echo $url ?>">
                    <div class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">Coupon Name</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <input type="text" value="<?php echo $model->coupon_name; ?>" class="form-control input-sm" required name="Coupon[coupon_name]" placeholder="Enter the coupon name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">Coupon Code</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <span><?php echo $model->coupon_code; ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">Discount Method</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <span>
                            <?php
                                if($model->discount_type ==1){
                                    echo "Fixed Amount Off";
                                }else if($model->discount_type == 2){
                                    echo "Percent Off";
                                }
                            ?>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">Discount Off</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <span><?php
                                if($model->discount_type == '1'){
                                    echo '$'.$model->amount_off;
                                }else if($model->discount_type == '2'){
                                    echo $model->amount_off.'%';
                                }
                             ?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">Coupon Used Duration Type</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <span>
                                <?php
                                if($model->duration ==1){
                                    echo "Once";
                                }else if($model->duration == 2){
                                    echo "Forever";
                                }else if($model->duration == 3){
                                    echo "Repeating";
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                    <?php
                    if($model->duration == '3'){ ?>
                        <div  id="duration_time" class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">The Number Months To Valid The Coupon Code</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <span><?php echo $model->duration_time .'&nbspMonth'; ?></span>
                        </div>
                    </div>
                    <?php }
                    ?>


                    <div class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">Message</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <input type="text" value="<?php echo $model->message; ?>" class="form-control input-sm" required name="Coupon[message]" placeholder="Enter The Success Coupon Message To Show On Frontend! ">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">Status</label>
                        <div class="col-sm-2 col-md-2 col-lg-3">
                            <select required name="Coupon[status]" class="form-control input-sm">
                                <option value="">Please select Coupon Status </option>
                                <option value="1" <?php if($model->status == '1'){?> selected ='selected'<?php }?>>Active</option>
                                <option value="0" <?php if($model->status == '0'){?> selected ='selected'<?php }?>>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button id="submit_coupon"class="btn btn-success btn-sm" type="submit">Update</button> or <?=Html::a('Cancel', ['index'], ['class' => 'text-danger'])?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
	<script type="text/javascript">
	  jQuery(document).ready(function(){
		jQuery('#duration').on('change', function() {
			var duration = jQuery('#duration :selected').val();
			if(duration == 3){
				jQuery('#duration_time').show();
			}else{
				jQuery('#duration_time').hide();
			}
			//alert(duration);
		});
		jQuery( "#submit_coupon" ).click(function(){
			//var form = jQuery( "#coupon_form" );
			jQuery("#coupon_form").validate();
			form.validate();
			});
		});
	</script>	
</div>
