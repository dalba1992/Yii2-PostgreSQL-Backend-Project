<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model \app\models\Products */
/* @var $variantDataProvider \yii\data\ActiveDataProvider */

$this->title = 'Product - ' . $model->name;
//$this->params['breadcrumbs'][] = $this->title;
?>

<div id="content-header">
    <h1><?=$model->name?></h1>
    <div class="btn-group">
        <?=Html::a('<i class="glyphicon glyphicon-trash"></i> Delete', ['delete', 'id' => $model->id], [
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
    <a href="<?php echo Yii::$app->urlManager->createUrl('/inventory'); ?>">Inventory</a>
    <a href="javascript:;" class="current"><?=$model->name?></a>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        <dl>
                            <dt>Product type</dt>
                            <dd><?=$model->productType?></dd>
                        </dl>
                    </div>
                    <div class="col-md-3">
                        <dl>
                            <dt>Supplier</dt>
                            <dd><?=$model->supplier?></dd>
                        </dl>
                    </div>
                    <div class="col-md-3">
                        <dl>
                            <dt>Added On</dt>
                            <dd><?=date('M d Y', strtotime($model->createdAt))?></dd>
                        </dl>
                    </div>
                </div>
                <?php if(count($model->tags)) { ?>
                    <div class="row">
                        <div class="col-md-5">
                            <dl>
                                <dt>Tags</dt>
                                <dd>
                                    <?php foreach($model->tags as $tag) { ?>
                                        <span class="label label-success" style="padding: 6px; font-weight: bold;"><?=$tag->name?></span>
                                    <?php } ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                <?php } ?>
                <div class="row">
                    <div class="col-md-5">
                        <dt>Description</dt>
                        <dd><?=$model->description?></dd>
                    </div>
                </div>
            </div>
            <?php if($variantDataProvider->count > 0): ?>
                <?= GridView::widget([
                    'dataProvider' => $variantDataProvider,
                    'tableOptions' => [
                        'class' => 'table table-bordered table-striped table-hover'
                    ],
                    'layout' => '{items}<div style="padding-right: 25px;" class="pull-right">{pager}</div><div class="clearfix"></div>',
                    'pager' => [
                        'options' => [
                            'class' => 'pagination'
                        ],
                        'prevPageCssClass' => '',
                        'nextPageCssClass' => ''
                    ],
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
                            'attribute' => 'sku',
                            'label' => 'SKU',
                            'options' => [
                                'style' => 'width: 13.66666666%',
                            ],
                            'sortLinkOptions' => [
                                'data-toggle' => 'tooltip',
                                'data-placement' => 'top',
                                'title' => 'Stock Keeping Unit. The unique identifier for a variant'
                            ]
                        ],
                        [
                            'attribute' => 'name',
                            'options' => [
                                'style' => 'width: 10%'
                            ],
                        ],
                        [
                            'attribute' => 'wholesalePrice',
                            'label' => 'Wholesale',
                            'encodeLabel' => false,
                            'format' => 'html',
                            'value' => function($variantModel, $key, $index, $column) {
                                if ($variantModel['wholesalePrice'] != "")
                                    return '<i class="fa fa-usd"></i>' . $variantModel['wholesalePrice'];
                            },
                            'options' => [
                                'style' => 'width:3%'
                            ],
                            'contentOptions' => [
                                'class' => 'text-center'
                            ]
                        ],
                        [
                            'attribute' => 'retailPrice',
                            'label' => 'RRP',
                            'encodeLabel' => false,
                            'format' => 'html',
                            'value' => function($variantModel, $key, $index, $column) {
                                if ($variantModel['retailPrice'] != "")
                                    return '<i class="fa fa-usd"></i>' . $variantModel['retailPrice'];
                            },
                            'options' => [
                                'style' => 'width:3%'
                            ],
                            'contentOptions' => [
                                'class' => 'text-center'
                            ],
                            'sortLinkOptions' => [
                                'data-toggle' => 'tooltip',
                                'data-placement' => 'top',
                                'title' => 'Recommended Retail Price'
                            ]
                        ],
                        [
                            'attribute' => 'buyPrice',
                            'label' => 'Buy Price',
                            'encodeLabel' => false,
                            'format' => 'html',
                            'value' => function($variantModel, $key, $index, $column) {
                                if ($variantModel['buyPrice'] != "")
                                    return '<i class="fa fa-usd"></i>' . $variantModel['buyPrice'];
                            },
                            'options' => [
                                'style' => 'width:8%'
                            ],
                            'contentOptions' => [
                                'class' => 'text-center'
                            ],
                        ],
                        [
                            'attribute' => 'movingAverageCost',
                            'label' => 'MAC',
                            'encodeLabel' => false,
                            'format' => 'html',
                            'value' => function($variantModel, $key, $index, $column) {
                                if ($variantModel['movingAverageCost'] != "")
                                    return '<i class="fa fa-usd"></i>' . $variantModel['movingAverageCost'];
                            },
                            'options' => [
                                'style' => 'width:3%'
                            ],
                            'contentOptions' => [
                                'class' => 'text-center'
                            ],
                            'sortLinkOptions' => [
                                'data-toggle' => 'tooltip',
                                'data-placement' => 'top',
                                'title' => 'Moving Average Cost'
                            ]
                        ],
                        [
                            'attribute' => 'availableStock',
                            'label' => 'Available',
                            'encodeLabel' => false,
                            'options' => [
                                'style' => 'width:3%'
                            ],
                            'contentOptions' => [
                                'class' => 'text-center'
                            ],
                            'sortLinkOptions' => [
                                'data-toggle' => 'tooltip',
                                'data-placement' => 'top',
                                'title' => 'Total Available Stock'
                            ]
                        ],
                        [
                            'attribute' => 'stockOnHand',
                            'label' => 'On Hand',
                            'encodeLabel' => false,
                            'options' => [
                                'style' => 'width:3%'
                            ],
                            'contentOptions' => [
                                'class' => 'text-center'
                            ],
                            'sortLinkOptions' => [
                                'data-toggle' => 'tooltip',
                                'data-placement' => 'top',
                                'title' => 'Total Stock On Hand'
                            ]
                        ],
                        [
                            'attribute' => 'opt1',
                            'label' => $model->opt1,
                            'encodeLabel' => false,
                            'options' => [
                                'style' => 'width:7%'
                            ],
                            'contentOptions' => [
                                'class' => 'text-center'
                            ],
                            'visible' => ($model->opt1 != null || trim($model->opt1) != '') ? true : false
                        ],
                        [
                            'attribute' => 'opt2',
                            'label' => $model->opt2,
                            'encodeLabel' => false,
                            'options' => [
                                'style' => 'width:7%'
                            ],
                            'contentOptions' => [
                                'class' => 'text-center'
                            ],
                            'visible' => ($model->opt2 != null || trim($model->opt2) != '') ? true : false
                        ],
                        [
                            'attribute' => 'opt3',
                            'label' => $model->opt3,
                            'encodeLabel' => false,
                            'options' => [
                                'style' => 'width:7%'
                            ],
                            'contentOptions' => [
                                'class' => 'text-center'
                            ],
                            'visible' => ($model->opt3 != null || trim($model->opt3) != '') ? true : false
                        ],
                        [
                            'class' => \yii\grid\ActionColumn::className(),
                            'buttons' => [
                                'delete' => function($url, $model, $key) {
                                    return Html::a('<i class="fa fa-trash-o"></i> Delete', ['variantdelete', 'variantId' => $model['id']], ['class' => 'btn btn-danger btn-xs', 'data-confirm' => 'Are you sure?']);
                                },
                            ],
                            'template' => '
                                    {delete}
                                ',
                            'options' => [
                                'style' => 'width: 5%;',
                            ],
                            'contentOptions' => [
                                'class' => 'text-center'
                            ]
                        ]
                    ]
                ]);?>
            <?php endif; ?>
        </div>
    </div>
</div>