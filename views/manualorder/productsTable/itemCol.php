<?php

/* @var $this \yii\web\View */
/* @var $form \yii\bootstrap\ActiveForm */
/* @var $item \app\models\ProductVariants */
/* @var $model \app\models\Order */

$uniqueId = uniqid();
?>

<div class='row'>
    <div class='form-group'>
        <div class='row'>
            <select id='productType<?php echo (isset($item) ? $uniqueId : ''); ?>' name='productType' data-customerid='<?=$model->customerId?>' style='width:100%;max-width:90%;'>
                <option>Product type</option>
                <?php
                if(count(\app\models\Products::productTypes()) > 0) {
                    foreach(\app\models\Products::productTypes() as $productTypeValue => $productType) {
                        echo "<option value='". $productTypeValue ."' ". (isset($item) && $item->product->productType == $productTypeValue ? "selected='selected'" : '') .">". $productType ."</option>";
                    }
                }
                ?>
            </select>
        </div>
        <div class='row'>
            <select id='products<?php echo (isset($item) ? $uniqueId : ''); ?>' name='products' style='width: 100%;max-width: 90%'>
                <option>Product</option>
            </select>
        </div>
    </div>
    <br/>
    <div class='text-center'>
        <img src='/web/img/suit_100.png' alt='' id='productImage' width='130' height='153'>
        <br><br>
        <a title='Remove this Product' href='#' data-toggle='tooltip' data-placement='top' class='btn-small btn btn-danger removeProduct'><i class='icon-trash icon-white'></i> Remove</a>
        <br><br>
        <p id='productPrice' style='font-weight: bold;'>&dollar;<?php echo isset($item) ? number_format($item->retailPrice, 2) : 0.00; ?></p>
    </div>
    <div class='form-group' id='options'></div>
    <h4 id='stock' class='text-center'> <?php echo isset($item) ? $item->stockOnHand : 0; ?> Left</h4>
</div>

<?php

$url = Yii::$app->urlManager->createUrl(['/inventory/ajax-products']);

$itemsScript = <<<JS

var items = [];
var submittedItems = [];
var selectedItems = [];

JS;

$this->registerJs($itemsScript, \yii\web\View::POS_BEGIN);

if(isset($item)) {
    $loadScript = <<<JS

$(function() {


$('select#productType{$uniqueId}').each(function(){

var productTypeValue = $(this).val();
if(productTypeValue) {
    var parent = $(this).parent().parent();
    var products = parent.find('select#products{$uniqueId}');
    $.each(products, function(index, product) {
        var prod = $(product);
        prod.html('<option value="0">Product</option>');
        $.ajax({
            url: '{$url}',
            type: 'POST',
            dataType: 'JSON',
            data: {
                ProductSearch: {
                    productType: productTypeValue,
                    customerId: {$model->customerId}
                }
            },
            success: function(data) {
                $.each(data, function(index, value) {
                    items.push(value);
                    var option = $('<option>');
                    option.attr('value', value.id);
                    option.text(value.name);
                    if(value.id == parseInt('{$item->product->id}')) {
                        option.attr('selected', 'selected');
                    }
                    prod.append(option);
                    if(value.id == parseInt('{$item->product->id}')) {
                        var opts = {
                            opt1: '{$item->opt1}',
                            opt2: '{$item->opt2}',
                            opt3: '{$item->opt3}',
                        };
                        prod.val(value.id).trigger('change', [opts]);
                        var options = prod.parent().parent().parent().find("#options{$uniqueId}")[0];
                        if(value.opt1 != '' && value.opt1 != null) {
                            var optionId = value.opt1.replace(' ', '').toLowerCase() + "id" + value.id + "rand" + Math.floor((Math.random() * 1000000) + 1);

                            var variants = [];

                            var div = $('<div>');
                            div.addClass('row');
                            var select = $('<select>');
                            select.html('<option value="0">' + value.opt1 + '</option>');

                            $.each(value.variants, function(variantIndex, variant) {
                                if($.inArray(variant.opt1, variants) == -1) {
                                    variants.push(variant.opt1);
                                } else {
                                    return;
                                }
                            });

                            for(var i = 0; i < variants.length; i++) {
                                var option = $('<option>');
                                option.attr('value', variants[i]);
                                if(variants[i] == '{$item->opt1}') {
                                    option.attr('selected', 'selected');
                                }
                                option.text(variants[i]);
                                select.append(option[0]);
                            }

                            select.attr('id', optionId);
                            select.attr('style', '');
                            select.addClass('form-control');

                            div.html(select);
                            $(options).append(div);
                        }
                        if(value.opt2 != '' && value.opt2 != null) {
                            var optionId = value.opt2.replace(' ', '').toLowerCase() + "id" + value.id + "rand" + Math.floor((Math.random() * 1000000) + 1);

                            var variants = [];

                            var div = $('<div>');
                            div.addClass('row');
                            var select = $('<select>');
                            select.html('<option value="0">' + value.opt2 + '</option>');
                            $.each(value.variants, function(variantIndex, variant) {
                                if($.inArray(variant.opt2, variants) == -1) {
                                    variants.push(variant.opt2);
                                } else {
                                    return;
                                }
                            });
                            $.each(variants, function(variantIndex, variant) {
                                var option = $('<option>');
                                option.attr('value', variant);
                                option.text(variant);
                                select.append(option);
                            });

                            select.attr('id', optionId);
                            select.attr('style', '');
                            select.addClass('form-control');
                            div.html(select);
                            $(options).append(div);

                            $.each(value.variants, function(variantIndex, variant) {
                                if(variant.id == parseInt('{$item->id}')) {
                                    select.val(variant.opt2).trigger('change');
                                    select.find('option[value='+ variant.opt2 +']').attr('selected', 'selected');
                                }
                            });
                        }
                        if(value.opt3 != '' && value.opt3 != null) {
                            var optionId = value.opt3.replace(' ', '').toLowerCase() + "id" + value.id + "rand" + Math.floor((Math.random() * 1000000) + 1);

                            var variants = [];

                            var div = $('<div>');
                            div.addClass('row');
                            var select = $('<select>');
                            select.html('<option value="0">' + value.opt3 + '</option>');
                            $.each(value.variants, function(variantIndex, variant) {
                                if($.inArray(variant.opt3, variants) == -1) {
                                    variants.push(variant.opt3);
                                } else {
                                    return;
                                }
                            });
                            $.each(variants, function(variantIndex, variant) {
                                var option = $('<option>');
                                option.attr('value', variant);
                                option.text(variant);
                                select.append(option);
                            });

                            select.attr('id', optionId);
                            select.attr('style', '');
                            select.addClass('form-control');
                            div.html(select);
                            $(options).append(div);

                            $.each(value.variants, function(variantIndex, variant) {
                                if(variant.id == parseInt('{$item->id}')) {
                                    select.val(variant.opt3).trigger('change');
                                    select.find('option[value='+ variant.opt3 +']').attr('selected', 'selected');
                                }
                            });
                        }
                    }
                    var img = prod.parent().parent().parent().find("#productImage{$uniqueId}")[0];
                    if(img != undefined) {
                        if(value.imageUrl) {
                            $(img).attr('src', value.imageUrl);
                        } else {
                            $(img).attr('src', '/web/img/suit_100.png');
                        }
                    }

                    if(index == data.length - 1) return false;
                });
            }
        });

    });

}

});


});

JS;
    $this->registerJs($loadScript, \yii\web\View::POS_END, $uniqueId);
}


$script = <<<JS

$(function() {

    $("#productsTable").on('change', 'select[name=productType]', function(e) {
        var productType = $(this).val();
        var parent = $(this).parent().parent();
        var products = parent.find('select[name=products]');
        $.each(products, function(index, product){
            var prod = $(product);
            prod.html('<option value="0">Product</option>');
            prod.val(0).trigger('change');
            $.ajax({
                url: '{$url}',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    ProductSearch: {
                        productType: productType,
                        customerId: {$model->customerId}
                    }
                },
                success: function(data) {
                    $.each(data, function(index, value) {
                        items.push(value);
                        var option = $('<option>');
                        option.attr('value', value.id);
                        option.text(value.name);
                        prod.append(option);
                    });
                }
            });
        });
    });

    $("#productsTable").on('change', 'select[name=products]', function(e, opts) {
        opts = opts || undefined;
        var itemId = $(this).val();
        var prod = $(this);

        for(var i = 0; i < items.length; i++) {
            var options = prod.parent().parent().parent().find("#options")[0];
            $(options).empty();
            var stock = prod.parent().parent().parent().find("#stock")[0];
            $(stock).empty();
            var price = prod.parent().parent().parent().find("#productPrice")[0];
            $(price).empty();
            if(itemId != 0 && itemId == items[i].id){
                var item = items[i];

                var img = prod.parent().parent().parent().find("#productImage")[0];
                if(img != undefined) {
                    if(items[i].imageUrl) {
                        $(img).attr('src', items[i].imageUrl);
                    } else {
                        $(img).attr('src', '/web/img/suit_100.png');
                    }
                }
                if(options !== undefined) {
                    if(item.opt1 != '' && item.opt1 != null) {
                        var optionId = item.opt1.replace(' ', '').toLowerCase() + "id" + item.id + "rand" + Math.floor((Math.random() * 1000000) + 1);

                        var variants = [];

                        var div = $('<div>');
                        div.addClass('row');
                        var select = $('<select>');
                        select.attr('name', 'opt1');
                        select.html('<option value="0">' + item.opt1 + '</option>');

                        $.each(item.variants, function(variantIndex, variant) {
                            if($.inArray(variant.opt1, variants) == -1) {
                                variants.push(variant.opt1);
                            } else {
                                return;
                            }
                        });
                        $.each(variants, function(variantIndex, variant) {
                            var option = $('<option>');
                            option.attr('value', variant);
                            if(opts !== undefined && opts.opt1 == variant) {
                                var selectedItemId = prod.val();
                                if(selectedItems[selectedItemId] == undefined) selectedItems[selectedItemId] = {};

                                selectedItems[selectedItemId].element = prod;

                                selectedItems[selectedItemId].opt1 = variant;
                                if(selectedItems[selectedItemId].opt2 == undefined) selectedItems[selectedItemId].opt2 = '';
                                if(selectedItems[selectedItemId].opt3 == undefined) selectedItems[selectedItemId].opt3 = '';

                                option.attr('selected', 'selected');
                                $('#productsTable').trigger('changeProduct');
                            }
                            option.text(variant);
                            select.append(option);
                            select.trigger('change');
                        });


                        select.attr('id', optionId);
                        select.attr('style', 'width:100%;');
                        select.addClass('form-control');
                        if(opts !== undefined) {
                            select.val(opts.opt1).trigger('change');
                        }
                        div.html(select);
                        $(options).append(div);
                    }
                    if(item.opt2 != '' && item.opt2 != null) {
                        var optionId = item.opt2.replace(' ', '').toLowerCase() + "id" + item.id + "rand" + Math.floor((Math.random() * 1000000) + 1);

                        var variants = [];

                        var div = $('<div>');
                        div.addClass('row');
                        var select = $('<select>');
                        select.attr('name', 'opt2');
                        select.html('<option value="0">' + item.opt2 + '</option>');

                        $.each(item.variants, function(variantIndex, variant) {
                            if($.inArray(variant.opt2, variants) == -1) {
                                variants.push(variant.opt2);
                            } else {
                                return;
                            }
                        });
                        $.each(variants, function(variantIndex, variant) {
                            var option = $('<option>');
                            option.attr('value', variant);
                            if(opts !== undefined && opts.opt2 == variant) {
                                var selectedItemId = prod.val();
                                if(selectedItems[selectedItemId] == undefined) selectedItems[selectedItemId] = {};

                                selectedItems[selectedItemId].element = prod;

                                if(selectedItems[selectedItemId].opt1 == undefined) selectedItems[selectedItemId].opt1 = '';
                                selectedItems[selectedItemId].opt2 = variant;
                                if(selectedItems[selectedItemId].opt3 == undefined) selectedItems[selectedItemId].opt3 = '';

                                option.attr('selected', 'selected');
                                $('#productsTable').trigger('changeProduct');
                            }
                            option.text(variant);
                            select.append(option);
                        });

                        select.attr('id', optionId);
                        select.attr('style', '');
                        select.addClass('form-control');
                        if(opts !== undefined) {
                            select.val(opts.opt2).trigger('change');
                        }
                        div.html(select);
                        $(options).append(div);
                    }
                    if(item.opt3 != '' && item.opt3 != null) {
                        var optionId = item.opt3.replace(' ', '').toLowerCase() + "id" + item.id + "rand" + Math.floor((Math.random() * 1000000) + 1);

                        var variants = [];

                        var div = $('<div>');
                        div.addClass('row');
                        var select = $('<select>');
                        select.attr('name', 'opt3');
                        select.html('<option value="0">' + item.opt3 + '</option>');

                        $.each(item.variants, function(variantIndex, variant) {
                            if($.inArray(variant.opt3, variants) == -1) {
                                variants.push(variant.opt3);
                            } else {
                                return;
                            }
                        });
                        $.each(variants, function(variantIndex, variant) {
                            var option = $('<option>');
                            option.attr('value', variant);
                            if(opts !== undefined && opts.opt3 == variant) {
                                var selectedItemId = prod.val();
                                if(selectedItems[selectedItemId] == undefined) selectedItems[selectedItemId] = {};

                                selectedItems[selectedItemId].element = prod;

                                if(selectedItems[selectedItemId].opt1 == undefined) selectedItems[selectedItemId].opt1 = '';
                                if(selectedItems[selectedItemId].opt2 == undefined) selectedItems[selectedItemId].opt2 = '';
                                selectedItems[selectedItemId].opt3 = variant;

                                option.attr('selected', 'selected');
                                $('#productsTable').trigger('changeProduct');
                            }
                            option.text(variant);
                            select.append(option);

                        });

                        select.attr('id', optionId);
                        select.attr('style', '');
                        select.addClass('form-control');
                        if(opts !== undefined) {
                            select.val(opts.opt3).trigger('change');
                        }
                        div.html(select);
                        $(options).append(div);
                    }
                }

                break;
            } else {
                continue;
            }
        }
    });

    $("#productsTable").on('change', 'select[name=opt1]', function(e) {
        var opt = $(this);
        var optVal = $(this).val();
        if(optVal != 0) {
            var products = $(this).parent().parent().parent().find('select[name=products]');
            var selectedItemId = $(products).val()

            for(var i = 0; i < items.length; i++) {
                var item = items[i];
                if(item.id == selectedItemId) {
                    if(selectedItems[selectedItemId] == undefined) selectedItems[selectedItemId] = {};

                    selectedItems[selectedItemId].element = opt;

                    selectedItems[selectedItemId].opt1 = optVal;
                    if(selectedItems[selectedItemId].opt2 == undefined) selectedItems[selectedItemId].opt2 = '';
                    if(selectedItems[selectedItemId].opt3 == undefined) selectedItems[selectedItemId].opt3 = '';

                    opt.trigger('changeProduct');
                    break;
                } else {
                    continue;
                }
            }
        }
    });

    $("#productsTable").on('change', 'select[name=opt2]', function(e) {
        var opt = $(this);
        var optVal = $(this).val();
        if(optVal != 0) {
            var products = $(this).parent().parent().parent().find('select[name=products]');
            var selectedItemId = $(products).val()

            for(var i = 0; i < items.length; i++) {
                var item = items[i];
                if(item.id == selectedItemId) {
                    if(selectedItems[selectedItemId] == undefined) selectedItems[selectedItemId] = {};

                    selectedItems[selectedItemId].element = opt;

                    if(selectedItems[selectedItemId].opt1 == undefined) selectedItems[selectedItemId].opt1 = '';
                    selectedItems[selectedItemId].opt2 = optVal;
                    if(selectedItems[selectedItemId].opt3 == undefined) selectedItems[selectedItemId].opt3 = '';

                    opt.trigger('changeProduct');
                    break;
                } else {
                    continue;
                }
            }
        }
    });

    $("#productsTable").on('change', 'select[name=opt3]', function(e) {
        var opt = $(this);
        var optVal = $(this).val();
        if(optVal != 0) {
            var products = $(this).parent().parent().parent().find('select[name=products]');
            var selectedItemId = $(products).val()
            for(var i = 0; i < items.length; i++) {
                var item = items[i];
                if(item.id == selectedItemId) {
                    if(selectedItems[selectedItemId] == undefined) selectedItems[selectedItemId] = {};

                    selectedItems[selectedItemId].element = opt;

                    if(selectedItems[selectedItemId].opt1 == undefined) selectedItems[selectedItemId].opt1 = '';
                    if(selectedItems[selectedItemId].opt2 == undefined) selectedItems[selectedItemId].opt2 = '';
                    selectedItems[selectedItemId].opt3 = optVal;

                    opt.trigger('changeProduct');
                    break;
                } else {
                    continue;
                }
            }
        }
    });

    $("#productsTable").on('changeProduct', function(e) {
        var backSubmittedItems = submittedItems;
        submittedItems.length = 0;
        for(var i = 0; i < items.length; i++) {
            var item = items[i];

            if(selectedItems[item.id] != undefined) {
                var selectedItem = selectedItems[item.id];
                var el = selectedItem.element;
                var stock = el.parent().parent().parent().find("#stock")[0];
                $(stock).empty();
                var price = el.parent().parent().parent().find("#productPrice")[0];
                $(price).empty();
                var removeBtn = el.parent().parent().parent().find(".removeProduct");
                var td = el.parent().parent().parent().parent();

                $.each(item.variants, function(variantIndex, variant) {
                    if(selectedItem.opt1 == variant.opt1 && selectedItem.opt2 == variant.opt2 && selectedItem.opt3 == variant.opt3) {
                        $(price).html('$' + parseFloat(variant.retailPrice));
                        $(".totalPrice b").trigger('changeTotalPrice');
                        if(parseInt(variant.availableStock) > 0) {
                            $(stock).html(parseInt(variant.availableStock) + ' Left');
                        } else {
                            $(stock).html('<span style="color: red">The product is not on stock</span>');
                        }

                        if($.inArray(variant.sku, backSubmittedItems) == -1) {
                            submittedItems.push(variant.sku);
                        }

                        td.attr('data-item', variant.sku);

                        removeBtn.attr('data-sku', variant.sku);
                        removeBtn.attr('data-itemid', item.id);

                        return false;
                    } else {
                        return;
                    }
                });
            }
        }
    });


    $(".totalPrice b").on('changeTotalPrice', function(e) {
        var productTds = $("#productsTable").find('td[data-type=col-item]');
        var totalPrice = 0.00;
        if(productTds.length > 0) {
            productTds.each(function(){
                var td = $(this);
                if(td.attr('data-type') == 'col-item') {
                    var priceEl = td.find('#productPrice')[0];
                    if(priceEl !== undefined) {
                        var price = $(priceEl).text().replace('$', '').replace('&dollar;', '');
                        price = parseFloat(price);
                        totalPrice = totalPrice + price;
                    }
                }
            });
        }
        $(this).html('$' + totalPrice);
    });
});

JS;

$this->registerJs($script, \yii\web\View::POS_END);

?>