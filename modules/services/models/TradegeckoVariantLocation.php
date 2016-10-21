<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "tradegecko_variant_location".
 *
 * @property integer $id
 * @property integer $variant_id
 * @property integer $location_id
 * @property string $stock_on_hand
 * @property integer $committed
 * @property string $bin_location
 * @property integer $reorder_point
 *
 */
class TradegeckoVariantLocation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_variant_location';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'variant_id', 'location_id', 'committed', 'reorder_point'], 'integer'],
            [['bin_location', 'stock_on_hand'], 'string'],
        ];
    }
}
