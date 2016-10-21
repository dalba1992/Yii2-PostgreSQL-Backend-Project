<?php
/**
 * @var CustomerSubscription $subscription
 */

use yii\helpers\Html;

use app\models\CustomerEntityAddress;
use app\models\CustomerSubscription;

$subscription = $model->customerSubscription;
?>

<!--Stripe Customer Info-->
<div class="tab-pane" id="tab-stripe">
    <?php if($subscription){ ?>
        <div class="row" style="margin-top: 0;">
            <div class="col-md-4 col-xs-12">
                <strong>Customer</strong>&nbsp;<?php echo ($subscription->stripe_customer_id?$subscription->stripe_customer_id:'NULL'); ?>
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Property</th>
                            <th>Value</th>
                        </tr>
                    </thead>

                    <tfoot>
                    <tr><th>&nbsp;</th><th>&nbsp;</th></tr>
                    </tfoot>
                </table>
            </div>

            <div class="col-md-4 col-xs-12">
                <strong>Subscription</strong>&nbsp;<?php echo ($subscription->stripe_subscription_id?$subscription->stripe_subscription_id:'NULL'); ?>
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th>Property</th>
                        <th>Value</th>
                    </tr>
                    </thead>

                    <tfoot>
                    <tr><th>&nbsp;</th><th>&nbsp;</th></tr>
                    </tfoot>
                </table>
            </div>

            <div class="col-md-4 col-xs-12">
                <strong>Plan</strong>&nbsp;<?php echo ($subscription->stripe_plan_id?$subscription->stripe_plan_id:'NULL'); ?>
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th>Property</th>
                        <th>Value</th>
                    </tr>
                    </thead>

                    <tfoot>
                    <tr><th>&nbsp;</th><th>&nbsp;</th></tr>
                    </tfoot>
                </table>
            </div>

            <div class="col-md-12">
                <?php
                if($subscription->stripe_customer_id)
                    echo Html::a('Sync', ['services/stripe/sync', 'customerId'=>$subscription->stripe_customer_id], ['class'=>'btn btn-default btn-sm']);
                ?>
            </div>
        </div>
    <?php }else{ ?>
        <div class="alert alert-info">
            This members has no subscription!
        </div>

    <?php }?>
</div>