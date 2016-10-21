<?php 
	use yii\helpers\Html;
	use yii\grid\GridView;
	use app\components\Helper;
	use yii\widgets\LinkPager;
	$this->title = "Create Tradegecko Order";
?>

<div id="content-header">
    <h1><?php echo $this->title; ?></h1>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!--Notifications-->
            <?php if(isset($session_msg) && is_array($session_msg)) {
                if(isset($session_msg['error']) && !$session_msg['error'] && isset($session_msg['success_msg'])) { ?>
                    <div style="display:block" class="alert alert-success">
                        <button class="close" data-dismiss="alert">x</button>
                        <strong>Success!</strong><br><?php echo $session_msg['success_msg']; ?>
                    </div>
                <?php } else if(isset($session_msg['error']) && $session_msg['error'] && isset($session_msg['error_msg'])) { ?>
                    <div style="display:block" class="alert alert-danger">
                        <button class="close" data-dismiss="alert">x</button>
                        <strong>Error!</strong><br>
                        <?php echo implode('<br />',$session_msg['error_msg']); ?>
                    </div>
                <?php } ?>
            <?php } ?>

            <div class="widget-box collapsible">
                <div class="widget-title">
                    <a href="#collapseTwo" data-toggle="collapse">
                        <span class="icon"><i class="fa fa-signal"></i></span>
                        <h5>Create Order At Tradegecko by Import Csv </h5>
                    </a>
                </div>
                <div class="collapse in" id="collapseTwo">
                    <div class="widget-content nopadding">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th>Browse the Order Csv</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <?php $form = \yii\bootstrap\ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                                        <?=$form->field($csvModel, 'csvFile')->fileInput()?>
                                        <?=Html::submitButton('Create order', ['class' => 'btn btn-success'])?>
                                    <?php $form->end(); ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon"><i class="fa fa-signal"></i></span>
                    <h5>All Order's</h5>
                </div>
                <div class="widget-content">
                    Order Log Grid
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            'id',
                            'customer_id',
                            'tradegecko_order_id',
                            'created_at',
                            'import_csv_title',
                            ['class' => 'yii\grid\ActionColumn'],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>