<?php

/**
 * @var \yii\web\View $this
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\services\models\TradegeckoOrderSearch $searchModel
 */

use yii\helpers\Html;
use yii\grid\GridView;
use app\components\Helper;
use yii\widgets\LinkPager;

$this->title = 'Tradegecko Order';
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
                <h5>Tradegecko Orders</h5>

                <div class="pull-right" style="padding: 8px 8px 0 8px;">
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
                </div>
            </div>

            <div class="widget-content nopadding">
                <?php
                echo GridView::widget([
                    'dataProvider' => $dataProvider,
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
                            'attribute' => 'order_number',
                            'label'=>'Order #',
                        ],

                        [
                            'label'=>'Company Name',
                            'value' => function($data) {
                                return $data['tradegeckoCompany']['company_name'];
                            }
                        ],

                        [
                            'attribute' => 'order_status',
                            'label'=>'Status',
                            'format' => 'raw',
                            'contentOptions' => [
                                'class' => 'text-center'
                            ],
                            'value' => function($data) {
                                $html = '<span class="label label-status status-'.$data->order_status.'">'.$data->order_status.'</span>';
                                return $html;
                            }
                        ],

                        [
                            'label'=>'Invoiced',
                            'format' => 'raw',
                            'contentOptions' => [
                                'class' => 'text-center'
                            ],
                            'options' => [
                                'style' => 'width:5%'
                            ],
                            'value' => function($data) {
                                $html = '';
                                if ($data->invoice_status == 'invoiced')
                                    $html = '<i class="fa fa-circle"></i>';
                                else
                                    $html = '<i class="fa fa-circle-o"></i>';
                                return $html;
                            }
                        ],

                        [
                            'label'=>'Packed',
                            'format' => 'raw',
                            'contentOptions' => [
                                'class' => 'text-center'
                            ],
                            'options' => [
                                'style' => 'width:5%'
                            ],
                            'value' => function($data) {
                                $html = '';
                                if ($data->packed_status == 'packed')
                                    $html = '<i class="fa fa-circle"></i>';
                                else
                                    $html = '<i class="fa fa-circle-o"></i>';
                                return $html;
                            }
                        ],

                        [
                            'label'=>'Fulfilled',
                            'format' => 'raw',
                            'contentOptions' => [
                                'class' => 'text-center'
                            ],
                            'options' => [
                                'style' => 'width:5%'
                            ],
                            'value' => function($data) {
                                $html = '';
                                if ($data->fulfillment_status == 'shiped')
                                    $html = '<i class="fa fa-circle"></i>';
                                else
                                    $html = '<i class="fa fa-circle-o"></i>';
                                return $html;
                            }
                        ],

                        [
                            'label'=>'Paid',
                            'format' => 'raw',
                            'contentOptions' => [
                                'class' => 'text-center'
                            ],
                            'options' => [
                                'style' => 'width:5%'
                            ],
                            'value' => function($data) {
                                $html = '';
                                if ($data->payment_status == 'paid')
                                    $html = '<i class="fa fa-circle"></i>';
                                else
                                    $html = '<i class="fa fa-circle-o"></i>';
                                return $html;
                            }
                        ],

                        [
                            'attribute' => 'total',
                            'label'=>'Total',
                            'value' => function($data) {
                                return $data['tradegeckoCurrency']['symbol'].number_format($data->total, 2);
                            }
                        ],

                        ['attribute' => 'created_at',
                            'label'=>'Created',
                            'value' => function($data) {
                                return date('M d Y', strtotime($data->created_at));
                            },
                        ],

                        ['attribute' => 'updated_at',
                            'label'=>'Last Updated',
                            'value' => function($data) {
                                return date('M d Y', strtotime($data->updated_at));
                            },
                        ],

                        [
                            'class' => \yii\grid\ActionColumn::className(),
                            'buttons' => [
                                'view' => function($url, $model, $key) {
                                    return Html::a('View', ['orderview', 'id' => $model->id], ['class' => 'btn btn-default btn-xs']);
                                },
                                'delete' => function($url, $model, $key) {
                                    return Html::a('Delete', ['orderdelete', 'id' => $model->id], ['data-confirm' => 'Are you sure?', 'class' => 'btn btn-danger btn-xs']);
                                },
                            ],
                            'template' => '
                                {view} {delete}
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

<div id="message" style="display: none;">
    <h4><i class="fa fa-spinner fa-pulse"></i> Syncing Order Data...</h4>
</div><!-- /.modal -->

<?php
$search_script = <<<JS
    $(function(){
        $('#syncTradeGecko').click(function(){
            $.blockUI({ message: $("#message") });
            $.ajax({
                url: '/services/tradegecko/syncorder',
                type: 'POST',
                success: function(data) {},
                complete: function(data) {
                    window.location.href = '';
                }
            });
        });
    });
JS;
$this->registerJs($search_script, \yii\web\View::POS_END);
?>