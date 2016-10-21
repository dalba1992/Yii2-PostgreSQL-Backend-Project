<?php

    use yii\helpers\Html;
    use yii\grid\GridView;

/**
 * @var \yii\web\View $this
 * @var \yii\data\ActiveDataProvider $taxTypeDataProvider
 * @var \yii\data\ActiveDataProvider $currencyDataProvider
 * @var \yii\data\ActiveDataProvider $locationDataProvider
 * @var \yii\data\ActiveDataProvider $paymentTermDataProvider
 * @var \yii\data\ActiveDataProvider $priceListDataProvider
 */

    $this->title = 'Tradegecko Data';
    //$this->params['breadcrumbs'][] = $this->title;
?>

<div id="content-header">
    <h1><?=$this->title?></h1>
</div>
<?php if( Yii::$app->session->hasFlash('success')) { ?>
    <div class="alert alert-success">
        <button data-dismiss="alert" class="close">x</button>
        <strong>Success!</strong>
        <?php echo Yii::$app->session->getFlash('success'); ?>
    </div>
<?php } ?>
<?php if( Yii::$app->session->hasFlash('fail')) { ?>
    <div class="alert alert-error">
        <button data-dismiss="alert" class="close">x</button>
        <strong>Error!</strong>
        <?php echo Yii::$app->session->getFlash('fail'); ?>
    </div>
<?php } ?>

<div class="row">
    <div class="col-xs-12">
        <div class="panel panel-default">
            <div id="collapseTwo" class="in">
                <div class="widget-content">
                    <div id="stripe_data">
                        <div class="widget-box">
                            <div class="widget-title nopadding">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#tab1" data-toggle="tab"> Currency</a></li>
                                    <li><a href="#tab2" data-toggle="tab"> Location</a></li>
                                    <li><a href="#tab3" data-toggle="tab"> Payment Term</a></li>
                                    <li><a href="#tab4" data-toggle="tab"> Price List</a></li>
                                    <li><a href="#tab5" data-toggle="tab"> Tax Type</a></li>
                                </ul>
                            </div>
                            <div class="widget-content tab-content">
                                <div class="tab-pane active" id="tab1">
                                    <?php if($currencyDataProvider->count > 0): ?>
                                        <?= GridView::widget([
                                            'dataProvider' => $currencyDataProvider,
                                            'formatter' => ['class' => 'yii\i18n\Formatter'],
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
                                            'layout' => '{items}',
                                            'columns' => [
                                                [
                                                    'class' => \yii\grid\SerialColumn::className(),
                                                    'options' => [
                                                        'style' => 'width:2%'
                                                    ],
                                                    'contentOptions' => [
                                                        'class' => 'text-center'
                                                    ]
                                                ],

                                                [
                                                    'attribute' => 'currency_name',
                                                    'label' => 'Name'
                                                ],

                                                [
                                                    'attribute' => 'iso',
                                                    'label' => 'ISO'
                                                ],

                                                [
                                                    'attribute' => 'symbol',
                                                    'label' => 'Symbol'
                                                ],

                                                [
                                                    'attribute' => 'rate',
                                                    'label' => 'Exchange Rate'
                                                ],

                                                [
                                                    'attribute' => 'currency_precision',
                                                    'label' => 'Precision'
                                                ],

                                                [
                                                    'attribute' => 'created_at',
                                                    'label'=>'Created',
                                                    'value' => function($data) {
                                                        return date('M d Y', strtotime($data->created_at));
                                                    },
                                                ],

                                                [
                                                    'attribute' => 'updated_at',
                                                    'label'=>'Last Updated',
                                                    'value' => function($data) {
                                                        return date('M d Y', strtotime($data->updated_at));
                                                    },
                                                ],

                                            ]
                                        ]);?>
                                    <?php endif; ?>
                                </div>
                                <div class="tab-pane" id="tab2">
                                    <?php if($locationDataProvider->count > 0): ?>
                                        <?= GridView::widget([
                                            'dataProvider' => $locationDataProvider,
                                            'formatter' => ['class' => 'yii\i18n\Formatter'],
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
                                            'layout' => '{items}',
                                            'columns' => [
                                                [
                                                    'class' => \yii\grid\SerialColumn::className(),
                                                    'options' => [
                                                        'style' => 'width:2%'
                                                    ],
                                                    'contentOptions' => [
                                                        'class' => 'text-center'
                                                    ]
                                                ],

                                                [
                                                    'attribute' => 'label',
                                                    'label' => 'Label'
                                                ],

                                                [
                                                    'attribute' => 'address1',
                                                    'label' => 'Address 1'
                                                ],

                                                [
                                                    'attribute' => 'address2',
                                                    'label' => 'Address 2'
                                                ],

                                                [
                                                    'attribute' => 'city',
                                                    'label' => 'City'
                                                ],

                                                [
                                                    'attribute' => 'suburb',
                                                    'label' => 'Suburb'
                                                ],

                                                [
                                                    'attribute' => 'state',
                                                    'label' => 'State'
                                                ],

                                                [
                                                    'attribute' => 'country',
                                                    'label' => 'Country'
                                                ],

                                                [
                                                    'attribute' => 'zip_code',
                                                    'label' => 'Zip'
                                                ],

                                                [
                                                    'attribute' => 'created_at',
                                                    'label'=>'Created',
                                                    'value' => function($data) {
                                                        return date('M d Y', strtotime($data->created_at));
                                                    },
                                                ],

                                                [
                                                    'attribute' => 'updated_at',
                                                    'label'=>'Last Updated',
                                                    'value' => function($data) {
                                                        return date('M d Y', strtotime($data->updated_at));
                                                    },
                                                ],

                                            ]
                                        ]);?>
                                    <?php endif; ?>
                                </div>
                                <div class="tab-pane" id="tab3">
                                    <?php if($paymentTermDataProvider->count > 0): ?>
                                        <?= GridView::widget([
                                            'dataProvider' => $paymentTermDataProvider,
                                            'formatter' => ['class' => 'yii\i18n\Formatter'],
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
                                            'layout' => '{items}',
                                            'columns' => [
                                                [
                                                    'class' => \yii\grid\SerialColumn::className(),
                                                    'options' => [
                                                        'style' => 'width:2%'
                                                    ],
                                                    'contentOptions' => [
                                                        'class' => 'text-center'
                                                    ]
                                                ],

                                                [
                                                    'attribute' => 'payment_term_name',
                                                    'label' => 'Name'
                                                ],

                                                [
                                                    'attribute' => 'payment_term_status',
                                                    'label' => 'Status'
                                                ],

                                                [
                                                    'attribute' => 'payment_term_from',
                                                    'label' => 'From Date'
                                                ],

                                                [
                                                    'attribute' => 'due_in_days',
                                                    'label' => 'Due in Days'
                                                ],

                                            ]
                                        ]);?>
                                    <?php endif; ?>
                                </div>
                                <div class="tab-pane" id="tab4">
                                    <?php if($priceListDataProvider->count > 0): ?>
                                        <?= GridView::widget([
                                            'dataProvider' => $priceListDataProvider,
                                            'formatter' => ['class' => 'yii\i18n\Formatter'],
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
                                            'layout' => '{items}',
                                            'columns' => [
                                                [
                                                    'class' => \yii\grid\SerialColumn::className(),
                                                    'options' => [
                                                        'style' => 'width:2%'
                                                    ],
                                                    'contentOptions' => [
                                                        'class' => 'text-center'
                                                    ]
                                                ],

                                                [
                                                    'attribute' => 'id',
                                                    'label' => 'ID'
                                                ],

                                                [
                                                    'attribute' => 'price_list_code',
                                                    'label' => 'Code'
                                                ],

                                                [
                                                    'attribute' => 'price_list_name',
                                                    'label' => 'Name'
                                                ],

                                                [
                                                    'label' => 'Currency',
                                                    'value' => function($data) {
                                                        return $data->currency_iso.' ('.$data->currency_symbol.')';
                                                    }
                                                ],

                                            ]
                                        ]);?>
                                    <?php endif; ?>
                                </div>
                                <div class="tab-pane" id="tab5">
                                    <?php if($taxTypeDataProvider->count > 0): ?>
                                        <?= GridView::widget([
                                            'dataProvider' => $taxTypeDataProvider,
                                            'formatter' => ['class' => 'yii\i18n\Formatter'],
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
                                            'layout' => '{items}',
                                            'columns' => [
                                                [
                                                    'class' => \yii\grid\SerialColumn::className(),
                                                    'options' => [
                                                        'style' => 'width:2%'
                                                    ],
                                                    'contentOptions' => [
                                                        'class' => 'text-center'
                                                    ]
                                                ],

                                                [
                                                    'attribute' => 'tax_type_name',
                                                    'label' => 'Name'
                                                ],

                                                [
                                                    'attribute' => 'tax_type_code',
                                                    'label' => 'Code'
                                                ],

                                                [
                                                    'attribute' => 'effective_rate',
                                                    'label' => 'Rate'
                                                ],

                                                [
                                                    'label' => 'Component Label',
                                                    'value' => function($data) {
                                                        return $data['tradegeckoTaxComponent']['label'];
                                                    }
                                                ],

                                            ]
                                        ]);?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>