<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tb_attribute_detail".
 *
 * @property integer $id
 * @property integer $tb_attribute_id
 * @property string $title
 * @property string $description
 * @property boolean $is_image
 * @property integer $sort_order
 */
class TbAttributeDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_attribute_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tb_attribute_id', 'sort_order'], 'integer'],
            [['description'], 'string'],
            [['is_image'], 'boolean'],
            [['title'], 'string', 'max' => 300]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tb_attribute_id' => 'Tb Attribute ID',
            'title' => 'Title',
            'description' => 'Description',
            'is_image' => 'Is Image',
            'sort_order' => 'Sort Order',
        ];
    }
}
