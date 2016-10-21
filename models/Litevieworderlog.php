<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "liteview_order_log".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property string $company_id
 * @property string $tradegecko_order_id
 * @property string $tradegecko_order_number
 * @property string $liteview_order_id
 * @property boolean $liteview_order_status
 * @property string $liteview_order_created_at
 */
class Litevieworderlog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'liteview_order_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id'], 'integer'],
            [['liteview_order_status'], 'boolean'],
            [['liteview_order_created_at'], 'safe'],
            [['company_id', 'tradegecko_order_id', 'tradegecko_order_number', 'liteview_order_id'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'company_id' => 'Company ID',
            'tradegecko_order_id' => 'Tradegecko Order ID',
            'tradegecko_order_number' => 'Tradegecko Order Number',
            'liteview_order_id' => 'Liteview Order ID',
            'liteview_order_status' => 'Liteview Order Status',
            'liteview_order_created_at' => 'Liteview Order Created At',
        ];
    }

    /**
     * @inherit
     */
    public function afterSave($insert, $changedAttributes)
    {
        $this->sendPackingConfirmationEmail($this->customer_id);
        return parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Send a confirmation email when an order is successfully created
     * @param $customerId
     */
    protected function sendPackingConfirmationEmail($customerId)
    {
        $template = 'pack';

        //@todo remove tgDevMode dependent section
        if(Yii::$app->params['tgDevMode']){
            $customer = CustomerEntity::findOne(37);
            $email = 'office@ionut-titei.ro';
        }
        else{
            $customer = CustomerEntity::findOne($customerId);
            $email = $customer->customerEntityInfo->email;
        }

        if(!$customer)
            return false;

        $subject = 'Your Trendy Butler Package';
        $text = Yii::$app->controller->renderPartial('@app/views/mail/text/order/'.$template, ['customer'=>$customer]);
        $html = Yii::$app->controller->renderPartial('@app/views/mail/html/order/'.$template, ['customer'=>$customer]);

        return Yii::$app->mailer->compose()
            ->setTo($email)
            ->setFrom(Yii::$app->params['campaignEmail'])
            ->setSubject($subject)
            ->setTextBody($text)
            ->setHtmlBody($html)
            ->send();
    }
}
