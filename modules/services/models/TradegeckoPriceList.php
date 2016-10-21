<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "tradegecko_price_list".
 *
 * @property string $id
 * @property string $price_list_code
 * @property string $price_list_name
 * @property string $is_cost
 * @property integer $currency_id
 * @property string $currency_symbol
 * @property string $currency_iso
 * @property integer $is_default
 *
 */
class TradegeckoPriceList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_price_list';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_default', 'currency_id'], 'integer'],
            [['id', 'price_list_code', 'price_list_name', 'is_cost', 'currency_symbol', 'currency_iso'], 'string'],
        ];
    }
}
