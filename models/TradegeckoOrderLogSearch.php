<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TradegeckoOrderLog;

/**
 * TradegeckoOrderLogSearch represents the model behind the search form about `app\models\TradegeckoOrderLog`.
 */
class TradegeckoOrderLogSearch extends TradegeckoOrderLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'customer_id', 'tradegecko_order_id'], 'integer'],
            [['created_at', 'import_csv_title', 'tradegecko_order_number'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = TradegeckoOrderLog::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'tradegecko_order_id' => $this->tradegecko_order_id,
        ]);

        $query->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'import_csv_title', $this->import_csv_title])
            ->andFilterWhere(['like', 'tradegecko_order_number', $this->tradegecko_order_number]);

        return $dataProvider;
    }
}
