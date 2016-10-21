<?php
namespace app\commands;

use app\models\CustomerSubscription;
use app\models\Notification;
use Yii;
use yii\console\Controller;
use yii\base\ErrorException;

use app\components;

use app\components\StripeHelper;
use app\models\CustomerEntity;
use app\models\CustomerSubscriptionState;
use app\models\CustomerSubscriptionLog;

class NotificationController extends Controller
{
    /**
     * Check if there is any pending notification
     * @var string $interval is the range of dates to use when looking for status updates. Ex: 01.01.2015,01.04.2015 [the format is dd.mm.yyyy]
     */
    public function actionCheckResume($interval = null)
    {
        if(!$interval){
            $date1 = date('Y-m-d 00:00:00');
            $date2 = date('Y-m-d 00:00:00', (strtotime('+1 day')));

            $interval = date('d.m.Y') . ',' . date('d.m.Y', (strtotime('+1 day')));
        }
        else{
            $arr = explode(',', $interval);
            $date = \DateTime::createFromFormat('d.m.Y', $arr[0]);
            $date->setTime(0,0,0);
            $date1 = date('Y-m-d 00:00:00', $date->getTimestamp());

            $date = \DateTime::createFromFormat('d.m.Y', $arr[1]);
            $date->setTime(0,0,0);
            $date2 = date('Y-m-d 00:00:00', $date->getTimestamp());
        }

        Yii::$app->utils->logToConsole('Checking interval (date): '.$interval);
        Yii::$app->utils->logToConsole('Checking interval (timestamp): '.$date1.' - '.$date2);

        $alias = Notification::tableName();
        $notifications = Notification::find()
            ->where('type = :resume AND notify_next > :date1 AND notify_next < :date2', [':resume'=>Notification::TYPE_RESUME, ':date1'=>$date1, ':date2'=>$date2])
            ->all();

        foreach($notifications as $notification){
            $customer = $notification->customer;
            if($customer->customerEntityInfos->email){
                Yii::$app->utils->logToConsole('Customer: '.$customer->customerEntityInfos->email);
                $this->sendNotification($customer, $notification);
            }
        }
    }

    protected function sendNotification($customer, $notification)
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

            /**
             * Update the date for the next notification.
             */
            $date = \DateTime::createFromFormat('Y-m-d H:i:s', $notification->notify_next);
            $notification->notify_next = date('Y-m-d H:i:s', (strtotime('+3 months', $date->getTimestamp())));
            $notification->save();
        }
        else
            return false;
    }
}
