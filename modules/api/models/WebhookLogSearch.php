<?php

namespace app\modules\api\models;

use Yii;
use yii\data\ActiveDataProvider;

class WebhookLogSearch extends WebhookLog {

    public $fromDate;
    public $toDate;

    public function rules() {
        return [
            [['fromDate', 'toDate'], 'safe']
        ];
    }

    public function search($params) {

        $query = WebhookLog::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if(!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['>=', 'createdAt' , strtotime($this->fromDate)]);
        if($this->toDate) {
            $query->andFilterWhere(['between', 'createdAt', strtotime($this->fromDate), strtotime($this->toDate)]);
        }

        return $dataProvider;
    }

}