<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tb_attribute_detail_image".
 *
 * @property integer $id
 * @property integer $attribute_detail_id
 * @property string $image
 * @property string $title
 */
class TbAttributeDetailImage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_attribute_detail_image';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['attribute_detail_id'], 'integer'],
            [['image', 'title'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'attribute_detail_id' => 'Attribute Detail ID',
            'image' => 'Image',
            'title' => 'Title',
        ];
    }
}
