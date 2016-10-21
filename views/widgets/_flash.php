<?php if( Yii::$app->session->hasFlash('success')) { ?>
    <div class="alert alert-success">
        <button data-dismiss="alert" class="close">x</button>
        <?php echo Yii::$app->session->getFlash('success'); ?>
    </div>
<?php } ?>
<?php if( Yii::$app->session->hasFlash('error')) { ?>
    <div class="alert alert-error">
        <button data-dismiss="alert" class="close">x</button>
        <?php echo Yii::$app->session->getFlash('error'); ?>
    </div>
<?php } ?>