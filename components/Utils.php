<?php

namespace app\components;
use Yii;
use app\models\CustomerEntityStatus;
use yii\base\Component;
use app\models\CustomerEntity;
use app\models\CustomerSubscription;
use app\models\CustomerEntityAddress;

class Utils extends Component
{
    /**
     * Log a message either to file, either to screen/console
     * @param $message
     * @param array $toFile
     * @param $isConsole
     */
    public function log($message, $params = [], $isConsole = false)
    {
        if($isConsole)
            echo $message."\n";
        elseif(empty($params))
            echo $message;
        else
            Yii::$app->log($message, $params);
    }

    public function logToConsole($message)
    {
        $this->log($message, [], true);
    }

    public function logToFile($message, $params = [])
    {
        $this->log($message, $params);
    }
}

