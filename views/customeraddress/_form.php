<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CustomerEntityAddress */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-entity-address-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'customer_id')->textInput() ?>

    <?= $form->field($model, 'type_id')->textInput() ?>

    <?= $form->field($model, 'first_name')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'last_name')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'address')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'address2')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'city')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'state')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'country')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'zipcode')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'is_default')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'updated_at')->textInput(['maxlength' => 50]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
