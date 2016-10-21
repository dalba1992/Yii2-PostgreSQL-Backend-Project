<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tb_product_variants_image".
 *
 * @property integer $id
 * @property integer $variant_id
 * @property string $img_url
 *
 * @property TbProductVariants $variant
 */
class Variantsimage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_product_variants_image';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['variant_id'], 'integer'],
            [['img_url'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'variant_id' => 'Variant ID',
            'img_url' => 'Img Url',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVariant()
    {
        return $this->hasOne(TbProductVariants::className(), ['id' => 'variant_id']);
    }
}
