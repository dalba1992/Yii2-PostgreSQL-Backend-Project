<?php
/**
 * @var CustomerEntity $model
 */

use app\models\CustomerEntityStatus;
use app\models\CustomerEntity;
use app\models\CustomerEntityInfo;

$CustomerStatus = $model->customerEntityStatus;
$customerInfo = $model->customerEntityInfo;

$active = 'Not Active';
$status ='';
?>
<div class="widget-title nopadding">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab1" data-toggle="tab"><i class="icon-thumbs-up"></i> Subscription</a></li>
        <li><a href="#tab2" data-toggle="tab"><i class="icon-user"></i> Profile</a></li>
        <li><a href="#tab-stripe" data-toggle="tab"><i class="icon-tasks"></i> Stripe</a></li>
        <li><a href="#tab3" data-toggle="tab"><i class="icon-shopping-cart"></i> Billing</a></li>
        <li><a href="#tab4" data-toggle="tab"><i class="icon-road"></i> Shipping</a></li>
        <li><a href="#tab5" data-toggle="tab"><i class="icon-star"></i> Style</a></li>
<!--        <li><a href="#tab-charge" data-toggle="tab"><i class="icon-star"></i> Charges</a></li>-->
    </ul>
</div>
<div class="widget-content tab-content">
    <div class="tab-pane active" id="tab1">
        <!--Current Status Info-->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fa fa-info"></i></span>
                <h5>Current Status Info</h5>
            </div>
            <div class="widget-content nopadding">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th>eStatus</th>
                        <th>vStatus</th>
                        <th>Sign Up Date</th>
                        <th>Register Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $e_activeClass = "label-warning";
                    $created_at ='';
                    if(sizeof($model)>0){
                        if($model->is_active == '1'){
                            $active = "Active";
                            $created_at = $model->created_at;
                            $e_activeClass = "label-success";
                        }

                    }
                    $class="label-success";

                    if(sizeof($CustomerStatus)>0){
                        $status = $CustomerStatus->status;

                        if($status!='active'){
                            $class="label-warning";
                        }
                    }?>
                    <tr><td><span class="label <?php echo $e_activeClass?>" ><?php echo $active; ?></span></td>
                        <td><span class="label <?php echo $class?>"><?php echo $status ?></span></td>
                        <td><?php echo date('d/m/Y',strtotime($created_at)); ?></td>
                        <td><?php echo date('d/m/Y',strtotime($created_at)); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!--System Info-->
    <?php   echo Yii::$app->controller->renderPartial('@app/views/members/profile/tabs/_profile.php', ['model'=>$model, 'customerInfo'=>$customerInfo]); ?>
    <!--Billing Info-->
    <?php   echo Yii::$app->controller->renderPartial('@app/views/members/profile/tabs/_stripe.php', ['model'=>$model]); ?>
    <!--Billing Info-->
    <?php	echo Yii::$app->controller->renderPartial('@app/views/members/profile/tabs/_billing.php'); ?>
    <!--Shipping Info-->
    <?php	echo Yii::$app->controller->renderPartial('@app/views/members/profile/tabs/_shipping.php'); ?>
    <!--Style Info-->
    <?php	echo Yii::$app->controller->renderPartial('@app/views/members/profile/tabs/_style.php'); ?>
    <!--Style Info-->
    <?php   //echo Yii::$app->controller->renderPartial('@app/views/members/profile/tabs/_charges.php', ['model'=>$model]); ?>
</div>