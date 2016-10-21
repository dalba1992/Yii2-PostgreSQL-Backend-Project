<?php

namespace app\components;
use Yii;
use app\models\CustomerEntityStatus;
use yii\base\Component;
use app\models\CustomerEntity;
use app\models\CustomerSubscription;
use app\models\CustomerEntityAddress;
use app\models\TradegeckoInfo;


use app\models\Product;
use app\models\Brand;
use app\models\Category;
use app\models\Productattribute;
use app\models\Productattributeoption;
use app\models\Productattributevalue;
use app\models\Variants;
use app\models\Variantsimage;

class Helper extends Component
{
    /**
     * @param $memberid
     * @return string
     */
    public function manage($memberid)
    {
		return '<div class="btn-group">
  					<button class="btn btn-sm btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
  					Manage  <span class="caret"></span>
  					</button>
  					<ul class="dropdown-menu dropdown-menu-right" role="menu">
						<li><a href="'.Yii::$app->request->baseUrl.'/members/profile/?member='.$memberid.'">Edit Profile</a></li>
        				<li><a href="'.Yii::$app->request->baseUrl.'/members/future/?member='.$memberid.'">Set Future Month Status</a></li>
        				<li><a href="'.Yii::$app->request->baseUrl.'/members/payments/?member='.$memberid.'">Payments &amp; Orders</a></li>
        				<li><a href="'.Yii::$app->request->baseUrl.'/members/arb/?member='.$memberid.'">ARB Role</a></li>
						<li><a href="'.Yii::$app->request->baseUrl.'/members/referrals/?member='.$memberid.'">Referrals</a></li>
        				<li class="divider"></li>
        				<li><a href="/members/create-order?member='.$memberid.'">Create Order</a></li>
  					</ul>
			</div>';
    }

    /**
     * @param $id
     * @param bool $status
     * @return string
     */
    public function status($id, $status = false){

		$condition['customer_id'] =$id;

		if($status)
			$condition['status'] = $status;

		$customer=CustomerEntityStatus::find()->where($condition)->orderBy(['id'=>SORT_DESC])->one();

        if($customer){
            $status = ucfirst($customer->status);
            if($status=='active')
                return '<span class="label label-success">'.$status.'</span>';
            else
                return '<span class="label label-warning">'.$status.'</span>';
        }
        else{
            return '<span class="label label-danger">Error</span>';
        }
    }
}

