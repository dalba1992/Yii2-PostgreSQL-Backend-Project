<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "tradegecko_contact".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $company_id
 * @property string $email
 * @property string $fax
 * @property string $first_name
 * @property string $last_name
 * @property string $location
 * @property string $mobile
 * @property string $notes
 * @property string $phone_number
 * @property string $position
 * @property string $contact_status
 * @property string $iguana_admin
 * @property string $online_ordering
 * @property string $invitation_accepted_at
 * @property string $phone
 *
 */
class TradegeckoContact extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tradegecko_contact';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'company_id'], 'integer'],
            [['fax', 'location', 'mobile', 'notes', 'position', 'email', 'first_name', 'last_name', 'contact_status', 'phone_number', 'iguana_admin', 'online_ordering', 'invitation_accepted_at', 'phone'], 'string'],
            [['created_at', 'updated_at'], 'string']
        ];
    }
}
