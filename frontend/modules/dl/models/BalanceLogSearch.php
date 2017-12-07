<?php

namespace frontend\modules\dl\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\BalanceLog;

/**
 * BalanceLogSearch represents the model behind the search form of `common\models\BalanceLog`.
 */
class BalanceLogSearch extends BalanceLog
{
    public function attributes(){
        return array_merge(parent::attributes(),['orderSn']);
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
    public function rules()
    {
        return [
            [['id', 'type'], 'integer'],
            [[ 'before_amount', 'after_amount'], 'number'],
            [['content', 'order_sn', 'orderSn', 'uid'], 'safe'],
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
        $query = BalanceLog::find();

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
            'type' => $this->type,
            //'source_type' => $this->source_type,
            //'add_time' => $this->add_time,
        ]);
        
        if(!empty($add_time)){
            $query->andFilterWhere(['between','balance_log.add_time',$add_time['start'],$add_time['end']]);
        }
        
        //order_sn
        $query->join('LEFT JOIN','logistics_order','logistics_order.logistics_sn = balance_log.order_sn');
        $query->andFilterWhere(['like','logistics_order.order_sn',$this->orderSn]);
        $dataProvider->sort->attributes['orderSn']=[
                'asc' =>['logistics_order.order_sn'=>SORT_ASC],
                'desc' =>['logistics_order.order_sn'=>SORT_DESC],
        ];
        
        $query->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'order_sn', $this->order_sn]);
           
//         echo '<pre>';
//         var_dump($dataProvider);die;
        return $dataProvider;
    }
    
    public function search2($params)
    {
        $query = BalanceLog::find();
        
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
        
        $query->innerJoin('user', 'user.id = balance_log.uid')
              ->join('LEFT JOIN','logistics_order','logistics_order.logistics_sn = balance_log.order_sn');
        $query->select('balance_log.*, logistics_order.order_sn as lorder_sn');
        
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'amount' => $this->amount,
            'before_amount' => $this->before_amount,
            'after_amount' => $this->after_amount,
            'type' => $this->type,
            'source_type' => $this->source_type,
            'add_time' => $this->add_time,
        ]);
        
        $query->andFilterWhere(['like', 'order_sn', $this->order_sn]);
        
        $query->andFilterWhere(['or', ['like', 'username', $this->uid], ['like', 'user_truename', $this->uid], ['like', 'small_num', $this->uid]]);
        
        return $dataProvider;
    }
}
