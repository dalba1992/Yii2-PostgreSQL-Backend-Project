<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "tradegecko_tax_type".
 *
 * @property integer $id
 * @property string $tax_type_code
 * @property string $imported_from
 * @property string $tax_type_name
 * @property string $tax_type_status
 * @property integer $quickbooks_online_id
 * @property integer $xero_online_id
 * @property string $effective_rate
 * @property string $tax_component_ids
 *
 */
class TradegeckoTaxType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_tax_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'quickbooks_online_id', 'xero_online_id'], 'integer'],
            [['tax_type_code', 'imported_from', 'tax_type_name', 'tax_type_status', 'effective_rate', 'tax_component_ids'], 'string'],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTradegeckoTaxComponent() {
        return $this->hasOne(TradegeckoTaxComponent::className(), ['tax_type_id' => 'id']);
    }
}
