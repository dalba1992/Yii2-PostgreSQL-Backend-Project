<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "liteview_order_shipping_contact".
 *
 * @property integer $id
 * @property integer $liteview_order_id
 * @property string $shipto_prefix
 * @property string $shipto_first_name
 * @property string $shipto_last_name
 * @property string $shipto_suffix
 * @property string $shipto_company_name
 * @property string $shipto_address1
 * @property string $shipto_address2
 * @property string $shipto_address3
 * @property string $shipto_city
 * @property string $shipto_state
 * @property string $shipto_postal_code
 * @property string $shipto_country
 * @property string $shipto_telephone_no
 * @property string $shipto_email
 *
 */
class LiteviewOrderShippingContact extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'liteview_order_shipping_contact';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'liteview_order_id'], 'integer'],
            [['shipto_prefix', 'shipto_first_name', 'shipto_last_name', 'shipto_suffix', 'shipto_company_name', 'shipto_address1', 'shipto_address2', 'shipto_address3', 'shipto_city', 'shipto_state', 'shipto_postal_code', 'shipto_country', 'shipto_telephone_no', 'shipto_email'], 'string'],
        ];
    }
}
