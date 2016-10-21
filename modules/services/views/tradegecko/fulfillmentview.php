<?php

    use yii\helpers\Html;
    use yii\grid\GridView;
    use app\modules\services\models\TradegeckoVariant;

/**
 * @var \yii\web\View $this
 * @var \yii\data\ActiveDataProvider $fulfillmentLineItemDataProvider
 * @var app\modules\services\models\TradegeckoFulfillment $model
 * @var array $fulfillmentData
 */

    $this->title = 'Shipment #' . $model['order_number'];
?>

<div id="content-header">
    <h1><?=$this->title?></h1>
    <div class="btn-group">
        <?=Html::a('<i class="glyphicon glyphicon-trash"></i> Delete', ['fulfillmentdelete', 'id' => $model->id], [
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
    <a href="<?php echo Yii::$app->urlManager->createUrl('services/tradegecko/fulfillment'); ?>">Shipment</a>
    <a href="javascript:;" class="current"><?=$this->title?></a>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <dl class="dl-horizontal">
                            <dt>Bill To</dt>
                            <dd><?php echo $fulfillmentData['billTo']; ?></dd>
                        </dl>
                    </div>
                    <div class="col-md-3">
                        <dl class="dl-horizontal">
                            <dt>Packed</dt>
                            <dd><?php echo date('M d Y', strtotime($fulfillmentData['packed_at'])); ?></dd>
                        </dl>
                    </div>
                    <div class="col-md-3">
                        <dl class="dl-horizontal">
                            <dt>Delivery</dt>
                            <dd></dd>
                        </dl>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <dl class="dl-horizontal">
                            <dt>Ship To</dt>
                            <dd><?php echo $fulfillmentData['shipTo']; ?></dd>
                        </dl>
                    </div>
                    <div class="col-md-3">
                        <dl class="dl-horizontal">
                            <dt>Shipped</dt>
                            <dd><?php echo date('M d Y', strtotime($fulfillmentData['shipped_at'])); ?></dd>
                        </dl>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <dl class="dl-horizontal">
                            <dt>Ship From</dt>
                            <dd><?php echo $fulfillmentData['shipFrom']; ?></dd>
                        </dl>
                    </div>
                    <div class="col-md-3">
                        <dl class="dl-horizontal">
                            <dt>Received</dt>
                            <dd><?php echo date('M d Y', strtotime($fulfillmentData['received_at'])); ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
            <?php if($fulfillmentLineItemDataProvider->count > 0): ?>
                <?= GridView::widget([
                    'dataProvider' => $fulfillmentLineItemDataProvider,
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
                            'label'=>'Product',
                            'value' => function($data) {
                                $var = TradegeckoVariant::find()->where(['id' => $data['tradegeckoOrderLineItem']['variant_id']])->one();
                                return $var['sku'].' - '.$var['product_name'].' - '.$var['variant_name'];
                            }
                        ],

                        [
                            'attribute' => 'quantity',
                            'label' => 'Quantity'
                        ],

                        [
                            'label' => 'Not Packed',
                        ],

                        [
                            'label' => 'Total Qty',
                            'value' => function($data) {
                                return $data['tradegeckoOrderLineItem']['quantity'];
                            }
                        ],

                        [
                            'label' => 'Total ('.$fulfillmentData['currencySymbol'].')',
                            'value' => function($data) {
                                return number_format(floatval($data['tradegeckoOrderLineItem']['price'])*floatval($data['tradegeckoOrderLineItem']['quantity']), 2);
                            }
                        ],

                    ]
                ]);?>
            <?php endif; ?>
        </div>
    </div>
</div>