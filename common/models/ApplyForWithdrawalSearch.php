<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ApplyForWithdrawal;

/**
 * ApplyForWithdrawalSearch represents the model behind the search form of `common\models\ApplyForWithdrawal`.
 */
class ApplyForWithdrawalSearch extends ApplyForWithdrawal
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'type'], 'integer'],
            [['amount'], 'number'],
            [['user_id', 'bank_info_account_name', 'bank_info_card_no', 'add_time'], 'safe'],
        ];
    }
    
    public function attributes() {
        return array_merge(parent::attributes(), ['bank_info_account_name', 'bank_info_card_no']);
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
        $query = ApplyForWithdrawal::find();

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
            'amount' => $this->amount,
            'apply_for_withdrawal.status' => $this->status,
//            'add_time' => $this->add_time,
            'type' => $this->type
        ]);
        
        if($this->user_id) {
            $query->innerJoin('user', 'user.id = apply_for_withdrawal.user_id');
            $query->andFilterWhere(['or', ['like', 'username', $this->user_id], ['like', 'user_truename', $this->user_id]]);
        }


        if($this->add_time) {
            list($start, $end) = explode(' - ', $this->add_time);
            $start = strtotime($start);
            $end = strtotime($end.' 23:59:59');
            $query->andFilterWhere(['between', 'add_time', $start, $end]);

        }
        
        if ($this->bank_info_account_name || $this->bank_info_card_no) {
            $query->innerJoin('bank_info', 'bank_info.user_id = apply_for_withdrawal.user_id');
            if($this->bank_info_account_name) {
                $query->andFilterWhere(['like', 'bank_info_account_name', $this->bank_info_account_name]);
            }
            if($this->bank_info_card_no) {
                $query->andFilterWhere(['like', 'bank_info_card_no', $this->bank_info_card_no]);
            }
        }

        return $dataProvider;
    }
    
    public function historySearch($params,$add_time)
    {
        $query = ApplyForWithdrawal::find();
    
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
    
        if(!empty($add_time)){
            $query->andFilterWhere(['between','add_time',$add_time['start'],$add_time['end']]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
                'id' => $this->id,
                'user_id' => Yii::$app->user->id,
                'amount' => $this->amount,
                'status' => $this->status,
                'add_time' => $this->add_time,
        ]);
        
    
        return $dataProvider;
    }
}
