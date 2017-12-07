<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\OrderThirdAdvance;

/**
 * OrderThirdAdvancesearch represents the model behind the search form of `common\models\OrderThirdAdvance`.
 */
class OrderThirdAdvanceSearch extends OrderThirdAdvance
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'member_id', 'state', 'add_time', 'add_user', 'income_time', 'income_user'], 'integer'],
            [['amount'], 'number'],
            [['logistics_sn'], 'safe'],
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
        $query = OrderThirdAdvance::find();

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
            'member_id' => $this->member_id,
            'amount' => $this->amount,
            'state' => $this->state,
            'add_time' => $this->add_time,
            'add_user' => $this->add_user,
            'income_time' => $this->income_time,
            'income_user' => $this->income_user,
        ]);

        $query->andFilterWhere(['like', 'logistics_sn', $this->logistics_sn]);

        return $dataProvider;
    }
}
