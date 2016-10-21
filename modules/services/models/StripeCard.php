<?php

namespace app\modules\services\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "stripe_card".
 *
 * @property integer $id
 * @property string $customerId
 * @property string $cardId
 * @property string $last4
 * @property string $brand
 * @property string $funding
 * @property integer $exp_month
 * @property integer $exp_year
 * @property string $fingerprint
 * @property string $country
 * @property string $name
 * @property string $address_line1
 * @property string $address_line2
 * @property string $address_city
 * @property string $address_state
 * @property string $address_zip
 * @property string $address_country
 * @property string $cvc_check
 * @property string $address_line1_check
 * @property string $address_zip_check
 * @property string $tokenization_method
 * @property string $dynamic_last4
 * @property string $regDate
 * @property string $updateDate
 *
 */
class StripeCard extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stripe_card';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['exp_month', 'exp_year'], 'integer'],
            [['cardId', 'customerId', 'last4', 'brand', 'funding', 'fingerprint', 'country', 'name', 'address_line1', 'address_line2', 'address_city', 'address_state', 'address_zip', 'address_country', 'cvc_check', 'address_line1_check', 'address_zip_check', 'tokenization_method', 'dynamic_last4'], 'string', 'max' => 255],
            [['regDate', 'updateDate'], 'string', 'max' => 255]
        ];
    }
}
