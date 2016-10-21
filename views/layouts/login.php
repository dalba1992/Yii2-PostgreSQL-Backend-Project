<?php
    use yii\helpers\Html;
    use yii\bootstrap\Nav;
    use yii\bootstrap\NavBar;
    use yii\widgets\Breadcrumbs;
    use app\assets\AppAsset;
    use app\models\CustomerEntity;
    use app\models\CustomerSubscriptionOrder;

    /* @var $this \yii\web\View */
    /* @var $content string */

    AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Yii::$app->name . ' | ' . Html::encode($this->title) ?></title>
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
    <link rel="stylesheet" href="<?php echo Yii::$app->getHomeUrl(); ?>web/css/unicorn-login.css" />
</head>
<body>
<?php $this->beginBody() ?>

<div id="container">
    <div id="loginbox" style="margin-top: 140px;">
        <?= $content ?>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
