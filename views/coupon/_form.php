<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Coupon */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="row">
    <div class="col-xs-12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"> <i class="fa fa-ticket"></i>
                </span>
                <h5>Coupon Info</h5>
            </div>
            <div id="coupon_form_wrap" class="widget-content nopadding">
                <?php
                $url = Yii::$app->homeUrl.'/coupon/create';
                ?>
                <form id="coupon_form" class="form-horizontal" method="post"
                    action="<?php echo $url ?>">
                    <div class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">Coupon Name</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <input type="text" value="" required class="form-control"
                                name="Coupon[coupon_name]"
                                placeholder="Enter the coupon name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">Enter The Coupon Code</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <input type="text" value="" id="coupon_code" checked="false" onKeyup="checklower()" required class="form-control" name="Coupon[coupon_code]" placeholder="Enter the coupon code">
                            <label><input type="checkbox" id="getlower" name="Coupon[lower_code]"> use lower case</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">Please Select Discount Type</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <select required class="form-control" name="Coupon[discount_type]">
                                <option value="">Please Select the Discount Method</option>
                                <option value="1">Fixed Amount Off</option>
                                <option value="2">Percent Off</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">Discount Off</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <input type="number" min="0" value="" required class="form-control"
                                name="Coupon[amount_off]"
                                placeholder="Enter the Amount Want To Off ">
                            <h6>Hint: Either Fixed Amount($) Or Percent(%) Off.</h6>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">Select Coupon Duration Type</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <select id="duration" required class="form-control" name="Coupon[duration]">
                                <option value="">Please select duration type</option>
                                <option value="1">once</option>
                                <option value="2">forever</option>
                                <option value="3">repeating</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: none;" id="duration_time"
                        class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">Enter The Number Months To Apply
                            Coupon</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <input type="digits" value="" min="1" max="12" required class="form-control"
                                name="Coupon[duration_time]"
                                placeholder="Enter the coupon apply valid month ">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">Message</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <input type="text" value="" required class="form-control" name="Coupon[message]"
                                placeholder="Enter The Success Coupon Message To Show On Frontend! ">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">Status</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <select required class="form-control" name="Coupon[status]">
                                <option value="">Please select Coupon Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button id="submit_coupon" class="btn btn-success btn-sm" type="submit">Create Coupon</button> or <?=Html::a('Cancel', ['index'], ['class' => 'text-danger'])?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
function checklower(){
if(jQuery('#getlower').is(':checked')){
	jQuery('#coupon_code').val(jQuery('#coupon_code').val().toLowerCase());
}
	
}

  jQuery(document).ready(function(){
	  jQuery('#getlower').change(function(){
		  if(jQuery('#getlower').is(':checked')){
				jQuery('#coupon_code').val(jQuery('#coupon_code').val().toLowerCase());
			}

	  });

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
<?php /*
<div class="coupon-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'coupon_name')->textInput(['maxlength' => 100]) ?>

    <?= $form->field($model, 'coupon_code')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'discount_type')->textInput() ?>

    <?= $form->field($model, 'amount_off')->textInput() ?>

    <?= $form->field($model, 'duration')->textInput() ?>

    <?= $form->field($model, 'duration_time')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'is_apply')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
*/ ?>
