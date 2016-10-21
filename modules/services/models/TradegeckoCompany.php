<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "tradegecko_company".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $assignee_id
 * @property integer $default_ledger_account_id
 * @property integer $default_payment_term_id
 * @property integer $default_stock_location_id
 * @property integer $default_tax_type_id
 * @property string $company_code
 * @property string $company_type
 * @property double $default_discount_rate
 * @property string $default_price_list_id
 * @property double $default_tax_rate
 * @property string $default_document_theme_id
 * @property string $description
 * @property string $email
 * @property string $fax
 * @property string $company_name
 * @property string $phone_number
 * @property string $company_status
 * @property string $tax_number
 * @property string $website
 * @property string $address_ids
 * @property string $contact_ids
 * @property string $note_ids
 * @property string $default_price_type_id
 *
 */
class TradegeckoCompany extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_company';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'assignee_id', 'default_ledger_account_id', 'default_payment_term_id', 'default_stock_location_id', 'default_tax_type_id'], 'integer'],
            [['default_discount_rate', 'default_tax_rate'], 'double'],
            [['company_code', 'company_type', 'default_price_list_id', 'default_document_theme_id', 'description', 'email', 'fax', 'company_name', 'phone_number', 'company_status', 'tax_number', 'website', 'address_ids', 'contact_ids', 'note_ids', 'default_price_type_id'], 'string'],
            [['created_at', 'updated_at'], 'string']
        ];
    }
}
