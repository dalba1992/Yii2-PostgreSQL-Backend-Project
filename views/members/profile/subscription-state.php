<?php
/**
 * @var CustomerEntity $model
 * @var CustomerSubscriptionState $customerSubscriptionState
 */

use yii\bootstrap;
use yii\helpers\Html;
use yii\web\View;

use yii\widgets\ActiveForm;
use yii\widgets\ListView;
use yii\widgets\Pjax;

use yii\jui\DatePicker;

use app\models;
use app\models\CustomerEntity;
use app\models\CustomerSubscriptionState;
use app\models\CustomerSubscriptionLog;

$activeStateChange = $model->customerSubscription->activeStateChange;
?>

<div class="row" id="subscription-section">
    <div class="widget-box">
        <?php Pjax::begin(['id' => 'customer-subscription-status-pjax', 'timeout' => false, 'enablePushState' => false]); ?>
        <div class="widget-title nopadding">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#subscription-status-form" data-toggle="tab"><i class="icon-time"></i> Set Future Month Status</a></li>
                <li><a href="#subscription-status-log" data-toggle="tab"><i class="icon-align-justify"></i> Future Month Status Log</a></li>
            </ul>
        </div>

        <div class="widget-content tab-content">

            <!--Future Month Status-->
            <div class="tab-pane active" id="subscription-status-form">
                <?php
                if(!$activeStateChange){
                    if($model->customerSubscription->customerSubscriptionState && $model->customerSubscription->customerSubscriptionState->status == CustomerSubscriptionState::STATUS_CANCEL){
                        echo Yii::$app->view->renderFile('@app/views/members/profile/subscription-state/_quickResume.php', ['model'=>$model]);
                    }
                    else{
                        echo Yii::$app->view->renderFile('@app/views/members/profile/subscription-state/_tabForm.php', ['model'=>$model]);
                    }
                }
                elseif(!$model->customerSubscription->stripe_plan_id) {
                    echo '<div class="alert alert-danger bottom0">';
                    echo 'Cannot change the subscription status because the user doesn\'t have a plan id from .';
                    echo '</div>';
                }
                else{
                    echo '<div class="alert alert-danger bottom0">';
                    echo 'You have a status update request, please cancel it first before being able to alter this subscription.';
                    echo '</div>';
                }
                ?>
            </div>

            <!--Future Month Status Log-->
            <div class="tab-pane" id="subscription-status-log">
                <?php
                echo Yii::$app->view->renderFile('@app/views/members/profile/subscription-state/_tabLog.php', ['model'=>$model]);
                ?>
            </div>
        </div>
        <?php Pjax::end(); ?>
    </div>
</div>