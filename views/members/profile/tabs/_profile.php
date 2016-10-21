<?php
/**
 * @var CustomerEntity $model
 */

use app\models\CustomerEntityStatus;
use app\models\CustomerEntity;
use app\models\CustomerEntityInfo;

?>

<div class="tab-pane" id="tab2">
    <div class="widget-box">
        <div class="widget-title">
            <span class="icon"><i class="fa fa-cog"></i></span>
            <h5>System Info</h5>
        </div>
        <div class="widget-content nopadding">
            <div class="row" style="margin-top: 0;">
                <div class="col-md-6 col-xs-12">
                    <form id="profileForm" class="form-horizontal" method="post" action="<?php echo '/members/update-profile?member='.$_GET['member']?>">
                        <div class="form-group">
                            <label class="control-label">Concierge Member ID</label>
                            <div class="controls">
                                <span><?php echo $model->id; ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Stripe Customer Id</label>
                            <div class="controls">
                                <span><?php echo ($model->customerSubscription?$model->customerSubscription->stripe_customer_id:'<i>none</i>'); ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Email</label>
                            <div class="controls">
                                <input type="text" name="Profile[email]" class="form-control" value="<?php echo ($customerInfo?$customerInfo->email:''); ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Phone</label>
                            <div class="controls">
                                <input type="text" name="Profile[phone]" class="form-control" value="<?php echo ($customerInfo?$customerInfo->phone:'');?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Password</label>
                            <div class="controls">
                                <input type="password" name="Profile[password]" class="form-control" />
                            </div>
                            <p class="help-block">Leave this field empty if you don't want to change the password.</p>
                        </div>
                        <div class="form-actions">
                            <button class="btn btn-success" type="submit">Save</button>
                            <button class="btn btn-danger" type="submit">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>