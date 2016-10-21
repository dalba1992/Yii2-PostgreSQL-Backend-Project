<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "productorderlog".
 *
 * @property integer $id
 * @property integer $customerId
 * @property integer $orderId
 * @property integer $productId
 * @property string $createdAt
 * @property string $updatedAt
 *
 * @property Order $order
 * @property Products $product
 */
class ProductOrderLog extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'productorderlog';
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
            [['customerId', 'orderId', 'productId'], 'integer'],
            [['createdAt', 'updatedAt'], 'safe']
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder() {
        return $this->hasOne(Order::className(), ['id' => 'orderId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct() {
        return $this->hasOne(ProductVariants::className(), ['id' => 'productId']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customerId' => 'Customer ID',
            'orderId' => 'Order ID',
            'productId' => 'Product ID',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
        ];
    }
}
