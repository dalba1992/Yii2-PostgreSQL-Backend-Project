<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "auth_urls".
 *
 * @property integer $id
 * @property string $controller
 * @property string $action
 * @property string $description
 *
 */
class AuthUrls extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_urls';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['controller', 'action', 'description'], 'string', 'max' => 255]
        ];
    }
}
