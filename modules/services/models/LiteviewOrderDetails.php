<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "liteview_order_details".
 *
 * @property integer $id
 * @property integer $liteview_order_id
 * @property string $order_status
 * @property string $order_date
 * @property string $order_number
 * @property string $order_source
 * @property string $order_type
 * @property string $catalog_name
 * @property string $gift_order
 * @property string $po_number
 * @property string $passthrough_field_01
 * @property string $passthrough_field_02
 * @property string $future_date_release
 * @property string $allocate_inventory_now
 *
 */
class LiteviewOrderDetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'liteview_order_details';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'liteview_order_id'], 'integer'],
            [['order_status', 'order_date', 'order_number', 'order_source', 'order_type', 'catalog_name', 'gift_order', 'po_number', 'passthrough_field_01', 'passthrough_field_02', 'future_date_release', 'allocate_inventory_now'], 'string'],
        ];
    }
}
