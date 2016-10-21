<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CustomerSubscriptionLog;

/**
 * CustomerSubscriptionLogSearch represents the model behind the search form about `app\models\CustomerSubscriptionLog`.
 */
class CustomerSubscriptionLogSearch extends CustomerSubscriptionLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'subscription_id', 'user_id'], 'integer'],
            [['start_date', 'status', 'reason', 'created_at', 'updated_at'], 'safe'],
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
        $query = CustomerSubscriptionLog::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->addOrderBy('created_at DESC, id DESC');

        $query->andFilterWhere([
            'id' => $this->id,
            'subscription_id' => $this->subscription_id,
            'user_id' => $this->user_id,
        ]);

        $query->andFilterWhere(['like', 'date', $this->date])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'reason', $this->reason]);

        return $dataProvider;
    }
}