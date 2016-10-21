<?php
namespace app\models;

use Yii;

/**
 * This is the model class for table "tradegecko_importcsv_order_log".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property string $created_at
 * @property string $import_csv_title
 * @property string $tradegecko_order_number
 * @property integer $tradegecko_order_id
 */
class TradegeckoOrderLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_importcsv_order_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'tradegecko_order_id'], 'integer'],
            [['created_at', 'tradegecko_order_number'], 'string', 'max' => 50],
            [['import_csv_title'], 'string', 'max' => 255]
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
            'created_at' => 'Created At',
            'import_csv_title' => 'Import Csv Title',
            'tradegecko_order_number' => 'Tradegecko Order Number',
            'tradegecko_order_id' => 'Tradegecko Order ID',
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        $this->sendOrderConfirmationEmail($this->customer_id);
        return parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Send a confirmation email when an order is successfully created
     * @param $customerId
     * @return boolean
     */
    protected function sendOrderConfirmationEmail($customerId)
    {
        $template = 'create';

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

        $subject = 'Your Trendy Butler box has been picked by your stylist';
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

    /**
     * Find orders between two dates
     * @param string $first_dt
     * @param string $second_dt
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getOrdersBetweenTwoDates($first_dt = '', $second_dt = '')
    {
        $sql = 'select "tradegecko_importcsv_order_log".* from "tradegecko_importcsv_order_log" WHERE 1=1 ';
        if ($first_dt != '')
            $sql .= ' AND "created_at" >= \''.$first_dt.'\'';
        if ($second_dt != '')
            $sql .= ' AND "created_at" <= \''.$second_dt.'\'';

        $query = TradegeckoOrderLog::findBySql($sql)->all();
        return $query;
    }

    //old deprecated stuff
    public function getItems()
    {
        return $this->hasMany(TradegeckoOderItemsLog::className(), ['log_order_id' => 'id']);
    }

    //old deprecated stuff
    public function Usernames()
    {
        return ($this->user) ? $this->user->first_name .' '.$this->user->last_name : null;
    }

    //old deprecated stuff
    public function getUser()
    {
        return $this->hasOne(CustomerEntityInfo::className(), ['customer_id' => 'customer_id']);
    }

    //old deprecated stuff
    public function getEmail()
    {
        return ($this->user) ? $this->user->email : null;
    }
}