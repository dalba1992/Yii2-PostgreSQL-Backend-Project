<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "tradegecko_user".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $action_items_email
 * @property string $avatar_url
 * @property integer $billing_contact
 * @property string $email
 * @property string $first_name
 * @property string $last_name
 * @property string $last_sign_in_at
 * @property string $location
 * @property integer $login_id
 * @property string $mobile
 * @property integer $notification_email
 * @property string $permissions
 * @property string $phone_number
 * @property integer $position
 * @property integer $sales_report_email
 * @property string $user_status
 * @property integer $account_id
 *
 */
class TradegeckoUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'billing_contact', 'login_id', 'position', 'account_id', 'notification_email', 'sales_report_email'], 'integer'],
            [['created_at', 'updated_at', 'action_items_email', 'avatar_url', 'email', 'first_name', 'last_name', 'last_sign_in_at', 'location', 'mobile', 'permissions', 'phone_number', 'user_status'], 'string'],
        ];
    }
}
