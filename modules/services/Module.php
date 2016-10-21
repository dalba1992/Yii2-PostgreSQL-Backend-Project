<?php

namespace app\modules\services;

class Module extends \yii\base\Module implements \yii\base\BootstrapInterface
{
    public $controllerNamespace = 'app\modules\services\controllers';

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'app\modules\services\commands';
        }
    }
}
