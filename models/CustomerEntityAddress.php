<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customer_entity_address".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property integer $type_id
 * @property string $first_name
 * @property string $last_name
 * @property string $address
 * @property string $address2
 * @property string $city
 * @property string $state
 * @property string $country
 * @property string $zipcode
 * @property integer $is_default
 * @property string $created_at
 * @property string $updated_at
 *
 * @property CustomerEntity $customer
 */
class CustomerEntityAddress extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_entity_address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'type_id', 'is_default'], 'integer'],
            [['address', 'address2'], 'string'],
            [['first_name', 'last_name', 'city', 'state', 'country', 'zipcode', 'created_at', 'updated_at'], 'string', 'max' => 50]
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
            'type_id' => 'Type ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'address' => 'Address',
            'address2' => 'Address2',
            'city' => 'City',
            'state' => 'State',
            'country' => 'Country',
            'zipcode' => 'Zipcode',
            'is_default' => 'Is Default',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(CustomerEntity::className(), ['id' => 'customer_id']);
    }
}
