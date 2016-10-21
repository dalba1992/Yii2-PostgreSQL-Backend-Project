<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "tradegecko_variant_incoming_stock_level".
 *
 * @property integer $id
 * @property integer $variant_id
 * @property integer $incoming_stock_level_id
 * @property string $incoming_stock_level_value
 *
 */
class TradegeckoVariantIncomingStockLevel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_variant_incoming_stock_level';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'variant_id', 'incoming_stock_level_id'], 'integer'],
            [['incoming_stock_level_value'], 'string'],
        ];
    }
}
