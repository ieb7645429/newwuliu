<?php

namespace frontend\modules\dl\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\dl\models\ApplyForWithdrawal;

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
            [['id', 'status'], 'integer'],
            [['amount'], 'number'],
            [['user_id'], 'safe'],
        ];
    }
    
    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_dl');
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
        
        $query->innerJoin('user', 'user.id = apply_for_withdrawal.user_id');

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
//             'user_id' => $this->user_id,
            'amount' => $this->amount,
            'apply_for_withdrawal.status' => $this->status,
            'add_time' => $this->add_time,
        ]);
        
        $query->andFilterWhere(['or', ['like', 'username', $this->user_id], ['like', 'user_truename', $this->user_id]]);

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
