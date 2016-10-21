<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "tradegecko_payment_term".
 *
 * @property integer $id
 * @property string $payment_term_name
 * @property integer $due_in_days
 * @property string $payment_term_status
 * @property string $payment_term_from
 *
 */
class TradegeckoPaymentTerm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_payment_term';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'due_in_days'], 'integer'],
            [['payment_term_name', 'payment_term_status', 'payment_term_from'], 'string'],
        ];
    }
}
