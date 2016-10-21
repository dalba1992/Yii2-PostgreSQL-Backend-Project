<?php

namespace app\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

use app\models;

/**
 * This is the model class for table "customer_subscription_state.
 *
 * @property integer     $id
 * @property integer     $subscription_id
 * @property string      $status
 *
 * @property string      $update_time
 * @property string      $update_status
 * @property string      $update_reason
 * @property string      $update_note
 * @property integer     $pause_duration
 *
 * @property string      $created_at
 * @property string      $updated_at
 *
 * @property CustomerSubscription            $customerSubscription
 */
class CustomerSubscriptionState extends \yii\db\ActiveRecord
{
    public $userIdentity = null;
    /**
     * Status possible values
     */
    const STATUS_PAUSE  = 'pause';
    const STATUS_CANCEL = 'cancel';
    const STATUS_RESUME = 'resume';

    /**
     * @var array these are the status labels
     */
    static public $statusLabels = [
        self::STATUS_PAUSE => 'Pause',
        self::STATUS_CANCEL => 'Cancel',
        self::STATUS_RESUME => 'Resume',
    ];

    static public $reasonOptions = [
        null => 'Select a reason',
        0 => 'Financial / Can not afford it',
        1 => 'Does not want anymore clothes',
        2 => 'Does not like club/clothes',
        3 => 'Late/Lost Package or Delays',
        4 => 'Other'
    ];

    const EMAIL_SUBJECT = 'Trendy Butler Notification';

    static public $emailFrom = ['office@trendy-butler.com'=>'Trendy Butler'];

    static public $pauseDuration = [1=>'1 Month', 2=>'2 Months', 3=>'3 Months', null=>'Do not resume'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_subscription_state';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subscription_id', 'update_time', 'pause_duration'], 'integer'],
            [['status', 'update_status', 'update_reason', 'update_note'], 'string'],
            [['created_at', 'updated_at'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'subscription_id' => 'Subscription ID',
            'update_time' => 'Update on',
            'pause_duration' => 'Pause duration',
            'current_status' => 'Current status',
            'update_status' => 'Status',
            'update_reason' => 'Reason',
            'update_note' => 'Note'
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => function(){ return date("Y-m-d H:i:s");},
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerSubscription()
    {
        return $this->hasOne(CustomerSubscription::className(), ['id' => 'subscription_id']);
    }

    /**
     * @inherit
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        if($this->validStateUpdate())
            CustomerSubscriptionLog::logStateChange($this, ($this->userIdentity?$this->userIdentity->id:null), CustomerSubscriptionLog::TYPE_REQUEST);

        return parent::afterSave($insert, $changedAttributes);
    }

    public function validStateUpdate()
    {
        return ($this->update_time && $this->update_status);
    }

    public function resetState()
    {
        $this->update_time = null;
        $this->update_status = null;
        $this->update_reason = null;
        $this->update_note = null;
        $this->pause_duration = null;
        return $this;
    }

    /**
     * Create a default state for a given subscription
     * @param integer $subscriptionId
     * @return CustomerSubscriptionState
     */
    public static function defaultState($subscriptionId)
    {
        $model = new CustomerSubscriptionState();
        $model->subscription_id = $subscriptionId;
        $model->resetState();
        $model->save();
        return $model;
    }

    /**
     * Send a notification email for the 3 state updates: cancel, pause & resume
     * @param $template
     * @return bool
     */
    public function sendNotificationEmail($template)
    {
        $customer = $this->customerSubscription->customer;
        if($customer->customerEntityInfos->email){
            $email = $customer->customerEntityInfos->email;
            $subject = self::EMAIL_SUBJECT;
            $text = Yii::$app->controller->renderPartial('@app/views/mail/text/subscription/'.$template, ['customer'=>$customer]);
            $html = Yii::$app->controller->renderPartial('@app/views/mail/html/subscription/'.$template, ['customer'=>$customer]);

            return Yii::$app->mailer->compose()
                ->setTo($email)
                ->setFrom(Yii::$app->params['campaignEmail'])
                ->setSubject($subject)
                ->setTextBody($text)
                ->setHtmlBody($html)
                ->send();
        }
        else
            return false;
    }
}
