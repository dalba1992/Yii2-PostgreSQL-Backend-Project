<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Coupon;

/**
 * CouponSearch represents the model behind the search form about `app\models\Coupon`.
 */
class CouponSearch extends Coupon
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'discount_type', 'duration', 'duration_time', 'is_apply','status'], 'integer'],
            [['coupon_name', 'coupon_code','created_at'], 'safe'],
            [['amount_off'], 'number'],
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
        $query = Coupon::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'discount_type' => $this->discount_type,
            'amount_off' => $this->amount_off,
            'duration' => $this->duration,
            'duration_time' => $this->duration_time,
            'created_at' => $this->created_at,
            'is_apply' => $this->is_apply,
			'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'coupon_name', $this->coupon_name])
            ->andFilterWhere(['like', 'coupon_code', $this->coupon_code])
			->andFilterWhere(['is_apply' => $this->is_apply])
			->andFilterWhere(['status' => $this->status]);            

        return $dataProvider;
    }
}
