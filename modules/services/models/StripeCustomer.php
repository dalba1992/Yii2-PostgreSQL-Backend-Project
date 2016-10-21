<?php

namespace app\modules\services\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "stripe_customer".
 *
 * @property integer $id
 * @property string $customerId
 * @property integer $created
 * @property string $email
 * @property double $account_balance
 * @property string $currency
 * @property string $livemode
 * @property string $default_card
 * @property string $default_source
 * @property string $description
 * @property string $regDate
 * @property string $updateDate
 *
 */
class StripeCustomer extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stripe_customer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_balance'], 'double'],
            [['created'], 'integer'],
            [['customerId', 'email', 'currency', 'livemode', 'default_card', 'default_source'], 'string', 'max' => 255],
            [['regDate', 'updateDate'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 255]
        ];
    }
}
