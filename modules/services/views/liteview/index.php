<?php

/**
 * @var \yii\web\View $this
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\services\models\TradegeckoInvoiceSearch $searchModel
 */

use yii\helpers\Html;
use yii\grid\GridView;
use app\components\Helper;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;

$this->title = 'Liteview Orders';
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
        <div class="buttons">
            <a class="btn btn-large btn-success" id="submitOrder" href="<?php echo Yii::$app->urlManager->createUrl('/services/liteview/create'); ?>">Create a Liteview Order</a>
        </div>
        <div class="widget-box">

            <div class="widget-title">
                <span class="icon">
                    <i class="fa fa-th"></i>
                </span>
                <h5>Liteview Orders</h5>

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
                    'emptyText' => 'No Liteview Orders Found',
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
                            'attribute' => 'ifs_order_number',
                            'label'=>'IFS Order Number',
                            'value' => function($data) {
                                if ($data->ifs_order_number != "")
                                    return $data->ifs_order_number;
                            },
                        ],

                        [
                            'attribute' => 'liteview_order_status',
                            'format' => 'raw',
                            'label'=>'Status',
                            'contentOptions' => [
                                'class' => 'text-center'
                            ],
                            'value' => function($data) {
                                if ($data->liteview_order_status == 'Active')
                                    return '<span class="label label-success" style="font-size:11px">'.$data->liteview_order_status.'</span>';
                                else if ($data->liteview_order_status == 'Submitted')
                                    return '<span class="label label-primary" style="font-size:11px">'.$data->liteview_order_status.'</span>';
                                else if ($data->liteview_order_status == 'Cancelled')
                                    return '<span class="label label-warning" style="font-size:11px">'.$data->liteview_order_status.'</span>';
                                else if ($data->liteview_order_status == 'Re-Submitted')
                                    return '<span class="label label-info" style="font-size:11px">'.$data->liteview_order_status.'</span>';
                            }
                        ],

                        [
                            'attribute' => 'submitted_at',
                            'label'=>'Submitted At',
                            'value' => function($data) {
                                if ($data->submitted_at != "")
                                    return date('Y-m-d H:i:s', strtotime($data->submitted_at));
                            },
                        ],

                        [
                            'attribute' => 'cancelled_at',
                            'label'=>'Cancelled At',
                            'value' => function($data) {
                                if ($data->cancelled_at != "")
                                    return date('Y-m-d H:i:s', strtotime($data->cancelled_at));
                            },
                        ],

                        [
                            'attribute' => 'created_at',
                            'label'=>'Created',
                            'value' => function($data) {
                                return date('Y-m-d', strtotime($data->updated_at));
                            },
                        ],

                        [
                            'attribute' => 'updated_at',
                            'label'=>'Last Updated',
                            'value' => function($data) {
                                return date('Y-m-d', strtotime($data->updated_at));
                            },
                        ],

                        [
                            'class' => \yii\grid\ActionColumn::className(),
                            'buttons' => [
                                'submit' => function($url, $model, $key) {
                                    if ($model->liteview_order_status == "Submitted" || $model->liteview_order_status == "Re-Submitted")
                                        return Html::a('Cancel', ['ordercancel', 'id' => $model->id], ['class' => 'btn btn-warning btn-xs cancel']) . ' ' . Html::a('Re-submit', ['orderresubmit', 'id' => $model->id], ['class' => 'btn btn-info btn-xs resubmit']);
                                    else if ($model->liteview_order_status == "Active")
                                        return Html::a('Submit', ['ordersubmit', 'id' => $model->id], ['class' => 'btn btn-primary btn-xs submit']);
                                },
                                'edit' => function($url, $model, $key) {
                                    if ($model->liteview_order_status == "Cancelled")
                                        return Html::a('View', ['orderedit', 'id' => $model->id], ['class' => 'btn btn-default btn-xs']);
                                    else
                                        return Html::a('Edit', ['orderedit', 'id' => $model->id], ['class' => 'btn btn-default btn-xs']);
                                },
                                'delete' => function($url, $model, $key) {
                                    return Html::a('Delete', ['orderdelete', 'id' => $model->id], ['data-confirm' => 'Are you sure?', 'class' => 'btn btn-danger btn-xs']);
                                },
                            ],
                            'template' => '
                                {submit} {edit} {delete}
                            ',
                            'options' => [
                                'style' => 'width: 20%;',
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
    <h4><i class="fa fa-spinner fa-pulse"></i> Submitting an order to Liteview...</h4>
</div><!-- /.modal -->
<div id="message1" style="display: none;">
    <h4><i class="fa fa-spinner fa-pulse"></i> Resubmitting an order to Liteview...</h4>
</div><!-- /.modal -->
<div id="message2" style="display: none;">
    <h4><i class="fa fa-spinner fa-pulse"></i> Cancelling Liteview order...</h4>
</div><!-- /.modal -->

<?php
$search_script = <<<JS
    $(function(){
        $('.submit').click(function(e){
            e.preventDefault();
            var href = $(this).attr('href');
            $.blockUI({ message: $("#message") });
            $.ajax({
                url: href,
                type: 'POST',
                success: function(data) {},
                complete: function(data) {
                    var response = data.responseText;
                    var ret = jQuery.parseJSON(response);
                    if (ret.code == '0')
                        alert(ret.error);
                    else
                        window.location.href = '';
                    $.unblockUI();
                }
            });
        });

        $('.resubmit').click(function(e){
            e.preventDefault();
            var href = $(this).attr('href');
            $.blockUI({ message: $("#message1") });
            $.ajax({
                url: href,
                type: 'POST',
                success: function(data) {},
                complete: function(data) {
                    var response = data.responseText;
                    var ret = jQuery.parseJSON(response);
                    if (ret.code == '0')
                        alert(ret.error);
                    else
                        window.location.href = '';
                    $.unblockUI();
                }
            });
        });

        $('.cancel').click(function(e){
            e.preventDefault();
            var href = $(this).attr('href');
            $.blockUI({ message: $("#message2") });
            $.ajax({
                url: href,
                type: 'POST',
                success: function(data) {},
                complete: function(data) {
                    var response = data.responseText;
                    var ret = jQuery.parseJSON(response);
                    if (ret.code == '0')
                        alert(ret.error);
                    else
                        window.location.href = '';
                    $.unblockUI();
                }
            });
        });
    });
JS;
$this->registerJs($search_script, \yii\web\View::POS_END);
?>
