<?php
/**
 * @var $this \yii\web\View
 */

?>

<div id="switcher">
    <div id="switcher-inner">
        <h3>Theme Options</h3>
        <h4>Colors</h4>
        <p id="color-style">
            <a data-color="orange" title="Orange" class="button-square orange-switcher" href="#"></a>
            <a data-color="turquoise" title="Turquoise" class="button-square turquoise-switcher" href="#"></a>
            <a data-color="blue" title="Blue" class="button-square blue-switcher" href="#"></a>
            <a data-color="green" title="Green" class="button-square green-switcher" href="#"></a>
            <a data-color="red" title="Red" class="button-square red-switcher" href="#"></a>
            <a data-color="purple" title="Purple" class="button-square purple-switcher" href="#"></a>
            <a href="#" data-color="grey" title="Grey" class="button-square grey-switcher active"></a>
        </p>

        <h4 class="visible-lg">Layout Type</h4>
        <p id="layout-type">
            <a data-option="flat" class="button active" href="#">Flat</a>
            <a data-option="old" class="button" href="#">Old</a>
        </p>
    </div>
    <div id="switcher-button">
        <i class="fa fa-cogs"></i>
    </div>
</div>