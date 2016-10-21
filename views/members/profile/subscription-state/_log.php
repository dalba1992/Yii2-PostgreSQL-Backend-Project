<?php
/**
 * @var CustomerSubscriptionStatusLog $model
 * @var CustomerEntity $member
 */

use yii\helpers\Html;

use app\components;
use app\models\User;
use app\models\CustomerSubscriptionState;
use app\models\CustomerSubscriptionLog;

$user = User::findIdentity($model->user_id);
$member = $model->customerSubscription->customer;

$date = DateTime::createFromFormat('Y-m-d H:i:s', $model->date);
$time = $date->getTimestamp();
?>

<a href="javascript:;">
    <i class="fa <?php echo ($user?'fa-user':'fa-gear'); ?>"></i>
    <?php if($model->type == CustomerSubscriptionLog::TYPE_REQUEST){ ?>

        <strong><?php echo ($user?$user->username:'System'); ?></strong> requested:&nbsp;
                                                                         "<strong><?php echo ucfirst($model->status); ?></strong>&nbsp;
        <u><?php echo $member->customerEntityAddress->first_name.' '.$member->customerEntityAddress->last_name; ?></u>&nbsp;
        <span>for</span>&nbsp;
        <strong><?php echo date('F d, Y', $time); ?></strong>&nbsp;
        <span>because</span>&nbsp;
        <strong><?php echo CustomerSubscriptionState::$reasonOptions[$model->reason]; ?></strong>&nbsp;"
        <span><?php echo Yii::$app->date->elapsedTimeString($model->created_at); ?></span>
        <?php if($index == 0){ ?>
            &nbsp;&nbsp;<button class="btn btn-danger btn-mini cancel-state-update" data-id="<?php echo  $model->customerSubscription->customerSubscriptionState->id; ?>">Cancel</button>
        <?php }?>
        <?php if($model->note){?>
            <p><strong>Note: </strong><?php echo $model->note; ?></p>
        <?php }?>

    <?php }elseif($model->type == CustomerSubscriptionLog::TYPE_SET){ ?>

        <strong><?php echo ($user?$user->username:'System'); ?></strong> set:&nbsp;
                                                                         "<strong><?php echo CustomerSubscriptionLog::$actionSetLabels[$model->status]; ?></strong>&nbsp;
        <u><?php echo $member->customerEntityAddress->first_name.' '.$member->customerEntityAddress->last_name; ?></u>&nbsp;
        <span>for</span>&nbsp;
        <strong><?php echo date('F d, Y', $time); ?></strong>&nbsp;
        <span>because</span>&nbsp;
        <strong><?php echo CustomerSubscriptionState::$reasonOptions[$model->reason]; ?></strong>&nbsp;"
        <span><?php echo Yii::$app->date->elapsedTimeString($model->created_at); ?></span>
        <?php if($model->note){?>
            <p><strong>Note: </strong><?php echo $model->note; ?></p>
        <?php }?>

    <?php }elseif($model->type == CustomerSubscriptionLog::TYPE_CANCEL){ ?>

        <strong><?php echo ($user?$user->username:'System'); ?></strong> canceled:&nbsp;
                                                                         "<strong><?php echo ucfirst($model->status); ?></strong>&nbsp;
        <u><?php echo $member->customerEntityAddress->first_name.' '.$member->customerEntityAddress->last_name; ?></u>&nbsp;
        <span>for</span>&nbsp;
        <strong><?php echo date('F d, Y', $time); ?></strong>&nbsp;
        <span>because</span>&nbsp;
        <strong><?php echo CustomerSubscriptionState::$reasonOptions[$model->reason]; ?></strong>&nbsp;"
        <span><?php echo Yii::$app->date->elapsedTimeString($model->created_at); ?></span>
        <?php if($model->note){?>
            <p><strong>Note: </strong><?php echo $model->note; ?></p>
        <?php }?>

    <?php } ?>
</a>