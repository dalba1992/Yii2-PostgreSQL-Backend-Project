<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;


/**
 * This is the model class for table "orderlog".
 *
 * @property integer $id
 * @property integer $orderId
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 */
class OrderLog extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orderlog';
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
            [['orderId'], 'required'],
            [['orderId', 'userId', 'tradegeckoOrderId'], 'integer'],
            [['createdAt', 'updatedAt', 'tradegeckoOrderId', 'liteviewOrderNo', 'userId'], 'safe'],
            [['status'], 'string', 'max' => 255]
        ];
    }

    /**
     * Get order details
     * @return \yii\db\ActiveQuery
     */
    public function getOrder() {
        return $this->hasOne(Order::className(), ['id' => 'orderId']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'orderId' => 'Order ID',
            'status' => 'Status',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
        ];
    }
}