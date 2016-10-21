<?php

use yii\helpers\Html;
use \app\models\UserEntity;

/* @var $this yii\web\View */
/* @var $model app\models\Coupon */

$this->title = 'Assign Operations';

?>
<div class="user-create">
	<div id="content-header">
		<h1>Assign Operations</h1>
	</div>
	<?php if( Yii::$app->session->hasFlash('fail')) { ?>
		<div class="alert alert-error">
			<button data-dismiss="alert" class="close">x</button>
			<strong>Error!</strong>
			 <?php echo Yii::$app->session->getFlash('fail'); ?>
		</div>
	<?php } ?>
	
<!--<h1><?= Html::encode($this->title) ?></h1> -->

<?= $this->render('_form_assign_operations', [
	'assigned' => $assigned,
	'unassigned' => $unassigned,
	'name' => $name,
]) ?>

</div>
