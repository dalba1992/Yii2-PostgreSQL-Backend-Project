<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customer_entity_attribute".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property integer $attribute_id
 * @property integer $value
 * @property string $created_at
 * @property string $updated_at
 */
class CustomerEntityAttribute extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_entity_attribute';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'attribute_id', 'value'], 'integer'],
            [['created_at', 'updated_at'], 'string', 'max' => 50]
        ];
    }

    public function getTbAttribute() {
        return $this->hasOne(TbAttribute::className(), ['id' => 'attribute_id']);
    }

    public function getTbAttributeDetail() {
        return $this->hasOne(TbAttributeDetail::className(), ['id' => 'value', 'tb_attribute_id' => 'attribute_id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'attribute_id' => 'Attribute ID',
            'value' => 'Value',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
