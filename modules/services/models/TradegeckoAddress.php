<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "tradegecko_address".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $company_id
 * @property string $address1
 * @property string $address2
 * @property string $city
 * @property string $company_name
 * @property string $country
 * @property string $email
 * @property string $first_name
 * @property string $last_name
 * @property string $label
 * @property string $phone_number
 * @property string $state
 * @property string $address_status
 * @property string $suburb
 * @property string $zip_code
 *
 */
class TradegeckoAddress extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'company_id'], 'integer'],
            [['address1', 'address2', 'city', 'company_name', 'country', 'email', 'first_name', 'last_name', 'label', 'phone_number', 'state', 'address_status', 'suburb', 'zip_code'], 'string'],
            [['created_at', 'updated_at'], 'string']
        ];
    }
}
