<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "tradegecko_image".
 *
 * @property integer $id
 * @property integer $variant_id
 * @property integer $uploader_id
 * @property string $image_name
 * @property integer $position
 * @property string $base_path
 * @property string $file_name
 * @property string $versions
 * @property string $image_processing
 *
 */
class TradegeckoImage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_image';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'variant_id', 'uploader_id', 'position'], 'integer'],
            [['image_name', 'base_path', 'file_name', 'versions', 'image_processing'], 'string'],
        ];
    }
}
