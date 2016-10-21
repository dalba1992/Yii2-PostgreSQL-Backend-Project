<?php

namespace app\modules\services\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\services\models\TradegeckoPriceList;

/**
 * TradegeckoPriceListSearch represents the model behind the search form about `modules\services\models\TradegeckoPriceList`.
 */
class TradegeckoPriceListSearch extends TradegeckoPriceList
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
        $query = TradegeckoPriceList::find();
        $this->load($params); // load query params, but don't return just yet.

        $this->dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array('defaultPageSize' => 10),
        ]);

        // enable sorting for the related column
        $this->dataProvider->sort->attributes['id'] = [
            'asc' => ['id' => SORT_ASC],
            'desc' => ['id' => SORT_DESC],
        ];
        
        return $this;
    }
}