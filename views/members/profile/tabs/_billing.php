<?php
use app\models\CustomerEntityAddress;
$customerbill = CustomerEntityAddress::find()->where(['customer_id'=>$_GET['member'],'type_id'=>1])->one(); ?>

    <!--Billing Info-->
    <div id="tab3" class="tab-pane">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fa fa-shopping-cart"></i></span>
                <h5>Billing Info</h5>
            </div>
            <?php if(count($customerbill)>0){?>
                <div class="widget-content nopadding">
                    <form id="BbllingForm" class="form-horizontal" method="post" action="<?php echo Yii::$app->homeUrl.'/members/update-billing?member='.$_GET['member']?>">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">First Name</label>
                            <div class="col-sm-10">
                                <input type="text" value="<?php echo $customerbill->first_name;?>" name="billing[first_name]" class="form-control" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Last Name</label>
                            <div class="col-sm-10">
                                <input type="text" value="<?php echo $customerbill->last_name;?>" name="billing[last_name]" class="form-control" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Address 1</label>
                            <div class="col-sm-10">
                                <input type="text" value="<?php echo $customerbill->address;?>" name="billing[address]" class="form-control" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Address 2</label>
                            <div class="col-sm-10">
                                <input type="text" value="<?php echo $customerbill->address2;?>" name="billing[address2]" class="form-control" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">City</label>
                            <div class="col-sm-10">
                                <input type="text" value="<?php echo $customerbill->city;?>" name="billing[city]" class="form-control" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">State</label>
                            <div class="col-sm-10">
                                <select  id="billing_state" class="custom_select form-control" name="billing[state]" required>
                                    <option value="">State</option>
                                    <option value="AL" <?php if($customerbill->state =='AL'){?> selected="selected" <?php }?>>AL</option>
                                    <option value="AK" <?php if($customerbill->state =='AK'){?> selected="selected" <?php }?>>AK</option>
                                    <option value="AZ" <?php if($customerbill->state =='AZ'){?> selected="selected" <?php }?>>AZ</option>
                                    <option value="AR" <?php if($customerbill->state =='AR'){?> selected="selected" <?php }?>>AR</option>
                                    <option value="CA" <?php if($customerbill->state =='CA'){?> selected="selected" <?php }?>>CA</option>
                                    <option value="CO" <?php if($customerbill->state =='CO'){?> selected="selected" <?php }?>>CO</option>
                                    <option value="CT" <?php if($customerbill->state =='CT'){?> selected="selected" <?php }?>>CT</option>
                                    <option value="DE" <?php if($customerbill->state =='DE'){?> selected="selected" <?php }?>>DE</option>
                                    <option value="FL" <?php if($customerbill->state =='FL'){?> selected="selected" <?php }?>>FL</option>
                                    <option value="GA" <?php if($customerbill->state =='GA'){?> selected="selected" <?php }?>>GA</option>
                                    <option value="HI" <?php if($customerbill->state =='HI'){?> selected="selected" <?php }?>>HI</option>
                                    <option value="ID" <?php if($customerbill->state =='ID'){?> selected="selected" <?php }?>>ID</option>
                                    <option value="IL" <?php if($customerbill->state =='IL'){?> selected="selected" <?php }?>>IL</option>
                                    <option value="IN" <?php if($customerbill->state =='IN'){?> selected="selected" <?php }?>>IN</option>
                                    <option value="IA" <?php if($customerbill->state =='IA'){?> selected="selected" <?php }?>>IA</option>
                                    <option value="KS" <?php if($customerbill->state =='KS'){?> selected="selected" <?php }?>>KS</option>
                                    <option value="KY" <?php if($customerbill->state =='KY'){?> selected="selected" <?php }?>>KY</option>
                                    <option value="LA" <?php if($customerbill->state =='LA'){?> selected="selected" <?php }?>>LA</option>
                                    <option value="ME" <?php if($customerbill->state =='ME'){?> selected="selected" <?php }?>>ME</option>
                                    <option value="MD" <?php if($customerbill->state =='MD'){?> selected="selected" <?php }?>>MD</option>
                                    <option value="MA" <?php if($customerbill->state =='MA'){?> selected="selected" <?php }?>>MA</option>
                                    <option value="MI" <?php if($customerbill->state =='MI'){?> selected="selected" <?php }?>>MI</option>
                                    <option value="MN" <?php if($customerbill->state =='MN'){?> selected="selected" <?php }?>>MN</option>
                                    <option value="MS" <?php if($customerbill->state =='MS'){?> selected="selected" <?php }?>>MS</option>
                                    <option value="MO" <?php if($customerbill->state =='MO'){?> selected="selected" <?php }?>>MO</option>
                                    <option value="MT" <?php if($customerbill->state =='MT'){?> selected="selected" <?php }?>>MT</option>
                                    <option value="NE" <?php if($customerbill->state =='NE'){?> selected="selected" <?php }?>>NE</option>
                                    <option value="NV" <?php if($customerbill->state =='NV'){?> selected="selected" <?php }?>>NV</option>
                                    <option value="NH" <?php if($customerbill->state =='NH'){?> selected="selected" <?php }?>>NH</option>
                                    <option value="NJ" <?php if($customerbill->state =='NJ'){?> selected="selected" <?php }?>>NJ</option>
                                    <option value="NM" <?php if($customerbill->state =='NM'){?> selected="selected" <?php }?>>NM</option>
                                    <option value="NY" <?php if($customerbill->state =='NY'){?> selected="selected" <?php }?>>NY</option>
                                    <option value="NC" <?php if($customerbill->state =='NC'){?> selected="selected" <?php }?>>NC</option>
                                    <option value="ND" <?php if($customerbill->state =='ND'){?> selected="selected" <?php }?>>ND</option>
                                    <option value="OH" <?php if($customerbill->state =='OH'){?> selected="selected" <?php }?>>OH</option>
                                    <option value="OK" <?php if($customerbill->state =='OK'){?> selected="selected" <?php }?>>OK</option>
                                    <option value="OR" <?php if($customerbill->state =='OR'){?> selected="selected" <?php }?>>OR</option>
                                    <option value="PA" <?php if($customerbill->state =='PA'){?> selected="selected" <?php }?>>PA</option>
                                    <option value="RI" <?php if($customerbill->state =='RI'){?> selected="selected" <?php }?>>RI</option>
                                    <option value="SC" <?php if($customerbill->state =='SC'){?> selected="selected" <?php }?>>SC</option>
                                    <option value="SD" <?php if($customerbill->state =='SD'){?> selected="selected" <?php }?>>SD</option>
                                    <option value="TN" <?php if($customerbill->state =='TN'){?> selected="selected" <?php }?>>TN</option>
                                    <option value="TX" <?php if($customerbill->state =='TX'){?> selected="selected" <?php }?>>TX</option>
                                    <option value="UT" <?php if($customerbill->state =='UT'){?> selected="selected" <?php }?>>UT</option>
                                    <option value="VT" <?php if($customerbill->state =='VT'){?> selected="selected" <?php }?>>VT</option>
                                    <option value="VA" <?php if($customerbill->state =='VA'){?> selected="selected" <?php }?>>VA</option>
                                    <option value="WA" <?php if($customerbill->state =='WA'){?> selected="selected" <?php }?>>WA</option>
                                    <option value="WV" <?php if($customerbill->state =='WV'){?> selected="selected" <?php }?>>WV</option>
                                    <option value="WI" <?php if($customerbill->state =='WI'){?> selected="selected" <?php }?>>WI</option>
                                    <option value="WY" <?php if($customerbill->state =='WY'){?> selected="selected" <?php }?>>WY</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Country</label>
                            <div class="col-sm-10">
                                <select id="billing_country" name="billing[country]" class="custom_select_country form-control" required>
                                    <option value="">Country</option>
                                    <option value="US" <?php if($customerbill->country =='US'){?> selected="selected" <?php }?>>USA</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Zip Code</label>
                            <div class="col-sm-10">
                                <input type="text" value="<?php echo $customerbill->zipcode;?>" name="billing[zipcode]" class="form-control" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button class="btn btn-success btn-lg" id="billing_submit" type="submit" name="submit">Save Billing Changes</button>
                                <button type="submit" class="btn btn-danger btn-lg">Discard Billing Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            <?php } ?>
        </div>
    </div>

<?php
$script = <<< JS
$(function(){
    jQuery( ".billing_submit" ).click(function() {
        var form = jQuery( "#billing_form" );
        form.validate();
    });
});
JS;
$this->registerJs($script, yii\web\View::POS_END);