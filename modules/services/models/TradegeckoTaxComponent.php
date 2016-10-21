<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "tradegecko_tax_component".
 *
 * @property integer $id
 * @property integer $tax_type_id
 * @property integer $position
 * @property string $label
 * @property string $rate
 * @property string $compound
 *
 */
class TradegeckoTaxComponent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_tax_component';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'tax_type_id', 'position'], 'integer'],
            [['label', 'rate', 'compound'], 'string'],
        ];
    }
}
