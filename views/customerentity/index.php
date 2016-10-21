<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerEntitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Customer Entities';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-entity-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Customer Entity', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'email',
            'first_name',
        //    'created_at',
      //      'updated_at',
       
          
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
