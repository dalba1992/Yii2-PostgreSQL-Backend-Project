<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "liteview_order_items".
 *
 * @property integer $id
 * @property integer $liteview_order_id
 * @property string $inventory_item
 * @property string $inventory_item_sku
 * @property string $inventory_item_description
 * @property double $inventory_item_price
 * @property double $inventory_item_ext_price
 * @property double $inventory_item_qty
 * @property string $inventory_passthrough_01
 * @property string $inventory_passthrough_02
 * @property integer $tradegecko_currency_id
 *
 */
class LiteviewOrderItems extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'liteview_order_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'liteview_order_id', 'tradegecko_currency_id'], 'integer'],
            [['inventory_item_price', 'inventory_item_qty', 'inventory_item_ext_price'], 'double'],
            [[ 'inventory_item', 'inventory_item_sku', 'inventory_item_description', 'inventory_passthrough_01', 'inventory_passthrough_02'], 'string'],
        ];
    }
}
