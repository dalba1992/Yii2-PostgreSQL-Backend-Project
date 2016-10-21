<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CustomerEntity;
use app\models\CustomerEntityAddress;

/**
 * CustomerEntitySearch represents the model behind the search form about `app\models\CustomerEntity`.
 */
class CustomerEntitySearch extends CustomerEntity
{
    public $dataProvider = null;
    /**
     * @inheritdoc
     */
    //   public $enableMultiSort = true;
    public $customerEntityInfos;
    public $customerEntityAddresses;
    public $customerEntityStatuses;

    public function rules()
    {
        return [
            [['id', 'is_active'], 'integer'],
            [['id', 'is_active', 'created_at', 'updated_at', 'customerEntityInfos.email','customerEntityInfos.first_name','customerEntityInfos.last_name'], 'safe'],
        ];
    }

    public function attributes()
    {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['customerEntityInfos.email','customerEntityInfos.first_name','customerEntityInfos.last_name']);
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
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = CustomerEntity::find();

        $query->joinWith(['customerEntityInfos', 'customerSubscription']);

        $this->load($params); // load query params, but don't return just yet.

        $this->dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array('defaultPageSize' => 10),
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
        ]);

        /**
         * Sorting options
         */
        $this->dataProvider->sort->attributes['customerEntityInfos.first_name'] = [
            'asc' => ['customer_entity_info.first_name' => SORT_ASC],
            'desc' => ['customer_entity_info.first_name' => SORT_DESC],
        ];

        $this->dataProvider->sort->attributes['customerEntityInfos.last_name'] = [
            'asc' => ['customer_entity_info.last_name' => SORT_ASC],
            'desc' => ['customer_entity_info.last_name' => SORT_DESC],
        ];

        $this->dataProvider->sort->attributes['customerEntityInfos.email'] = [
            'asc' => ['customer_entity_info.email' => SORT_ASC],
            'desc' => ['customer_entity_info.email' => SORT_DESC],
        ];

        $this->dataProvider->sort->attributes['customerSubscription.stripe_customer_id'] = [
            'asc' => ['customer_subscription.stripe_customer_id' => SORT_ASC],
            'desc' => ['customer_subscription.stripe_customer_id' => SORT_DESC],
        ];

        /**
         * Search options
         */
        $query->andFilterWhere([
            'customer_entity.id' => $this->id,
            'customer_entity.is_active' => $this->is_active,
        ]);

        if(isset($_GET['CustomerEntitySearch']['customerEntityInfos.first_name'])){
            $query->andFilterWhere(['ilike', 'customer_entity_info.first_name', $this->attributes['customerEntityInfos.first_name']]);
        }
        if(isset($_GET['CustomerEntitySearch']['customerEntityInfos.email'])){
            $query->andFilterWhere(['ilike', 'customer_entity_info.email', $this->attributes['customerEntityInfos.email']]);
        }
        if(isset($_GET['CustomerEntitySearch']['customerEntityInfos.last_name'])){
            $query->andFilterWhere(['ilike', 'customer_entity_info.last_name', $this->attributes['customerEntityInfos.last_name']]);
        }
        if(isset($_GET['CustomerEntitySearch']['customerSubscription.stripe_customer_id'])){
            $query->andFilterWhere(['ilike', 'customer_subscription.stripe_customer_id', $this->attributes['customerSubscription.stripe_customer_id']]);
        }

        return $this;
    }

    /**
     * Search by status
     *
     * @param string $status
     * @return $this
     */
    public function byStatus($status = null)
    {
        if(!$status)
            return $this;

        $query = $this->dataProvider->query;

        //$query->leftJoin(CustomerEntityStatus::tableName().' ces', 'ces.customer_id = customer_entity.id order by ces.id DESC');
        $query->leftJoin('(select max(id) as id, customer_id from customer_entity_status group by customer_id order by id DESC) "ces"', 'ces.customer_id = customer_entity.id');

        //@todo review this, do not remove the following commented out lines
//        if($status == CustomerEntity::LABEL_STATUS_ACTIVE)
//            $query->andWhere('customer_entity.is_active = :ceActive AND ces.status = :cesActive', [':ceActive'=>CustomerEntity::STATUS_ACTIVE, ':cesActive'=>CustomerEntityStatus::STATUS_ACTIVE]);
//
//        if($status == CustomerEntity::LABEL_STATUS_INACTIVE)
//            $query->andWhere('customer_entity.is_active <> :ceActive OR ces.status <> :cesActive', [':ceActive'=>CustomerEntity::STATUS_ACTIVE, ':cesActive'=>CustomerEntityStatus::STATUS_ACTIVE]);

        if($status == CustomerEntity::LABEL_STATUS_ACTIVE)
            $query->andWhere('customer_entity.is_active = :ceActive', [':ceActive'=>CustomerEntity::STATUS_ACTIVE]);

        if($status == CustomerEntity::LABEL_STATUS_INACTIVE)
            $query->andWhere('customer_entity.is_active <> :ceActive', [':ceActive'=>CustomerEntity::STATUS_ACTIVE]);

        //$query->andWhere('ces.id = (select max(id) from customer_entity_status where customer_id = customer_entity.id)');

        return $this;
    }
}