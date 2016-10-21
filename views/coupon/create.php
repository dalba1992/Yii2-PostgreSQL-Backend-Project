<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Coupon */

$this->title = 'Create Coupon';
//$this->params['breadcrumbs'][] = ['label' => 'Coupons', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coupon-create">
	<?php if(!isset($_GET['id'])){ ?>
			<div id="content-header">
				<h1>Create New Coupons</h1>
			</div>
	<?php } ?>
	<?php if( Yii::$app->session->hasFlash('fail')) { ?>
			<div class="alert alert-error">
				<button data-dismiss="alert" class="close">x</button>
				<strong>Error!</strong>
				 <?php echo Yii::$app->session->getFlash('fail'); ?>
			</div>
	<?php } ?>
	
<!--<h1><?= Html::encode($this->title) ?></h1> -->

<?= $this->render('_form', [
	'model' => $model,
]) ?>

</div>
