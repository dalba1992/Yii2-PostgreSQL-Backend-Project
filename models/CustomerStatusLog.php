<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customer_entity_status_log".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property string $status
 * @property string $date
 */
class CustomerStatusLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_entity_status_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id'], 'integer'],
            [['status', 'date'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'status' => 'Status',
            'date' => 'Date',
        ];
    }
}
