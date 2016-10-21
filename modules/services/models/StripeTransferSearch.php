<?php

namespace app\modules\services\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\services\models\StripeTransfer;

class StripeTransferSearch extends StripeTransfer
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
        $query = StripeTransfer::find();

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
            ->andFilterWhere(['like', 'to_char(date(to_timestamp("scheduledDate")), \'MM/DD/YYYY\')', $this->scheduledDate])
            ->andFilterWhere(['like', 'balance_transaction', $this->balance_transaction])
            ->andFilterWhere(['like', 'bank_account_id', $this->bank_account_id])
            ->andFilterWhere(['like', 'transferId', $this->transferId]);
        
        return $this;
    }
}