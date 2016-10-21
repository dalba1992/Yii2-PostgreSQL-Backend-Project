<?php

/**
 * @var \yii\web\View $this
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var app\models\CustomerEntity $searchModel
 */

use yii\helpers\Html;
use yii\grid\GridView;
use app\components\Helper;
use yii\widgets\LinkPager;

$this->title = 'Stripe Customers';
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
                    <i class="fa fa-users"></i>
                </span>
                <h5>Stripe Customers</h5>

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
                        
                        ['attribute' => 'customerId',
                            'label'=>'Customer ID',
                        ],

                        ['attribute' => 'created',
                            'label'=>'Created Date',
                            'value' => function($data){
                                return gmdate("m/d/Y H:i:s", $data->created);
                            },
                            'format' => 'html',
                            'filter' => \yii\jui\DatePicker::widget([
                                'language' => 'en',
                                'dateFormat' => 'MM/dd/yyyy',
                                'model'=>$searchModel,
                                'attribute'=>'created',
                            ]),
                        ],

                        ['attribute' => 'email',
                            'label'=>'Email',
                        ],

                        ['attribute' => 'account_balance',
                            'label'=>'Balance',
                            'value' => function($data){
                                return number_format(floatval($data->account_balance) / 100, 2);
                            },
                            'filter' => false,
                        ],

                        ['attribute' => 'currency',
                            'label'=>'Currency',
                            'filter' => false,
                        ],

                        ['attribute' => 'default_card',
                            'label'=>'Default Card',
                        ],

                        ['attribute' => 'default_source',
                            'label'=>'Default Source',
                        ],

                        [
                            'format' => 'raw',
                            'label' => '',
                            'value' => function ($data) {
                                $html = '<div class="btn-group-vertical" role="group">';
                                $html = '<a class="btn btn-xs btn-default sync" href="javascript:void(0);" data-id="'.$data->customerId.'"><i class="fa fa-refresh"></i> Sync</a>';
                                $html .= '<a class="btn btn-xs btn-default" href="'.Yii::$app->getHomeUrl().'services/stripe/view/?customerId='.$data->customerId.'"><i class="fa fa-users"></i> View</a> ';
                                $html .= '</div>';
                                return $html;
                            },
                        ],
                    ],
                ]);
                ?>
            </div>

        </div>
    </div>
</div>

<div id="message" style="display: none;">
    <h4><i class="fa fa-spinner fa-pulse"></i> Syncing customer data...</h4>
</div><!-- /.modal -->

<?php
$search_script = <<<JS
    $(function(){
        $('.sync').click(function(){
            var customerId = $(this).attr('data-id');
            var i = $(this);
            $.blockUI({ message: $("#message") });
            $.ajax({
                url: '/services/stripe/sync',
                type: 'POST',
                data: {customerId: customerId},
                success: function(data) {},
                complete: function(data) {
                    var obj = jQuery.parseJSON(data['responseText']);
                    console.log(obj);
                    var tr = i.parent().parent();
                    $(tr).find('td:nth-child(3)').text(obj['email']);
                    $(tr).find('td:nth-child(4)').text((parseFloat(obj['account_balance']) / 100).toFixed(2));
                    $(tr).find('td:nth-child(5)').text(obj['currency']);
                    $(tr).find('td:nth-child(6)').text(obj['default_card']);
                    $(tr).find('td:nth-child(7)').text(obj['default_source']);
                    $.unblockUI();
                }
            });
        });
    });
JS;
$this->registerJs($search_script, \yii\web\View::POS_END);
?>