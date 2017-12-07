<?php

namespace frontend\modules\dl\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\dl\models\User;

/**
 * UserSearch represents the model behind the search form of `common\models\User`.
 */
class UserSearch extends User
{
    
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
            [['id',  'is_poundage', 'is_buy_out', 'buy_out_price', 'buy_out_time', 'member_areaid', 'member_cityid', 'member_provinceid'], 'integer'],
            [['username','auth_key', 'password_hash', 'password_reset_token', 'email', 'user_truename', 'member_phone', 'member_areainfo', 'App_Key','small_name','small_num'], 'safe'],
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
        $query = User::find();
        
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }
        if(empty($this->user_truename)){
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_poundage' => $this->is_poundage,
            'is_buy_out' => $this->is_buy_out,
            'buy_out_price' => $this->buy_out_price,
            'buy_out_time' => $this->buy_out_time,
            'member_areaid' => $this->member_areaid,
            'member_cityid' => $this->member_cityid,
            'member_provinceid' => $this->member_provinceid,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'user_truename', $this->user_truename])
            ->andFilterWhere(['like', 'member_phone', $this->member_phone])
            ->andFilterWhere(['like', 'member_areainfo', $this->member_areainfo])
            ->andFilterWhere(['like', 'App_Key', $this->App_Key])
            ->andFilterWhere(['like', 'small_num', $this->small_num])
			 ->andFilterWhere(['like','small_name', $this->small_name]);
        
//         $query->andFilterWhere(['id'=>Yii::$app->user->id]);
            
        return $dataProvider;
    }
    
    
    public function editSearch($params){
        $query = User::find();
        
        // add conditions that should always apply here
        
        $dataProvider = new ActiveDataProvider([
                'query' => $query,
        ]);
        
        $this->load($params);
        
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }
        if(empty($this->username)){
            $query->where('0=1');
            return $dataProvider;
        }
        
        // grid filtering conditions
        $query->andFilterWhere([
                'id' => $this->id,
                'is_poundage' => $this->is_poundage,
                'is_buy_out' => $this->is_buy_out,
                'buy_out_price' => $this->buy_out_price,
                'buy_out_time' => $this->buy_out_time,
                'member_areaid' => $this->member_areaid,
                'member_cityid' => $this->member_cityid,
                'member_provinceid' => $this->member_provinceid,
        ]);
        
        $query->andFilterWhere(['like', 'username', $this->username])
        ->andFilterWhere(['like', 'user_truename', $this->user_truename])
        ->andFilterWhere(['like', 'member_phone', $this->member_phone])
        ->andFilterWhere(['like', 'member_areainfo', $this->member_areainfo])
        ->andFilterWhere(['like', 'small_num', $this->small_num])
        ->andFilterWhere(['like','small_name', $this->small_name]);
        
//         $query->join('INNER JOIN','auth_assignment','auth_assignment.user_id = user.id');
//         $query->andFilterWhere(['auth_assignment.item_name'=>'大连用户']);
        
        //         $query->andFilterWhere(['id'=>Yii::$app->user->id]);
        
        return $dataProvider;
    }
}
