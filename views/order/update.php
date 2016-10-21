<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TradegeckoOrderLog */

$this->title = 'Update Tradegecko Order Log: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tradegecko Order Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tradegecko-order-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
