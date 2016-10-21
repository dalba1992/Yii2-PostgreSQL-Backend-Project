<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "tradegeckocustomer".
 *
 * @property integer $id
 * @property integer $customerId
 * @property integer $tradegeckoId
 * @property string $createdAt
 * @property string $updatedAt
 *
 * @property TradegeckoCustomerAddress $address
 */
class TradegeckoCustomer extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegeckocustomer';
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
            [['customerId', 'tradegeckoId'], 'integer'],
            [['createdAt', 'updatedAt'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customerId' => 'Customer ID',
            'tradegeckoId' => 'Tradegecko ID',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddress() {
        return $this->hasOne(TradegeckoCustomerAddress::className(), ['tradegeckoCustomerId' => 'tradegeckoId']);
    }
}
