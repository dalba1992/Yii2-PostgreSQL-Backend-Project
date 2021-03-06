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

$this->title = 'Registered Users';
?>
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
                <h5><?php echo $this->title; ?></h5>

                <div class="pull-right cuopon_button" style="padding: 4px;">
                    <?= Html::a('Create New User', ['create'], ['class' => 'btn btn-success btn-xs', 'style' => 'color: #fff;']) ?>
                </div>

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

                        ['attribute' => 'username',
                            'label'=>'UserName',
                        ],

                        ['attribute' => 'firstName',
                            'label'=>'First Name',
                        ],

                        ['attribute' => 'lastName',
                            'label'=>'Last Name',
                        ],

                        ['attribute' => 'email',
                            'label'=>'Email',
                        ],

                        ['attribute' => 'regDate',
                            'label'=>'Registration Date',
                            'value' => function($data){
                                return gmdate("m/d/Y H:i:s", $data->regDate);
                            },
                            'filter' => false,
                            'format' => 'raw',
                            /*'filter' => \yii\jui\DatePicker::widget([
                                'language' => 'en',
                                'dateFormat' => 'MM/dd/yyyy',
                                'model'=>$searchModel,
                                'attribute'=>'regDate',
                            ]),*/
                        ],

                        [
                            'format' => 'raw',
                            'label' => 'Manage',
                            'value' => function ($data) {
                                $html = '<div class="btn-group-vertical" role="group">';
                                $html .= '<a class="btn btn-xs btn-default" href="'.Yii::$app->getHomeUrl().'user-management/update/?id='.$data->id.'"><i class="fa fa-edit"></i> Edit</a> ';
                                $html .= '<a class="btn btn-xs btn-default" href="'.Yii::$app->getHomeUrl().'user-management/assign/?id='.$data->id.'"><i class="fa fa-users"></i> Roles</a> ';
                                $html .= '<a class="btn btn-xs btn-danger" href="'.Yii::$app->getHomeUrl().'user-management/delete/?id='.$data->id.'"><i class="fa fa-cross"></i> Delete</a> ';
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