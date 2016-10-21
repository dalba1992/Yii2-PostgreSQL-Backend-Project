<?php
/**
 * @var $this \yii\web\View
 * @var $content string
 */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;

use app\assets\AppAsset;
use app\models\CustomerEntity;
use app\models\CustomerSubscriptionOrder;
use app\models\Products;
use app\models\Order;
use app\modules\services\models\TradegeckoProduct;

AppAsset::register($this);

$allMemberCount = CustomerEntity::getTotalMembersCount();
$activeMemberCount = CustomerEntity::getActiveMembersCount();
$inactiveMemberCount = CustomerEntity::getInactiveMembersCount();
$conciergeInventoryCount = count(Products::find()->where(['shop' => '0'])->all());
$shopInventoryCount = count(Products::find()->where(['shop' => '1'])->all());
$orders = Order::find()->count();
?>

<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <link rel='shortcut icon' type='image/x-icon' href='<?php echo Yii::$app->getHomeUrl(); ?>favicon.ico' />

        <?php $this->head(); ?>

        <script type="text/javascript">
            var switcher_trendy='<?php echo Yii::$app->getHomeUrl(); ?>web/';
        </script>

    </head>

    <body data-color="grey" class="flat">
    <?php $this->beginBody() ?>
    <div id="wrapper">
        <?php
        echo Yii::$app->controller->renderPartial('//common/layout/_header', []);

        if(!Yii::$app->user->isGuest){
            echo Yii::$app->controller->renderPartial('//common/layout/_userNav', []);
            //echo Yii::$app->controller->renderPartial('//common/layout/_switcher', []);
            echo Yii::$app->controller->renderPartial('//common/layout/_sidebar', [
                'allMemberCount'=>$allMemberCount,
                'activeMemberCount'=>$activeMemberCount,
                'inactiveMemberCount'=>$inactiveMemberCount,
                'orders'=>$orders,
                'conciergeInventoryCount' => $conciergeInventoryCount,
                'shopInventoryCount' => $shopInventoryCount,
            ]);
        }
        ?>

        <div id="content">
            <?php echo $content; ?>
        </div>

        <div class="row">
            <div id="footer" class="col-xs-12">&copy; 2014 - <?php echo date('Y');?> Trendy Butler | version <strong><?php echo Yii::$app->version; ?></strong></div>
        </div>

    </div>
    <?php $this->endBody() ?>

    <script>
        jQuery('.sidebar_menu li a').click(function() {
            jQuery(".sidebar_menu li").removeClass("active");
            jQuery(this).parent().addClass('active');
        });
    </script>
    </body>
    </html>
<?php $this->endPage() ?>