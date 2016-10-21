<?php
/**
 * @var $this yii\web\View
 */

use yii\helpers\Html;

\yii\helpers\VarDumper::dump($params, 10, true);

$action = '';
?>

<div class="coupon-update">
    <div id="content-header">
        <h1>Parameters</h1>
    </div>
    <?php if(Yii::$app->session->hasFlash('success')) { ?>
        <div class="alert alert-success">
            <button data-dismiss="alert" class="close">x</button>
            <strong>Success!</strong><?php echo Yii::$app->session->getFlash('success'); ?>
        </div>
    <?php } ?>
    <?php if(Yii::$app->session->hasFlash('fail')) { ?>
        <div class="alert alert-error">
            <button data-dismiss="alert" class="close">x</button>
            <strong>Error!</strong> <?php echo Yii::$app->session->getFlash('fail'); ?>
        </div>
    <?php } ?>


    <div class="row">
        <div class="col-md-12">
            <div class="widget-box">
                <div class="widget-title">
                <span class="icon">
                    <i class="fa fa-ticket"></i>
                </span>
                    <h5>Coupon Info</h5>
                </div>
                <div id="params-form-wrapper" class="widget-content nopadding">
                    <form id="params-form" class="form-horizontal" method="post" action="<?php echo $action ?>">
                        <?php
                        foreach($params as $label=>$value){
                            if(is_array($value)) {?>
                                <fieldset>
                                    <h5><?php echo $label; ?></h5>
                                <?php
                                foreach($value as $sLabel=>$sValue){
                                    if(!is_array($sValue)) {?>
                                        <div class="form-group">
                                            <label class="col-sm-3 col-md-3 col-lg-2 control-label"><?php echo $sLabel; ?></label>
                                            <div class="col-sm-9 col-md-9 col-lg-10">
                                                <input type="text" class="form-control" name="" value="<?php echo $sValue; ?>" />
                                            </div>
                                        </div>
                                    <?php }
                                }
                                ?>
                                </fieldset>
                            <?php }else{ ?>
                                <div class="form-group">
                                    <label class="col-sm-3 col-md-3 col-lg-2 control-label"><?php echo $label; ?></label>
                                    <div class="col-sm-9 col-md-9 col-lg-10">
                                        <input type="text" class="form-control" name="" value="<?php echo $value; ?>" />
                                    </div>
                                </div>
                            <?php }
                        }
                        ?>

                        <div class="form-actions">
                            <button id="submit_coupon"class="btn btn-success btn-sm" type="submit">Save</button>&nbsp;<?=Html::a('Cancel', ['index'], ['class' => 'btn btn-danger btn-sm'])?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>