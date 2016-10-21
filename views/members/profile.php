<?php
/**
 * @var \app\models\CustomerEntity $model
 */

use yii\helpers\Html;
use yii\web\View;

use app\models\TbAttribute;
use app\models\TbAttributeDetail;
use app\models\TbAttributeDetailImage;
use app\models\CustomerEntityAttribute;
use app\models\CustomerEntityInfo;
use app\models\CustomerStatusLog;

$this->title = "Edit Member Profile";

$customerEntityInfo = $model->customerEntityInfo;
$userLog = CustomerStatusLog::find()->where(['customer_id'=>$_GET['member']])->orderBy(['id' => SORT_DESC,])->all();
?>

<?php if($customerEntityInfo){?>
    <div id="content-header">
        <h1><?php echo $customerEntityInfo->first_name.' '.$customerEntityInfo->last_name.' - '.$customerEntityInfo->email ?></h1>
    </div>

    <div id="breadcrumb">
        <a class="tip-bottom" title="" href="<?php echo Yii::$app->request->BaseUrl ?>" data-original-title="Go to Home"><i class="icon-home"></i> Home</a>
        <a href="/members/all">Manage Members</a>
        <a class="current" href="javascript:;"><?php echo $customerEntityInfo->first_name.' '.$customerEntityInfo->last_name ?></a>
    </div>
<?php }?>

    <div class="row">
        <div class="col-xs-12">
            <?php if( Yii::$app->session->hasFlash('success')) { ?>
                <div class="alert alert-success">
                    <button data-dismiss="alert" class="close">x</button>
                    <?php echo Yii::$app->session->getFlash('success'); ?>
                </div>
            <?php } ?>
            <?php if( Yii::$app->session->hasFlash('error')) { ?>
                <div class="alert alert-error">
                    <button data-dismiss="alert" class="close">x</button>
                    <?php echo Yii::$app->session->getFlash('error'); ?>
                </div>
            <?php } ?>
            <!-- Profile -->


            <!-- Style overview section -->
            <?php   echo Yii::$app->view->renderFile('@app/views/members/profile/style-overview.php', ['model'=>$model]); ?>
            <!-- End Style overview section -->

            <!-- Edit profile info -->
            <?php   echo Yii::$app->controller->renderPartial('profile/_info', ['model'=>$model]); ?>
            <!-- /Edit profile info -->

            <!-- Subscription/Future month status -->
            <?php
            if($model->customerSubscription) // the section should be rendered only if the user has a subscription
                echo Yii::$app->controller->renderPartial('profile/subscription-state', ['model'=>$model]);
            ?>
            <!-- /Subscription/Future month status -->

            <!-- User log status -->
            <?php if($userLog && is_array($userLog) && !empty($userLog)){ ?>
                <div class="widget-box" id="log-section">
                    <div class="widget-title">
                        <span class="icon"><i class="fa fa-list"></i></span>
                        <h5>User Log Status</h5>
                    </div>
                    <div class="widget-content nopadding">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Status</th>
                                <th>Date &amp; Time</th>
                                <th>Description</th>
                            </tr>
                            </thead>
                            <?php ?>
                            <tbody>
                            <?php foreach($userLog as $val){?>
                                <tr>
                                    <td><?php echo $val->customer_id?></td>
                                    <?php if(($val->status=='trialing') || ($val->status=='active')){?>
                                        <td><span class="label label-success">Active</span></td>
                                        <td><?php echo $val->date?></td>
                                        <td>Active Customer</td>
                                    <?php }else{?>
                                        <td><span class="label label-important">Declined</span></td>
                                        <td><?php echo $val->date?></td>
                                        <td>Payment not successful</td>
                                    <?php }?>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php }?>
            <!-- /User log status -->
        </div>
    </div>

    <div id="subChangeWaitingMessage" style="display: none;">
        <h4><i class="fa fa-spinner fa-pulse"></i> Please wait until the status is changed</h4>
    </div>

<?php
$script = <<< JS
$(function(){

    $(document).on('pjax:complete', function() {
        console.log('pjax:complete');
        $.unblockUI();
    });
    $(document).on('pjax:timeout', function() {
        return false;
    });

    $('body').on('submit', '#customer-subscription-state-form', function(e) {
        var formObject = $(e.currentTarget);
        var url = formObject.attr('action'); // the script where you handle the form input.
        var nowObj = $('#subStartDateNow');

        if(nowObj.prop('checked')) {
            bootbox.dialog({
                message: 'Are you sure you want to change the subscription status now?',
                buttons: {
                    ok: {
                        label: 'OK',
                        className: 'btn-info',
                        callback: function() {
                            $.blockUI({ message: '<h4><i class="fa fa-spinner fa-pulse"></i> Please wait until the status is changed</h4>' });
                            $.ajax({
                                type: formObject.attr('method'),
                                url: url,
                                data: formObject.serialize(), // serializes the form's elements.
                                success: function(data)
                                {
                                    $.pjax.reload('#customer-subscription-status-pjax');
                                },
                                complete: function(data) {
                                    //$.unblockUI();
                                }
                            });
                        }
                    },
                    cancel: {
                        label: "Cancel",
                        className: "btn-default",
                        callback: function() {}
                    }
                }
            });
        } else {
            $.blockUI({ message: '<h4><i class="fa fa-spinner fa-pulse"></i> Please wait until the request is submitted</h4>' });
            $.ajax({
                type: formObject.attr('method'),
                url: url,
                data: formObject.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    $.pjax.reload('#customer-subscription-status-pjax');
                },
                complete: function(data) {
                    //$.unblockUI();
                }
            });
        }

        e.preventDefault();
        return false; // avoid to execute the actual submit of the form.
    });

    $('body').on('click', 'ul.rl-state-form li', function(){
        if($(this).find('input').prop('checked')){
            $('ul.rl-state-form li label').removeClass('btn-danger').addClass('btn-warning');
            $(this).find('label').addClass('btn-danger');
        }
    });

    $('body').on('change', '#subStartDateNow', function() {
        if($(this).prop('checked')) {
            $('#customersubscriptionstate-start_date').prop('disabled', true).css('opacity', '0.7');
        } else {
            $('#customersubscriptionstate-start_date').prop('disabled', false).css('opacity', '1');
        }
    });

    $('body').on('click', '#customersubscriptionstate-update_status label', function(){
        if($(this).find('input').val() == 'pause')
            $('ul.rl-state-form').show();
        else
            $('ul.rl-state-form').hide();
    });

    $('body').on('click', 'button.cancel-state-update', function(){
        $.blockUI({ message: '<h4><i class="fa fa-spinner fa-pulse"></i> Please wait until the request is cancelled</h4>' });
        var id = $(this).attr('data-id');
        $.ajax({
            type: 'POST',
            url: '/customer-subscription-state/cancel-update',
            data: {id: id},
            success: function(data) {
                $.pjax.reload('#customer-subscription-status-pjax');
            },
            complete: function(data) {
                //$.unblockUI();
            }
        });
    });

});
JS;
$this->registerJs($script, View::POS_END);