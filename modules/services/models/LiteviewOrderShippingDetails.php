<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "liteview_order_shipping_details".
 *
 * @property integer $id
 * @property integer $liteview_order_id
 * @property string $ship_method
 * @property string $signature_requested
 * @property string $insurance_requested
 * @property double $insurance_value
 * @property string $saturday_delivery_requested
 * @property string $third_party_billing_requested
 * @property string $third_party_billing_account_no
 * @property string $third_party_billing_zip
 * @property string $third_party_country
 * @property string $general_description
 * @property string $content_description
 *
 */
class LiteviewOrderShippingDetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'liteview_order_shipping_details';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'liteview_order_id'], 'integer'],
            [['insurance_value'], 'double'],
            [['ship_method', 'signature_requested', 'insurance_requested', 'saturday_delivery_requested', 'third_party_billing_requested', 'third_party_billing_account_no', 'third_party_billing_zip', 'third_party_country', 'general_description', 'content_description'], 'string'],
        ];
    }
}
