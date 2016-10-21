<?php
/**
 * @var CustomerEntity $model
 */

use yii\bootstrap;
use yii\helpers\Html;

use yii\widgets\ActiveForm;
use yii\widgets\ListView;
use yii\widgets\Pjax;

use yii\jui\DatePicker;

use app\models;


$searchModel = new models\CustomerSubscriptionLogSearch();
$searchModel->subscription_id = $model->customerSubscription->id;
$dataProvider = $searchModel->search([]);
$dataProvider->pagination->pageSize = 5;
?>

<div class="widget-content nopadding">
        <?php
        echo ListView::widget( [
            'id' => 'customer-subscription-log',
            'dataProvider' => $dataProvider,
            'itemView' => '_log',
            'layout' => "{items}\n{pager}",
            'options' => ['tag'=>'ul', 'class'=>'activity-list'],
            'itemOptions' => ['tag'=>'li'],
            'pager' => [
                'class' => 'app\components\ALinkPager',
                'options' => [
                    'class' => 'dataTables_paginate fg-buttonset ui-buttonset fg-buttonset-multi ui-buttonset-multi paging_full_numbers',
                ],
                'activePageCssClass' => 'fg-button ui-button ui-state-default ui-state-disabled',
                'linkOptions' => ['class'=>'fg-button ui-button ui-state-default'],
                'wrapperTag' => 'li',
                'wrapperStyle' => 'overflow: hidden; padding: 10px 0;'
            ],
        ] );
        ?>
</div>