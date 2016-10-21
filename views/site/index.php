<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Admin Login';
?>

<form  method="post" action="<?php echo Yii::$app->request->baseUrl; ?>/site/login">
	<p>Enter username and password to continue.</p>
	<div class="input-group input-sm">
		<span class="input-group-addon"><i class="fa fa-user"></i></span>
		<input class="form-control" required name="LoginForm[username]" type="username" placeholder="Enter your username">
	</div>
	<div class="input-group">
		<span class="input-group-addon"><i class="fa fa-lock"></i></span>
		<input type="password" name="LoginForm[password]" class="form-control" placeholder="Enter Password" required />
	</div>
	<div class="form-actions clearfix">
		<input type="submit" class="btn btn-block btn-primary btn-default" value="Login" />
	</div>
</form>
		

          

