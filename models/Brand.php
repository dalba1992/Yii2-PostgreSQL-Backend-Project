<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tb_product_brand".
 *
 * @property integer $id
 * @property string $brand
 */
class Brand extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_product_brand';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['brand'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'brand' => 'Brand',
        ];
    }
}
