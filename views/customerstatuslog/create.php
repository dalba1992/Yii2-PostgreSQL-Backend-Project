<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CustomerStatusLog */

$this->title = 'Create Customer Status Log';
$this->params['breadcrumbs'][] = ['label' => 'Customer Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-status-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
