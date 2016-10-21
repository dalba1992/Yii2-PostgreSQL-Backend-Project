<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CustomerEntity */

$this->title = 'Create Customer Entity';
$this->params['breadcrumbs'][] = ['label' => 'Customer Entities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-entity-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
