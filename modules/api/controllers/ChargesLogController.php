<?php

namespace app\modules\api\controllers;

use app\modules\api\models\WebhookLogSearch;
use Yii;
use yii\web\Controller;

class ChargesLogController extends Controller {

    public function actionIndex() {

        $model = new WebhookLogSearch();
        $dataProvider = $model->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

}