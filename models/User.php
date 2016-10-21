<?php

namespace app\models;

class User extends \yii\base\Object implements \yii\web\IdentityInterface
{
    public $id;
    public $username;
    public $password;
    public $authKey;
    public $accessToken;
    public $email;
    public $firstName;
    public $lastName;
    public $regDate;
    public $updateDate;
    public $profilePic;

    /*private static $users = [
        '100' => [
            'id' => '100',
            'username' => 'admin',
            'password' => 'admin',
            'authKey' => 'test100key',
            'accessToken' => '100-token',
        ],
        '101' => [
            'id' => '101',
            'username' => 'demo',
            'password' => 'demo',
            'authKey' => 'test101key',
            'accessToken' => '101-token',
        ],
    ];*/

    private static $users = [];

 	public static function tableName()
    {
        return 'admin_entity';
    }
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        $user_entity = UserEntity::find()->where(['id' => $id])->one();
        
        if ($user_entity != null)
        {
            $user = $user_entity->getAttributes();
            return new static($user);
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $user_entity = UserEntity::find()->where(['accessToken' => $token])->one();
        
        if ($user_entity != null)
        {
            $user = $user_entity->getAttributes();
            return new static($user);
        }

        return null;
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        $user_entity = UserEntity::find()->where(['username' => $username])->one();
        
        if ($user_entity != null)
        {
            $user = $user_entity->getAttributes();
            return new static($user);
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
    	return $this->password === md5($password);
    }
}
