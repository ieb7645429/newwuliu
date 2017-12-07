<?php

namespace backend\modules\dl\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\dl\models\TellerLog;
use yii\helpers\ArrayHelper;

/**
 * TellerLogSearch represents the model behind the search form of `backend\models\TellerLog`.
 */
class TellerLogSearch extends TellerLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'order_type', 'type', 'user_id'], 'integer'],
            [['content', 'add_time'], 'safe'],
            [['amount'], 'number'],
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
    public function search($params, $userList)
    {
        $query = TellerLog::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'order_id' => $this->order_id,
            'teller_log.order_type' => $this->order_type,
            'type' => 1,
            'amount' => $this->amount,
        ]);
        
        // 查询时间
        $add_time = ArrayHelper::getValue($params, 'TellerLogSearch.add_time', null);
        if($add_time) {
            list($start, $end) = explode(' - ', $add_time);
            $start = $start;
            $end = $end.' 23:59:59';
        } else {
            $start = date('Y-m-d');
            $end = date('Y-m-d').' 23:59:59';
        }
        
        $query->andFilterWhere(['between', 'teller_log.add_time', $start, $end]);

        $query->andFilterWhere(['like', 'content', $this->content])
              ->andFilterWhere(['in', 'user_id', $this->user_id ? $this->user_id : array_keys($userList)]);

        return $dataProvider;
    }
    
    public function getIncomeAllAmount($query) {
        $orderQuery = serialize($query);
        $orderQuery = unserialize($orderQuery);
        return $orderQuery->sum('amount') ? $orderQuery->sum('amount') : 0;
    }
    
    public function getSameCityIncomeAllAmount($query) {
        $orderQuery = serialize($query);
        $orderQuery = unserialize($orderQuery);
        $returnOrderQuery = serialize($query);
        $returnOrderQuery = unserialize($returnOrderQuery);

        $amount = $orderQuery->leftJoin('logistics_order', 'teller_log.order_id = logistics_order.order_id')
                             ->andFilterWhere(['teller_log.order_type' => 1])
                             ->andFilterWhere(['logistics_order.same_city' => 1])
                             ->sum('amount');
         $amount = $amount ? $amount : 0;
         $rAmount = $returnOrderQuery->leftJoin('logistics_return_order', 'teller_log.order_id = logistics_return_order.order_id')
                                     ->andFilterWhere(['teller_log.order_type' => 2])
                                     ->andFilterWhere(['logistics_return_order.same_city' => 1])
                                     ->sum('amount');
         $rAmount= $rAmount ? $rAmount : 0;
         return $amount+$rAmount;
    }
}
