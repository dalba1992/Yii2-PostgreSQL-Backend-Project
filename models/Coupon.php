<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "coupon_record".
 *
 * @property integer $id
 * @property string $coupon_name
 * @property string $coupon_code
 * @property string $message
 * @property integer $discount_type
 * @property double $amount_off
 * @property integer $duration
 * @property integer $duration_time
 * @property string $created_at
 * @property integer $is_apply
 */
class Coupon extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'coupon_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['coupon_code'], 'string'],
            [['discount_type', 'duration', 'duration_time', 'is_apply', 'use_count'], 'integer'],
            [['amount_off'], 'number'],
            [['created_at'], 'safe'],
            [['coupon_name'], 'string', 'max' => 100],
			[['coupon_code'], 'unique'],
			[['message'], 'safe'],
			[['status'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'coupon_name' => 'Coupon Name',
            'coupon_code' => 'Coupon Code',
            'discount_type' => 'Discount Type',
            'amount_off' => 'Amount Off',
            'duration' => 'Duration',
            'duration_time' => 'Duration Time',
            'created_at' => 'Created At',
            'is_apply' => 'Is Apply',
			'use_count' => 'Use Count',
			'message' => 'Coupon Message',
			'status' => 'Status',
        ];
    }
}
