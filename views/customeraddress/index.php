<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerEntityAddressSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Customer Entity Addresses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-entity-address-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Customer Entity Address', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'customer_id',
            'type_id',
            'first_name',
            'last_name',
            // 'address:ntext',
            // 'address2:ntext',
            // 'city',
            // 'state',
            // 'country',
            // 'zipcode',
            // 'is_default',
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
