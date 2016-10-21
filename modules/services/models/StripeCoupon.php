<?php

namespace app\modules\services\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "stripe_coupon".
 *
 * @property integer $id
 * @property string $couponId
 * @property integer $created
 * @property integer $percent_off
 * @property integer $amount_off
 * @property string $currency
 * @property string $livemode
 * @property string $duration
 * @property integer $redeem_by
 * @property integer $max_redemptions
 * @property integer $times_redeemed
 * @property integer $duration_in_months
 * @property string $valid
 * @property string $regDate
 * @property string $updateDate
 *
 */
class StripeCoupon extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stripe_coupon';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created', 'percent_off', 'amount_off', 'redeem_by', 'max_redemptions', 'times_redeemed', 'duration_in_months'], 'integer'],
            [['couponId', 'currency', 'livemode', 'duration', 'valid'], 'string', 'max' => 255],
            [['regDate', 'updateDate'], 'string', 'max' => 255]
        ];
    }
}
