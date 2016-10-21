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
                <h5>Operation Info</h5>
            </div>
            <?php if ($error == '1') { ?>
            <div class="widget-title">
                <h5 style="color:red">Name already exists.</h5>
            </div>
            <?php } else if ($error == '2') { ?>
            <div class="widget-title">
                <h5 style="color:red">Enter correct name.</h5>
            </div>
            <?php } ?>
            <div id="user_form_wrap" class="widget-content nopadding">
                <?php
                    if ($option == '0')
                        $url = Yii::$app->homeUrl.'user-management/create-operation';
                    else
                        $url = Yii::$app->homeUrl.'user-management/update-operation';
                ?>
                <form id="user_form" class="form-horizontal" method="post"
                    action="<?php echo $url ?>">
                    <?php if ($id != '-1') { ?>
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <?php } ?>
                    <div class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">Name*</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <input type="text" value="<?php echo $name; ?>" required class="form-control" name="Operation[name]" style="<?php if ($error == '1' || $error == '2') echo 'border:1px solid red';?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">Description*</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <input type="text" value="<?php echo $model->description; ?>" class="form-control" name="Operation[description]" required autocomplete="false">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">BizRule</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <input type="text" id="bizrule" class="form-control" name="Operation[bizrule]" placeholder="" autocomplete="false" value="" >
                        </div>
                    </div>
                    <div class="form-actions">
                        <?php if ($option == '0') { ?>
                        <button id="submit_form" class="btn btn-success btn-sm" type="submit">Create Operation</button> or <?=Html::a('Cancel', ['rbac'], ['class' => 'text-danger'])?>
                        <?php } else { ?>
                        <button id="submit_form" class="btn btn-success btn-sm" type="submit">Update Operation</button> or <?=Html::a('Cancel', ['rbac'], ['class' => 'text-danger'])?>
                        <?php } ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

jQuery(document).ready(function(){
	jQuery( "#submit_form" ).click(function(){
		//var form = jQuery( "#user_form" );
		jQuery("#user_form").validate();
		form.validate();
	});
});
</script>