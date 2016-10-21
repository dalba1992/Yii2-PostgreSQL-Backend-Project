<?php
/**
 * @var $this \yii\web\View
 */

$actionId = Yii::$app->controller->action->id;
$getStatus = Yii::$app->request->get('status');
$getShop = Yii::$app->request->get('shop');
$moduleId = Yii::$app->controller->module->id;

?>


<div id="sidebar">
    <ul>
        <li <?php if(Yii::$app->controller->id=='site'){?>class="active"<?php } ?>><a href="<?php echo Yii::$app->homeUrl.'site/dashboard'?>"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
        <li class="submenu <?php if (Yii::$app->controller->id=='members') { echo 'active open'; } ?>">
            <a href="javascript:;"><i class="fa fa-users"></i> <span>Members</span> <span class="label"><?php echo $allMemberCount;?></span></a>
            <ul>
                <li <?php echo ($actionId =='list' && $getStatus == 'all')? 'class="active"':''; ?>><a href="<?php echo Yii::$app->homeUrl.'members/list/all'?>">All Members</a></li>
                <li <?php echo ($actionId =='list' && $getStatus == 'active')? 'class="active"':''; ?>><a href="<?php echo Yii::$app->homeUrl.'members/list/active'?>">Active Members  <span class="label"><?php echo $activeMemberCount;?></span></a></li>
                <li <?php echo ($actionId =='list' && $getStatus == 'inactive')? 'class="active"':''; ?>><a href="<?php echo Yii::$app->homeUrl.'members/list/inactive'?>">Inactive Members  <span class="label"><?php echo $inactiveMemberCount;?></span></a></li>
            </ul>
        </li>
        <li class="submenu <?php
                if (Yii::$app->controller->id=='manualorder' ||
                    Yii::$app->controller->id=='order' ||
                    Yii::$app->controller->id=='displaychargedusers') {
                    echo 'active open';
                }
            ?>">
            <a href="javascript:;"><i class="fa fa-money"></i> <span>Orders</span> <span class="label"><?php echo $orders;?></span></a>
            <ul>
                <li <?php if(Yii::$app->controller->id=='manualorder'){?>class="active" <?php } ?> >
                    <a href="<?php echo Yii::$app->homeUrl.'manualorder/'?>">Manual Order</a>
                </li>
                <!--<li <?php if(Yii::$app->controller->id=='manageorder'){?>class="active" <?php } ?> >
							<a href="<?php echo Yii::$app->homeUrl.'manageorder/'?>">Manual Queue</a>
						</li>-->
                <li <?php if(Yii::$app->controller->id=='order'){?>class="active" <?php } ?>>
                    <a href="<?php echo Yii::$app->homeUrl.'order/create-order'?>">Create Order By CSV</a>
                </li>
                <li <?php if(Yii::$app->controller->id=='internalorder'){?>class="active" <?php } ?>>
                    <a href="<?php echo Yii::$app->homeUrl.'displaychargedusers/fetchchargeduser'?>">Create Internal Order</a>
                </li>
                <li>
                    <a href="<?php echo Yii::$app->homeUrl.'stripe/monthly-charges'?>">Stripe Monthly Charges</a>
                </li>
            </ul>
        </li>
        <li class="submenu <?php
        if (Yii::$app->controller->id=='tradegecko' && $moduleId != 'services') {
            echo 'active open';
        }
        ?>">
            <a href="javascript:;"><i class="fa fa-shopping-cart"></i> <span>TradeGecko Orders</span></a>
            <ul>
                <li <?php if(Yii::$app->controller->id=='tradegecko'){?>class="active" <?php } ?>>
                    <a href="<?php echo Yii::$app->homeUrl.'tradegecko/list'?>">List</a>
                </li>
                <li <?php if(Yii::$app->controller->id=='internalorder'){?>class="active" <?php } ?>>
                    <a href="<?php echo Yii::$app->homeUrl.'tradegecko/checkorder'?>">Mismatch Order items</a>
                </li>
                <li <?php if(Yii::$app->controller->id=='internalorder'){?>class="active" <?php } ?>>
                    <a href="<?php echo Yii::$app->homeUrl.'tradegecko/duplicateorders'?>">Duplicate Orders</a>
                </li>
            </ul>
        </li>
        <li class="submenu <?php if (Yii::$app->controller->id=='liteview') { echo 'active open'; } ?>">
            <a href="javascript:;"><i class="fa fa-shopping-cart"></i> <span>Liteview Orders</span> </a>
            <ul>
                <li <?php echo (Yii::$app->controller->id=='liteview' && ($actionId =='index' || $actionId == 'view'))? 'class="active"':''; ?>><a href="<?php echo Yii::$app->homeUrl.'services/liteview/index'?>">List</a></li>
            </ul>
        </li>
        <li class="submenu <?php if (Yii::$app->controller->id=='coupon') { echo 'active open'; } ?>">
            <a href="#"><i class="fa fa-ticket"></i> <span>Coupons</span> <span class="label"></span></a>
            <ul>
                <li <?php if(Yii::$app->controller->action->id=='index'){ ?>class="active"<?php } ?>><a href="<?php echo Yii::$app->homeUrl.'coupon/index'?>">All Coupons</a></li>
            </ul>
        </li>
        <!--<li class="submenu <?php if (Yii::$app->controller->id=='payment') { echo 'active open'; } ?>">
					<a href="javascript:;"><i class="icon icon-plane"></i> <span>Stripe Payment</span> <span class="label"></span></a>
					<ul>
						<li <?php if(Yii::$app->controller->action->id=='charge'){ ?>class="active"<?php } ?>><a href="<?php echo Yii::$app->homeUrl.'payment/charge'?>">Payment Info</a></li>
					</ul>
				</li>-->
        <li class="submenu <?php if(Yii::$app->controller->id=='inventory'){ echo 'active open'; } ?>">
            <a href="javascript:;"><i class="fa fa-calculator"></i> <span>Inventory</span></a>
            <ul>
                <li <?php echo (Yii::$app->controller->action->id =='index' && $getShop == 'all')? 'class="active"':''; ?>><a href="<?php echo Yii::$app->homeUrl.'inventory'?>">All Inventory</a></li>
                <li <?php echo (Yii::$app->controller->action->id =='index' && $getShop == 'concierge')? 'class="active"':''; ?>><a href="<?php echo Yii::$app->homeUrl.'inventory/list/concierge'?>">Concierge Items  <span class="label"><?php echo $conciergeInventoryCount;?></span></a></li>
                <li <?php echo (Yii::$app->controller->action->id =='index' && $getShop == 'shop')? 'class="active"':''; ?>><a href="<?php echo Yii::$app->homeUrl.'inventory/list/shop'?>">Shop Items  <span class="label"><?php echo $shopInventoryCount;?></span></a></li>
            </ul>
        </li>
        <li class="submenu <?php if (Yii::$app->controller->id=='customerstatuslog') { echo 'active open'; } ?>">
            <a href="javascript:;"><i class="fa fa-list-ol"></i> <span>Customer Log</span> <span class="label"></span></a>
            <ul>
                <li <?php if(Yii::$app->controller->action->id=='index'){ ?>class="active"<?php } ?>><a href="<?php echo Yii::$app->homeUrl.'customerstatuslog/index'?>">Log Details</a></li>
            </ul>
        </li>
        <li class="submenu <?php if (Yii::$app->controller->id=='stripe') { echo 'active open'; } ?>">
            <a href="javascript:;"><i class="fa fa-info-circle"></i> <span>Stripe Info</span> </a>
            <ul>
                <li <?php echo ($actionId =='customers' || $actionId == 'view')? 'class="active"':''; ?>><a href="<?php echo Yii::$app->homeUrl.'services/stripe/customers'?>">Customers</a></li>
                <li <?php echo ($actionId =='payments')? 'class="active"':''; ?>><a href="<?php echo Yii::$app->homeUrl.'services/stripe/payments'?>">Payments</a></li>
                <li <?php echo ($actionId =='plans')? 'class="active"':''; ?>><a href="<?php echo Yii::$app->homeUrl.'services/stripe/plans'?>">Plans</a></li>
                <li <?php echo ($actionId =='coupons')? 'class="active"':''; ?>><a href="<?php echo Yii::$app->homeUrl.'services/stripe/coupons'?>">Coupons</a></li>
                <li <?php echo ($actionId =='transfers')? 'class="active"':''; ?>><a href="<?php echo Yii::$app->homeUrl.'services/stripe/transfers'?>">Transfers</a></li>
                <li <?php echo ($actionId =='balance-transactions')? 'class="active"':''; ?>><a href="<?php echo Yii::$app->homeUrl.'services/stripe/balance-transactions'?>">Balance Transactions</a></li>
                <!--<li <?php echo ($actionId =='detail')? 'class="active"':''; ?>><a href="<?php echo Yii::$app->homeUrl.'services/stripe/detail'?>">Find by customer</a></li>-->
            </ul>
        </li>
        <li class="submenu <?php if (Yii::$app->controller->id=='tradegecko' && $moduleId == 'services' && !isset($getStatus)) { echo 'active open'; } ?>">
            <a href="javascript:;"><i class="fa fa-info"></i> <span>Tradegecko Info</span> </a>
            <ul>
                <li <?php echo (($actionId == 'inventory' || $actionId == 'inventoryview') && $moduleId == 'services')? 'class="active"':''; ?>><a href="<?php echo Yii::$app->homeUrl.'services/tradegecko/inventory'?>">Inventory</a></li>
                <li <?php echo (($actionId == 'order' || $actionId == 'orderview') && $moduleId == 'services')? 'class="active"':''; ?>><a href="<?php echo Yii::$app->homeUrl.'services/tradegecko/order'?>">Order</a></li>
                <li <?php echo (($actionId == 'invoice' || $actionId == 'invoiceview') && $moduleId == 'services')? 'class="active"':''; ?>><a href="<?php echo Yii::$app->homeUrl.'services/tradegecko/invoice'?>">Invoice</a></li>
                <li <?php echo (($actionId == 'fulfillment' || $actionId == 'fulfillmentview') && $moduleId == 'services')? 'class="active"':''; ?>><a href="<?php echo Yii::$app->homeUrl.'services/tradegecko/fulfillment'?>">Shipment</a></li>
                <li <?php echo (($actionId == 'company' || $actionId == 'companyview') && $moduleId == 'services')? 'class="active"':''; ?>><a href="<?php echo Yii::$app->homeUrl.'services/tradegecko/company'?>">Company</a></li>
                <li <?php echo (($actionId == 'data') && $moduleId == 'services')? 'class="active"':''; ?>><a href="<?php echo Yii::$app->homeUrl.'services/tradegecko/data'?>">Tradegecko Data</a></li>
            </ul>
        </li>
        <li class="submenu <?php if (Yii::$app->controller->id=='user-management') { echo 'active open'; } ?>">
            <a href="javascript:;"><i class="fa fa-user"></i> <span>User Management</span> </a>
            <ul>
                <li <?php echo ($actionId == 'user' || $actionId == 'create' || $actionId == 'update' || $actionId == 'edit' || $actionId == 'assign')? 'class="active"':''; ?>><a href="<?php echo Yii::$app->homeUrl.'user-management/user'?>">User</a></li>
                <li <?php echo ($actionId == 'rbac' || $actionId == 'create-operation' || $actionId == 'edit-operation' || $actionId == 'create-role' || $actionId == 'edit-role' || $actionId == 'assign-operations' || $actionId == 'update-role' || $actionId == 'create-task' || $actionId == 'edit-task' || $actionId == 'update-task')? 'class="active"':''; ?>><a href="<?php echo Yii::$app->homeUrl.'user-management/rbac'?>">RBAC</a></li>
            </ul>
        </li>
        <li class="submenu <?php if (Yii::$app->controller->id=='attribute') { echo 'active open'; } ?>">
            <a href="javascript:;"><i class="fa fa-cog"></i> <span>Settings</span> </a>
            <ul>
                <li <?php echo ($actionId == 'index' || $actionId == 'create' || $actionId == 'edit')? 'class="active"':''; ?>><a href="<?php echo Yii::$app->homeUrl.'attribute/index'?>">Style attributes</a></li>
            </ul>
        </li>
    </ul>
</div>