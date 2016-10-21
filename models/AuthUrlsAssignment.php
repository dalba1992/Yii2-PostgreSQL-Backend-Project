<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "auth_urls".
 *
 * @property integer $id
 * @property string $role
 * @property integer auth_urls_id
 *
 */
class AuthUrlsAssignment extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_urls_assignment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['auth_urls_id'], 'integer'],
            [['role'], 'string', 'max' => 255]
        ];
    }
}
