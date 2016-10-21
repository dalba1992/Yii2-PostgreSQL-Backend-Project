<?php

    use yii\helpers\Html;
    use yii\grid\GridView;

/**
 * @var \yii\web\View $this
 * @var \yii\data\ActiveDataProvider $orderLineItemDataProvider
 * @var app\modules\services\models\TradegeckoOrder $model
 * @var array $orderData
 */

    $this->title = 'Order - ' . $model['tradegeckoCompany']['company_name'];
?>

<div id="content-header">
    <h1><?=$this->title?></h1>
    <div class="btn-group">
        <?=Html::a('<i class="glyphicon glyphicon-trash"></i> Delete', ['orderdelete', 'id' => $model->id], [
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
    <a href="<?php echo Yii::$app->urlManager->createUrl('services/tradegecko/order'); ?>">Order</a>
    <a href="javascript:;" class="current"><?=$model['tradegeckoCompany']['company_name']?></a>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <dl class="dl-horizontal">
                            <dt>Bill To</dt>
                            <dd><?php echo $orderData['billTo']; ?></dd>
                        </dl>
                    </div>
                    <div class="col-md-3">
                        <dl class="dl-horizontal">
                            <dt>Issue Date</dt>
                            <dd><?php echo date('M d Y', strtotime($orderData['issued_at'])); ?></dd>
                        </dl>
                    </div>
                    <div class="col-md-3">
                        <dl class="dl-horizontal">
                            <dt>Currency</dt>
                            <dd><?php echo $orderData['currency']; ?></dd>
                        </dl>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <dl class="dl-horizontal">
                            <dt>Ship To</dt>
                            <dd><?php echo $orderData['shipTo']; ?></dd>
                        </dl>
                    </div>
                    <div class="col-md-3">
                        <dl class="dl-horizontal">
                            <dt>Shipment Date</dt>
                            <dd><?php echo date('M d Y', strtotime($orderData['ship_at'])); ?></dd>
                        </dl>
                    </div>
                    <div class="col-md-3">
                        <dl class="dl-horizontal">
                            <dt>Totals Are</dt>
                            <dd><?php echo $orderData['tax_treatment']; ?></dd>
                        </dl>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <dl class="dl-horizontal">
                            <dt>Ship From</dt>
                            <dd><?php echo $orderData['shipFrom']; ?></dd>
                        </dl>
                    </div>
                    <div class="col-md-3">
                        <dl class="dl-horizontal">
                            <dt>Email</dt>
                            <dd><?php echo $orderData['email']; ?></dd>
                        </dl>
                    </div>
                    <div class="col-md-3">
                        <dl>
                            <dt></dt>
                            <dd></dd>
                        </dl>
                    </div>
                </div>
            </div>
            <?php if($orderLineItemDataProvider->count > 0): ?>
                <?= GridView::widget([
                    'dataProvider' => $orderLineItemDataProvider,
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
                            'label'=>'Item Name',
                            'value' => function($data) {
                                return $data['tradegeckoVariant']['sku'].' - '.$data['tradegeckoVariant']['product_name'].' - '.$data['tradegeckoVariant']['variant_name'];
                            }
                        ],

                        [
                            'attribute' => 'quantity',
                            'label' => 'Quantity'
                        ],

                        [
                            'label' => 'Available',
                            'value' => function($data) {
                                return $data['tradegeckoVariant']['stock_on_hand'] - $data['tradegeckoVariant']['committed_stock'];
                            }
                        ],

                        [
                            'attribute' => 'price',
                            'label' => 'Price ('.$orderData['currencySymbol'].')',
                            'value' => function($data) {
                                if ($data['price'] != "")
                                    return number_format($data['price'], 2);
                            }
                        ],

                        [
                            'attribute' => 'discount',
                            'label' => 'Discount',
                            'value' => function($data) {
                                if ($data['discount'] != "")
                                    return $data['discount'].'%';
                            }
                        ],

                        [
                            'attribute' => 'tax_rate',
                            'label' => 'Tax Rate',
                            'value' => function($data) {
                                if ($data['tax_rate'] != "")
                                    return $data['tax_rate'].'%';
                            }
                        ],

                        [
                            'label' => 'Total ('.$orderData['currencySymbol'].')',
                            'value' => function($data) {
                                return number_format(floatval($data['price'])*floatval($data['quantity']), 2);
                            }
                        ],

                    ]
                ]);?>
            <?php endif; ?>
        </div>
    </div>
</div>