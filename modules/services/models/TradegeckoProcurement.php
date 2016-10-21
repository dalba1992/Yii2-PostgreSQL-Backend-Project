<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "tradegecko_procurement".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $received_at
 * @property integer $purchase_order_id
 * @property string $purchase_order_line_item_ids
 *
 */
class TradegeckoProcurement extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_procurement';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'purchase_order_id'], 'integer'],
            [['created_at', 'updated_at', 'received_at', 'purchase_order_line_item_ids'], 'string'],
        ];
    }
}
