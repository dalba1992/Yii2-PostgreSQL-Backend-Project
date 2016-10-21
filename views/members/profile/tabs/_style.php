
<?php
use app\models\TbAttribute;
use app\models\TbAttributeDetail;
use app\models\TbAttributeDetailImage;
use app\models\CustomerEntityAttribute;

$TbAttribute = 	TbAttribute::find()->orderBy(['id' => SORT_ASC,])->all();
?>
<!--Style Info-->
<div class="tab-pane" id="tab5">
    <div class="widget-box">
        <div class="widget-title">
			<span class="icon">
				<i class="fa fa-headphones"></i>
			</span>
            <h5>Style Info</h5>
            <span class="icon" style="float:right;"> 
             <a href="javascript:;" data-toggle="modal" id="modalUp"data-target="#myModal"><i class="fa fa-cog"></i></a>
             
            </span>
        </div>
        <div class="widget-content nopadding">
            <form id="style_form" class="form-horizontal" method="post" action="<?php echo Yii::$app->homeUrl.'members/updatestyle?member='.$_GET['member'];?>">
                <?php if(sizeof($TbAttribute)>0)
                {
                foreach($TbAttribute as $attributeinfo){
                $style = '';

                if($attributeinfo->is_active)
                {

                $customerAttribute = CustomerEntityAttribute::find()->where(['customer_id'  => $_GET['member'], 'attribute_id' => $attributeinfo->id])->all(); ?>
                <div class="form-group">
                    <label  class="col-sm-2 control-label"><?php echo $attributeinfo->tooltip_bar_title?></label>
                    <div class="col-sm-10">
                        <?php $i = 0;
                        if ($attributeinfo->is_multiselect == true && $attributeinfo->is_active)
                        {
                        if (sizeof($customerAttribute) > 0)
                        { ?>
                        <select id="mySelect" class="form-control" required multiple name='style[<?php echo $attributeinfo->id;?>][]'>
                            <option value="">-- Please Select <?php echo $attributeinfo->tooltip_bar_title?>--</option>
                            <?php
                            $selected_array  = array();
                            $tb_attribute_id = array();
                            foreach ($customerAttribute as $attribute) :
                                $selected_array[] = $attribute->value;
                                $tb_attribute_id  = $attribute->attribute_id;//7
                            endforeach;

                            $TbAttributeDetail = TbAttributeDetail::find()
                                ->where(['tb_attribute_id' => $tb_attribute_id])
                                ->orderBy(['id' => SORT_ASC])
                                ->all();

                            foreach ($TbAttributeDetail as $detail) : ?>
                                <option value="<?php echo $detail->id?>" <?php if (in_array($detail->id, $selected_array)) {
                                    echo 'selected="selected"';} ?>><?php echo $detail->title; ?></option>
                            <?php endforeach;

                            echo "</select>";

                            } else { ?>
                            <select id="mySelect" required multiple="multiple" name='style[<?php echo $attributeinfo->id; ?>][]' class="form-control">
                                <option value="">-- Please Select <?php echo $attributeinfo->tooltip_bar_title ?> --</option>
                                <?php
                                $TbAttributeDetail = TbAttributeDetail::find()
                                    ->where(['tb_attribute_id' => $attributeinfo->id])
                                    ->orderBy(['id' => SORT_ASC])
                                    ->all();
                                foreach ($TbAttributeDetail as $detail) : ?>
                                    <option value="<?php echo $detail->id?>"><?php echo $detail->title; ?></option>
                                <?php endforeach;
                                echo "</select>";
                                }
                                }

                                if ($attributeinfo->is_multiselect==false)
                                {
                                if(sizeof($customerAttribute)>0){?>
                                <select required name='style[<?php echo $attributeinfo->id;?>]' class="form-control">
                                    <option value="">-- Please Select <?php echo $attributeinfo->tooltip_bar_title?> --</option>

                                    <?php foreach($customerAttribute as $attribute):
                                        $TbAttributeDetail = TbAttributeDetail::find()
                                            ->where(['tb_attribute_id'=>$attribute->attribute_id])
                                            ->orderBy(['id' => SORT_ASC])
                                            ->all();

                                        foreach($TbAttributeDetail as $detail)
                                        { ?>
                                            <option value="<?php echo $detail->id?>" <?php if($attribute->value == $detail->id){
                                                echo 'selected="selected"'; } ?>><?php echo $detail->title; ?></option>
                                        <?php }

                                    endforeach; //outer foreach
                                    echo "</select>";
                                    } else{  ?>
                                    <select required name='style[<?php echo $attributeinfo->id;?>]' class="form-control">
                                        <option value="">-- Please Select <?php echo $attributeinfo->tooltip_bar_title?> --</option>
                                        <?php $TbAttributeDetail = TbAttributeDetail::find()
                                            ->where(['tb_attribute_id'=>$attributeinfo->id])
                                            ->orderBy(['id' => SORT_ASC])
                                            ->all();
                                        foreach($TbAttributeDetail as $detail): ?>
                                            <option value="<?php echo $detail->id?>"><?php echo $detail->title; ?></option>
                                        <?php  endforeach;
                                        echo "</select>";
                                        }
                                        }

                                        echo"</div>";
                                        echo "</div>";
                                        }
                                        }
                                        }?>
                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-10">
                                                <button id="save_style" class="btn btn-success" type="submit"><i class="fa fa-check"></i> Save</button>
                                                <button id="discard_style" class="btn btn-danger" type="reset"><i class="fa fa-undo"></i> Revert</button>
                                            </div>
                                        </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Attributes</h4>
            </div>

            <div class="modal-body">
                <div id="Form">
                    <form id="attrForm" method="post" action="<?php echo Yii::$app->request->baseUrl; ?>/attribute/create" enctype="multipart/form-data">
                        <div id="default">
                            <input type="text" required id="title" placeholder="Title" name="title">
                            <input type="text" required id="tooltip_bar_title" placeholder="Tooltip Title" name="tooltip_bar_title">
                            <span class="wrap-checkbox">
                                <div>
                                    <input type="checkbox" id="is_multiselect" name="is_multiselect" >&nbsp; <span class="shift-text">Multiselect</span>
                                </div>
                            <div style="clear:both"></div>
                            <div>
                                <input type="checkbox" id="is_btn" name="is_btn" >&nbsp;<span class="shift-text"> Get Started Button</span>
                            </div>
                            </span>
                            <span class="icon">
                                <a id="submit" href="javascript:;">
                                    <i class="fa fa-plus"></i>
                                </a>
                            </span>
                        </div>
                        <div id="form1">

                            <input type="submit" onClick="return validate('#form1');" class="btn btn-success" value="Save" >
                            <a href="javascript:;" data-count="1" class="add_more">Add Options</a>
                        </div>
                        <div id="form2">

                            <input type="submit" onClick="return validate('#form2');" class="btn btn-success" value="Save" >
                            <a href="javascript:;" data-count="1" class="add_more">Add Option</a>
                        </div>
                    </form>
                </div>
                <table>
                    <thead>
                        <th>#</th>
                        <th>Title</th>
                        <th>Tooltip Title</th>
                        <th class="text-center">Multiselect</th>
                        <th class="text-center">Buttons</th>
                        <th class="text-center">Status</th>
                    </thead>
                    <tbody>
                    <?php
                    $attributes = Yii::$app->data->getAttributes();

                    foreach($attributes as $count=>$attribute)
                    {?>
                        <tr>
                            <td><?php echo $count+1; ?></td>
                            <td><?php echo str_replace('?',"",$attribute->title); ?></td>
                            <td><?php echo $attribute->tooltip_bar_title ?></td>
                            <td class="text-center"><span class="icon"><i class="fa fa-<?php echo $attribute->is_multiselect?'check':'remove'; ?>"></i></span></td>
                            <td class="text-center"><span class="icon"><i class="fa fa-<?php echo $attribute->is_btn?'check':'remove'; ?>"></i></span></td>
                            <td class="text-center"><span class="icon"><i class="fa fa-<?php echo $attribute->is_active?'check':'remove'; ?>"></i></span></td>
                            <td class="text-center"><a href="<?= Yii::$app->request->baseUrl.'/attribute/edit?id='.$attribute->id ?>"><span class="icon"><i class="fa fa-edit"></i></span></a></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<style>
    .error{
        border:1px solid #d00;
    }
</style>

<?php

$script = <<< JS
$(function(){
    jQuery( ".save_style" ).click(function() {
        var form = jQuery( "#style_form" );
        form.validate();
    });
}); 

function validate(form)
{
    var valid =true;
    if($(form).children().length > 2)
    {
        $(form + " input").each(function() {
            if(this.value == '') {
                $(this).addClass('error');        
                valid = false;
            }else{
                $(this).removeClass('error');
            }
        });
        if(valid == false)
            {
                return false;
            }else{
                return true;
            }
    }else{
        alert("Attribute requires atleast one Option.");
        return false;
    }
}

$('#modalUp').click(function(){
    $('#attrForm')[0].reset();
    $('#form1').hide();
    $('#form2').hide();
});

$('#is_multiselect').click(function(){
    if($('#is_multiselect').prop('checked')){
        $('#is_btn').prop('checked','checked');   
    }
     $('#form1').hide();
     $('#form2').hide();
});

$(document).on( 'click','.row > span > .fa.fa-remove', function(){
    var target =  $(this).parent().parent();
    target.hide('slow', function(){ target.remove(); });
}); 

$('#is_btn').click(function(){
    if($('#is_multiselect').prop('checked') && $('#is_btn').prop('checked','')){
        $('#is_multiselect').prop('checked','');
    }
     $('#form1').hide("slow");
     $('#form2').hide("slow");
});

$('.add_more').click(function(){
    var thiz = $(this).parent();
    var count = parseInt($(this).attr('data-count'));
    var html = '';
    if(thiz.is('#form1'))
    {
        addForm("#form1",count);
    }else if(thiz.is('#form2'))
    {
        addForm("#form2",count);
    }
});
function addForm(form,count)
{ var html ='';
    if(form == "#form1")
    {
        html += '<div class="row"><input type="text" class="required" placeholder="Option Title" name="f1[o'+count+'][title]">';
        html +='<input type="text" placeholder="Description" class="required" name="f1[o'+count+'][desc]"><input type="file" class="required" name="f1[o'+count+'][file]">';
        html +='<span class="icon"><i class="fa fa-remove"></i></span></div>';
        $('#form1 > .add_more').attr('data-count',count+1);
        $(html).insertBefore('#form1 > .btn').hide().show('slow');

    }else if(form == "#form2"){
        html +='<div class="row"><input type="text" class="required" placeholder="Option Title" name="f2[o'+count+'][title]">';
        html +='<input type="text" placeholder="Description" class="required" name="f2[o'+count+'][desc]">';
        html +='<span class="icon"><i class="fa fa-remove"></i></span></div>';
        $('#form2 > .add_more').attr('data-count',count+1);
        $(html).insertBefore('#form2 > .btn').hide().show('slow');
    }
}
$('#submit').click(function(){
        if($('#title').val() == '')
        {
            alert('Please fill the Title for attribute.');

        }else if($('#tooltip_bar_title').val() == '')
        {
            alert('Please fill the Tooltip for attribute.');

        }else if($('#is_btn').prop('checked') || $('#is_multiselect').prop('checked'))
        {
           
           $('#form1 .row').remove();
           addForm("#form1",1);
           $('#form1').show("slow");
        }else 
        {
            $('#form2 .row').remove()
            addForm("#form2",1);
            $('#form2').show("slow");
        }
    });
JS;
$this->registerJs($script, yii\web\View::POS_END);
