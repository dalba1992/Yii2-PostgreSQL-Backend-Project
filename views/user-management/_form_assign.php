<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Coupon */
/* @var $form yii\widgets\ActiveForm */

?>

<style type="text/css">
#left, #right {background: white; border: 2px dashed #444; padding:15px;}
.role {
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
				<h5>Assign Roles of <?php echo $model->username; ?></h5>
			</div>

			<input type="hidden" name="user_id" id="user_id" value="<?php echo $model->id; ?>">
			<input type="hidden" name="item" id="item" value="">
			<input type="hidden" name="from" id="from" value="">

			<div class="widget-content" style="min-height:400px">
				<div class="col-xs-1">
				</div>
				<div class="col-xs-4" id="left">
					<h5>Assigned Roles</h5>
					<table style="width:100%; text-align:center;">
						<?php foreach($assigned as $role) { ?>
						<tr id="<?php echo $role->name; ?>_row">
							<td>
								<button class="btn btn-info role" type="button" draggable="true" id="<?php echo $role->name; ?>" data-location="left" ondragstart="processDragStart($(this));">
									<?php echo $role->name; ?>
								</button>
							</td>
						</tr>
						<?php } ?>
					</table>
				</div>
				<div class="col-xs-2">
				</div>
				<div class="col-xs-4" id="right">
					<h5>Unassigned Roles</h5>
					<table style="width:100%; text-align:center;">
						<?php foreach($unassigned as $role) { ?>
						<tr id="<?php echo $role->name; ?>_row">
							<td>
								<button class="btn btn-warning role" type="button" draggable="true" id="<?php echo $role->name; ?>" data-location="right" ondragstart="processDragStart($(this));">
									<?php echo $role->name; ?>
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
				if (from == 'left')
					processDragAndDrop('left', 'right', id);
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
				if (from == 'right')
					processDragAndDrop('right', 'left', id);
		});
	});

	function processDragStart(obj)
	{
		$('#item').val($(obj).attr('id'));
		$('#from').val($(obj).attr('data-location'));
	}

	function processDragAndDrop(from_div, to_div, item_id)
	{
		var row_id = item_id + '_row';
		$('#' + row_id).remove();

		var new_row_html = '<tr id="' + item_id + '_row"><td>';

		if (to_div == 'left')
			new_row_html += '<button class="btn btn-info role" type="button" draggable="true" id="' + item_id + '"  data-location="left" ondragstart="processDragStart($(this));">';
		else
			new_row_html += '<button class="btn btn-warning role" type="button" draggable="true" id="' + item_id + '"  data-location="right" ondragstart="processDragStart($(this));">';
		new_row_html += item_id;
		new_row_html += '</button>';
		new_row_html += '</td></tr>';

		$('#' + to_div).find('table').append(new_row_html);

		var assignFlag = 1;
		if (to_div == 'left')
			assignFlag = 1;
		else if (to_div == 'right')
			assignFlag = 0;

		var user_id = $('#user_id').val();


		$.blockUI({ message: $("#message") });
    	$.ajax({
            url: '/user-management/process-assign',
            type: 'POST',
            data: {role: item_id, user_id:user_id, assignFlag : assignFlag},
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