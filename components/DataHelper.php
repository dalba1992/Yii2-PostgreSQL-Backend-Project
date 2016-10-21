<?php
namespace app\components;

use Yii;
use yii\base\Component;
use app\models\TbAttribute;

class DataHelper extends Component
{

    public function getAttributes()
    {
        $attributes = TbAttribute::find()->orderBy(['id'=>SORT_ASC])->All();
        return $attributes;
    }

}