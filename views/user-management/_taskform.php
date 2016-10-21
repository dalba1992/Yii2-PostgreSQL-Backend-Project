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
                <h5>Task Info</h5>
            </div>
            <?php if ($error == '1') { ?>
            <div class="widget-title">
                <h5 style="color:red">Name already exists.</h5>
            </div>
            <?php } ?>
            <div id="user_form_wrap" class="widget-content nopadding">
                <?php
                    if ($option == '0')
                        $url = Yii::$app->homeUrl.'user-management/create-task';
                    else
                        $url = Yii::$app->homeUrl.'user-management/update-task';
                ?>
                <form id="user_form" class="form-horizontal" method="post"
                    action="<?php echo $url ?>">
                    <?php if(isset($Task['old_name'])) { ?>
                    <input type="hidden" name="Task[old_name]" value="<?php echo $Task['old_name']; ?>">
                    <?php } ?>
                    <div class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">Name*</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <input type="text" value="<?php echo $Task['name']; ?>" required class="form-control" name="Task[name]" style="<?php if ($error == '1') echo 'border:1px solid red';?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">Description*</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <input type="text" value="<?php echo $Task['description']; ?>" required class="form-control" name="Task[description]" autocomplete="false">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">BizRule</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <input type="text" id="bizrule" class="form-control" name="Task[bizrule]" placeholder="" autocomplete="false" value="" >
                        </div>
                    </div>

                    <div class="form-actions">
                        <?php if ($option == '0') { ?>
                        <button id="submit_form" class="btn btn-success btn-sm" type="submit">Create Task</button> or <?=Html::a('Cancel', ['rbac'], ['class' => 'text-danger'])?>
                        <?php } else { ?>
                        <button id="submit_form" class="btn btn-success btn-sm" type="submit">Update Task</button> or <?=Html::a('Cancel', ['rbac'], ['class' => 'text-danger'])?>
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