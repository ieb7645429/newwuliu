<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LogisticsOrder;

/**
 * LogisticsOrderSearch represents the model behind the search form of `common\models\LogisticsOrder`.
 */
class LogisticsOrderFushunSearch extends LogisticsOrderFushun
{
    public function attributes(){
        return array_merge(parent::attributes(),['receivingCityName','memberCityName','userName','trueName']);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'goods_num', 'order_state', 'state', 'abnormal', 'collection', 'order_type', 'member_id', 'member_cityid', 'receiving_cityid', 'receiving_areaid', 'terminus_id', 'logistics_route_id'], 'integer'],
            [['logistics_sn', 'order_sn',  'member_name', 'member_phone', 'receiving_name', 'receiving_phone', 'receiving_name_area','receivingCityName','memberCityName','userName','trueName'], 'safe'],
            [['freight', 'goods_price', 'make_from_price', 'collection_poundage_one', 'collection_poundage_two'], 'number'],
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
        $query = LogisticsOrderFushun::find();
        // add conditions that should always apply here
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['page_size'],
            ],
            'sort' => [
                'defaultOrder' => [
                    'order_id' => SORT_DESC,
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
            'order_id' => $this->order_id,
            'freight' => $this->freight,
            'goods_price' => $this->goods_price,
            'make_from_price' => $this->make_from_price,
            'goods_num' => $this->goods_num,
            //'order_state' => $this->order_state,
            'state' => $this->state,
            'abnormal' => $this->abnormal,
            'collection' => $this->collection,
            'collection_poundage_one' => $this->collection_poundage_one,
            'collection_poundage_two' => $this->collection_poundage_two,
            'order_type' => $this->order_type,
            'member_id' => $this->member_id,
            'member_cityid' => $this->member_cityid,
            'receiving_cityid' => $this->receiving_cityid,
            'receiving_areaid' => $this->receiving_areaid,
            'terminus_id' => $this->terminus_id,
            'logistics_route_id' => $this->logistics_route_id,
        ]);
        
        //开单时间
        $add_time = ArrayHelper::getValue($params, 'LogisticsOrderSearch.add_time', null);
        if($add_time){
            list($start, $end) = explode(' - ', $add_time);
            $start = strtotime($start);
            $end = strtotime($end)+60*60*24;
            
            $query->andFilterWhere(['between', 'add_time', $start , $end]);
        }
        //状态下拉框order_state
        if($this->order_state==71){
            $query->andFilterWhere(['and','order_state = 70','collection = 1','return_logistics_sn = ""',['NOT',['&','state','4']]]);
        }elseif($this->order_state==72){
            $query->andFilterWhere(['and','order_state = 70',['or','collection <> 1','return_logistics_sn <> ""',['&','state','4']]]);
        }else{
            $query->andFilterWhere(['order_state' => $this->order_state]);
        }
        
        $query->andFilterWhere(['like', 'logistics_sn', $this->logistics_sn])
        ->andFilterWhere(['like', 'order_sn', $this->order_sn])
        //->andFilterWhere(['like', 'add_time', $this->add_time])
        ->andFilterWhere(['like', 'member_name', $this->member_name])
        ->andFilterWhere(['like', 'logistics_order_fushun.member_phone', $this->member_phone])
        ->andFilterWhere(['like', 'receiving_name', $this->receiving_name])
        ->andFilterWhere(['like', 'receiving_phone', $this->receiving_phone])
        ->andFilterWhere(['like', 'receiving_name_area', $this->receiving_name_area]);
        
        //会员号
        $query->join('LEFT JOIN','user as username','username.id = logistics_order_fushun.member_id');
        $query->andFilterWhere(['like','username.username',$this->userName]);
        $dataProvider->sort->attributes['userName']=[
            'asc' =>['username.username'=>SORT_ASC],
            'desc' =>['username.username'=>SORT_DESC],
        ];
        //开单员
        $query->join('LEFT JOIN','user as truename','truename.id = logistics_order_fushun.employee_id');
        $query->andFilterWhere(['like','truename.user_truename',$this->trueName]);
        $dataProvider->sort->attributes['trueName']=[
            'asc' =>['truename.user_truename'=>SORT_ASC],
            'desc' =>['truename.user_truename'=>SORT_DESC],
        ];
        
        
        //收货人市
        $query->join('INNER JOIN','area AS A','A.area_id = logistics_order_fushun.receiving_cityid');
        $query->andFilterWhere(['like','A.area_name',$this->receivingCityName]);
        $dataProvider->sort->attributes['receivingCityName']=[
            'asc' =>['A.area_name'=>SORT_ASC],
            'desc' =>['A.area_name'=>SORT_DESC],
        ];
        //发货人市
        $query->join('INNER JOIN','area AS B','B.area_id = logistics_order_fushun.member_cityid');
        $query->andFilterWhere(['like','B.area_name',$this->memberCityName]);
        $dataProvider->sort->attributes['memberCityName']=[
            'asc' =>['B.area_name'=>SORT_ASC],
            'desc' =>['B.area_name'=>SORT_DESC],
        ];
        
        return $dataProvider;
    }
}
