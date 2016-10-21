<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "tradegecko_currency".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $delimiter
 * @property string $currency_format
 * @property string $iso
 * @property string $currency_name
 * @property integer $currency_precision
 * @property string $rate
 * @property string $currency_separator
 * @property string $symbol
 * @property string $currency_status
 * @property integer $subunit
 *
 */
class TradegeckoCurrency extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_currency';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'currency_precision', 'subunit'], 'integer'],
            [['delimiter', 'currency_format', 'iso', 'currency_name', 'rate', 'currency_separator', 'symbol', 'currency_status'], 'string'],
            [['created_at', 'updated_at'], 'string']
        ];
    }
}
