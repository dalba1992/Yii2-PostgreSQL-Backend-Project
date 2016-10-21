<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\VarDumper;
use yii\helpers\Json;

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property integer $userId
 * @property integer $customerId
 * @property string $items
 * @property string $status
 * @property string $date
 * @property string $createdAt
 * @property string $updatedAt
 *
 * @property CustomerEntity $customer
 */
class Order extends ActiveRecord
{
    const STATUS_ACTIVE = 'active';
    const STATUS_DRAFT = 'draft';
    const STATUS_FINALIZED = 'finalized';
    const STATUS_FULFILLED = 'fulfilled';
    const STATUS_RETURNED = 'returned';

    const STATUS_PAID = 'paid';
    const STATUS_UNPAID = 'unpaid';


    const STATUS_SENT = 'sent';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'updatedAt',
                'value' => new Expression('NOW()')
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'customerId'], 'required'],
            [['userId', 'customerId'], 'integer'],
            [['items'], 'string'],
            [['date', 'createdAt', 'updatedAt'], 'safe'],
            [['status'], 'string', 'max' => 255]
        ];
    }

    public function beforeSave($insert) {
        if(parent::beforeSave($insert)) {
            if(!is_string($this->items)) {
                $this->items = Json::encode($this->items);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function afterFind() {
        parent::afterFind();
        $this->items = Json::decode($this->items);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer() {
        return $this->hasOne(CustomerEntity::className(), ['id' => 'customerId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderLog() {
        return $this->hasOne(OrderLog::className(), ['orderId' => 'id']);
    }


    /**
     * Sends the order to TradeGecko
     * @param array $items Order items
     * @param array $tradegecko Customer information from TradeGecko
     */
    public function send($items, $tradegecko) {
        $customer = $this->customer;
        $orderLineItems = [];
        $newItems = [];
        if(is_array($items) && count($items)) {
            foreach($items as $item_sku) {
                $item = ProductVariants::find()->where(['sku' => $item_sku])->one();
                if($item) {
                    $item->setAttributes([
                        'availableStock' => $item->availableStock - 1,
                        'committedStock' => $item->committedStock + 1
                    ]);
                    if($item->save()) {
                        $productOrderLog = new ProductOrderLog();
                        $productOrderLog->setAttributes([
                            'customerId' => $customer->id,
                            'orderId' => $this->id,
                            'productId' => $item->id
                        ]);
                        if($productOrderLog->save()) {
                            $orderLineItems[] = [
                                'variant_id' => $item->tradegeckoId,
                                'quantity' => 1,
                                'price' => $item->retailPrice,
                            ];
                        }
                        $newItems[] = $item->sku;
                    }
                }
            }
        }

        $params = [
            'order' => [
                'company_id' => $tradegecko['info']->tradegeckoId,
                'billing_address_id' => $tradegecko['address']->tradegeckoAddressId,
                'shipping_address_id' => $tradegecko['address']->tradegeckoAddressId,
                'email' => $customer->customerEntityInfo->email,
                'issued_at' => date('Y-m-d'),
                'tax_type' => 'exclusive',
                'status' => self::STATUS_ACTIVE,
                'payment_status' => self::STATUS_PAID,
                'order_line_items' => $orderLineItems
            ]
        ];
        $tradeGeckoOrder = Yii::$app->tradeGecko->createOrder($params);
        if($tradeGeckoOrder) {

            $orderLog = new OrderLog();
            $orderLog->setAttributes([
                'orderId' => $this->id,
                'status' => $tradeGeckoOrder['status'],
                'userId' => Yii::$app->user->getId(),
                'tradegeckoOrderId' => $tradeGeckoOrder['id']
            ]);

            if($orderLog->save()) {
                $this->status = 'sent';
                $this->items = Json::encode($newItems);
                if($this->save()) {

                } else {

                }
            }
        }
    }

    /**
     * Get all items
     * @return array
     */
    public function getItems() {
        $response = [];

        if(is_string($this->items)) {
            $items = Json::decode($this->items);
        } else {
            $items = $this->items;
        }

        foreach($items as $item) {
            $variant = ProductVariants::find()->where(['sku' => $item])->one();
            if($variant) {
                $response[] = $variant;
            }
        }

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userId' => 'User ID',
            'customerId' => 'Customer ID',
            'status' => 'Status',
            'items' => 'Items',
            'date' => 'Date',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
        ];
    }

    /**
    * @Find orders where status is active
    */
    public static function findActiveOrders()
    {
        $query = self::findBySql('select "order".* from "order" WHERE "status" = \''.self::STATUS_ACTIVE.'\'');
        return $query;
    }

    /**
    * @Find orders where status is draft
    */
    public static function findDraftOrders()
    {
        $query = self::findBySql('select "order".* from "order" WHERE "status" = \''.self::STATUS_DRAFT.'\'');
        return $query;
    }

    /**
    * @Find orders where status is finalized
    */
    public static function findFinalizedOrders()
    {
        $query = self::findBySql('select "order".* from "order" WHERE "status" = \''.self::STATUS_FINALIZED.'\'');
        return $query;
    }

    /**
    * @Find orders where status is fulfilled
    */
    public static function findFulfilledOrders()
    {
        $query = self::findBySql('select "order".* from "order" WHERE "status" = \''.self::STATUS_FULFILLED.'\'');
        return $query;
    }

    /**
    * @Find orders where status is returned
    */
    public static function findReturnedOrders()
    {
        $query = self::findBySql('select "order".* from "order" WHERE "status" = \''.self::STATUS_RETURNED.'\'');
        return $query;
    }

    /**
    * @Find orders where status is paid
    */
    public static function findPaidOrders()
    {
        $query = self::findBySql('select "order".* from "order" WHERE "status" = \''.self::STATUS_PAID.'\'');
        return $query;
    }

    /**
    * @Find orders where status is unpaid
    */
    public static function findUnpaidOrders()
    {
        $query = self::findBySql('select "order".* from "order" WHERE "status" = \''.self::STATUS_UNPAID.'\'');
        return $query;
    }

    /**
    * @Find orders where status is sent
    */
    public static function findSentOrders()
    {
        $query = self::findBySql('select "order".* from "order" WHERE "status" = \''.self::STATUS_SENT.'\'');
        return $query;
    }
}
