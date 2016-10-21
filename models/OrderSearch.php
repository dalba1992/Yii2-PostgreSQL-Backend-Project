<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\VarDumper;

class OrderSearch extends Order {

    public $state;
    public $style;
    public $tsize;
    public $bsize;
    public $bfit;
    public $mailid;


    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['state', 'style','tsize', 'bsize', 'bfit', 'mailid'], 'safe']
        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params) {

        $filters = '';
        if($this->load($params)) {
            if ($this->state && trim($this->state) != '') {
                if(trim($filters) != '') $filters .= ' AND ';
                $filters .= "address.state = '". $this->state ."'";
            }
            if($this->mailid && trim($this->mailid) != '') {
                if(trim($filters) != '') $filters .= ' AND ';
                $filters .= "info.email = '". $this->mailid ."'";
            }
            if($this->style && trim($this->style) != '') {
                if(trim($filters) != '') $filters .= ' AND ';
                $filters .= "(select value from customer_entity_attribute where customer_id = customer.id and attribute_id = 1) = " . (int)$this->style;
            }
            if($this->tsize && trim($this->tsize) != '') {
                if(trim($filters) != '') $filters .= ' AND ';
                $filters .= "(select value from customer_entity_attribute where customer_id = customer.id and attribute_id = 4) = " . (int)$this->tsize;
            }
            if($this->bsize && trim($this->bsize) != '') {
                if(trim($filters) != '') $filters .= ' AND ';
                $filters .= "(select value from customer_entity_attribute where customer_id = customer.id and attribute_id = 5) = " . (int)$this->bsize;
            }
            if($this->bfit && trim($this->bfit) != '') {
                if(trim($filters) != '') $filters .= ' AND ';
                $filters .= "(select value from customer_entity_attribute where customer_id = customer.id and attribute_id = 6) = " . (int)$this->bfit;
            }

        }
        $where = '';
        if(trim($filters) != '') {
            $where = ' AND ' . $filters;
        }

        $query = Order::findBySql('
            SELECT "order".*,

            (
                select value from customer_entity_attribute where customer_id = customer.id and attribute_id = 1
            ) as "attr1",

            (
                select value from customer_entity_attribute where customer_id = customer.id and attribute_id = 4
            ) as "attr4",

            (
                select value from customer_entity_attribute where customer_id = customer.id and attribute_id = 5
            ) as "attr5",

            (
                select value from customer_entity_attribute where customer_id = customer.id and attribute_id = 6
            ) as "attr6"


            FROM "order"

            LEFT JOIN "customer_entity" "customer" ON "order"."customerId" = "customer"."id"
            LEFT JOIN "customer_entity_info" "info" ON "customer"."id" = "info"."customer_id"
            LEFT JOIN "customer_entity_address" "address" ON "customer"."id" = "address"."customer_id"

            WHERE "status" = \''.Order::STATUS_ACTIVE.'\'
                '. $where .'

            GROUP BY "order"."id", "attr1", "attr4", "attr5", "attr6"

            ORDER BY "order"."id" DESC
        ');

        $dataProvider = new ArrayDataProvider([
            'allModels' => $query->all(),
            'pagination' => [
                'pageSize' => 1,
                'page' => !isset($params['OrderSearch']['orderPage']) || $params['OrderSearch']['orderPage'] < 0 ? 0 : $params['OrderSearch']['orderPage']
            ]
        ]);

        return $dataProvider;
    }

}