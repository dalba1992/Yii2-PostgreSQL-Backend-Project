<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\grid\DataColumn;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "customer_entity".
 *
 * @property integer $id
 * @property integer $is_active
 * @property string $created_at
 * @property string $updated_at
 *
 * @property CustomerSubscription $customerSubscription
 * @property CustomerSubscriptionOrder[] $customerSubscriptionOrders
 * @property CustomerEntityInfo[] $customerEntityInfos
 * @property CustomerEntityInfo $customerEntityInfo
 * @property CustomerEntityStatus[] $customerEntityStatuses
 * @property CustomerEntityAddress $customerEntityAddress
 * @property CustomerEntityAddress[] $customerEntityAddresses
 * @property CustomerEntityAttribute[] $customerEntityAttributes
 * @property TradegeckoCustomer $tradegecko
 * @property ProductOrderLog[] $previouslySentProducts
 * @property Order[] $latestOrders
 *
 */
class CustomerEntity extends \yii\db\ActiveRecord
{
    const LABEL_STATUS_ACTIVE = 'active';
    const LABEL_STATUS_INACTIVE = 'inactive';

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    public static function tableName()
    {
        return 'customer_entity';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_active'], 'integer'],
            [['created_at', 'updated_at'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'is_active' => 'Is Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerSubscriptionOrders()
    {
        return $this->hasOne(CustomerSubscriptionOrder::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerEntityInfos()
    {
        return $this->hasOne(CustomerEntityInfo::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerEntityInfo()
    {
        return $this->hasOne(CustomerEntityInfo::className(), ['customer_id' => 'id'], ['orderBy'=>['id' => SORT_DESC]]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
     
    public function getCustomerEntityStatuses()
    {
         return $this->hasMany(CustomerEntityStatus::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerEntityStatus()
    {
        return $this->hasOne(CustomerEntityStatus::className(), ['customer_id' => 'id'], ['orderBy'=>['id' => SORT_DESC]]);
    }
    
	 
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerEntityAddresses()
    {
        return $this->hasMany(CustomerEntityAddress::className(), ['customer_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerEntityAddress()
    {
        return $this->hasOne(CustomerEntityAddress::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerSubscription()
    {
        return $this->hasOne(CustomerSubscription::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTradegecko() {
        return $this->hasOne(TradegeckoCustomer::className(), ['customerId' => 'id']);
    }

    /**
     * Previously sent products
     * @return \yii\db\ActiveQuery
     */
    public function getPreviouslySentProducts() {
        $models = ProductOrderLog::find()->where(['customerId' => $this->id])->all();
        $logs = [];
        if($models) {
            foreach($models as $model) {
                if($model) {
                    if($model->product) {
                        $logs[] = $model;
                    }
                }
            }
        }

        return $logs;
    }

    /**
     * If the customer not exists in TradeGecko will be exported
     */
    public function exportToTradegecko() {
        $response = [];
        if($this->tradegecko != null) {
            $tradegecko = $this->tradegecko;
            $response['info'] = $tradegecko;
            if($tradegecko->address) {
                $tradegeckoAddress = $tradegecko->address;
                $response['address'] = $tradegecko->address;
            } else {
                $tradegeckoAddress = new TradegeckoCustomerAddress();
                $params = [
                    'address' => [
                        'company_id' => $tradegecko->tradegeckoId,
                        'label' => 'Shipping',
                        'address1' => $this->customerEntityAddress->address,
                        'address2' => $this->customerEntityAddress->address2,
                        'city' => $this->customerEntityAddress->city,
                        'country' => $this->customerEntityAddress->country,
                        'state' => $this->customerEntityAddress->state,
                        'zip_code' => $this->customerEntityAddress->zipcode,
                    ]
                ];
                $address = Yii::$app->tradeGecko->createAddress($params);
                if($address) {
                    if(isset($address['id'])) {
                        $tradegeckoAddress->setAttributes([
                            'customerId' => $this->id,
                            'tradegeckoCustomerId' => $address['company_id'],
                            'tradegeckoAddressId' => $address['id']
                        ]);
                        if($tradegeckoAddress->save()) {
                            $response['address'] = $tradegeckoAddress;
                        }
                    }
                }
            }
        } else {
            $tradegecko = new TradegeckoCustomer();

            $params = [
                'company' => [
                    'name' => $this->customerEntityInfo->first_name . ' ' . $this->customerEntityInfo->last_name,
                    'company_type' => 'consumer',
                    'email' => $this->customerEntityInfo->email
                ]
            ];

            $customer = Yii::$app->tradeGecko->createCompany($params);
            if($customer) {
                if(isset($customer['id'])) {
                    $tradegecko->setAttributes([
                        'customerId' => $this->id,
                        'tradegeckoId' => $customer['id']
                    ]);
                    if($tradegecko->save()) {
                        $response['info'] = $tradegecko;
                        if($tradegecko->address) {
                            $tradegeckoAddress = $tradegecko->address;
                            $response['address'] = $tradegeckoAddress;
                        } else {
                            $tradegeckoAddress = new TradegeckoCustomerAddress();
                            $params = [
                                'address' => [
                                    'company_id' => $tradegecko->tradegeckoId,
                                    'label' => 'Shipping',
                                    'address1' => $this->customerEntityAddress->address,
                                    'address2' => $this->customerEntityAddress->address2,
                                    'city' => $this->customerEntityAddress->city,
                                    'country' => $this->customerEntityAddress->country,
                                    'state' => $this->customerEntityAddress->state,
                                    'zip_code' => $this->customerEntityAddress->zipcode,
                                ]
                            ];
                            $address = Yii::$app->tradeGecko->createAddress($params);
                            if($address) {
                                if(isset($address['id'])) {
                                    $tradegeckoAddress->setAttributes([
                                        'customerId' => $this->id,
                                        'tradegeckoCustomerId' => $address['company_id'],
                                        'tradegeckoAddressId' => $address['id']
                                    ]);
                                    if($tradegeckoAddress->save()) {
                                        $response['address'] = $tradegeckoAddress;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $response;
    }

    /**
     * Custome_entity_info Relation
     */
	public function getCustomer_entity_info()
    {
        return $this->hasMany(CustomerEntityInfo::className(), ['customer_id' => 'id']);
    }

    /**
     * Returns the number of members
     * @return int
     */
    public static function getTotalMembersCount()
    {
        //return CustomerEntity::find()->indexBy('id')->count();
        $searchModel = new CustomerEntitySearch();
        return $searchModel->search([])->byStatus()->dataProvider->totalCount;
    }

    /**
     * Returns the number of active members
     * @return int
     */
    public static function getActiveMembersCount()
    {
        $searchModel = new CustomerEntitySearch();
        return $searchModel->search([])->byStatus(CustomerEntity::LABEL_STATUS_ACTIVE)->dataProvider->totalCount;
    }

    /**
     * Returns the number of inactive members
     * @return int
     */
    public static function getInactiveMembersCount()
    {
        $searchModel = new CustomerEntitySearch();
        return $searchModel->search([])->byStatus(CustomerEntity::LABEL_STATUS_INACTIVE)->dataProvider->totalCount;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerEntityAttributes() {
        return $this->hasMany(CustomerEntityAttribute::className(), ['customer_id' => 'id'])->orderBy('attribute_id');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerEntityAttributes1() {
        return $this->hasMany(CustomerEntityAttribute::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerEntityAttributes2() {
        return $this->hasMany(CustomerEntityAttribute::className(), ['customer_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerEntityAttributes3() {
        return $this->hasMany(CustomerEntityAttribute::className(), ['customer_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerEntityAttributes4() {
        return $this->hasMany(CustomerEntityAttribute::className(), ['customer_id' => 'id']);
    }

    /**
     * @return static
     */
    public function getOrders() {
        return Order::find()->where(['customerId' => $this->id, 'status' => Order::STATUS_SENT])->groupBy(['id'])->orderBy(['id' => SORT_DESC]);
    }

    /**
     * @return static
     */
    public function getLatestOrders() {
        return $this->hasMany(Order::className(), ['customerId' => 'id'])->where(['status' => Order::STATUS_SENT])->orderBy(['id' => SORT_DESC])->limit(2);
    }
}
