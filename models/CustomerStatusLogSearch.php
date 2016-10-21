<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CustomerStatusLog;

/**
 * CustomerStatusLogSearch represents the model behind the search form about `app\models\CustomerStatusLog`.
 */
class CustomerStatusLogSearch extends CustomerStatusLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'customer_id'], 'integer'],
            [['status', 'date'], 'safe'],
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
        $query = CustomerStatusLog::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'pagination' => array('defaultPageSize' => 100),
			'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'customer_id' => $this->customer_id,
        ]);

        $query->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'date', $this->date]);

        return $dataProvider;
    }
}
