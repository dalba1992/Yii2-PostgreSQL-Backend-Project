<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Coupon */

$this->title = 'Create a new operation';

?>
<div class="user-create">
	<div id="content-header">
		<h1>Create New Operation</h1>
	</div>
	<?php if( Yii::$app->session->hasFlash('fail')) { ?>
			<div class="alert alert-error">
				<button data-dismiss="alert" class="close">x</button>
				<strong>Error!</strong>
				 <?php echo Yii::$app->session->getFlash('fail'); ?>
			</div>
	<?php } ?>
	
<!--<h1><?= Html::encode($this->title) ?></h1> -->

<?= $this->render('_operationform', [
	'model' => $model,
	'error' => $error,
	'option' => '0',
	'id' => '-1',
	'name' => $name
]) ?>

</div>
