<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tb_attribute".
 *
 * @property integer $id
 * @property string $title
 * @property boolean $is_multiselect
 * @property boolean $is_active
 * @property boolean $is_btn
 * @property string $tooltip_bar_title
 */
class TbAttribute extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_attribute';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'string'],
            [['is_multiselect', 'is_active', 'is_btn'], 'boolean'],
            [['tooltip_bar_title'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'is_multiselect' => 'Is Multiselect',
            'is_active' => 'Is Active',
            'is_btn' => 'Is Btn',
            'tooltip_bar_title' => 'Tooltip Bar Title',
        ];
    }
}
