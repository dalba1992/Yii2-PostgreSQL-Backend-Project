<?php

namespace app\modules\services\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\services\models\LiteviewOrder;

/**
 * LiteviewOrderSearch represents the model behind the search form about `app\modules\services\models\LiteviewOrder`.
 */
class LiteviewOrderSearch extends LiteviewOrder
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
        $query = LiteviewOrder::find();

        $this->load($params); // load query params, but don't return just yet.

        $this->dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array('defaultPageSize' => 10),
            'sort'=> ['defaultOrder' => ['submitted_at'=>SORT_DESC]],
        ]);

        // enable sorting for the related column
        $this->dataProvider->sort->attributes['submitted_at'] = [
            'asc' => ['submitted_at' => SORT_ASC],
            'desc' => ['submitted_at' => SORT_DESC],
        ];

        $query->andFilterWhere(['like', 'to_char(date(to_timestamp(created)), \'MM/DD/YYYY\')', $this->created_at])
            ->andFilterWhere(['like', 'to_char(date(to_timestamp(updated)), \'MM/DD/YYYY\')', $this->updated_at])
            ->andFilterWhere(['like', 'ifs_order_number', $this->ifs_order_number])
            ->andFilterWhere(['like', 'liteview_order_status', $this->liteview_order_status]);
        
        return $this;
    }
}