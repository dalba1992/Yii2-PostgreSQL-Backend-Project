<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\CustomerEntityAddress */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Customer Entity Addresses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-entity-address-view">

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
            'type_id',
            'first_name',
            'last_name',
            'address:ntext',
            'address2:ntext',
            'city',
            'state',
            'country',
            'zipcode',
            'is_default',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
