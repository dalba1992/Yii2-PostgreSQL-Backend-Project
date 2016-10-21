<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customer_subscription_order".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property string $coupon_code
 * @property string $discount
 * @property string $tax_amount
 * @property string $shipping_description
 * @property string $shipping_amount
 * @property string $subtotal
 * @property string $subtotal_incl_tax
 * @property string $grand_total
 * @property string $status
 * @property string $created_at
 *
 * @property CustomerEntity $customer
 */
class CustomerSubscriptionOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_subscription_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id'], 'integer'],
            [['coupon_code'], 'string'],
            [['discount', 'tax_amount', 'shipping_description', 'shipping_amount', 'subtotal', 'subtotal_incl_tax', 'grand_total', 'status', 'created_at'], 'string', 'max' => 100]
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
            'coupon_code' => 'Coupon Code',
            'discount' => 'Discount',
            'tax_amount' => 'Tax Amount',
            'shipping_description' => 'Shipping Description',
            'shipping_amount' => 'Shipping Amount',
            'subtotal' => 'Subtotal',
            'subtotal_incl_tax' => 'Subtotal Incl Tax',
            'grand_total' => 'Grand Total',
            'status' => 'Status',
            'created_at' => 'Created At',
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
