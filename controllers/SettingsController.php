<?php

namespace app\controllers;

use app\models\Products;
use app\models\ProductSearch;
use app\models\ProductTags;
use app\models\ProductVariants;
use Yii;
use app\components\AController;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\HttpException;

class SettingsController extends AController {

    public function actionIndex() {
        die('boom oklahoma!');
    }

    public function actionParams() {
        $location = __DIR__ . '/../config/params.php';
        $params = $this->parseCode($location);
        echo $this->render('params', ['params'=>$params]);
    }

    protected  function parseCode($location) {
        $code = file_get_contents($location);
        $code = trim($code, '<?php php?>');
        return eval($code);
    }


}