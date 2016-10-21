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
use yii\widgets\Pjax;

$this->title = 'Create a Liteview Order';
$createAction = Yii::$app->urlManager->createUrl('services/liteview/create');
$editableAction = Yii::$app->urlManager->createUrl('services/liteview/editable');
$updateAction = Yii::$app->urlManager->createUrl('services/liteview/update');
$submitAction = Yii::$app->urlManager->createUrl('services/liteview/ordersubmit');
$resubmitAction = Yii::$app->urlManager->createUrl('services/liteview/orderresubmit');

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
            </div>

            <div class="widget-content nopadding">
                <?php
                Pjax::begin();
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
                            'class' => \yii\grid\CheckboxColumn::className(),
                            'contentOptions' => [
                                'class' => 'text-center'
                            ],
                        ],

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

                    ],
                ]);
                Pjax::end();
                ?>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <button type="submit" class="btn btn-large btn-success" id="createOrder">Create a Liteview Order</button>
    </div>
</div>

<div class="row">
    <div class="col-xs-12" id="detailArea">
    </div>
</div>

<div id="message" style="display: none;">
    <h4><i class="fa fa-spinner fa-pulse"></i> Creating Liteview Order ...</h4>
</div><!-- /.modal -->
<div id="message1" style="display: none;">
    <h4><i class="fa fa-spinner fa-pulse"></i> Submitting to Liteview now ...</h4>
</div><!-- /.modal -->
<div id="message2" style="display: none;">
    <h4><i class="fa fa-spinner fa-pulse"></i> Resubmitting to Liteview now ...</h4>
</div><!-- /.modal -->

<?php
$search_script = <<<JS
    $(function(){
        $('#createOrder').click(function(){
            var params = [];
            var obj = $('[name="selection[]"]:checked');
            if (obj.length == 0)
            {
                alert('You should choose at least one tradegecko order.');
                return;
            }

            for (var i = 0; i < obj.length; i++)
                params[i] = $(obj[i]).val();
            $.blockUI({ message: $("#message") });
            $.ajax({
                url: createAction,
                type: 'GET',
                data: {params: params},
                success: function(data) {},
                complete: function(data) {
                    console.log(data);
                    $('#createOrder').hide();
                    for (var i = 0; i < obj.length; i++)
                        $(obj[i]).attr('disabled', 'true');
                    $('#detailArea').html(data.responseText);
                    $.unblockUI();
                }
            });
        });
    });

    function updateOrder()
    {
        $('.detailWidget').css('opacity', '0.3');
        $('.detailWidget').css('pointer-events', 'none');
        $( ".tab-content" ).wrap( "<form id='editableForm'></form>" );
        var params = $('#editableForm').serialize();
        var id = $('[name="liteview_order_id"]').val();
        $.ajax({
            url: updateAction,
            type: 'GET',
            data: params + "&id=" + id,
            success: function(data) {},
            complete: function(data) {
                console.log(data);
                $('#createOrder').removeAttr('disabled');
                $('#detailArea').html(data.responseText);
            }
        });
    }

    function editOrder()
    {
        $('.detailWidget').css('opacity', '0.3');
        $('.detailWidget').css('pointer-events', 'none');
        var id = $('[name="liteview_order_id"]').val();
        $.ajax({
            url: editableAction,
            type: 'GET',
            data: {id: id},
            success: function(data) {},
            complete: function(data) {
                console.log(data);
                $('#createOrder').attr('disabled', 'true');
                $('#detailArea').html(data.responseText);
            }
        });
    }

    function submitOrder()
    {
        $.blockUI({ message: $("#message1") });
        var id = $('[name="liteview_order_id"]').val();
        $.ajax({
            url: submitAction,
            type: 'GET',
            data: {id: id},
            success: function(data) {},
            complete: function(data) {
                var response = data.responseText;
                var ret = jQuery.parseJSON(response);
                if (ret.code == '0')
                    alert(ret.error);
                else
                {
                    console.log(data);
                    $('#createOrder').attr('disabled', 'true');
                    $('#editOrder').attr('disabled', 'true');
                    $('#submitOrder').attr('disabled', 'true');
                }
                $.unblockUI();
            }
        });
    }

    function resubmitOrder()
    {
        $.blockUI({ message: $("#message2") });
        var id = $('[name="liteview_order_id"]').val();
        $.ajax({
            url: resubmitAction,
            type: 'GET',
            data: {id: id},
            success: function(data) {},
            complete: function(data) {
                var response = data.responseText;
                var ret = jQuery.parseJSON(response);
                if (ret.code == '0')
                    alert(ret.error);
                else
                {
                    console.log(data);
                    $('#createOrder').attr('disabled', 'true');
                    $('#editOrder').attr('disabled', 'true');
                    $('#resubmitOrder').attr('disabled', 'true');
                }
                $.unblockUI();
            }
        });
    }
JS;
$search_script = str_replace('url: createAction', 'url: "'.$createAction.'"', $search_script);
$search_script = str_replace('url: editableAction', 'url: "'.$editableAction.'"', $search_script);
$search_script = str_replace('url: updateAction', 'url: "'.$updateAction.'"', $search_script);
$search_script = str_replace('url: submitAction', 'url: "'.$submitAction.'"', $search_script);
$search_script = str_replace('url: resubmitAction', 'url: "'.$resubmitAction.'"', $search_script);
$this->registerJs($search_script, \yii\web\View::POS_END);
?>