<?php

    use yii\helpers\Html;
    use yii\grid\GridView;

/**
 * @var \yii\web\View $this
 * @var \yii\data\ActiveDataProvider $addressDataProvider
 * @var \yii\data\ActiveDataProvider $contactDataProvider
 * @var app\modules\services\models\TradegeckoCompany $companyData
 */

    $this->title = 'Company Details';
    //$this->params['breadcrumbs'][] = $this->title;
?>

<div id="content-header">
    <h1>Company - <?=$companyData['company_name']?></h1>
    <div class="btn-group">
        <?=Html::a('<i class="glyphicon glyphicon-trash"></i> Delete', ['companydelete', 'id' => $companyData['id']], [
            'class' => 'btn btn-large',
            'data-confirm' => 'Are you sure?'
        ])?>
    </div>
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
<div id="breadcrumb">
    <a data-original-title="Go to Home" href="<?php echo Yii::$app->homeUrl ?>" title="" class="tip-bottom"><i class="icon-home"></i> Home</a>
    <a href="<?php echo Yii::$app->urlManager->createUrl('services/tradegecko/company'); ?>">Company</a>
    <a href="javascript:;" class="current"><?=$companyData['company_name']?></a>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        <dl class="dl-horizontal">
                            <dt>Company Type</dt>
                            <dd><?=$companyData['company_type']; ?></dd>
                        </dl>
                    </div>
                    <div class="col-md-4">
                        <dl class="dl-horizontal">
                            <dt>E-mail</dt>
                            <dd><?=$companyData['email']; ?></dd>
                        </dl>
                    </div>
                    <div class="col-md-4">
                        <dl class="dl-horizontal">
                            <dt>Phone Number</dt>
                            <dd><?=$companyData['phone_number']; ?></dd>
                        </dl>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <dl class="dl-horizontal">
                            <dt>Company Website</dt>
                            <dd><a href="<?=$companyData['website']; ?>"><?=$companyData['website']; ?></a></dd>
                        </dl>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <dl class="dl-horizontal">
                            <dt>Created Date</dt>
                            <dd><?=date('M d Y', strtotime($companyData['created_at'])); ?></dd>
                        </dl>
                    </div>
                    <div class="col-md-3">
                        <dl class="dl-horizontal">
                            <dt>Updated Date</dt>
                            <dd><?=date('M d Y', strtotime($companyData['updated_at'])); ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div id="collapseTwo" class="in">
                <div class="widget-content">
                    <div id="stripe_data">
                        <div class="widget-box">
                            <div class="widget-title nopadding">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#tab1" data-toggle="tab"> Address</a></li>
                                    <li><a href="#tab2" data-toggle="tab"> Contact</a></li>
                                </ul>
                            </div>
                            <div class="widget-content tab-content">
                                <div class="tab-pane active" id="tab1">
                                    <?php if($addressDataProvider->count > 0): ?>
                                        <?= GridView::widget([
                                            'dataProvider' => $addressDataProvider,
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
                                                    'attribute' => 'email',
                                                    'label' => 'E-mail'
                                                ],

                                                [
                                                    'attribute' => 'phone_number',
                                                    'label' => 'Phone Number'
                                                ],

                                                [
                                                    'attribute' => 'zip_code',
                                                    'label' => 'Zip'
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
                                    <?php if($contactDataProvider->count > 0): ?>
                                        <?= GridView::widget([
                                            'dataProvider' => $contactDataProvider,
                                            'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
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
                                                    'label' => 'Name',
                                                    'value' => function($data) {
                                                        return $data->first_name.' '.$data->last_name;
                                                    }
                                                ],

                                                [
                                                    'attribute' => 'location',
                                                    'label' => 'Location'
                                                ],

                                                [
                                                    'attribute' => 'email',
                                                    'label' => 'E-mail'
                                                ],

                                                [
                                                    'attribute' => 'phone_number',
                                                    'label' => 'Phone Number'
                                                ],

                                                [
                                                    'attribute' => 'fax',
                                                    'label' => 'Fax'
                                                ],

                                                [
                                                    'attribute' => 'position',
                                                    'label' => 'Position'
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>