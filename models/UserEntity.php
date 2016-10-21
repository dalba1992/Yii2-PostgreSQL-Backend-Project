<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "user_entity".
 *
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $firstName
 * @property string $lastName
 * @property string $password
 * @property string $authKey
 * @property string $accessToken
 * @property integer $updateDate
 * @property integer $profilePic
 *
 */
class UserEntity extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_entity';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email', 'firstName', 'lastName', 'password', 'authKey', 'accessToken', 'profilePic'], 'string', 'max' => 255],
            [['regDate', 'updateDate'], 'integer']
        ];
    }
}
