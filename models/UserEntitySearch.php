<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UserEntity;

/**
 * CustomerEntitySearch represents the model behind the search form about `app\models\CustomerEntity`.
 */
class UserEntitySearch extends UserEntity
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
        $query = UserEntity::find();

        $this->load($params); // load query params, but don't return just yet.

        $this->dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array('defaultPageSize' => 10),
            'sort'=> ['defaultOrder' => ['regDate'=>SORT_ASC]],
        ]);

        // enable sorting for the related column
        $this->dataProvider->sort->attributes['id'] = [
            'asc' => ['id' => SORT_ASC],
            'desc' => ['id' => SORT_DESC],
        ];

        $query->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'firstName', $this->firstName])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'authKey', $this->authKey])
            ->andFilterWhere(['like', 'accessToken', $this->accessToken])
            ->andFilterWhere(['like', 'lastName', $this->lastName]);
        
        return $this;
    }
}