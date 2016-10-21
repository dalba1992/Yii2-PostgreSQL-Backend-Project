<?php

/* @var $this \yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $model \app\models\Order */
/* @var $customerInfo \app\models\CustomerEntityInfo */
/* @var $customerAddress \app\models\CustomerEntityAddress */
/* @var $order integer */
/* @var $items \app\models\ProductVariants[] */
/*  */

$this->title = "Manual Order";

$countItems = 0;
?>

<div id="content-header">
    <h1>Manual Queue</h1>
    <div class="btn-group">
        <?=\yii\helpers\Html::a('<i class="fa fa-arrow-left"></i> Previous', ['index', 'order' => $order - 1], [
            'class' => 'btn btn-large',
            'disabled' => $order <= 1 ? true : false
        ])?>
        <a href="#" class="btn btn-large disabled" title="Manage Users"><i class="icon-user"></i> Order <?=$dataProvider->getPagination()->getPage()+1?> of <?=$dataProvider->getPagination()->getPageCount()?></a>
        <?=\yii\helpers\Html::a('Next <i class="fa fa-arrow-right"></i>', ['index', 'order' => $order + 1], [
            'class' => 'btn btn-large',
            'disabled' => $order >= $dataProvider->getPagination()->getPageCount() ? true : false
        ])?>
    </div>
</div>
<div class="container-fluid">
    <!--Notifications-->
    <?php if(Yii::$app->session->getFlash('success')){?>
        <div class="alert alert-success">
            <button data-dismiss="alert" class="close">x</button>
            <strong>Success!</strong> <?php echo Yii::$app->session->getFlash('success'); ?>
        </div>
    <?php } ?>

    <?php if(Yii::$app->session->hasFlash('fail')){?>
        <?php
            $errors = Yii::$app->session->getFlash('fail');
            if(count($errors)) {
        ?>
            <?php foreach($errors as $error) { ?>
                <div class="alert alert-danger">
                    <button data-dismiss="alert" class="close">x</button>
                    <strong>Error!</strong> <?php echo $error; ?>
                </div>
            <?php } ?>
        <?php } ?>
    <?php } ?>
    <div class="widget-box collapsible">
        <?php echo $this->render('filter'); ?>
    </div>

    <div class="widget-box">
        <div class="widget-content nopadding">
            <?php $form = \yii\bootstrap\ActiveForm::begin(); ?>
                <?php if($model) { ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Info</th>
                                <th>Items</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td style="width: 13.6666666666666%;">
                                <b><?=$customerInfo->first_name?> <?=$customerInfo->last_name;?></b>
                                <br><?=$customerAddress->address?>, <?=$customerAddress->zipcode?>
                                <br><?=$customerAddress->city?>, <?=$customerAddress->state?>
                                <br><br>
                                <b>
                                    <?php echo array_filter($customerAttributes, function($attr) {
                                        return $attr['attribute_id'] == 1;
                                    })[0]['value']; ?>
                                </b>
                                <br><br>
                                <?php foreach($customerAttributes as $attr) { ?>
                                    <?php if($attr['attribute_id'] == 1) continue; ?>
                                    <strong><?=$attr['name']?></strong>(id = <?=$attr['attribute_id']?>): <?=$attr['value']?>
                                    <br/>
                                <?php } ?>
                                <br>
                                <b>Referral Code: </b><br>Cook
                                <br>
                                <b>Member Since: </b><br>03-24-12
                            </td>
                            <td class="nopadding">
                                <table id="productsTable" class="table table-bordered table-striped">
                                    <tbody>
                                        <tr>
                                            <?php if(count($items) > 0) { ?>
                                                <?php foreach($items as $item) { ?>
                                                    <td data-type="col-item" style="width: 15%;">
                                                        <?php echo $this->render('productsTable/itemCol', [
                                                            'item' => $item,
                                                            'form' => $form,
                                                            'model' => $model,
                                                            'count' => $countItems++
                                                        ]); ?>
                                                    </td>
                                                <?php } ?>
                                            <?php } ?>
                                            <td class="product-col">
                                                <?php echo $this->render('productsTable/addCol'); ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td style="width: 13.6666666666666%;">
                                <center>
                                    <div class="form-actions">
                                        <div class="totalPrice">Retail Pricing: <b>$0.00</b></div>
                                        <br>
                                        <button type="submit" class="btn btn-large btn-success" id="submitOrder">Submit Order</button>
                                    </div>
                                </center>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <?php

                    $itemCol = $this->render('productsTable/itemCol', [
                        'form' => $form,
                        'model' => $model,
                        'count' => $countItems
                    ]);

                    ?>
                <?php } else { ?>
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <td>No orders found.</td>
                            </tr>
                        </tbody>
                    </table>
                <?php } ?>
            <?php $form->end(); ?>
        </div>
    </div>

    <?php if($previouslySentProducts != null) { ?>
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fa fa-th-list"></i></span>
                <h5>Previously Sent</h5>
            </div>
            <div class="widget-content">
                <?php foreach($previouslySentProducts as $previouslyProduct) {  ?>
                    <img src="<?php echo $previouslyProduct->product->product->imageUrl ? $previouslyProduct->product->product->imageUrl : "/web/img/suit_100.png" ?>" alt="<?=$previouslyProduct->product->product->name?>" width="75" data-toggle="tooltip" data-placement="top" height="100" title="<?=$previouslyProduct->product->product->name?>">
                <?php } ?>
            </div>
        </div>
    <?php } ?>

    <?php if($latestOrders) { ?>
        <?php foreach($latestOrders as $latestOrder) { ?>
            <?php $tradegeckoOrder = Yii::$app->tradeGecko->getOrder($latestOrder->orderLog->tradegeckoOrderId); ?>
            <div class="col-lg-6">
                <div class="widget-box">
                    <div class="widget-title">
                        <span class="icon"><i class="fa fa-th-list"></i></span>
                        <h5>Order: <?=$tradegeckoOrder['order_number']?></h5>
                    </div>
                    <div class="widget-content">
                        <p><b>Customer</b>: <?=$latestOrder->customer->customerEntityInfo->first_name?> <?=$latestOrder->customer->customerEntityInfo->last_name?></p>
                        <p><b>Status</b>: <?=ucfirst(strtolower($tradegeckoOrder['status']))?></p>
                        <p><b>Issue Date</b>: <?=date('d M Y', strtotime($tradegeckoOrder['issued_at']))?></p>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="2%"></th>
                                    <th width="45%">Item Name</th>
                                    <th width="10%">Quantity</th>
                                    <th width="10%">Available</th>
                                    <th width="10%">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($latestOrder->getItems())) { ?>
                                    <?php $total = 0; ?>
                                    <?php foreach($latestOrder->getItems() as $item) { ?>
                                        <?php $total = $total + (float)$item->retailPrice; ?>
                                        <tr>
                                            <td><a href="<?php echo $item->product->imageUrl ? $item->product->imageUrl : "/web/img/suit_50.png"; ?>" target="_blank"><img src="<?php echo $item->product->imageUrl ? $item->product->imageUrl : "/web/img/suit_50.png"; ?>" width="20" height="25" /></a></td>
                                            <td><?=$item->sku?> - <?=$item->product->name?> - <?=$item->name?></td>
                                            <td class="text-center">1</td>
                                            <td class="text-center"><?=$item->availableStock?></td>
                                            <td class="text-center">$<?=number_format($item->retailPrice, 2)?></td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td colspan="3" rowspan="3"></td>
                                        <td class="text-right">Total Units</td>
                                        <td class="text-center"><?=count($latestOrder->getItems())?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-right">Total</td>
                                        <td class="text-center">$<?=number_format($total, 2)?></td>
                                    </tr>
                                <?php } else { ?>
                                    <tr>
                                        <td class="text-center" colspan="4">No items</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php } ?>

    <?php } ?>
</div>
<div id="message" style="display: none;">
    <h4><i class="fa fa-spinner fa-pulse"></i> Please wait until the order is sent</h4>
</div><!-- /.modal -->
<?php

$initSelect2 = <<<JS
    $(function(){
        $('select').select2();
    });
JS;
$this->registerJs($initSelect2, \yii\web\View::POS_END);

if($model) {

$itemCol = explode("\n", $itemCol);
$itemHtml = '';
foreach($itemCol as $key => $line) {
    $itemHtml .= trim($line);
}


$addCol = $this->render('productsTable/addCol');
$addCol = explode("\n", $addCol);
$addHtml = '';
foreach($addCol as $key => $line) {
    $addHtml .= trim($line);
}
    /** @var string $script */
    $script = <<<JS

$(function() {

    $('body').on('click', '#addNewProduct', function(e){
        var table = $("#productsTable");
        var tds = table.find("td");

        var currentTd = $(this).parent().parent();

        if(tds.length < 5) {
            var newTd = $('<td>');
            newTd.html("{$itemHtml}");
            newTd.attr('data-type', 'col-item');
            newTd.css('width', '15%');

            $('select', newTd).select2();

            currentTd.before(newTd);

            return false;
        } else if(tds.length >= 5) {
            currentTd.html("{$itemHtml}");
            currentTd.attr('data-type', 'col-item');
            currentTd.css('width', '15%');
            $('select', currentTd).select2();

            return false;
        }

        e.preventDefault();
    });

    $('body').on('click', '.removeProduct', function(e) {
        var table = $("#productsTable");
        var tdItems = table.find("td[data-type=col-item]");

        var btn = $(this);
        var currentTd = btn.parent().parent().parent();

        var countItems = tdItems.length;

        if(submittedItems.length > 0) {
            for(var i = 0; i < submittedItems.length; i++) {
                if(btn.attr('data-sku') == submittedItems[i]) {
                    submittedItems.splice(i, 1);
                    break;
                } else {
                    continue;
                }
            }
        }
        if(selectedItems.length > 0) {
            if(selectedItems[parseInt(btn.attr('data-itemid'))] !== undefined) {
                selectedItems.splice(parseInt(btn.attr('data-itemid')), 1);
            }
        }

        if(tdItems.length >= 5) {
            var currentTdIndex = currentTd.index();
            currentTd.remove();

            var newTd = $('<td>');
            newTd.html("{$addHtml}");
            newTd.attr('data-type', 'col-add');

            if((countItems-1) == currentTdIndex){
                $(tdItems[countItems-2]).after(newTd);
            } else {
                $(tdItems[countItems-1]).after(newTd);
            }
            $(".totalPrice b").trigger('changeTotalPrice');
            return false;
        } else if(tdItems.length < 5) {
            currentTd.remove();
            $(".totalPrice b").trigger('changeTotalPrice');
            return false;
        }

        e.preventDefault();
    });

    $('body').on('click', '#submitOrder', function(e) {
        var table = $("#productsTable");
        var tdItems = table.find("td[data-type=col-item]");
        var sItems = [];

        tdItems.each(function(){
            var td = $(this);
            if(td.attr('data-item') !== undefined && td.attr('data-item') != null) {
                sItems.push(td.attr('data-item'));
            }
        });

        if(tdItems.length > 0) {
            $.blockUI({ message: $("#message") });
            $.ajax({
                url: '/manualorder/submit-order',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    items: sItems,
                    orderId: {$model->id}
                },
                success: function(data) {},
                complete: function(data) {
                    $.unblockUI();
                    location.reload(true);
                }
            });
        } else {
            alert("The order is empty");
        }

        e.preventDefault();
    });
});

JS;

    $this->registerJs($script, \yii\web\View::POS_END);

}
\app\assets\Select2Asset::register($this);

?>