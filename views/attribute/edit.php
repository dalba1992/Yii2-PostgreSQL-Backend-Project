<div class="row">
    <div class="col-md-12">
        <div class="widget-box">
            <?php if (Yii::$app->session->hasFlash('success')){ ?>

                <div class="alert alert-success">
                    <?php echo Yii::$app->session->getFlash('success'); ?>
                </div>
            <?php }else if(Yii::$app->session->hasFlash('error')){ ?>
                <div class="alert alert-error">
                    <?php echo Yii::$app->session->getFlash('error'); ?>
                </div>
            <?php } ?>

            <div class="widget-title">
                <span class="icon">
                    <i class="fa fa-star"></i>
                </span>
                <h5>Editing - <strong><?php if(count($attribute)>0){ echo $attribute->title;  } ?></strong></h5>
                <label class="style_attribute">
                    <form onsubmit="return validateMyForm()" id="delete_attribute_form" action="<?php echo Yii::$app->request->BaseUrl.'/attribute/deleteattribute?id='.$attribute->id ?>" method="post" name="delete_attribute">
                        <button class="btn btn-danger btn-sm delete_btn" type="submit" name="delete_attribute" value="Delete">Delete</button>
                    </form>
                </label>

            </div>

            <div class="widget-content nopadding">
                <?php if(count($attribute)>0){ ?>
                    <form id="update_form" action="<?= Yii::$app->request->baseUrl ?>/attribute/save?id=<?= $attribute->id ?>" enctype="multipart/form-data" class="form-horizontal" method="post" >
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Status</label>
                            <div class="col-sm-10">
                                <?php if($attribute->is_active){ ?>
                                    <span class="enabled_btn" name="enabled_attribute">
							<span class="button_label"> Active </span>
						</span>
                                <?php }else{ ?>
                                    <span class="disabled_btn" name="disabled_attribute">
							<span class="button_label"> In Active </span>
						</span>
                                <?php }?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Attribute Title <em>*</em></label>
                            <div class="col-sm-10">
                                <input required name="title" type="text" class="form-control" value="<?= $attribute->title ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Tooltip Title <em>*</em></label>
                            <div class="col-sm-10">
                                <input required name="tooltip_bar_title" type="text" class="form-control" value="<?= $attribute->tooltip_bar_title ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <input id="is_active" name="is_multiselect" type="checkbox"  <?= $attribute->is_multiselect?"checked":''; ?> >
                                <label class="control-label">Allow multiselect</label>
                            </div>
                            <div class="col-sm-offset-2 col-sm-10">
                                <input id="is_active" name="is_btn" type="checkbox"  <?= $attribute->is_btn?"checked":''; ?> >
                                <label class="control-label">Get Started</label>
                            </div>
                            <div class="col-sm-offset-2 col-sm-10">
                                <input id="is_active" name="is_active" type="checkbox"  <?= $attribute->is_active?"checked":''; ?> >
                                <label class="control-label">Active</label>
                            </div>
                        </div>

                        <?php
                        $i=1;
                        foreach($attributeDetail as $detail): ?>
                            <div class="form-group" id="div_<?= $detail->id ?>">
                                <input type="hidden" name="form[o<?= $i ?>][attr_id]" value="<?= $detail->id ?>" >
                                <label class="col-xs-12 col-sm-2 control-label">Option <?= $i ?><em>*</em></label>
                                <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <input required type="text" placeholder="Title" value="<?= $detail->title ?>" class="form-control" name="form[o<?= $i ?>][title]">
                                </div>
                                <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <input type="text" placeholder="Description" value="<?= $detail->description ?>" class="form-control" name="form[o<?= $i ?>][desc]">
                                </div>
                                <?php
                                //if($detail->is_image){ 
				?>
                                    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2" >
                                        <label>
                                            <input type="checkbox" name="form[o<?= $i ?>][is_image]" value="1" data-id="<?= $i ?>" class="is_image" <?php echo $detail->is_image ? 'checked="checked"' : '' ?> /> Is image?
                                        </label>
                                        <div id="image<?= $i ?>" <?php echo $detail->is_image ? 'style="display: block"' : 'style="display: none"'?>>
                                <?php
				if($detail->id){
				?>
                    <?php if(isset($attributeImages[$detail->id]) && isset($attributeImages[$detail->id]['image'])) { ?>
				        <img height="100px" width="90px"  src="<?= Yii::$app->request->baseUrl.'/data/static/'.Yii::$app->params['orderStepsPath'].$attributeImages[$detail->id]['image']; ?>" name="form[o<?= $i ?>][img]" >
                                        <input type="hidden" name="form[o<?= $i ?>][image]" value="<?= $srcUrl.$attributeImages[$detail->id]['image'] ?>">

                    <?php } ?>
                        <?php
				}
				?>    	
					<input type="file" name="form[o<?= $i ?>][file]" >
                                        </div>
				    </div>
                                <?php
                                //}
                                ?>

                                <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
                                    <input  name="form[o<?= $i ?>][is_active]" <?= $detail->is_active?'':"checked"; ?> type="checkbox" >
                                    <label class="control-label">Disable</label>
                                    <label class="del_attr_style"><a class="del_link" href="javascript:;" onclick="style_delete('<?php echo $detail->id ?>')">Delete</a></label>
                                </div>
                            </div>
                        <?php
                        $i++;
                        endforeach;
                        ?>

                        <div class="form-group" id="btn-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <a  class="btn btn-info add_more" data-img="<?= $FormType ?>" data-count="<?= $i ?>" ><i class="fa fa-plus"></i> Option</a>
                                <button type="submit" class="btn btn-success" id="save_style"><i class="fa fa-check"></i> Save</button>
                                <button type="reset" class="btn btn-danger" id="discard_style" onclick="document.getElementById('update_form').reset();" ><i class="fa fa-undo"></i> Revert</button>
                            </div>
                        </div>
                    </form>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<style>
    input[type=file]{
        color:transparent;
    }
</style>

<?php
$script = <<< JS

function readURL(input) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $(input).siblings('img').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

$(document).on( 'change','input[type=file]', function(){
  readURL(this);  
}); 

$(document).on( 'click','.remove', function(){
    var target =  $(this).parent().parent();
    target.hide('slow', function(){ target.remove(); });
}); 

$('.add_more').click(function(){
    var thiz = $("#update_form");
    var count = parseInt($(this).attr('data-count'));
    var is_img = $(this).attr('data-img');
    var html = '';
    addForm(is_img,count);
});

$(document).on('change', '.is_image', function() {
    var self = $(this);
    var id = self.attr('data-id');
    var image = $("#image" + id);

    if(self.is(':checked')) {
        image.show();
    } else {
        image.hide();
    }
})

function addForm(form,count){
	var html ='';
    if(form == "form1"){
      	html +='<div class="form-group"><label class="col-xs-12 col-sm-2 control-label">New Option <em>*</em></label><div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">';
        html +='<input required type="text" name="form[o'+count+'][title]" class="form-control" placeholder="Title"></div><div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">';
        html +='<input type="text" name="form[o'+count+'][desc]" class="form-control" placeholder="Description"></div><div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">';
        html +='<label><input type="checkbox" name="form[o'+count+'][is_image]" data-id="'+count+'" value="1" class="is_image" /> Is image?</label>';
        html +='<div id="image'+count+'" style="display: none"><img width="90px" height="100px" name="form[o'+count+'][img]" alt="Add Image" src=""><input type="file" name="form[o'+count+'][file]""></div></div>';
        html +=' <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2"><a href="javascript:;" class="remove">Remove</a></div></div>';
    }else if(form == "form2"){
      html +='<div class="form-group"><label class="col-xs-12 col-sm-2 control-label">New Option <em>*</em></label>';
      html +='<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3"><input type="text" required name="form[o'+count+'][title]" class="form-control" placeholder="Title"></div>';
      html +='<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3"><input type="text" name="form[o'+count+'][desc]" class="form-control" placeholder="Description"></div>';
      html +=' <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2"><a href="javascript:;" class="remove">Remove</a></div></div>';
    }
     $('.add_more').attr('data-count',count+1);
     $(html).insertBefore('#btn-group').hide().show('slow');

}

JS;

$this->registerJs($script, yii\web\View::POS_END);
?>

<style type="text/css">
    label.attribute{
        font-size: 16px;
        line-height: 36px;
        padding-left: 211px;
    }
    .style_attribute {
        float: right;
        margin-right: 16px;
    }
    /*.style_attribute .delete_btn{*/
        /*background-image:linear-gradient(to bottom, #ee5f5b, #bd362f);*/
        /*border-radius: 7px;*/
        /*padding: 5px;*/
    /*}*/
    .enabled_btn{
        background-image:linear-gradient(to bottom, #62c462, #51a351);
        border-radius: 7px;
        padding: 5px;
    }
    .disabled_btn{
        background-color: #f89406;
        border-radius: 7px;
        padding: 5px;
    }
    /*.style_attribute .delete_btn .button_label {*/
        /*color: #fff;*/
        /*padding: 5px;*/
    /*}*/
    .enabled_btn .button_label{
        color: #fff;
        padding: 5px;
    }
    .disabled_btn .button_label{
        color: #fff;
        padding: 5px;
    }
    .checked_attribute .fa-check{
        color:green;
    }
</style>
<script type="text/javascript">

    function style_delete(attribute_detail_id){
        $.ajax({
            method: "POST",
            url: "<?php echo Yii::$app->request->baseUrl.'/attribute/deletestyle/'?>",
            data: { id: attribute_detail_id }
        }).success (function(result){
            $('#'+result).fadeOut(300, function(){ $(this).remove();});
        });
    }

    function validateMyForm(){
        var x = confirm("Are you sure you want to delete?");
        if (x)
            return true;
        else
            return false;
    }
</script>
