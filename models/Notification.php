<?php

namespace app\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

use app\models;

/**
 * This is the model class for table "notification".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property string $type
 * @property string $date
 */
class Notification extends \yii\db\ActiveRecord
{
    const TYPE_RESUME = 'resume';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id'], 'integer'],
            [['type', 'notify_next'], 'string', 'max' => 50]
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
            'type' => 'Type',
            'notify_next' => 'Date for the next notification',
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => function(){ return date("Y-m-d H:i:s");},
            ],
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
