<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "tradegeckocustomeraddress".
 *
 * @property integer $id
 * @property integer $customerId
 * @property integer $tradegeckoCustomerId
 * @property integer $tradegeckoAddressId
 * @property string $createdAt
 * @property string $updatedAt
 */
class TradegeckoCustomerAddress extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegeckocustomeraddress';
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
            [['customerId', 'tradegeckoCustomerId', 'tradegeckoAddressId'], 'integer'],
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
            'tradegeckoCustomerId' => 'Tradegecko Customer ID',
            'tradegeckoAddressId' => 'Tradegecko Address ID',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
        ];
    }
}
