<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "productimage".
 *
 * @property integer $id
 * @property integer $variantId
 * @property integer $tradegeckoId
 * @property integer $tradegeckoVariantId
 * @property string $uploaderId
 * @property string $name
 * @property string $position
 * @property string $basePath
 * @property string $fileName
 * @property string $versions
 * @property integer $imageProcessing
 * @property integer $createdAt
 * @property integer $updatedAt
 */
class ProductImage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'productimage';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['variantId', 'tradegeckoId', 'tradegeckoVariantId', 'imageProcessing', 'createdAt', 'updatedAt'], 'integer'],
            [['versions', 'basePath'], 'string'],
            [['uploaderId', 'name', 'position', 'fileName'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'updatedAt',
            ]
        ];
    }

    public function getVariant() {
        return $this->hasOne(ProductVariants::className(), ['tradegeckoId' => 'tradegeckoVariantId']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'variantId' => 'Variant ID',
            'tradegeckoId' => 'Tradegecko ID',
            'tradegeckoVariantId' => 'Tradegecko Variant ID',
            'uploaderId' => 'Uploader ID',
            'name' => 'Name',
            'position' => 'Position',
            'basePath' => 'Base Path',
            'fileName' => 'File Name',
            'versions' => 'Versions',
            'imageProcessing' => 'Image Processing',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
        ];
    }
}