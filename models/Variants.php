<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tb_product_variants".
 *
 * @property integer $id
 * @property integer $product_entity_info_id
 * @property integer $variant_id
 * @property string $sku
 * @property double $wholesale_price
 * @property double $retail_price
 * @property double $buy_price
 * @property integer $qty
 * @property string $created_at
 * @property string $updated_at
 *
 * @property TbProductVariantsImage[] $tbProductVariantsImages
 * @property TbProductEntityInfo $productEntityInfo
 */
class Variants extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_product_variants';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_entity_info_id', 'variant_id', 'qty'], 'integer'],
            [['sku'], 'string'],
            [['wholesale_price', 'retail_price', 'buy_price'], 'number'],
            [['created_at', 'updated_at'], 'safe']
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
            'variant_id' => 'Variant ID',
            'sku' => 'Sku',
            'wholesale_price' => 'Wholesale Price',
            'retail_price' => 'Retail Price',
            'buy_price' => 'Buy Price',
            'qty' => 'Qty',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTbProductVariantsImages()
    {
        return $this->hasMany(TbProductVariantsImage::className(), ['variant_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductEntityInfo()
    {
        return $this->hasOne(TbProductEntityInfo::className(), ['id' => 'product_entity_info_id']);
    }
}
