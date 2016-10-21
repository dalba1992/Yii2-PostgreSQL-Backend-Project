<?php
/**
 * @var $this \yii\web\View
 */

use yii\helpers\Html;
?>

<div id="user-nav">
    <ul class="btn-group">
        <li class="btn disabled"><a title="" href="#"><i class="fa fa-user"></i> <span class="text"><?php echo Yii::$app->user->identity->username?></span></a></li>
        <li class="btn">
            <?php echo Html::a('<i class="fa fa-share"></i>'.'<span class="text">Logout</span>', ['site/logout'], [
                'data' => [
                    'method' => 'post'
                ]
            ])?>
        </li>
    </ul>
</div>
