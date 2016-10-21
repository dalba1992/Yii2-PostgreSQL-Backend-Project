<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customer_entity_status".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property string $status
 * @property string $created_at
 *
 * @property CustomerEntity $customer
 */
class CustomerEntityStatus extends \yii\db\ActiveRecord
{
    const STATUS_SIGNUP = 'signup';
    const STATUS_REGISTER = 'register';
    const STATUS_DECLINED = 'declined';
    const STATUS_ACTIVE = 'active';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_entity_status';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id'], 'integer'],
            [['created_at'], 'string'],
            [['status'], 'string', 'max' => 50]
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
