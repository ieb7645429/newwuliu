<?php

namespace backend\modules\dl\models;

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
            [['id', 'count', 'user_id', 'add_time'], 'integer'],
            [['order_sn', 'receiving', 'userTrueName'], 'safe'],
            [['amount'], 'number'],
        ];
    }
    
    public function attributes(){
        return array_merge(parent::attributes(), ['userTrueName']);
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
            'count' => $this->count,
            'amount' => $this->amount,
            'user_id' => $this->user_id,
            'add_time' => $this->add_time,
        ]);

        $query->andFilterWhere(['like', 'order_sn', $this->order_sn])
            ->andFilterWhere(['like', 'receiving', $this->receiving]);
        
        //会员号
        $query->join('LEFT JOIN','admin_user as username','username.id = teller_income_sn_log.user_id');
        $query->andFilterWhere(['like','username.user_truename',$this->userTrueName]);
        $dataProvider->sort->attributes['userTrueName'] = [
            'asc' =>['username.user_truename'=>SORT_ASC],
            'desc' =>['username.user_truename'=>SORT_DESC],
        ];

        return $dataProvider;
    }
}
