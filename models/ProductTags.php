<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "producttag".
 *
 * @property integer $id
 * @property integer $frequency
 * @property string $name
 */
class ProductTags extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'producttag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['frequency'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'frequency' => 'Frequency',
            'name' => 'Name',
        ];
    }
}