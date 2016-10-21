<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TradegeckoOrderLog */

$this->title = 'Create Tradegecko Order Log';
$this->params['breadcrumbs'][] = ['label' => 'Tradegecko Order Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tradegecko-order-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
