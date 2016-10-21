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

<div class="widget-content nopadding">
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
    <div class="control-group">
        <label class="control-label">When will it take affect?</label>
        <div class="controls">
            <label for="subStartDateNow">Now <input type="checkbox" name="CustomerSubscriptionState[now]" id="subStartDateNow" value="1" /></label>

            <?php
            echo DatePicker::widget([
                'id' => 'customersubscriptionstate-start_date',
                'name' => 'CustomerSubscriptionState[start_date]',
                'value' => date('F j, Y', strtotime('+1 day')),
                'options' => ['class'=>'dp-state-form', 'style'=>'width: 120px !important;'],
                'clientOptions'=>[
                    'changeMonth'=> true,
                    'changeYear'=> true,
                    'showButtonPanel'=> true,
                    'minDate' => '1',
                    'dateFormat'=> 'MM d, yy',
                ]
            ]);
            ?>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">Set to what status?</label>
        <div class="controls">
            <?php
            $statusLabels = CustomerSubscriptionState::$statusLabels;
            //if($customerSubscriptionState->status){
            if($customerSubscriptionState->status && $customerSubscriptionState->status != CustomerSubscriptionState::STATUS_RESUME){
                unset($statusLabels[CustomerSubscriptionState::STATUS_CANCEL]);
                unset($statusLabels[CustomerSubscriptionState::STATUS_PAUSE]);
            }
            else
                unset($statusLabels[CustomerSubscriptionState::STATUS_RESUME]);
            //}

            echo $form->field($customerSubscriptionState, 'update_status', ['template' => '{input}'])->radioList($statusLabels);
            ?>
            <?php
            echo $form->field($customerSubscriptionState, 'pause_duration', ['template' => '{input}'])
                ->radioList(
                    CustomerSubscriptionState::$pauseDuration,
                    [
                        'tag'=>'ul',
                        'class'=>'rl-state-form',
                        'style'=>'display: none;',
                        'item' => function($index, $label, $name, $checked, $value) {
                            $id = str_replace(['[]', '][', '[', ']', ' '], ['', '-', '-', '', '-'], $name).'_'.$index;
                            $return = '<li>';
                            $return .= '<input type="radio" name="' . $name . '" id="' . $id . '" value="' . $value . '">';
                            $return .= '<label class="btn btn-warning" for="' . $id . '">' . $label . '</label>';
                            $return .= '</li>';

                            return $return;
                        }
                    ]);
            ?>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">For what Reason?</label>
        <div class="controls">
            <?php
            echo $form->field($customerSubscriptionState, 'update_reason', [
                'template' => '{input}'
            ])->dropDownList(CustomerSubscriptionState::$reasonOptions);
            ?>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">Description / Note</label>
        <div class="controls">
            <?php
            echo $form->field($customerSubscriptionState, 'update_note', [
                'template' => '{input}'
            ])->textarea(['rows'=>4]);
            ?>
        </div>
    </div>
    <div class="form-actions">
        <button class="btn btn-success" type="submit">Save</button>
        <button class="btn btn-danger" type="reset">Cancel</button>
    </div>
    <?php ActiveForm::end(); ?>
</div>