<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tradegecko_importcsv_order_items_log".
 *
 * @property integer $id
 * @property integer $log_order_id
 * @property string $item_id
 */
class TradegeckoOderItemsLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_importcsv_order_items_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['log_order_id','customer_id'], 'integer'],
            [['item_id'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'log_order_id' => 'Log Order ID',
            'item_id' => 'Item ID',
			'customer_id' => 'Customer ID'
        ];
    }
}
