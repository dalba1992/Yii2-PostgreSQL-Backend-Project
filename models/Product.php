<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tb_product_entity_info".
 *
 * @property integer $id
 * @property integer $tradegecko_product_id
 * @property string $name
 * @property integer $category_id
 * @property integer $brand_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property TbProductAttributeValue[] $tbProductAttributeValues
 * @property TbProductVariants[] $tbProductVariants
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_product_entity_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tradegecko_product_id', 'category_id', 'brand_id'], 'integer'],
            [['name'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['tradegecko_product_id'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tradegecko_product_id' => 'Tradegecko Product ID',
            'name' => 'Name',
            'category_id' => 'Category ID',
            'brand_id' => 'Brand ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTbProductAttributeValues()
    {
        return $this->hasMany(TbProductAttributeValue::className(), ['tradegecko_product_id' => 'tradegecko_product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTbProductVariants()
    {
        return $this->hasMany(TbProductVariants::className(), ['tradegecko_product_id' => 'tradegecko_product_id']);
    }
}
