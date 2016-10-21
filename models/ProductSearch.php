<?php

namespace app\models;

use yii\base\Model;

use yii\data\ActiveDataProvider;
use yii\helpers\VarDumper;

class ProductSearch extends Products {

    public $name;
    public $productType;
    public $brand;
    public $customerId;
    public $shop;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['productType', 'name', 'brand', 'customerId', 'shop'], 'safe']
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

    public function search($params) {
        $query = Products::find();
        $this->load($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20
            ],
            'sort'=> ['defaultOrder' => ['tradegeckoId'=>SORT_ASC]],
        ]);

        $query->andFilterWhere([
            'productType' => $this->productType
        ]);
        $query->andFilterWhere(['ilike', 'name', $this->name])
            ->andFilterWhere(['ilike', 'brand', $this->brand])
            ->andFilterWhere(['ilike', 'shop', $this->shop]);

        return $dataProvider;
    }
}