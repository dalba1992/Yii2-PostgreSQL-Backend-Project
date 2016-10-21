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

$this->title = ucwords(Yii::$app->request->get('status').' Members');
?>
<div id="content-header">
    <h1><?php echo $this->title; ?></h1>
</div>
<?php if (Yii::$app->session->hasFlash('attribute')){ ?>

    <div class="alert alert-success">
        <?php echo Yii::$app->session->getFlash('attribute'); ?>
    </div>
<?php } ?>
<div id="breadcrumb">
    <a href="/" title="" class="tip-bottom" data-original-title="Go to Home"><i class="fa fa-home"></i> Home</a>
    <a href="<?php echo Yii::$app->urlManager->createUrl('members/list/all'); ?>">Manage Members</a>
    <a class="current"><?php echo $this->title; ?></a>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="widget-box">

            <div class="widget-title">
                <span class="icon">
                    <i class="fa fa-users"></i>
                </span>
                <h5>Members</h5>

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
                        [
                            'format' => 'raw',
                            'attribute' => 'id',
                            'label'=>'Id',
                            'filterOptions'=>['style'=>'width: 118px;'],
                            'filterInputOptions'=>['maxlength'=>9, 'class'=>'form-control'],
                            'contentOptions'=>['class'=>'text-center']
                        ],

                        ['attribute' => 'customerEntityInfos.email',
                            'format' => 'raw',
                            'value' => function($data){
								if($data->customerEntityInfos)
									return '<strong>'.$data->customerEntityInfos->email.'</strong>';
								else
									return '(not set)';
                            },
                            'contentOptions'=>['class'=>'text-center']
                        ],

                        ['attribute' => 'customerEntityInfos.first_name',
                            'label'=>'First Name',

                        ],

                        ['attribute' =>  'customerEntityInfos.last_name',
                            'label'=>'Last Name',
                        ],

                        [
                            'format' => 'raw',
                            'attribute' => 'customerSubscription.stripe_customer_id',
                            'label' => 'Stripe Id',
                            'contentOptions'=>['class'=>'text-center'],

                            'value' => function(\app\models\CustomerEntity $data){
                                if($data->customerSubscription)
                                    return $data->customerSubscription->stripe_customer_id;
                                else
                                    return '(not set)';
                            }
                        ],

                        [
                            'format' => 'raw',
                            'attribute' => 'customerEntityAddresses.zipcode',
                            'label'=>'Zip Code',
                            'contentOptions'=>['class'=>'text-center'],

                            'value' => function($data){
                                return Yii::$app->tradeGeckoHelper->zipcode($data->id);
                            }
                        ],

                        [
                            'format' => 'raw',
                            'attribute' => 'created_at',
                            'label'=>'Created',
                            'value' => function($data){
                                $timestamp =  strtotime($data->created_at);
                                return date('F d, Y', $timestamp);
                            },
                            'filter' => false,
                            'contentOptions'=>['class'=>'text-center']
                        ],

                        [
                            'format' => 'raw',
                            'label'=>'Orders',
                            'contentOptions'=>['class'=>'text-center'],

                            'value' => function (\app\models\CustomerEntity $data) {
                                return $data->getOrders()->count();
                            },
                        ],

                        [
                            'format' => 'raw',
                            'attribute' => 'is_active',
                            'label'=>'Status',
                            'contentOptions'=>['class'=>'text-center'],
                            'filter' =>false,

                            'value'=>function ($data) {
                                return ($data->is_active==\app\models\CustomerEntity::STATUS_ACTIVE)? '<span class="label label-success">Active</span>':'<span class="label label-inverse">Inactive</span>';
                            },
                        ],

                        [
                            'format' => 'raw',
                            'attribute' => 'customerEntityStatuses.status',
                            'label'=>'Subscription',
                            'contentOptions'=>['class'=>'text-center'],

                            'value' => function($data){
                                return Yii::$app->message->status($data->id);
                            }
                        ],

                        [
                            'format' => 'raw',
                            'label' => ' ',
                            'value' => function ($data) {
                                return Html::a('Profile', ['profile', 'member'=>$data->id], ['class'=>'btn btn-xs btn-info']) . "&nbsp;" . Html::a('Create order', ['create-order', 'member'=>$data->id], ['class'=>'btn btn-xs btn-default']);
                            },
                        ],
                    ],
                ]);
                ?>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    function editmember(memberid){
        window.location='<?php echo Yii::$app->request->BaseUrl."/index.php/members/profile/?member="?>' +memberid;
    }
</script>