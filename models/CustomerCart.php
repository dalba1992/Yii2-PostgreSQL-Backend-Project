<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\grid\DataColumn;
use yii\helpers\VarDumper;
use yii\db\ActiveRecord;
use yii\db\Expression;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "customer_cart".
 *
 * @property integer $id
 * @property integer $customerId
 * @property string $data
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 *
 */
class CustomerCart extends \yii\db\ActiveRecord
{
    /**
     * Cart entity state. Can be either draft(still adding stuff) or order(meaning the cart has been checked out).
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_ORDER = 'order';
    const STATUS_EXPIRED = 'expired';
    const EXPIRE = 30; 

    public static function tableName()
    {
        return 'customer_cart';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'customerId'], 'integer'],
            [['data', 'status'], 'string'],
            [['createdAt', 'updatedAt'], 'string']
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
            'status' => 'Status',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'updatedAt',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['createdAt', 'updatedAt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updatedAt'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * Previously sent products
     * @return \yii\db\ActiveQuery
     */
    public function getCart($customerId) {
        $dt = date('Y-m-d H:i:s', strtotime('-'.CustomerCart::EXPIRE.' days'));
        $models = $this->find()->where(['status'=>CustomerCart::STATUS_DRAFT, 'customerId'=>$customerId])->andWhere(['>', 'updatedAt', $dt])->all();
        return $models;
    }
}
