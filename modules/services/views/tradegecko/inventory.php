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

if ($status == 'all')
    $this->title = 'Tradegecko Inventory';
else if ($status == 'shop')
    $this->title = 'Shop Items';
else if ($status == 'concierge')
    $this->title = 'Concierge Items';

$syncAction = Yii::$app->urlManager->createUrl('services/tradegecko/sync-all');
$syncOneAction = Yii::$app->urlManager->createUrl('services/tradegecko/sync-one');

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
                    <i class="fa fa-th"></i>
                </span>
                <h5><?php echo $this->title; ?></h5>
                
                <!--<div class="pull-right" style="padding: 8px 8px 0 8px;">
                    <form id="page-changer" name="form1" method="GET" action="">
                        <div class="filter filter-page-changer">
                            <label>
                                Show
                                <select  class="select2-offscreen" onchange="<?php if(isset($_GET['page'])){?>window.location.href='?per-page='+this.options[this.selectedIndex].text+'&page=<?php echo $_GET['page']; ?>'<?php }else { ?>window.location.href='?per-page='+this.options[this.selectedIndex].text<?php } ?>">
                                    <option value="1" <?php if(isset($_GET['per-page']) && ($_GET['per-page']==10)) {?> selected="selected" <?php } ?>>10</option>
                                    <option value="2" <?php if(isset($_GET['per-page']) && ($_GET['per-page']==15)) {?> selected="selected" <?php } ?>>15</option>
                                    <option value="3" <?php if(isset($_GET['per-page']) && ($_GET['per-page']==20)) { echo 'selected="selected"'; }?>>20</option>
                                    <option value="4" <?php if(isset($_GET['per-page']) && ($_GET['per-page']==25)) { echo 'selected="selected"'; }?>>25</option>
                                </select>
                                &nbsp;entries
                            </label>
                        </div>
                    </form>
                </div>-->
                <div class="buttons">
                    <a href="javascript:void(0);" class="btn" id="syncTradeGecko"><i class="fa fa-refresh"></i> <span class="text">Sync with TradeGecko</span></a>
                </div>
            </div>

            <div class="widget-content nopadding">
                <?php
                echo GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
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

                        ['attribute' => 'product_name',
                            'label'=>'Name',
                        ],

                        ['attribute' => 'supplier',
                            'label'=>'Supplier',
                        ],

                        ['attribute' => 'product_type',
                            'label'=>'Product Type',
                        ],

                        ['attribute' => 'brand',
                            'label'=>'Brand',
                            'filter' => $brandArray,
                            'value' => function($data) {
                                if ($data->brand == null)
                                    return "";
                                return $data->brand;
                            },
                        ],

                        ['attribute' => 'quantity',
                            'label'=>'Available',
                            'filter' => false,
                            'value' => function($data){
                                return $data->quantity.' in '.$data->variant_num.' Variants';
                            },
                        ],

                        ['attribute' => 'product_status',
                            'label'=>'Status',
                            'filter' => false,
                        ],

                        ['attribute' => 'updated_at',
                            'label'=>'Last Updated',
                            'value' => function($data) {
                                return date('M d Y', strtotime($data->updated_at));
                            },
                            'filter' => \yii\jui\DatePicker::widget([
                                'language' => 'en',
                                'dateFormat' => 'MM/dd/yyyy',
                                'model'=>$searchModel,
                                'attribute'=>'updated_at',
                            ]),
                            'options' => [
                                'style' => 'width:10%'
                            ],
                        ],

                        [
                            'class' => \yii\grid\ActionColumn::className(),
                            'buttons' => [
                                'sync' => function($url, $model, $key) {
                                    return Html::a('Sync', ['inventorysync', 'id' => $model->id], ['class' => 'btn btn-default btn-xs sync', 'data-id' => $model->id]);
                                },
                                'view' => function($url, $model, $key) {
                                    return Html::a('View', ['inventoryview', 'id' => $model->id], ['class' => 'btn btn-default btn-xs']);
                                },
                                'delete' => function($url, $model, $key) {
                                    return Html::a('Delete', ['inventorydelete', 'id' => $model->id], ['data-confirm' => 'Are you sure?', 'class' => 'btn btn-danger btn-xs']);
                                },
                            ],
                            'template' => '
                                {sync} {view} {delete}
                            ',
                            'options' => [
                                'style' => 'width: 5%;',
                            ],
                            'contentOptions' => [
                                'class' => 'text-center'
                            ]
                        ],

                    ],
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
