<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\TradegeckoOrderLog */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tradegecko Order Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tradegecko-order-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'customer_id',
            'tradegecko_order_number',
            'tradegecko_order_id',
            'created_at',
            'import_csv_title',
        ],
    ]) ?>

</div>
