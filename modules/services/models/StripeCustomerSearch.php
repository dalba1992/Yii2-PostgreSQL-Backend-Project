<?php

namespace app\modules\services\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\services\models\StripeCustomer;

/**
 * CustomerEntitySearch represents the model behind the search form about `app\models\CustomerEntity`.
 */
class StripeCustomerSearch extends StripeCustomer
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
        $query = StripeCustomer::find();

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
            ->andFilterWhere(['like', 'customerId', $this->customerId])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'default_card', $this->default_card])
            ->andFilterWhere(['like', 'default_source', $this->default_source]);
        
        return $this;
    }
}