<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "tradegecko_variant_committed_stock_level".
 *
 * @property integer $id
 * @property integer $variant_id
 * @property integer $committed_stock_level_id
 * @property string $committed_stock_level_value
 *
 */
class TradegeckoVariantCommittedStockLevel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_variant_committed_stock_level';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'variant_id', 'committed_stock_level_id'], 'integer'],
            [['committed_stock_level_value'], 'string'],
        ];
    }
}
