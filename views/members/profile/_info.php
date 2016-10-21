<?php
/**
 * @var CustomerEntity $model
 */
?>
<div class="row" id="info-section">
    <div class="widget-box">
        <?php echo Yii::$app->controller->renderPartial('profile/_tabs', ['model'=>$model]); ?>
    </div>
</div>