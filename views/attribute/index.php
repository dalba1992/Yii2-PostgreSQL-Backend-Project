<div class="row">
    <div class="col-md-12">
        <?php if (Yii::$app->session->hasFlash('success')){ ?>
            <div class="alert alert-success alert-dismissible">
                <?php echo Yii::$app->session->getFlash('success'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        <?php }else if(Yii::$app->session->hasFlash('error')){ ?>
            <div class="alert alert-error alert-dismissible">
                <?php echo Yii::$app->session->getFlash('error'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        <?php } ?>
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fa fa-star"></i>
                </span>
                <h5>New attribute</h5>
            </div>
            <div class="widget-content nopadding">
                <div id="Form">
                    <form id="attrForm" method="post" action="<?php echo Yii::$app->request->baseUrl; ?>/attribute/create" class="form-inline" enctype="multipart/form-data">
                        <div id="default">
                            <div class="form-group">
                                <label class="sr-only" for="title">Title</label>
                                <input type="text" class="form-control" required id="title" placeholder="Title" name="title">
                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="tooltip_bar_title">Tooltip Title</label>
                                <input type="text" required id="tooltip_bar_title" class="form-control" placeholder="Tooltip Title" name="tooltip_bar_title">
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="is_multiselect" name="is_multiselect" > Multiselect
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="is_btn" name="is_btn" > Get Started Button
                                </label>
                            </div>
                            <a href="javascript:;" class="btn btn-success" style="margin-left: 15px;" id="submit"><i class="fa fa-plus"></i></a>
                        </div>
                        <div id="form1">
                            <button type="submit" onclick="return validate('#form1');" class="btn btn-success" style="margin-left: 10px; margin-top: 10px; margin-bottom: 10px">Save</button>
                            <a href="javascript:;" data-count="1" class="add_more text-info">Add Options</a>
                        </div>
                        <div id="form2">
                            <input type="submit" onClick="return validate('#form2');" class="btn btn-success" style="margin-left: 10px; margin-top: 10px; margin-bottom: 10px" value="Save" >
                            <a href="javascript:;" data-count="1" class="add_more text-info">Add Option</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="widget-box">

            <div class="widget-title">
                <span class="icon">
                    <i class="fa fa-star"></i>
                </span>
                <h5>Style attributes</h5>
            </div>

            <div class="widget-content nopadding">

                <table class="table">
                    <thead>
                        <th>#</th>
                        <th>Title</th>
                        <th>Tooltip Title</th>
                        <th class="text-center">Multiselect</th>
                        <th class="text-center">Buttons</th>
                        <th class="text-center">Status</th>
                        <th></th>
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

        </div>
    </div>
</div>

<?php

$script = <<<JAVASCRIPT
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
        html += '<div class="row"><div class="form-group"><input type="text" class="required form-control" placeholder="Option Title" name="f1[o'+count+'][title]"></div>';
        html +='<div class="form-group"><input type="text" placeholder="Description" class="required form-control" name="f1[o'+count+'][desc]"></div><div class="form-group"><input type="file" class="required form-control" name="f1[o'+count+'][file]"></div>';
        html +='<span class="icon"><i class="fa fa-remove"></i></span></div>';
        $('#form1 > .add_more').attr('data-count',count+1);
        $(html).insertBefore('#form1 > .btn').hide().show('slow');

    }else if(form == "#form2"){
        html +='<div class="row"><div class="form-group"><input type="text" class="required form-control" placeholder="Option Title" name="f2[o'+count+'][title]"></div>';
        html +='<div class="form-group"><input type="text" placeholder="Description" class="required form-control" name="f2[o'+count+'][desc]"></div>';
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
JAVASCRIPT;
$this->registerJs($script, yii\web\View::POS_END);