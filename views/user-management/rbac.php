<?php

/**
 * @var \yii\web\View $this
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var app\models\CustomerEntity $searchModel
 */

use yii\helpers\Html;
use yii\grid\GridView;
use app\components\Helper;
use yii\widgets\LinkPager;

$this->title = 'Role Based Access Control';
?>
<div id="content-header">
    <h1><?php echo $this->title; ?></h1>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="widget-box">

            <div class="widget-title">
                <span class="icon">
                    <i class="fa fa-wrench"></i>
                </span>
                <h5><?php echo $this->title; ?></h5>                
            </div>

            <div class="widget-content ">
                <div class="dataTables_wrapper" style="margin-left:auto; margin-right:auto; width:80%">
                    <h4> Roles </h4>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th colspan='3' style="text-align:right;">
                                    <a class="btn btn-info btn-xs" href="<?php echo Yii::$app->homeUrl; ?>user-management/create-role" style="color:#fff;">Create a new role</a>
                                </th>
                            </tr>
                            <tr>
                                <th style="width:30%">Name</th>
                                <th style="width:50%">Description</th>
                                <th style="width:20%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($roles) == 0) { ?>
                                <tr>
                                    <td colspan='3'>No Result Found</td>
                                </tr>
                            <?php } else { ?>
                                <?php foreach($roles as $role) { ?>
                                <tr>
                                    <td><?php echo $role->name; ?></td>
                                    <td><?php echo $role->description; ?></td>
                                    <td style="text-align:center">
                                        <?php if ($role->name != Yii::$app->params['defaultRole'] ) { ?>
                                        <a class="btn btn-success btn-xs" href="<?php echo Yii::$app->homeUrl; ?>user-management/edit-role?name=<?php echo $role->name; ?>" style="color:#fff;">Edit</a>
                                        <a class="btn btn-success btn-xs" href="<?php echo Yii::$app->homeUrl; ?>user-management/assign-operations?name=<?php echo $role->name; ?>" style="color:#fff;">Assign</a>
                                        <a class="btn btn-success btn-xs" href="<?php echo Yii::$app->homeUrl; ?>user-management/remove-role?name=<?php echo $role->name; ?>" style="color:#fff;" onclick="return window.confirm('Are you sure?');">Remove</a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="dataTables_wrapper" style="margin-left:auto; margin-right:auto; width:80%">
                    <h4> Tasks </h4>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th colspan='3' style="text-align:right;">
                                    <a class="btn btn-info btn-xs" href="<?php echo Yii::$app->homeUrl; ?>user-management/create-task" style="color:#fff;">Create a new task</a>
                                </th>
                            </tr>
                            <tr>
                                <th style="width:30%">Name</th>
                                <th style="width:55%">Description</th>
                                <th style="width:15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($tasks) == 0) { ?>
                                <tr>
                                    <td colspan='3'>No Result Found</td>
                                </tr>
                            <?php } else { ?>
                                <?php foreach($tasks as $task) { ?>
                                <tr>
                                    <td><?php echo $task->name; ?></td>
                                    <td><?php echo $task->description; ?></td>
                                    <td style="text-align:center">
                                        <a class="btn btn-success btn-xs" href="<?php echo Yii::$app->homeUrl; ?>user-management/edit-task?name=<?php echo $task->name; ?>" style="color:#fff;">Edit</a>
                                        <a class="btn btn-success btn-xs" href="<?php echo Yii::$app->homeUrl; ?>user-management/remove-task?name=<?php echo $task->name; ?>" style="color:#fff;" onclick="return window.confirm('Are you sure?');">Remove</a>
                                    </td>
                                </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="dataTables_wrapper" style="margin-left:auto; margin-right:auto; width:80%">
                    <h4> Operations </h4>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th colspan='3' style="text-align:right;">
                                    <a class="btn btn-info btn-xs" href="<?php echo Yii::$app->homeUrl; ?>user-management/create-operation" style="color:#fff;">Create a new operation</a>
                                </th>
                            </tr>
                            <tr>
                                <th style="width:30%">Name</th>
                                <th style="width:55%">Description</th>
                                <th style="width:15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($operations) == 0) { ?>
                                <tr>
                                    <td colspan='3'>No Result Found</td>
                                </tr>
                            <?php } else { ?>
                                <?php foreach($operations as $operation) { ?>
                                <tr>
                                    <td><?php echo '/'.$operation->controller.'/'.$operation->action; ?></td>
                                    <td><?php echo $operation->description; ?></td>
                                    <td style="text-align:center">
                                        <a class="btn btn-success btn-xs" href="<?php echo Yii::$app->homeUrl; ?>user-management/edit-operation?id=<?php echo $operation->id; ?>" style="color:#fff;">Edit</a>
                                        <a class="btn btn-success btn-xs" href="<?php echo Yii::$app->homeUrl; ?>user-management/remove-operation?id=<?php echo $operation->id; ?>" style="color:#fff;" onclick="return window.confirm('Are you sure?');">Remove</a>
                                    </td>
                                </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>