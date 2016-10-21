<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tb_product_attribute".
 *
 * @property integer $id
 * @property string $attribute_name
 *
 * @property TbProductAttributeOption[] $tbProductAttributeOptions
 * @property TbProductAttributeValue[] $tbProductAttributeValues
 */
class Productattribute extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_product_attribute';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['attribute_name'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'attribute_name' => 'Attribute Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
	 /*
    public function getTbProductAttributeOptions()
    {
        return $this->hasMany(TbProductAttributeOption::className(), ['attribute_id' => 'id']);
    }
    public function getTbProductAttributeValues()
    {
        return $this->hasMany(TbProductAttributeValue::className(), ['attribute_id' => 'id']);
    } */
}
