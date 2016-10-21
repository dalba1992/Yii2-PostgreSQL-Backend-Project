<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use app\models\CustomerCart;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class TestController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($message = 'hello world')
    {
    	$itemCustomerCart = [];
    	$itemCustomerCart['data'] = 'C';
    	$itemCustomerCart['customerId'] = 3;
    	$itemCustomerCart['cartStatus'] = 'Active';

    	$model = new CustomerCart();
    	$model->attributes = $itemCustomerCart;

    	$model->save();
    }

    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionUpdate()
    {
    	$model = CustomerCart::find()->where(['id'=>2])->one();
    	$model->data = 'D';

    	$model->save();
    }

    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionPull()
    {
    	$model = new CustomerCart();
    	$data = $model->getCart(3);

    	print_r($data);
    }
}
