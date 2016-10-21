<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\fileupload\FileUpload;

/* @var $this yii\web\View */
/* @var $model app\models\Coupon */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row">
    <div class="col-md-12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"> <i class="fa fa-ticket"></i>
                </span>
                <h5>User Info</h5>
            </div>
            <?php if ($error == '1') { ?>
            <div class="widget-title">
                <h5 style="color:red">Email already exists.</h5>
            </div>
            <?php } ?>
            <div id="user_form_wrap" class="widget-content nopadding">
                <?php
                    if ($option == '0')
                        $url = Yii::$app->homeUrl.'user-management/create';
                    else
                        $url = Yii::$app->homeUrl.'user-management/edit';
                ?>
                <input type="hidden" name="homeurl" value="<?php echo Yii::$app->homeUrl; ?>">
                <input type="hidden" name="user_id" value="<?php echo $model->id; ?>">
                <form id="user_form" class="form-horizontal" method="post"
                    action="<?php echo $url ?>">

                    <div class='col-md-3'>
                        <div class="form-group">
                            <label class="control-label">Profile Picture</label>
                        </div>
                        <div class="form-group" style="text-align:center;">
                            <?php if ($model->profilePic != "") { ?>
                            <img id="profile_img" src="<?php echo Yii::$app->homeUrl; ?>static/<?php echo Yii::$app->params['avatarPath']; ?><?php echo $model->profilePic; ?>" style="width:230px; height:230px;">
                            <?php } else { ?>
                            <img id="profile_img" src="<?php echo Yii::$app->homeUrl; ?>web/img/default.jpg" style="width:230px; height:230px;">
                            <?php } ?>
                        </div>
                        <div class="form-group">
                            <?= FileUpload::widget([
                                'model' => $model,
                                'attribute' => 'profilePic',
                                'url' => ['user-management/upload', 'flag' => '0'],
                                'options' => ['accept' => 'image/*'],
                                'clientOptions' => [
                                    'maxFileSize' => 2000000
                                ],
                                // Also, you can specify jQuery-File-Upload events
                                // see: https://github.com/blueimp/jQuery-File-Upload/wiki/Options#processing-callback-options
                                'clientEvents' => [
                                    'fileuploaddone' => 'function(e, data) {
                                        var homeurl = $("[name=homeurl]").val();
                                        $("[type=hidden][name=\"UserEntity[profilePic]\"").val(data["result"]);
                                        $("#profile_img").attr("src", homeurl + "static/'.Yii::$app->params['avatarPath'].'" + data["result"]);
                                    }',
                                    'fileuploadfail' => 'function(e, data) {
                                    }',
                                ],
                            ]);?>
                        </div>
                    </div>
                    <div class='col-md-9'>
                        <?php if ($id != '-1') { ?>
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <?php } ?>
                        <div class="form-group">
                            <label class="col-sm-3 col-md-3 col-lg-3 control-label">User Name<?php if ($option != '1') { ?>*<?php } ?></label>
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <?php if ($option == '1') { ?>
                                <p style="padding-top:7px; padding-left:5px"><?php echo $model->username; ?></p>
                                <?php } else { ?>
                                <input type="text" value="<?php echo $model->username; ?>" class="form-control"
                                    name="UserEntity[username]"
                                    required placeholder="Enter the user name">
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 col-md-3 col-lg-3 control-label">Email<?php if ($option != '1') { ?>*<?php } ?></label>
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <input type="text" value="<?php echo $model->email; ?>" id="email" <?php if ($option != '1') { ?>required<?php } ?> class="form-control" name="UserEntity[email]" placeholder="admin@admin.com" autocomplete="false" style="<?php if ($error == '1') echo 'border:1px solid red';?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 col-md-3 col-lg-3 control-label">First Name</label>
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <input type="text" id="firstName" class="form-control" name="UserEntity[firstName]" placeholder="" autocomplete="false" value="<?php echo $model->firstName; ?>" >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 col-md-3 col-lg-3 control-label">Last Name</label>
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <input type="text" id="lastName" class="form-control" name="UserEntity[lastName]" placeholder="" autocomplete="false" value="<?php echo $model->lastName; ?>" >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 col-md-3 col-lg-3 control-label">Enter Password<?php if ($option != '1') { ?>*<?php } ?></label>
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <input type="password" value="" id="passKey3" name="UserEntity[password]" <?php if ($option != '1') { ?>required<?php } ?> class="form-control" autocomplete="false">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 col-md-3 col-lg-3 control-label">Confirm Password<?php if ($option != '1') { ?>*<?php } ?></label>
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <input type="password" value="" id="passKey2" <?php if ($option != '1') { ?>required<?php } ?> class="form-control" autocomplete="false">
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style='text-align:center;'>
                        <?php if ($option == '0') { ?>
                        <button id="submit_form" class="btn btn-success btn-sm" type="submit">Create User</button> or <?=Html::a('Cancel', ['user'], ['class' => 'text-danger'])?>
                        <?php } else { ?>
                        <button id="submit_form" class="btn btn-success btn-sm" type="submit">Update User</button> or <?=Html::a('Cancel', ['user'], ['class' => 'text-danger'])?>
                        <?php } ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="message" style="display: none;">
    <h4><i class="fa fa-spinner fa-pulse"></i> Uploading now...</h4>
</div>

<div id="msg_remove" style="display: none;">
    <h4><i class="fa fa-spinner fa-pulse"></i> Removing now...</h4>
</div>

<?php
$search_script = <<<JS
    jQuery(document).ready(function(){
        jQuery( "#submit_form" ).click(function(){
            if ($('#passKey2').val() != $('#passKey3').val())
            {
                alert("Password doesn't match.");
                return false;
            }
            //var form = jQuery( "#user_form" );
            jQuery("#user_form").validate();
            form.validate();
        });
    });
JS;
$this->registerJs($search_script, \yii\web\View::POS_END);
?>