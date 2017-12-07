<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WithdrawalOrder;

/**
 * WithdrawalOrderSearch represents the model behind the search form of `common\models\WithdrawalOrder`.
 */
class WithdrawalOrderSearch extends WithdrawalOrder
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
            [['order_sn','orderSn',], 'safe'],
            [[ 'is_withdrawal', 'apply_id', 'user_id'], 'integer'],
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
    public function search($params)
    {
        $query = WithdrawalOrder::find();

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
            'add_time' => $this->add_time,
            'is_withdrawal' => $this->is_withdrawal,
            'apply_id' => $this->apply_id,
            'user_id' => $this->user_id,
            'amount' => $this->amount,
        ]);

        $query->andFilterWhere(['like', 'order_sn', $this->order_sn]);

        return $dataProvider;
    }
    
    public function orderSearch($params,$add_time,$type = null)
    {
        $query = WithdrawalOrder::find();
    
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
                'add_time' => $this->add_time,
                'is_withdrawal' => empty($type)?0:1,
                'apply_id' => $this->apply_id,
                'user_id' => Yii::$app->user->id,
                'amount' => $this->amount,
        ]);
        
        if(!empty($add_time)){
            if($type=='over'){
                $query->andFilterWhere(['between','withdrawal_order.add_time',$add_time['start'],$add_time['end']]);
            }
//             else{
//                 $query->andFilterWhere(['between','withdrawal_log.add_time',$add_time['start'],$add_time['end']]);
//             }
        }
        //order_sn
        $query->join('LEFT JOIN','logistics_order','logistics_order.logistics_sn = withdrawal_order.order_sn');
        $query->andFilterWhere(['like','logistics_order.order_sn',$this->orderSn]);
        $dataProvider->sort->attributes['orderSn']=[
                'asc' =>['logistics_order.order_sn'=>SORT_ASC],
                'desc' =>['logistics_order.order_sn'=>SORT_DESC],
        ];
    
        $query->andFilterWhere(['like', 'order_sn', $this->order_sn]);
    
        return $dataProvider;
    }
}
