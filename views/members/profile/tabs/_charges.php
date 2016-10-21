<?php
/**
 * @var CustomerSubscription $subscription
 */

use app\models\CustomerEntityAddress;
use app\models\CustomerSubscription;

use Stripe\Stripe;
use Stripe\Charge as Stripe_Charge;

$subscription = $model->customerSubscription;

$stripeSecret_key = Yii::$app->params['stripe']['stripesecretkey'];
Stripe::setApiKey($stripeSecret_key);
//$all_customer = Stripe_Customer::all();
$charges = Stripe_Charge::all();

$charge_data = [];
if ($subscription)
{
    foreach($charges->data as $charge)
    {
        if ($subscription->stripe_customer_id == $charge->customer)
            $charge_data[] = $charge;
    }
}

?>

<!--Stripe Customer Info-->
<div class="tab-pane" id="tab-charge">
    <div class="row" style="margin-top: 0;">
        <div class="widget-box">
            <div class="widget-title">
                <h5>Charges</h5>
            </div>
            <div class="widget-content nopadding">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th>Charge ID</th>
                        <th>Created Date</th>
                        <th>Status</th>
                        <th>Amount</th>
                        <th>Currency</th>
                        <th>Invoice</th>
                        <th>Receipt Email</th>
                        <th>Receipt Number</th>
                        <th>Description</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (count($charge_data) > 0) { ?>
                        <?php foreach ($charge_data as $charge) { ?>
                            <tr>
                                <td><?php echo $charge->id; ?></td>
                                <td><?php echo gmdate("m/d/Y", $charge->created); ?></td>
                                <td><?php echo $charge->status; if ($charge->failure_message != "") echo ' ('.$charge->failure_message.')'; ?></td>
                                <td><?php echo $charge->amount; ?></td>
                                <td><?php echo $charge->currency; ?></td>
                                <td><?php echo $charge->invoice; ?></td>
                                <td><?php echo $charge->receipt_email; ?></td>
                                <td><?php echo $charge->receipt_number; ?></td>
                                <td><?php echo $charge->description; ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else {?>
                        <tr>
                            <td colspan='10' style="text-align:center;">No Charges</td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
