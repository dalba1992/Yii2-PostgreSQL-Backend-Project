<?php

namespace app\modules\services\models;

use Yii;

/**
 * This is the model class for table "liteview_order_notes".
 *
 * @property integer $id
 * @property integer $liteview_order_id
 * @property string $note_type
 * @property string $note_description
 * @property string $show_on_ps
 *
 */
class LiteviewOrderNotes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'liteview_order_notes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'liteview_order_id'], 'integer'],
            [['note_type', 'note_description', 'show_on_ps'], 'string'],
        ];
    }
}
