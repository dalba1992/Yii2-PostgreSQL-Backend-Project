<?php

namespace app\commands;

use yii\console\Controller;

/**
 * Test controller
 */
class StatusController extends Controller{

    public function actionIndex(){
        echo "Cron service running";
    }

}