<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CouponSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Coupons';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div id="content-header">
	<h1>All Coupons</h1>
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
	<a href="javascript:;">Coupons</a>
	<a href="<?php echo Yii::$app->homeUrl.'/coupon/index'; ?>" class="current">All Coupons</a>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fa fa-ticket"></i>
                </span>
                <h5 class="cuopon_info">Coupons Information</h5>
                <div class="pull-right cuopon_button" style="padding: 4px;">
                    <?= Html::a('Create New Coupon', ['create'], ['class' => 'btn btn-success btn-xs', 'style' => 'color: #fff;']) ?>
                </div>
                <div style="clear:both"></div>
            </div>
            <div class="widget-content nopadding" id="coupon_grid">
                <?= GridView::widget([
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
                        ['class' => 'yii\grid\SerialColumn'],

                        //'id',
                        'coupon_name',
                        'coupon_code:ntext',
                        //'discount_type',
                        ['attribute' => 'discount_type',
                          'label'=>'discount type',
                          'filter' => [ '1' => 'Fixed Amount Off', '2' => 'Percent Off',],
                           'value' => function($data){
                                        //return Html::a($data->discount_type);
                                        if($data->discount_type == '1'){
                                            return "Fixed Amount Off";
                                        }else if($data->discount_type == '2'){
                                            return "Percent Off";
                                        }
                                     }
                        ],
                        //'amount_off',
                        ['attribute' => 'amount_off',
                          'label'=>'Amount Off',
                           'value' => function($data){
                                        //return Html::a($data->discount_type);
                                        if($data->discount_type == '1'){
                                            return "$".$data->amount_off;
                                        }else if($data->discount_type == '2'){
                                            return $data->amount_off."%";
                                        }
                                     },
                            'options' => [
                                'class' => 'test'
                            ]
                        ],
                        //'message',
                        ['attribute' => 'duration',
                          'label'=>'duration',
                          'filter' => [ '1' => 'Once', '2' => 'Forever', '3' => 'Repeating'],
                          'value' => function($data){
                                        if($data->duration == '1'){
                                            return "Once";
                                        }else if($data->duration == '2'){
                                            return "Forever";
                                        }else if($data->duration == '3'){
                                            return "Repeating";
                                        }
                                     }
                        ],
                        //'duration_time',
                        ['attribute' => 'duration_time',
                          'label'=>'Duration Time',
                          'filter' => true,
                          'value' => function($data){
                                        if($data->duration == '3'){
                                            return $data->duration_time;
                                        }else {
                                            return "--";
                                        }
                                     }
                        ],
                        ['attribute' => 'status',
                          'label'=>'Status',
                          'filter' => [ '1' => 'Active', '0' => 'Inactive',],
                          'value' => function($data){
                                        if($data->status == '1'){
                                            return "Active";
                                        }else{
                                            if($data->status == '0'){
                                                return "Inactive";
                                            }
                                        }
                                     }
                        ],
                        //'is_apply',
                        ['attribute' => 'is_apply',
                          'label'=>'Apply Coupon',
                          'filter' => [ '0' => 'unused', '1' => 'used',],
                          'value' => function($data){
                                        if($data->is_apply == '0' && $data->use_count == '0'){
                                            return "Unused";
                                        }else{
                                                return "Used (".$data->use_count.")";
                                        }
                                    }
                        ],
                        'created_at',
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' =>'{update}{delete}',
                        ],
                    ],
                ]); ?>

            </div>
        </div>
    </div>
</div>
</div>
<style type="text/css">
	#coupon_grid select{
		width:130px;
	}
</style>

