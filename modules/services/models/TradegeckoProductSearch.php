<?php

namespace app\modules\services\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\services\models\TradegeckoProduct;

/**
 * TradegeckoProductSearch represents the model behind the search form about `modules\services\models\TradegeckoProduct`.
 */
class TradegeckoProductSearch extends TradegeckoProduct
{
    public $dataProvider = null;
    public $shop;
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
        $query = TradegeckoProduct::find();
        $this->load($params); // load query params, but don't return just yet.

        $this->dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array('defaultPageSize' => 10),
            'sort'=> ['defaultOrder' => ['id'=>SORT_ASC]],
        ]);

        // enable sorting for the related column
        $this->dataProvider->sort->attributes['id'] = [
            'asc' => ['id' => SORT_ASC],
            'desc' => ['id' => SORT_DESC],
        ];
        $query->andFilterWhere(['ilike', 'to_char(date(updated_at), \'MM/DD/YYYY\')', $this->updated_at])
            ->andFilterWhere(['ilike', 'product_name', $this->product_name])
            ->andFilterWhere(['ilike', 'supplier', $this->supplier])
            ->andFilterWhere(['ilike', 'product_type', $this->product_type])
            ->andFilterWhere(['ilike', 'product_status', $this->product_status])
            ->andFilterWhere(['=', 'brand', $this->brand])
            ->andFilterWhere(['ilike', 'shop', $this->shop]);

        return $this;
    }
}