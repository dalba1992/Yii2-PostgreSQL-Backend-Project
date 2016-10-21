<?php

/**
 * @var \yii\web\View $this
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\services\models\TradegeckoProductSearch $searchModel
 */

use yii\helpers\Html;
use yii\grid\GridView;
use app\components\Helper;
use yii\widgets\LinkPager;

$this->title = 'Tradegecko Inventory';
$syncAction = Yii::$app->urlManager->createUrl('inventory/sync');
$syncOneAction = Yii::$app->urlManager->createUrl('inventory/sync-one');
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
    <a href="javascript:;">Inventory</a>
 </div>

<div class="row">
    <div class="col-xs-12">
        <div class="widget-box">

            <div class="widget-title">
                <span class="icon">
                    <i class="fa fa-th"></i>
                </span>
                <h5>Inventory</h5>

                <div class="buttons">
                    <a href="javascript:void(0);" class="btn" id="syncTradeGecko"><i class="fa fa-refresh"></i> <span class="text">Sync with TradeGecko</span></a>
                </div>
            </div>

            <div class="widget-content nopadding">
                    <?php
                    echo GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
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
                                'attribute' => 'name',
                                'options' => [
                                    'style' => 'width: 13.66666666%'
                                ]
                            ],
                            [
                                'attribute' => 'supplier',
                                'options' => [
                                    'style' => 'width: 13.66666666%'
                                ]
                            ],
                            [
                                'attribute' => 'brand',
                                'options' => [
                                    'style' => 'width: 13.66666666%'
                                ]
                            ],
                            [
                                'class' => \yii\grid\DataColumn::className(),
                                'attribute' => 'quantity',
                                'label' => 'Available',
                                'format' => 'text',
                                'value' => function($model, $key, $index, $column) {
                                    return $model['quantity'] . " in " . count($model->variants) . " " . (count($model->variants) > 1 ? "variants" : (count($model->variants) != 0 ? "variant" : "variants"));
                                },
                                'options' => [
                                    'style' => 'width: 8%'
                                ],
                                'contentOptions' => [
                                    'class' => 'text-center'
                                ]
                            ],
                            [
                                'attribute' => 'status',
                                'value' => function($model, $key, $index, $column) {
                                    return ucfirst($model['status']);
                                },
                                'options' => [
                                    'style' => 'width: 6%;',
                                ],
                                'contentOptions' => [
                                    'class' => 'text-center'
                                ]
                            ],
                            [
                                'class' => \yii\grid\DataColumn::className(),
                                'attribute' => 'updatedAt',
                                'label' => 'Last updated',
                                'format' => ['date', 'php:M d Y'],
                                'options' => [
                                    'style' => 'width: 6%;',
                                ],
                                'contentOptions' => [
                                    'class' => 'text-center'
                                ]
                            ],
                            [
                                'attribute' => 'shop',
                                'label'=>'Shop',
                                'format' => 'raw',
                                'options' => [
                                    'style' => 'width:10%'
                                ],
                                'value' => function($data){
                                    $data_label = '';
                                    if ($data->shop == '0' || $data->shop == null)
                                        $data_label = 'Concierage Item';
                                    else
                                        $data_label = 'Shop Item';
                                    return $data_label;
                                },
                                'filter'=> false,
                            ],
                            [
                                'class' => \yii\grid\ActionColumn::className(),
                                'buttons' => [
                                    'toggle' => function($url, $model, $key) {
                                        if($model->shop)
                                            return Html::a('Shop', ['toggle-shop-flag', 'id' => $model->id], ['class' => 'btn btn-success btn-xs']);
                                        else
                                            return Html::a('Concierge', ['toggle-shop-flag', 'id' => $model->id], ['class' => 'btn btn-default btn-xs']);
                                    },
                                    'view' => function($url, $model, $key) {
                                        return Html::a('View', $url, ['class' => 'btn btn-default btn-xs']);
                                    },
                                    'sync' => function($url, $model, $key) {
                                        return Html::a('Sync', ['inventorysync', 'id' => $model->tradegeckoId], ['class' => 'btn btn-default btn-xs sync', 'data-id' => $model->tradegeckoId]);
                                    },
                                    'delete' => function($url, $model, $key) {
                                        return Html::a('Delete', $url, ['data-confirm' => 'Are you sure?', 'class' => 'btn btn-danger btn-xs']);
                                    },
                                ],
                                'template' => '
                                        {toggle}<br>{view}<br>{sync}<br>{delete}
                                    ',
                                'options' => [
                                    'style' => 'width: 5%;',
                                ],
                                'contentOptions' => [
                                    'class' => 'text-center'
                                ]
                            ]
                        ]
                    ]);
                    ?>
            </div>

        </div>
    </div>
</div>

<div id="message1" style="display: none;">
    <h4><i class="fa fa-spinner fa-pulse"></i> Please wait for a while</h4>
</div><!-- /.modal -->

<?php
$search_script = <<<JS
    $(function(){
        $('.sync').click(function(){
            var inventoryId = $(this).attr('data-id');
            $.blockUI({ message: $("#message1") });
            $.ajax({
                url: syncOneAction,
                type: 'GET',
                data: {inventoryId: inventoryId},
                success: function(data) {},
                complete: function(data) {
                    var obj = jQuery.parseJSON(data.responseText);
                    if (obj['code'] == '0')
                        window.location.href = '';
                    else
                    {
                        $.unblockUI();
                        alert(obj['message']);
                    }
                }
            });

            return false;
        });

        $('#syncTradeGecko').click(function() {
            $.blockUI({ message: $("#message1") });
            $.ajax({
                url: syncAction,
                type: 'GET',
                data: {},
                success: function(data) {},
                complete: function(data) {
                    var obj = jQuery.parseJSON(data.responseText);
                    if (obj['code'] == '0')
                        window.location.href = '';
                    else
                    {
                        $.unblockUI();
                        alert(obj['message']);
                    }
                }
            });

            return false;
        });
    });
JS;
$search_script = str_replace('url: syncOneAction', 'url: "'.$syncOneAction.'"', $search_script);
$search_script = str_replace('url: syncAction', 'url: "'.$syncAction.'"', $search_script);
$this->registerJs($search_script, \yii\web\View::POS_END);
?>
