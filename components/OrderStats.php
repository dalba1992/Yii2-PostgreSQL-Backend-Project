<?php namespace app\components;

use yii\base\Component;
use app\models\CustomerSubscriptionOrder;


class OrderStats extends Component
{
	const ACTIVE_STATUS    = 1;
	const FINALIZED_STATUS = 2;
	const FULFILLED_STATUS = 3;
	const DRAFT_STATUS     = 4;

	public function filter($startDate = '', $endDate = '')
	{
		// all orders
		$orders = CustomerSubscriptionOrder::find()->all();

		// active orders
		$orderActive = CustomerSubscriptionOrder::find()
					->where(['status' => self::ACTIVE_STATUS])
					->all();

		// filnalized orders
		$orderFilnalized = CustomerSubscriptionOrder::find()
					->where(['status' => self::FINALIZED_STATUS])
					->all();

		// filnalized orders
		$orderFulfilled = CustomerSubscriptionOrder::find()
					->where(['status' => self::FULFILLED_STATUS])
					->all();


		// draft orders
		$orderDraft = CustomerSubscriptionOrder::find()
					->where(['status' => self::DRAFT_STATUS])
					->all();
	}
}