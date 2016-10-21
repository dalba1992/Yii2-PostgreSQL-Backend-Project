<?php

    use yii\helpers\Html;
    use yii\grid\GridView;

/**
 * @var \yii\web\View $this
 * @var \yii\data\ActiveDataProvider $variantDataProvider
 * @var app\modules\services\models\TradegeckoProduct $model
 */

    $this->title = 'Product - ' . $model->product_name;
    //$this->params['breadcrumbs'][] = $this->title;

    $tagArr = [];
    $tag = $model->tags;
    if ($tag != '')
        $tagArr = explode(',', $tag);
?>

<div id="content-header">
    <h1><?=$model->product_name?></h1>
    <div class="btn-group">
        <?=Html::a('<i class="glyphicon glyphicon-trash"></i> Delete', ['inventorydelete', 'id' => $model->id], [
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
    <a href="<?php echo Yii::$app->urlManager->createUrl('services/tradegecko/inventory'); ?>">Inventory</a>
    <a href="javascript:;" class="current"><?=$model->product_name?></a>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        <dl>
                            <dt>Product type</dt>
                            <dd><?=$model->product_type?></dd>
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
                            <dd><?=date('M d Y', strtotime($model->created_at))?></dd>
                        </dl>
                    </div>
                </div>
                <div class="row">
                    <?php if(count($tagArr)) { ?>
                    <div class="col-md-4">
                        <dl class="dl-horizontal">
                            <dt style="text-align:left; width:60px;">Tags</dt>
                            <dd style="margin-left:0px; margin-top:5px;">
                                <?php foreach($tagArr as $tag) { ?>
                                    <span class="label label-success" style="padding: 7px; font-weight: bold;"><?=$tag?></span>
                                <?php } ?>
                            </dd>
                        </dl>
                    </div>
                    <?php } ?>
                    <div class="col-md-4">
                        <dl class="dl-horizontal">
                            <dt style="text-align:left; margin-top:5px;">Type</dt>
                            <dd>
                                <select class="form-control" name="shopType" style="padding:0" id="shopType" data-value="<?=$model->id;?>">
                                    <option value="0" <?php if ($model->shop =='0' || $model->shop == NULL) echo 'selected'; ?> >Concierge</option>
                                    <option value="1" <?php if ($model->shop =='1') echo 'selected'; ?>>Shop</option>
                                </select>
                            </dd>
                        </dl>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <dt>Description</dt>
                        <dd><?=$model->description?></dd>
                    </div>
                </div>
            </div>
            <?php if($variantDataProvider->count > 0): ?>
                <?= GridView::widget([
                    'dataProvider' => $variantDataProvider,
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
                            'attribute' => 'variant_name',
                            'options' => [
                                'style' => 'width: 13.66666666%'
                            ],
                        ],
                        [
                            'attribute' => 'wholesale_price',
                            'label' => 'Wholesale',
                            'encodeLabel' => false,
                            'format' => 'html',
                            'value' => function($variantModel, $key, $index, $column) {
                                if ($variantModel['wholesale_price'] != "")
                                    return '<i class="fa fa-usd"></i>' . $variantModel['wholesale_price'];
                            },
                            'options' => [
                                'style' => 'width:3%'
                            ],
                            'contentOptions' => [
                                'class' => 'text-center'
                            ]
                        ],
                        [
                            'attribute' => 'retail_price',
                            'label' => 'RRP',
                            'encodeLabel' => false,
                            'format' => 'html',
                            'value' => function($variantModel, $key, $index, $column) {
                                if ($variantModel['retail_price'] != "")
                                    return '<i class="fa fa-usd"></i>' . $variantModel['retail_price'];
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
                            'attribute' => 'buy_price',
                            'label' => 'Buy Price',
                            'encodeLabel' => false,
                            'format' => 'html',
                            'value' => function($variantModel, $key, $index, $column) {
                                if ($variantModel['buy_price'] != "")
                                    return '<i class="fa fa-usd"></i>' . $variantModel['buy_price'];
                            },
                            'options' => [
                                'style' => 'width:3%'
                            ],
                            'contentOptions' => [
                                'class' => 'text-center'
                            ],
                        ],
                        [
                            'attribute' => 'moving_average_cost',
                            'label' => 'MAC',
                            'encodeLabel' => false,
                            'format' => 'html',
                            'value' => function($variantModel, $key, $index, $column) {
                                if ($variantModel['moving_average_cost'] != "")
                                    return '<i class="fa fa-usd"></i>' . $variantModel['moving_average_cost'];
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
                            'label' => 'Available',
                            'value' => function($data) {
                                return $data->stock_on_hand - $data->committed_stock;
                            },
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
                            'attribute' => 'stock_on_hand',
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

    <div class="col-xs-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <?php if ($model->image_url != "") { ?>
                <div class="row">
                    <div class="col-md-4">
                        <dl>
                            <dt>Product Image</dt>
                            <dd><img src="<?php echo $model->image_url; ?>"></dd>
                        </dl>
                    </div>
                </div>
                <?php } ?>
                <?php foreach($variants as $variant) { ?>
                    <?php foreach($variant['tradegeckoImage'] as $image) { ?>
                        <div class="col-md-2">
                            <dl>
                                <dt><?php echo $variant->variant_name; ?></dt>
                                <dd>
                                    <img src="<?php echo $image->base_path.'/thumbnail_'.$image->file_name; ?>">
                                </dd>
                            </dl>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<div id="message" style="display: none;">
    <h4><i class="fa fa-spinner fa-pulse"></i> Updating...</h4>
</div><!-- /.modal -->

<?php

$script = <<<JS
// Sync with TradeGecko
$(function(){
    $('#shopType').change(function(){
        var inventoryId = $(this).attr('data-value');
        $.blockUI({ message: $("#message") });
        $.ajax({
            url: '/services/tradegecko/updateshop',
            type: 'GET',
            data: {inventoryId: inventoryId},
            success: function(data) {
                $.unblockUI();
            },
            complete: function(data) {
            }
        });
    });
});
JS;

$this->registerJs($script, \yii\web\View::POS_END);

?>