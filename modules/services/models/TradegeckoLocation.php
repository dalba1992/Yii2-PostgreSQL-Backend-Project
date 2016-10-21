<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "tradegecko_location".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $address1
 * @property string $address2
 * @property string $city
 * @property string $country
 * @property integer $holds_stock
 * @property string $label
 * @property string $state
 * @property string $location_status
 * @property string $suburb
 * @property string $zip_code
 *
 */
class TradegeckoLocation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_location';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'holds_stock'], 'integer'],
            [['address1', 'address2', 'city', 'country', 'label', 'state', 'location_status', 'suburb', 'zip_code'], 'string'],
            [['created_at', 'updated_at'], 'string'],
        ];
    }
}
