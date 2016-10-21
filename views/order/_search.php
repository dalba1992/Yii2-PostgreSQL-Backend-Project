<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TradegeckoOrderLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tradegecko-order-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'customer_id') ?>

    <?= $form->field($model, 'tradegecko_order_id') ?>

    <?= $form->field($model, 'item_id') ?>

    <?= $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'import_csv_title') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
