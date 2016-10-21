<?php

namespace app\modules\api\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "webhooklog".
 *
 * @property integer $id
 * @property string $stripeCustomerId
 * @property string $data
 * @property integer $createdAt
 * @property integer $updatedAt
 */
class WebhookLog extends \yii\db\ActiveRecord
{

    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'updatedAt'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'webhooklog';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['stripeCustomerId'], 'required'],
            [['data'], 'string'],
            [['createdAt', 'updatedAt'], 'integer'],
            [['stripeCustomerId'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'stripeCustomerId' => 'Stripe Customer ID',
            'data' => 'Data',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
        ];
    }
}