<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CustomerSubscriptionOrder;

/**
 * CustomerSubscriptionOrderSearch represents the model behind the search form about `app\models\CustomerSubscriptionOrder`.
 */
class CustomerSubscriptionOrderSearch extends CustomerSubscriptionOrder
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'customer_id'], 'integer'],
            [['coupon_code', 'discount', 'tax_amount', 'shipping_description', 'shipping_amount', 'subtotal', 'subtotal_incl_tax', 'grand_total', 'status', 'created_at'], 'safe'],
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
        $query = CustomerSubscriptionOrder::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'customer_id' => $this->customer_id,
        ]);

        $query->andFilterWhere(['like', 'coupon_code', $this->coupon_code])
            ->andFilterWhere(['like', 'discount', $this->discount])
            ->andFilterWhere(['like', 'tax_amount', $this->tax_amount])
            ->andFilterWhere(['like', 'shipping_description', $this->shipping_description])
            ->andFilterWhere(['like', 'shipping_amount', $this->shipping_amount])
            ->andFilterWhere(['like', 'subtotal', $this->subtotal])
            ->andFilterWhere(['like', 'subtotal_incl_tax', $this->subtotal_incl_tax])
            ->andFilterWhere(['like', 'grand_total', $this->grand_total])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'created_at', $this->created_at]);

        return $dataProvider;
    }
}
