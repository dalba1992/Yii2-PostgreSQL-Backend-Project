<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "liteview_order_billing_contact".
 *
 * @property integer $id
 * @property integer $liteview_order_id
 * @property string $billto_prefix
 * @property string $billto_first_name
 * @property string $billto_last_name
 * @property string $billto_suffix
 * @property string $billto_company_name
 * @property string $billto_address1
 * @property string $billto_address2
 * @property string $billto_address3
 * @property string $billto_city
 * @property string $billto_state
 * @property string $billto_postal_code
 * @property string $billto_country
 * @property string $billto_telephone_no
 * @property string $billto_email
 *
 */
class LiteviewOrderBillingContact extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'liteview_order_billing_contact';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'liteview_order_id'], 'integer'],
            [['billto_prefix', 'billto_first_name', 'billto_last_name', 'billto_suffix', 'billto_company_name', 'billto_address1', 'billto_address2', 'billto_address3', 'billto_city', 'billto_state', 'billto_postal_code', 'billto_country', 'billto_telephone_no', 'billto_email'], 'string'],
        ];
    }
}
