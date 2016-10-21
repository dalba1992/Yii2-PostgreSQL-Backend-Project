<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Customer Status Logs';
?>

<div class="row">
	<div class="col-md-12">
		<div class="widget-box">
			<div class="widget-title">
                <span class="icon">
                    <i class="fa fa-users"></i>
                </span>
				<h5><?= Html::encode($this->title) ?></h5>
                <div class="pull-right" style="padding: 8px 8px 0 8px;">
                    <form id="page-changer" name="form1" method="GET" action="">
                        <div class="filter filter-page-changer">
                            <label>Show
                             <select  class="select2-offscreen" onchange="<?php if(isset($_GET['page'])){?>window.location.href='?per-page='+this.options[this.selectedIndex].text+'&page=<?php echo $_GET['page']; ?>'<?php }else { ?>window.location.href='?per-page='+this.options[this.selectedIndex].text<?php } ?>">
                                <option value="1" <?php if(isset($_GET['per-page']) && ($_GET['per-page']==10)) {?> selected="selected" <?php } ?>>5</option>
                                <option value="1" <?php if(isset($_GET['per-page']) && ($_GET['per-page']==10)) {?> selected="selected" <?php } ?>>10</option>
                                <option value="2" <?php if(isset($_GET['per-page']) && ($_GET['per-page']==20)) {?> selected="selected" <?php } ?>>20</option>
                                <option value="3" <?php if(isset($_GET['per-page']) && ($_GET['per-page']==30)) {?> selected="selected" <?php } ?>>30</option>
                                <option value="4" <?php if(isset($_GET['per-page']) && ($_GET['per-page']==50)) {?> selected="selected" <?php } ?>>50</option>
                                <option value="4" <?php if(isset($_GET['per-page']) && ($_GET['per-page']==100)) {?> selected="selected" <?php } ?>>100</option>
                             </select>&nbsp;entries</label>
                        </div>
                    </form>
                </div>
			</div>
			<div class="widget-content nopadding">
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
                        'customer_id',
                        'status',
                        'date',

                        //['class' => 'yii\grid\ActionColumn'],
                    ],
					]); ?>
				</div>
            </div>
		</div>
	</div>
</div>
