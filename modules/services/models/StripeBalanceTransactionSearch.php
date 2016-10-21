<?php

namespace app\modules\services\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\services\models\StripeBalanceTransaction;

class StripeBalanceTransactionSearch extends StripeBalanceTransaction
{
    public $dataProvider = null;
    /**
     * @inheritdoc
     */
    //   public $enableMultiSort = true;

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
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = StripeBalanceTransaction::find();

        $this->load($params); // load query params, but don't return just yet.

        $this->dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array('defaultPageSize' => 10),
            'sort'=> ['defaultOrder' => ['created'=>SORT_DESC]],
        ]);

        // enable sorting for the related column
        $this->dataProvider->sort->attributes['id'] = [
            'asc' => ['id' => SORT_ASC],
            'desc' => ['id' => SORT_DESC],
        ];

        $query->andFilterWhere(['like', 'to_char(date(to_timestamp(created)), \'MM/DD/YYYY\')', $this->created])
            ->andFilterWhere(['like', 'to_char(date(to_timestamp(available_on)), \'MM/DD/YYYY\')', $this->available_on])
            ->andFilterWhere(['like', 'source', $this->source])
            ->andFilterWhere(['like', 'balanceId', $this->balanceId]);
        
        return $this;
    }
}