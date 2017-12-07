<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UserBalance;

/**
 * UserBalanceSearch represents the model behind the search form of `common\models\UserBalance`.
 */
class UserBalanceSearch extends UserBalance
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'safe'],
            [['user_amount', 'withdrawal_amount'], 'number'],
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
        $query = UserBalance::find();

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

        $query->innerJoin('user', 'user.id = user_balance.user_id');
        
        // grid filtering conditions
        $query->andFilterWhere([
            'user_amount' => $this->user_amount,
            'withdrawal_amount' => $this->withdrawal_amount,
        ]);
        
        $query->andFilterWhere(['or', ['like', 'username', $this->user_id], ['like', 'user_truename', $this->user_id], ['like', 'small_num', $this->user_id]]);

        return $dataProvider;
    }
}
