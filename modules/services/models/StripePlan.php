<?php

namespace app\modules\services\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "stripe_plan".
 *
 * @property integer $id
 * @property string $planId
 * @property string $interval
 * @property string $name
 * @property integer $created
 * @property double $amount
 * @property string $currency
 * @property string $livemode
 * @property integer $interval_count
 * @property integer $trial_period_days
 * @property string $statement_descriptor
 * @property string $statement_description
 * @property string $regDate
 * @property string $updateDate
 *
 */
class StripePlan extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stripe_plan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['amount'], 'double'],
            [['created', 'interval_count', 'trial_period_days'], 'integer'],
            [['planId', 'interval', 'name', 'currency', 'livemode', 'statement_descriptor', 'statement_description'], 'string', 'max' => 255],
            [['regDate', 'updateDate'], 'string', 'max' => 255]
        ];
    }
}
