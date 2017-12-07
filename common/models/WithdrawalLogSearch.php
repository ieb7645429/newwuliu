<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WithdrawalLog;

/**
 * WithdrawalLogSearch represents the model behind the search form of `common\models\WithdrawalLog`.
 */
class WithdrawalLogSearch extends WithdrawalLog
{
    public function attributes(){
        return array_merge(parent::attributes(),['orderSn','addTime']);
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['before_amount', 'after_amount'], 'number'],
            [['content', 'order_sn','orderSn', 'uid',], 'safe'],
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
    public function search($params,$add_time)
    {
        $query = WithdrawalLog::find();

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
            'uid' => Yii::$app->user->id,
            'amount' => $this->amount,
            'before_amount' => $this->before_amount,
            'after_amount' => $this->after_amount,
//             'type' => $this->type,
//             'add_time' => $this->add_time,
        ]);
        
        if(!empty($add_time)){
            $query->andFilterWhere(['between','withdrawal_log.add_time',$add_time['start'],$add_time['end']]);
        }
        
        //order_sn
        $query->join('LEFT JOIN','logistics_order','logistics_order.logistics_sn = withdrawal_log.order_sn');
        $query->andFilterWhere(['like','logistics_order.order_sn',$this->orderSn]);
        $dataProvider->sort->attributes['orderSn']=[
                'asc' =>['logistics_order.order_sn'=>SORT_ASC],
                'desc' =>['logistics_order.order_sn'=>SORT_DESC],
        ];

        $query->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'withdrawal_log.order_sn', $this->order_sn]);

        return $dataProvider;
    }
    public function allSearch($params,$add_time)
    {
        $query = WithdrawalLog::find();
    
        // add conditions that should always apply here
    
        $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'sort' => [
                    'defaultOrder' => [
                        'add_time' => SORT_DESC,            
                     ]
        ],
    
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
                'uid' => Yii::$app->user->id,
                'amount' => $this->amount,
                'before_amount' => $this->before_amount,
                'after_amount' => $this->after_amount,
                //             'type' => $this->type,
        //             'add_time' => $this->add_time,
        ]);
    
        if(!empty($add_time)){
            $query->andFilterWhere(['between','withdrawal_log.add_time',$add_time['start'],$add_time['end']]);
        }
    
        //order_sn
        $query->join('LEFT JOIN','logistics_order','logistics_order.logistics_sn = withdrawal_log.order_sn');
        $query->andFilterWhere(['like','logistics_order.order_sn',$this->orderSn]);
        $dataProvider->sort->attributes['orderSn']=[
                'asc' =>['logistics_order.order_sn'=>SORT_ASC],
                'desc' =>['logistics_order.order_sn'=>SORT_DESC],
        ];
    
        $query->andFilterWhere(['like', 'content', $this->content])
        ->andFilterWhere(['like', 'withdrawal_log.order_sn', $this->order_sn]);
    
        return $dataProvider;
    }
    public function orderSearch($params,$add_time,$type = null)
    {
        $query = WithdrawalLog::find();
        
        // add conditions that should always apply here
        if($type == 'over'){
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'sort' => [
                        'defaultOrder' => [
                                'addTime' => SORT_DESC,
                        ]  
                ],
            ]);
        }else{
            $dataProvider = new ActiveDataProvider([
                    'query' => $query,
            ]);
        }
        
        
        $this->load($params);
    
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
    
        // grid filtering conditions
        $query->andFilterWhere([
                'id' => $this->id,
                'uid' => Yii::$app->user->id,
                'amount' => $this->amount,
                'before_amount' => $this->before_amount,
                'after_amount' => $this->after_amount,
                //             'type' => $this->type,
        //             'add_time' => $this->add_time,
        ]);
        
        if(!empty($add_time)){
            if($type=='over'){
                $query->andFilterWhere(['between','withdrawal_order.add_time',$add_time['start'],$add_time['end']]);
            }else{
                $query->andFilterWhere(['between','withdrawal_log.add_time',$add_time['start'],$add_time['end']]);
            }
        }
    
        //order_sn
        $query->join('LEFT JOIN','logistics_order','logistics_order.logistics_sn = withdrawal_log.order_sn');
        $query->andFilterWhere(['like','logistics_order.order_sn',$this->orderSn]);
        $dataProvider->sort->attributes['orderSn']=[
                'asc' =>['logistics_order.order_sn'=>SORT_ASC],
                'desc' =>['logistics_order.order_sn'=>SORT_DESC],
        ];
        
        //关联可提现表
        $query->join('INNER JOIN','withdrawal_order','withdrawal_order.order_sn = withdrawal_log.order_sn');
        if($type=='over'){
            $query->andFilterWhere(['withdrawal_order.is_withdrawal'=>1]);
            $dataProvider->sort->attributes['addTime']=[
                    'asc' =>['withdrawal_order.add_time'=>SORT_ASC],
                    'desc' =>['withdrawal_order.add_time'=>SORT_DESC],
            ];
        }else{
            $query->andFilterWhere(['withdrawal_order.is_withdrawal'=>0]);
        }
        $query->andFilterWhere(['like', 'content', $this->content])
        ->andFilterWhere(['like', 'withdrawal_log.order_sn', $this->order_sn])
        ->andFilterWhere(['<>', 'withdrawal_log.amount',0]);
    
        return $dataProvider;
    }
    
    public function search2($params)
    {
        $query = WithdrawalLog::find();
        
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
        
        $query->innerJoin('user', 'user.id = withdrawal_log.uid')
              ->join('LEFT JOIN','logistics_order','logistics_order.logistics_sn = withdrawal_log.order_sn');
        $query->select('withdrawal_log.*, logistics_order.order_sn as lorder_sn');
        
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'amount' => $this->amount,
            'before_amount' => $this->before_amount,
            'after_amount' => $this->after_amount,
            'type' => $this->type,
            'add_time' => $this->add_time,
        ]);
        
        $query->andFilterWhere(['like', 'withdrawal_log.order_sn', $this->order_sn]);
        
        $query->andFilterWhere(['or', ['like', 'username', $this->uid], ['like', 'user_truename', $this->uid], ['like', 'small_num', $this->uid]]);
        
        return $dataProvider;
    }
}
