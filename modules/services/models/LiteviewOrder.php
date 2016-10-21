<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "liteview_order".
 *
 * @property integer $id
 * @property string $ifs_order_number
 * @property string $created_at
 * @property string $updated_at
 * @property string $submitted_at
 * @property string $cancelled_at
 * @property string $tradegecko_order_ids
 * @property string $liteview_order_status
 *
 */
class LiteviewOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'liteview_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['ifs_order_number', 'cancelled_at', 'submitted_at', 'created_at', 'updated_at', 'tradegecko_order_ids', 'liteview_order_status'], 'string'],
        ];
    }
}
