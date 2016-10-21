<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customer_entity_info".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property string $email
 * @property string $password
 * @property string $birth_date
 * @property string $phone
 * @property string $created_at
 * @property string $updated_at
 * @property string $fb_token
 *
 * @property CustomerEntity $customer
 */
class CustomerEntityInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
	public static function model($className=__CLASS__)
        {
            return parent::model($className);
        }
    public static function tableName()
    {
        return 'customer_entity_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id'], 'integer'],
            [['password'], 'string'],
            [['email', 'birth_date', 'phone', 'created_at', 'updated_at'], 'string', 'max' => 50],
            [['fb_token'], 'string', 'max' => 100]
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
            'email' => 'Email',
            'password' => 'Password',
            'birth_date' => 'Birth Date',
            'phone' => 'Phone',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'fb_token' => 'Fb Token',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer_entity()
    {
        return $this->hasOne(CustomerEntity::className(), ['id' => 'customer_id']);
    }
}
