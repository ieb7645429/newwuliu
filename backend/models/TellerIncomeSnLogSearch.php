<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\TellerIncomeSnLog;

/**
 * TellerIncomeSnLogSearch represents the model behind the search form of `backend\models\TellerIncomeSnLog`.
 */
class TellerIncomeSnLogSearch extends TellerIncomeSnLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'goods_state', 'number'], 'integer'],
            [['order_sn', 'receiving', 'userTrueName', 'user_id', 'add_time'], 'safe'],
            [['rel_amount', 'amount'], 'number'],
        ];
    }
    
    public function attributes(){
        return array_merge(parent::attributes(), ['userTrueName', 'goods_state']);
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
        $query = TellerIncomeSnLog::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'number' => SORT_ASC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        // 查询时间
        if($this->add_time) {
            list($start, $end) = explode(' - ', $this->add_time);
            $start = strtotime($start);
            $end = strtotime($end.' 23:59:59');
            $query->andFilterWhere(['between', 'teller_income_sn_log.add_time', $start, $end]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'number' => $this->number,
        ]);

        $query->andFilterWhere(['like', 'order_sn', $this->order_sn])
              ->andFilterWhere(['like', 'receiving', $this->receiving]);
        
        //会员号
        if($this->user_id) {
            $query->join('LEFT JOIN','admin_user as username','username.id = teller_income_sn_log.user_id');
            $query->andFilterWhere(['like','username.user_truename',$this->user_id]);
        }
        
        if ($this->goods_state) {
            $query->join('INNER JOIN','logistics_order as lo','lo.order_id = teller_income_sn_log.order_id');
            $query->andFilterWhere(['&','lo.goods_price_state', $this->goods_state]);
        }

        return $dataProvider;
    }
}
