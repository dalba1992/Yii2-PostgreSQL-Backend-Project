<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tb_product_attribute_option".
 *
 * @property integer $id
 * @property integer $attribute_id
 * @property string $attribute_value
 *
 * @property TbProductAttribute $attribute
 * @property TbProductAttributeValue[] $tbProductAttributeValues
 */
class Productattributeoption extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_product_attribute_option';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['attribute_id'], 'integer'],
            [['attribute_value'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'attribute_id' => 'Attribute ID',
            'attribute_value' => 'Attribute Value',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    /*public function getAttribute()
    {
        return $this->hasOne(TbProductAttribute::className(), ['id' => 'attribute_id']);
    }

    public function getTbProductAttributeValues()
    {
        return $this->hasMany(TbProductAttributeValue::className(), ['attribute_option_id' => 'id']);
    }*/
}
