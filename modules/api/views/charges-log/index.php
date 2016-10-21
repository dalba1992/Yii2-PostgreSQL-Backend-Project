<?php

use yii\grid\GridView;

$this->title = 'Stripe Monthly Charges';
?>

<style type="text/css">
    .btn-default:hover{
        cursor:pointer;
        color:red;
    }
</style>

<div id="content-header">
    <h1><?php echo $this->title; ?></h1>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="widget-box">

            <div class="widget-title">
                <span class="icon">
                    <i class="fa fa-list"></i>
                </span>
                <h5>Charges</h5>
            </div>

            <div class="widget-content nopadding">
                <?php echo GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $model,
                    'options' => [
                        'class' => 'dataTables_wrapper',
                    ],
                    'pager' => [
                        'class' => 'app\components\ALinkPager',
                        'options' => [
                            'class' => 'dataTables_paginate fg-buttonset ui-buttonset fg-buttonset-multi ui-buttonset-multi paging_full_numbers'
                        ],
                        'activePageCssClass' => 'fg-button ui-button ui-state-default ui-state-disabled',
                        'linkOptions' => ['class'=>'fg-button ui-button ui-state-default'],
                    ],
                    'layout' => '{items}<div class="fg-toolbar ui-toolbar ui-widget-header ui-corner-bl ui-corner-br ui-helper-clearfix"><div class="dataTables_filter">{summary}</div>{pager}</div>',
                    'columns' => [
                        [
                            'attribute' => 'id',
                            'label' => 'ID'
                        ],
                        [
                            'attribute' => 'stripeCustomerId',
                            'label' => 'Stripe Customer ID'
                        ],
                        [
                            'class' => \yii\grid\DataColumn::className(),
                            'label' => 'Event ID',
                            'value' => function($data) {
                                $obj = json_decode($data->data, true);
                                return $obj['event_id'];
                            }
                        ],
                        [
                            'class' => \yii\grid\DataColumn::className(),
                            'label' => 'Order ID',
                            'value' => function($data) {
                                $obj = json_decode($data->data, true);
                                return $obj['orderId'];
                            }
                        ],
                        [
                            'class' => \yii\grid\DataColumn::className(),
                            'label' => 'Created at',
                            'attribute' => 'createdAt',
                            'value' => function($data) {
                                return gmdate("m/d/Y H:i:s", $data->createdAt);
                            },
                            'filter' => '
                                <form class="form-inline">
                                    <div class="form-group" style="border-bottom: none">
                                        '. \yii\jui\DatePicker::widget([
                                                'language' => 'en',
                                                'dateFormat' => 'MM/dd/yyyy',
                                                'model'=>$model,
                                                'attribute'=>'fromDate',
                                                'options' => [
                                                    'class' => 'form-control',
                                                    'placeholder' => 'From date'
                                                ]
                                            ])
                                        .'
                                    </div>
                                    <div class="form-group" style="border-bottom: none">
                                        '.
                                            \yii\jui\DatePicker::widget([
                                                'language' => 'en',
                                                'dateFormat' => 'MM/dd/yyyy',
                                                'model'=>$model,
                                                'attribute'=>'toDate',
                                                'options' => [
                                                    'class' => 'form-control',
                                                    'placeholder' => 'To date'
                                                ]
                                            ])
                                        .'
                                    </div>
                                </form>
                            '
                        ]
                    ]
                ]);?>
            </div>
        </div>
    </div>
</div>
