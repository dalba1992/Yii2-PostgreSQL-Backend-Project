<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tb_product_attribute_value".
 *
 * @property integer $id
 * @property integer $product_entity_info_id
 * @property integer $tradegecko_product_id
 * @property integer $variant_id
 * @property integer $attribute_id
 * @property integer $attribute_option_id
 *
 * @property TbProductEntityInfo $productEntityInfo
 * @property TbProductAttribute $attribute
 * @property TbProductAttributeOption $attributeOption
 */
class Productattributevalue extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_product_attribute_value';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_entity_info_id', 'tradegecko_product_id', 'variant_id', 'attribute_id', 'attribute_option_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_entity_info_id' => 'Product Entity Info ID',
            'tradegecko_product_id' => 'Tradegecko Product ID',
            'variant_id' => 'Variant ID',
            'attribute_id' => 'Attribute ID',
            'attribute_option_id' => 'Attribute Option ID',
        ];
    }

  /*
    public function getProductEntityInfo()
    {
        return $this->hasOne(TbProductEntityInfo::className(), ['id' => 'product_entity_info_id']);
    }

    public function getAttribute()
    {
        return $this->hasOne(TbProductAttribute::className(), ['id' => 'attribute_id']);
    }
	
    public function getAttributeOption()
    {
        return $this->hasOne(TbProductAttributeOption::className(), ['id' => 'attribute_option_id']);
    }
	*/
}
