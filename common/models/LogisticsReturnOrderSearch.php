<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LogisticsReturnOrder;

/**
 * LogisticsReturnOrderSearch represents the model behind the search form of `common\models\LogisticsReturnOrder`.
 */
class LogisticsReturnOrderSearch extends LogisticsReturnOrder
{
    public function attributes(){
        return array_merge(parent::attributes(),['advance','trueName','senderName']);
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'goods_num', 'order_state', 'state', 'abnormal', 'collection', 'order_type', 'return_type', 'member_id', 'member_cityid', 'receiving_provinceid', 'receiving_cityid', 'receiving_areaid', 'terminus_id', 'shipping_type','goods_price_state','same_city'], 'integer'],
            [['logistics_sn', 'ship_logistics_sn', 'goods_sn', 'order_sn', 'member_name', 'member_phone', 'receiving_name', 'receiving_phone', 'add_time', 'receiving_name_area','advance','trueName','senderName'], 'safe'],
            [['freight', 'goods_price', 'make_from_price', 'collection_poundage_two'], 'number'],
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
    public function search($params,  $identity = 0)
    {
        $query = LogisticsReturnOrder::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'order_id' => SORT_DESC,
                ]
            ],
            'pagination' => [
                    'pageSize' => Yii::$app->params['page_size'],
            ],     ]);

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
            'order_state' => $this->order_state,
            'state' => $this->state,
            'abnormal' => $this->abnormal,
            'collection' => $this->collection,
            'collection_poundage_two' => $this->collection_poundage_two,
            'order_type' => empty($identity)||$identity==1?$this->order_type:$identity,
            'return_type' => $this->return_type,
//             'add_time' => $this->add_time,
            'member_id' => $this->member_id,
            'member_cityid' => $this->member_cityid,
            'receiving_provinceid' => $this->receiving_provinceid,
            'receiving_cityid' => $this->receiving_cityid,
            'receiving_areaid' => $this->receiving_areaid,
            'terminus_id' => $this->terminus_id,
//             'logistics_route_id' => $this->logistics_route_id,
            'shipping_type' => $this->shipping_type,
        ]);
        
        //开单时间
        $add_time = ArrayHelper::getValue($params, 'LogisticsReturnOrderSearch.add_time', null);
        if($add_time){
            list($start, $end) = explode(' - ', $add_time);
            $start = strtotime($start);
            $end = strtotime($end)+60*60*24;
        
            $query->andFilterWhere(['between', 'logistics_return_order.add_time', $start , $end]);
        }

        $query->andFilterWhere(['like', 'logistics_return_order.logistics_sn', $this->logistics_sn])
            ->andFilterWhere(['like', 'ship_logistics_sn', $this->ship_logistics_sn])
            ->andFilterWhere(['like', 'goods_sn', $this->goods_sn])
            ->andFilterWhere(['like', 'order_sn', $this->order_sn])
            ->andFilterWhere(['like', 'member_name', $this->member_name])
            ->andFilterWhere(['like', 'logistics_return_order.member_phone', $this->member_phone])
            ->andFilterWhere(['like', 'receiving_name', $this->receiving_name])
            ->andFilterWhere(['like', 'receiving_phone', $this->receiving_phone])
            ->andFilterWhere(['like', 'receiving_name_area', $this->receiving_name_area]);
        
        // 下单员只能看见
//         if($type=='over'){
//             $query->andFilterWhere(['>=', 'order_state', 70]);
//         }else{
//             $query->andFilterWhere(['in', 'order_state', [5, 10]]);
//         }
        
        //垫付
        $query->join('LEFT JOIN','order_advance as advance','advance.logistics_sn = logistics_return_order.ship_logistics_sn');
        $query->andFilterWhere(['like','advance.state',$this->advance]);
        $dataProvider->sort->attributes['advance']=[
                'asc' =>['advance.state'=>SORT_ASC],
                'desc' =>['advance.state'=>SORT_DESC],
        ];
        
        // 开单员 查询同城退货单
        if (in_array(Yii::$app->params['roleEmployee'], array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)))) {
            $query->andFilterWhere(['same_city' => 1]);
        }
        
        // 落地点查询 落地点退货单
        if (in_array(Yii::$app->params['roleTerminus'], array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)))) {
            $query->innerJoin('terminus_user', 'terminus_user.terminus_id = logistics_return_order.terminus_id')
                  ->andFilterWhere(['terminus_user.user_id' => Yii::$app->user->id]);
        }
        
        //开单员
        $query->join('LEFT JOIN','user as truename','truename.id = logistics_return_order.employee_id');
        $query->andFilterWhere(['like','truename.user_truename',$this->trueName]);
        $dataProvider->sort->attributes['trueName']=[
                'asc' =>['truename.user_truename'=>SORT_ASC],
                'desc' =>['truename.user_truename'=>SORT_DESC],
        ];
        
        //送货员
        $query->join('LEFT JOIN','return_order_remark as order_remark','order_remark.order_id = logistics_return_order.order_id');
        $query->andFilterWhere(['like','order_remark.sender',$this->senderName]);
        $dataProvider->sort->attributes['senderName']=[
                'asc' =>['order_remark.sender'=>SORT_ASC],
                'desc' =>['order_remark.sender'=>SORT_DESC],
        ];
        
        

        return $dataProvider;
    }
    
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function tellerSearch($params)
    {
        $query = LogisticsReturnOrder::find();
        
        // add conditions that should always apply here
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'order_id' => SORT_DESC,
                ]
            ],
            'pagination' => [
                'pageSize' => Yii::$app->params['page_size'],
            ],     ]);
        
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
            'order_state' => $this->order_state,
            'state' => $this->state,
            'abnormal' => $this->abnormal,
            'collection' => $this->collection,
            'collection_poundage_two' => $this->collection_poundage_two,
            'order_type' => $this->order_type,
            'return_type' => $this->return_type,
            //             'add_time' => $this->add_time,
            'member_id' => $this->member_id,
            'member_cityid' => $this->member_cityid,
            'receiving_provinceid' => $this->receiving_provinceid,
            'receiving_cityid' => $this->receiving_cityid,
            'receiving_areaid' => $this->receiving_areaid,
            'terminus_id' => $this->terminus_id,
            'same_city' => $this->same_city,
            //             'logistics_route_id' => $this->logistics_route_id,
            'shipping_type' => $this->shipping_type,
        ]);
        
        //开单时间
        $add_time = ArrayHelper::getValue($params, 'LogisticsReturnOrderSearch.add_time', null);
        if($add_time){
            list($start, $end) = explode(' - ', $add_time);
            $start = strtotime($start);
            $end = strtotime($end)+60*60*24;
            
            $query->andFilterWhere(['between', 'logistics_return_order.add_time', $start , $end]);
        }
        
        $query->andFilterWhere(['like', 'logistics_return_order.logistics_sn', $this->logistics_sn])
        ->andFilterWhere(['like', 'ship_logistics_sn', $this->ship_logistics_sn])
        ->andFilterWhere(['&', 'goods_price_state', $this->goods_price_state])
        ->andFilterWhere(['like', 'goods_sn', $this->goods_sn])
        ->andFilterWhere(['like', 'order_sn', $this->order_sn])
        ->andFilterWhere(['like', 'member_name', $this->member_name])
        ->andFilterWhere(['like', 'logistics_return_order.member_phone', $this->member_phone])
        ->andFilterWhere(['like', 'receiving_name', $this->receiving_name])
        ->andFilterWhere(['like', 'receiving_phone', $this->receiving_phone])
        ->andFilterWhere(['like', 'receiving_name_area', $this->receiving_name_area]);
        
        // 下单员只能看见
        //         if($type=='over'){
        //             $query->andFilterWhere(['>=', 'order_state', 70]);
        //         }else{
        //             $query->andFilterWhere(['in', 'order_state', [5, 10]]);
        //         }
            
        //垫付
//         $query->join('LEFT JOIN','order_advance as advance','advance.logistics_sn = logistics_return_order.ship_logistics_sn');
//         $query->andFilterWhere(['like','advance.state',$this->advance]);
//         $dataProvider->sort->attributes['advance']=[
//             'asc' =>['advance.state'=>SORT_ASC],
//             'desc' =>['advance.state'=>SORT_DESC],
//         ];
        
        //开单员
//         $query->join('LEFT JOIN','user as truename','truename.id = logistics_return_order.employee_id');
//         $query->andFilterWhere(['like','truename.user_truename',$this->trueName]);
//         $dataProvider->sort->attributes['trueName']=[
//             'asc' =>['truename.user_truename'=>SORT_ASC],
//             'desc' =>['truename.user_truename'=>SORT_DESC],
//         ];
        
        //送货员
//         $query->join('LEFT JOIN','return_order_remark as order_remark','order_remark.order_id = logistics_return_order.order_id');
//         $query->andFilterWhere(['like','order_remark.sender',$this->senderName]);
//         $dataProvider->sort->attributes['senderName']=[
//             'asc' =>['order_remark.sender'=>SORT_ASC],
//             'desc' =>['order_remark.sender'=>SORT_DESC],
//         ];
        
        
        
        return $dataProvider;
    }
    
    /**
     * @desc 取得参数
     * @author 暴闯
     * @param unknown $parameter
     * @return array
     */
    public function _getObjectUrlParameter($url, $parameter = array()) {
        $return[] = $url;
        $_param = Yii::$app->request->queryParams;
        if(isset($_param['r'])) {
            unset($_param['r']);
        }
        return array_merge($return, array_merge($_param, $parameter));
    }
}
