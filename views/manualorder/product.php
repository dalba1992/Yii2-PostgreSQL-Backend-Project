<?php 
use app\models\Product;
use app\models\Brand;
use app\models\Category;
use app\models\Productattribute;
use app\models\Productattributeoption;
use app\models\Productattributevalue;
use app\models\Variants;
use app\models\Variantsimage;

$Product = Yii::$app->tradeGeckoHelper->getInventory();

?>

<?php for($i=0; $i<=5; $i++){ ?>
		<td id="product_wrap_<?php echo $i?>">
		<?php if(sizeof($Product)>0){ ?>
				<div class="controls">
					<select class ="product" onchange="getproduct(<?php echo $i ?>);" style="width:100%;max-width:90%;">
						<option value="">--please select the product--</option>
						<?php foreach($Product as $products){ ?>
						<option value="<?php  echo $products->id ?>" cat=<?php  echo $products->category_id;?> ><?php echo $products->name; ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="control-group">
					<div class="controls">
						<select class="category" style="width:100%;max-width:90%;">
						<?php $Allcategory = Category::find()->all();?>
							<option value="0">Category</option>
							<?php foreach($Allcategory as $category){ ?>
							<option value="<?php echo $category->id ?>"><?php echo $category->category ?></option>
							<?php } ?>
							
						</select>
					</div>			
				</div>
				<center>
					<img id='product_image_<?php echo $i?>' width="130" alt="" src="<?php echo Yii::$app->homeUrl ?>demo/noimage.gif">
					<br><br>
					<a class="tip-top btn-small btn btn-danger" href="#" title="" data-original-title="Remove this Product"><i class="icon-trash icon-white"></i> Remove</a>
					<br><br>
					<p>$ <span id="product_price_<?php echo $i?>">00.00</span> MSRP</p>
				</center>
				<div class="control-group">
					<div class="controls">
						<select disabled ='true' required id="size_<?php echo $i?>" onchange="setcolor(<?php echo $i ?>);" style="width:100%;max-width:90%;">
							<option value="" >Size</option>
						</select>
					</div>
					<div class="controls">
						<select name="manualorder[variation_id][]" disabled = 'true' onchange="getinfo(<?php echo $i ?>);" required id="color_<?php echo $i?>" style="width:100%;max-width:90%;">
							<option value ="" >Color</option>							
						</select>
					</div>
					
				</div>
			<?php } else{ ?>
				No Product Found!
			<?php } ?>	
		</td>
<?php } ?>
<script type="text/javascript">

jQuery(document).ready(function(){
	var allVariants;
});
	function getproduct(count){
		globalcount = count;
		var prod_id = jQuery("#product_wrap_"+count+" .product").find('option:selected').val();
		if(prod_id!=''){
			var category = jQuery("#product_wrap_"+count+" .product").find('option:selected').attr('cat');
			 jQuery("#product_wrap_"+count+" .category").val(category);
			 jQuery("#color_"+count).prop("disabled", true);
			 jQuery("#color_"+count).html('<option value=""> Color </option>');
			
			 var customer_size = jQuery('.size_title').text().trim();
			 var trigger = 0;
			 jQuery.ajax({
						type: 'POST',
			            url: '<?php echo Yii::$app->homeUrl."manualorder/getvariation/"?>',
			            data: { product_id: prod_id },		            
					})
					.success (function(result){
						allVariants = result;
						var option = '<option value=""> Size </option>';
						var result = $.parseJSON(result);
						$.each(result, function(k, v) {
							$.each(result[k], function(size, value) {
								//alert(customer_size+'<-size->'+size);
							if(size == customer_size){
								option = option+'<option attribute='+k+' value='+size+' selected>'+size+'</option>';
								trigger = 1;
							}else{
								option = option+'<option attribute='+k+' value='+size+'>'+size+'</option>';
							}
									//option = option+'<option attribute='+k+' value='+size+' selected="selected">'+size+'</option>';
									//setcolor(count);
								
									
									
								
								
							});
							return false;
						});	
						
						jQuery('#size_'+count).html(option);
						if(trigger == 1){
							setcolor(count);
						}
						jQuery("#size_"+count).prop("disabled", false);	
											
					});
		}else{
			 jQuery("#product_wrap_"+count+" .category").val('0');
		}	
	}
	

	function setcolor(globalcount){
		
		//alert('globalcount->'+globalcount);
		jQuery("#color_"+globalcount).prop("disabled", false);
		var size = jQuery("#size_"+globalcount).find('option:selected').val();
		var attribute = jQuery("#size_"+globalcount).find('option:selected').attr('attribute');	
		var coloroption = '<option value=""> Color </option>';	
		var result = $.parseJSON(allVariants);
		$.each(result[attribute][size], function(k, v) {
			var size_variant_id = v;
			$.each(result[2], function(color, value) {
				$.each(result[2][color], function(i,color_variant_id){ 
					if(color_variant_id == size_variant_id){
						coloroption = coloroption+'<option value='+color_variant_id+'>'+color+'</option>';
					}
				});
			});			
		}); 
		
		jQuery('#color_'+globalcount).html(coloroption);
		
	} 
	
	function getinfo(id){
		var varinat_id = jQuery('#color_'+id).find('option:selected').val();
		jQuery.ajax({
				type: 'POST',
				url: '<?php echo Yii::$app->homeUrl."manualorder/changeprice/"?>',
				data: { id: varinat_id },
				dataType:'json',
		})
		.success (function(result){
			jQuery("#product_wrap_"+id+" #product_price_"+id ).html(result.price);
			jQuery("#product_wrap_"+id+" #product_image_"+id ).attr('src',result.img_url);
		});
	}
</script>	