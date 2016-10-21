<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\base\ErrorException;

use app\components;

use app\models\CustomerEntity;
use app\models\CustomerSubscriptionState;
use app\models\CustomerCart;

class CartController extends Controller
{
    /**
     * Check if there is any draft shopping carts
     */
    public function actionCheckDraftcart()
    {
        echo PHP_EOL;
        $customers = CustomerEntity::find()->joinWith(['customerEntityInfos'])->andWhere(['<>', 'customer_entity_info.email', ""])->all();
        foreach($customers as $customer)
        {
            $email = $customer->customerEntityInfos->email;

            if ($email != '')
            {
                $customerId = $customer->id;
                $model = new CustomerCart();
                $data = $model->getCart($customerId);

                if (count($data) > 0)
                    self::sendNotification($customer);
            }
        }
    }

    protected function sendNotification($customer)
    {
        $template = 'template_'.rand(1, 5);
        $email = $customer->customerEntityInfos->email;

        $subject = CustomerSubscriptionState::EMAIL_SUBJECT;
        $text = Yii::$app->controller->renderPartial('@app/views/mail/text/subscription/resume/'.$template, ['customer'=>$customer]);
        $html = Yii::$app->controller->renderPartial('@app/views/mail/html/subscription/resume/'.$template, ['customer'=>$customer]);

        if (
                Yii::$app->mailer->compose()
                ->setTo($email)
                ->setFrom(Yii::$app->params['campaignEmail'])
                ->setSubject($subject)
                ->setTextBody($text)
                ->setHtmlBody($html)
                ->send()
            ){
            echo 'Email was sent to '.$email.' correctly.'.PHP_EOL;
        }
        else
            return false;
    }
}
