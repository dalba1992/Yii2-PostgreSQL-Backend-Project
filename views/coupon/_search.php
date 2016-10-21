<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CouponSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="coupon-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'coupon_name') ?>

    <?= $form->field($model, 'coupon_code') ?>

    <?= $form->field($model, 'message') ?>

    <?= $form->field($model, 'discount_type') ?>

    <?php // echo $form->field($model, 'amount_off') ?>

    <?php // echo $form->field($model, 'duration') ?>

    <?php // echo $form->field($model, 'duration_time') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'is_apply') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
