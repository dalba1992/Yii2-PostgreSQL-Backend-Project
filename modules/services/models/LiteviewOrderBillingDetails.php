<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "liteview_order_billing_details".
 *
 * @property integer $id
 * @property integer $liteview_order_id
 * @property double $sub_total
 * @property double $shipping_handling
 * @property double $sales_tax_total
 * @property double $discount_total
 *
 */
class LiteviewOrderBillingDetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'liteview_order_billing_details';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'liteview_order_id'], 'integer'],
            [['sub_total', 'shipping_handling', 'sales_tax_total', 'discount_total'], 'double'],
        ];
    }
}
