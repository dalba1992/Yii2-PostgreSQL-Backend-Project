<?php
/**
 * @var CustomerEntity $model
 */

use yii\bootstrap;
use yii\helpers\Html;

use yii\widgets\ActiveForm;
use yii\widgets\ListView;
use yii\widgets\Pjax;

use yii\jui\DatePicker;

use app\models;
use app\models\CustomerSubscription;
use app\models\CustomerSubscriptionState;
use app\models\CustomerSubscriptionLog;

$customerSubscription = $model->customerSubscription;
if(!$customerSubscription->customerSubscriptionState){
    $customerSubscriptionState = CustomerSubscriptionState::defaultState($customerSubscription->id);
    $customerSubscriptionState->pause_duration = 1;
}
else{
    $customerSubscriptionState = $customerSubscription->customerSubscriptionState;
}
?>

<div class="alert alert-danger bottom0">
    <strong>Oh snap! Membership is not active.</strong><br>
    To enable the membership select a date when this members would like activate their subscription and hit the <i>Resume</i> button.
    <?php
    $form = ActiveForm::begin(
        [
            'id' => 'customer-subscription-state-form',
            'action' => Yii::$app->getUrlManager()->createUrl(['/customer-subscription-state/update', 'id'=>$customerSubscriptionState->id]),
            'options'=>[
                'class' => 'form-horizontal'
            ]
        ]
    );
    ?>
    <label for="subStartDateNow">Now <input type="checkbox" name="CustomerSubscriptionState[now]" id="subStartDateNow" value="1" /></label>
    <?php
    echo DatePicker::widget([
        'id' => 'customersubscriptionstate-start_date',
        'name' => 'CustomerSubscriptionState[start_date]',
        'value' => date('F j, Y', strtotime('+1 day')),
        'language' => 'en',
        'options' => ['class'=>'dp-state-form', 'style'=>'width: 120px !important;'],
        'clientOptions'=>[
            'changeMonth'=> true,
            'changeYear'=> true,
            'showButtonPanel'=> true,
            'minDate' => '1',
            'dateFormat'=> 'MM d, yy',
        ]
    ]);

    echo Html::activeHiddenInput($customerSubscriptionState, 'update_status', ['value'=>CustomerSubscriptionState::STATUS_RESUME]);
    ?>

    <button class="btn btn-success" type="submit">Resume Subscription</button>
    <?php ActiveForm::end(); ?>
</div>