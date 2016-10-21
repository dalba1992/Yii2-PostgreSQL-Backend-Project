<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Coupon */
/* @var $form yii\widgets\ActiveForm */

?>

<style type="text/css">
#left, #right {background: white; border: 2px dashed #444; padding:15px;}
.operation {
	width: 60%;
}

button[draggable=true] { 
		-moz-user-select:none; 
		-webkit-user-select: none; 
		-webkit-user-drag: element; 
}
</style>

<div class="row">
	<div class="col-xs-12">
		<div class="widget-box">
			<div class="widget-title">
				<span class="icon"> 
					<i class="fa fa-ticket"></i>
				</span>
				<h5>Assign Operations of <?php echo $name; ?></h5>
			</div>

			<input type="hidden" name="role" id="role" value="<?php echo $name; ?>">
			<input type="hidden" name="item" id="item" value="">
			<input type="hidden" name="from" id="from" value="">
			<input type="hidden" name="controller" id="controller" value="">
			<input type="hidden" name="action" id="action" value="">

			<div class="widget-content" style="min-height:400px">
				<div class="col-xs-1">
				</div>
				<div class="col-xs-4" id="left">
					<h5>Assigned Operations</h5>
					<table style="width:100%; text-align:center;">
						<?php foreach($assigned as $url) { ?>
						<tr id="<?php echo $url->id; ?>_row">
							<td>
								<button class="btn btn-info operation" type="button" draggable="true" id="<?php echo $url->id; ?>" data-location="left" ondragstart="processDragStart($(this));" data-controller="<?php echo $url->controller; ?>" data-action="<?php echo $url->action; ?>">
									<?php echo '/'.$url->controller.'/'.$url->action; ?>
								</button>
							</td>
						</tr>
						<?php } ?>
					</table>
				</div>
				<div class="col-xs-2">
				</div>
				<div class="col-xs-4" id="right">
					<h5>Unassigned Operations</h5>
					<table style="width:100%; text-align:center;">
						<?php foreach($unassigned as $url) { ?>
						<tr id="<?php echo $url->id; ?>_row">
							<td>
								<button class="btn btn-warning operation" type="button" draggable="true" id="<?php echo $url->id; ?>" data-location="right" ondragstart="processDragStart($(this));" data-controller="<?php echo $url->controller; ?>" data-action="<?php echo $url->action; ?>">
									<?php echo '/'.$url->controller.'/'.$url->action; ?>
								</button>
							</td>
						</tr>
						<?php } ?>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="message" style="display: none;">
    <h4><i class="fa fa-spinner fa-pulse"></i> Processing now...</h4>
</div><!-- /.modal -->

<?php
$script = <<<JS
	$(function() {

		$('#right') 
			.bind('dragover', function (evt) { 
				evt.preventDefault(); 
		}) 
			.bind('dragenter', function (evt) { 
				evt.preventDefault(); 
		}) 
			.bind('drop', function (evt) {
				var id = $('#item').val();
				var from = $('#from').val();
				var controller = $('#controller').val();
				var action = $('#action').val();
				if (from == 'left')
					processDragAndDrop('left', 'right', id, controller, action);
		});
		
		$('#left') 
			.bind('dragover', function (evt) { 
				evt.preventDefault(); 
		}) 
			.bind('dragenter', function (evt) { 
				evt.preventDefault(); 
		}) 
			.bind('drop', function (evt) {
				var id = $('#item').val();
				var from = $('#from').val();
				var controller = $('#controller').val();
				var action = $('#action').val();
				if (from == 'right')
					processDragAndDrop('right', 'left', id, controller, action);
		});
	});

	function processDragStart(obj)
	{
		$('#item').val($(obj).attr('id'));
		$('#from').val($(obj).attr('data-location'));
		$('#controller').val($(obj).attr('data-controller'));
		$('#action').val($(obj).attr('data-action'));
	}

	function processDragAndDrop(from_div, to_div, item_id, controller, action)
	{
		var row_id = item_id + '_row';
		$('#' + row_id).remove();

		var new_row_html = '<tr id="' + item_id + '_row"><td>';

		if (to_div == 'left')
			new_row_html += '<button class="btn btn-info operation" type="button" draggable="true" id="' + item_id + '"  data-location="left" data-controller="' + controller + '" data-action="' + action + '" ondragstart="processDragStart($(this));">';
		else
			new_row_html += '<button class="btn btn-warning operation" type="button" draggable="true" id="' + item_id + '"  data-location="right" ondragstart="processDragStart($(this));" data-controller="' + controller + '" data-action="' + action + '">';
		new_row_html += '/' + controller + '/' + action;
		new_row_html += '</button>';
		new_row_html += '</td></tr>';

		$('#' + to_div).find('table').append(new_row_html);

		var assignFlag = 1;
		if (to_div == 'left')
			assignFlag = 1;
		else if (to_div == 'right')
			assignFlag = 0;

		var role = $('#role').val();


		$.blockUI({ message: $("#message") });
    	$.ajax({
            url: '/user-management/process-assign-operations',
            type: 'POST',
            data: {role : role, auth_urls_id : item_id, assignFlag : assignFlag},
            success: function(data) {},
            complete: function(data) {
            	console.log(data);
                $.unblockUI();
            }
        });

	}
JS;
$this->registerJs($script, \yii\web\View::POS_END);
?>