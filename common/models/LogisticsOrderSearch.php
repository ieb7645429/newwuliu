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
class LogisticsOrderSearch extends LogisticsOrder
{
    public function attributes(){
        return array_merge(parent::attributes(),['receivingCityName','memberCityName','userName','trueName','advance','routeName','driverTrueName','driverUserName','memberOrderNum','condition_time','condition_time_by','countnum']);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'goods_num', 'order_state', 'state', 'abnormal', 'collection', 'order_type', 'member_id', 'member_cityid', 'receiving_cityid', 'receiving_areaid', 'terminus_id', 'logistics_route_id', 'driver_member_id', 'freight_state', 'goods_price_state'], 'integer'],
            [['logistics_sn', 'order_sn',  'member_name', 'member_phone', 'receiving_name', 'receiving_phone', 'receiving_name_area','receivingCityName','memberCityName','userName','trueName','advance','routeName','driverTrueName','driverUserName','condition_time','condition_time_by','countnum'], 'safe'],
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
    public function search($params,$type='')
    {
        $query = LogisticsOrder::find();
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
            'driver_member_id' => $this->driver_member_id,
        ]);
        
        //开单时间
        $add_time = ArrayHelper::getValue($params, 'LogisticsOrderSearch.add_time', null);
        if($add_time){
            list($start, $end) = explode(' - ', $add_time);
            $start = strtotime($start);
            $end = strtotime($end)+60*60*24;
            
            $query->andFilterWhere(['between', 'logistics_order.add_time', $start , $end]);
        }
        //状态下拉框order_state
        if($this->order_state==71){
//             $query->andFilterWhere(['and','order_state = 70','collection = 1','return_logistics_sn = ""',['NOT',['&','logistics_order.state','4']]]);
            $query->andFilterWhere(['and','order_state = 70','freight_state = 2',['or','logistics_order.state = 1','logistics_order.state = 2'],'return_logistics_sn = ""']);
        }elseif($this->order_state==72){
//             $query->andFilterWhere(['and','order_state = 70',['or','collection <> 1','return_logistics_sn <> ""',['&','logistics_order.state','4']]]);
            $query->andFilterWhere(['and','order_state = 70',['or',['and','logistics_order.state != 1','logistics_order.state != 2'],'logistics_order.freight_state = 1'],'return_logistics_sn = ""']);
        }else{
            $query->andFilterWhere(['order_state' => $this->order_state]);
        }

        $query->andFilterWhere(['like', 'logistics_order.logistics_sn', $this->logistics_sn])
            ->andFilterWhere(['like', 'order_sn', $this->order_sn])
            //->andFilterWhere(['like', 'add_time', $this->add_time])
            ->andFilterWhere(['like', 'member_name', $this->member_name])
            ->andFilterWhere(['like', 'logistics_order.member_phone', $this->member_phone])
            ->andFilterWhere(['like', 'receiving_name', $this->receiving_name])
            ->andFilterWhere(['like', 'receiving_phone', $this->receiving_phone])
            ->andFilterWhere(['&', 'freight_state', $this->freight_state])
            ->andFilterWhere(['&', 'goods_price_state', $this->goods_price_state])
            ->andFilterWhere(['like', 'receiving_name_area', $this->receiving_name_area]);
        
        //会员号
        $query->join('LEFT JOIN','user as username','username.id = logistics_order.member_id');
        $query->andFilterWhere(['like','username.username',$this->userName]);
        $dataProvider->sort->attributes['userName']=[
                'asc' =>['username.username'=>SORT_ASC],
                'desc' =>['username.username'=>SORT_DESC],
        ];
        
        //司机
        $query->join('LEFT JOIN','user as drivername','drivername.id = logistics_order.driver_member_id');
        $query->andFilterWhere(['like','drivername.user_truename',$this->driverTrueName]);
        $dataProvider->sort->attributes['driverTrueName']=[
                'asc' =>['drivername.username'=>SORT_ASC],
                'desc' =>['drivername.username'=>SORT_DESC],
        ];
        
        
        //开单员
            $query->join('LEFT JOIN','user as truename','truename.id = logistics_order.employee_id');
            $query->andFilterWhere(['like','truename.user_truename',$this->trueName]);
            $dataProvider->sort->attributes['trueName']=[
                    'asc' =>['truename.user_truename'=>SORT_ASC],
                    'desc' =>['truename.user_truename'=>SORT_DESC],
            ];
        
        
        //收货人市
        $query->join('INNER JOIN','area AS A','A.area_id = logistics_order.receiving_cityid');
        $query->andFilterWhere(['like','A.area_name',$this->receivingCityName]);
        $dataProvider->sort->attributes['receivingCityName']=[
                'asc' =>['A.area_name'=>SORT_ASC],
                'desc' =>['A.area_name'=>SORT_DESC],
        ];
        //发货人市
        $query->join('INNER JOIN','area AS B','B.area_id = logistics_order.member_cityid');
        $query->andFilterWhere(['like','B.area_name',$this->memberCityName]);
        $dataProvider->sort->attributes['memberCityName']=[
                'asc' =>['B.area_name'=>SORT_ASC],
                'desc' =>['B.area_name'=>SORT_DESC],
        ];
        
        //垫付
        $query->join('LEFT JOIN','order_advance as advance','advance.order_id = logistics_order.order_id');
        $query->andFilterWhere(['like','advance.state',$this->advance]);
        $dataProvider->sort->attributes['advance']=[
                'asc' =>['advance.state'=>SORT_ASC],
                'desc' =>['advance.state'=>SORT_DESC],
        ];
        
        //路线
        $query->join('LEFT JOIN','logistics_route as route','route.logistics_route_id = logistics_order.logistics_route_id');
        $query->andFilterWhere(['like','route.logistics_route_name',$this->routeName]);
        $dataProvider->sort->attributes['routeName']=[
                'asc' =>['route.logistics_route_name'=>SORT_ASC],
                'desc' =>['route.logistics_route_name'=>SORT_DESC],
        ];

        /*
         * 0.0
         * 连表查询 order_time
         */
        /*$query->join('LEFT JOIN','order_time as time','time.order_id = logistics_order.order_id');
        $query->andFilterWhere(['and','logistics_order.order_id ='.'logistics_order.state = 6',$this->priceTime]);
        $dataProvider->sort->attributes['priceTime']=[
            'asc' =>['time.price_time'=>SORT_ASC],
            'desc' =>['time.price_time'=>SORT_DESC],
        ];*/

        
        // 下单员只能看见
        if($type=='over'){
            $query->andFilterWhere(['>=', 'order_state', 70]);
        }
        
        return $dataProvider;
    }
    
    public function driverSearch($params,$condition_time_bye='',$type='',$status='')
    {
    	$query = LogisticsOrder::find();
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
    			'driver_member_id' => $this->driver_member_id,
    	]);
    	
    	if($condition_time_bye == '3'){
    		$condition_time = date("Y-m-d",time()).' - '.date("Y-m-d",time());
    		if($condition_time){
    			list($start, $end) = explode(' - ', $condition_time);
    			$start = strtotime($start);
    			$end = strtotime($end)+60*60*24;
    		}
    		$query->join('LEFT JOIN','order_time as ctime','ctime.order_id = logistics_order.order_id');
    		$query->andFilterWhere(['between', 'ctime.ruck_time', $start , $end]);
    	}
    
    	//根据条件判断查询时间-'2'=>'开单时间','3'=>'封车时间','4'=>'已完成时间'
    	$condition_time_by = ArrayHelper::getValue($params, 'LogisticsOrderSearch.condition_time_by', null);
    	if($condition_time_by){
    		$condition_time = ArrayHelper::getValue($params, 'LogisticsOrderSearch.condition_time', null);
    		if($condition_time){
    			list($start, $end) = explode(' - ', $condition_time);
    			$start = strtotime($start);
    			$end = strtotime($end)+60*60*24;
    		}
    		if($condition_time_by == '2'){//开单时间
//     			$query->join('LEFT JOIN','condition_time');
    			$query->join('LEFT JOIN','order_time as ctime','ctime.order_id = logistics_order.order_id');
    			$query->andFilterWhere(['between', 'ctime.price_time', $start , $end]);
    		}elseif($condition_time_by == '3'){//封车时间
    			$query->join('LEFT JOIN','order_time as ctime','ctime.order_id = logistics_order.order_id');
    			$query->andFilterWhere(['between', 'ctime.ruck_time', $start , $end]);
    		}elseif($condition_time_by == '4'){//已完成时间
    			$query->join('LEFT JOIN','order_time as ctime','ctime.order_id = logistics_order.order_id');
    			$query->andFilterWhere(['between', 'ctime.collection_time', $start , $end]);
    		}
    	}
    
    	//根据条件判断查询时间-'2'=>'开单时间','3'=>'封车时间','4'=>'已完成时间'
    	$condition_time_by = ArrayHelper::getValue($params, 'condition_time_by', null);
    	if($condition_time_by){
    		$condition_time = ArrayHelper::getValue($params, 'condition_time', null);
    		if($condition_time){
    			list($start, $end) = explode(' - ', $condition_time);
    			$start = strtotime($start);
    			$end = strtotime($end)+60*60*24;
    		}
    		if($condition_time_by == '2'){//开单时间
    			$query->join('LEFT JOIN','order_time as ctime','ctime.order_id = logistics_order.order_id');
    			$query->andFilterWhere(['between', 'ctime.price_time', $start , $end]);
    		}elseif($condition_time_by == '3'){//封车时间
    			$query->join('LEFT JOIN','order_time as ctime','ctime.order_id = logistics_order.order_id');
    			$query->andFilterWhere(['between', 'ctime.ruck_time', $start , $end]);
    		}elseif($condition_time_by == '4'){//已完成时间
    			$query->join('LEFT JOIN','order_time as ctime','ctime.order_id = logistics_order.order_id');
    			$query->andFilterWhere(['between', 'ctime.collection_time', $start , $end]);
    		}
    	}
    	
    	$statusview = ArrayHelper::getValue($params, 'status', null);
    	//明细拉框state
    	if($statusview=='50'){//已封车
//     		$query->andFilterWhere(['and','logistics_order.order_state = 70','logistics_order.state = 2']);
//     		$query->andFilterWhere(['and','logistics_order.order_state = 50',['or','logistics_order.order_state = 70','logistics_order.state = 2']]);
    		$query->andFilterWhere(['and','logistics_order.order_state = 70','logistics_order.state = 6','logistics_order.same_city = 1']);
    	}elseif($statusview=='80'){//已完成
//     		$query->andFilterWhere(['and','logistics_order.order_state = 70','logistics_order.state = 6']);
    		$query->andFilterWhere(['and','logistics_order.order_state = 70','logistics_order.state = 2','logistics_order.same_city = 1']);
    	}else{
    		$query->andFilterWhere(['order_state' => $this->order_state]);
    	}
    	
    	//根据司机id获取司机订单列表
    	//司机user_truename
    	$driver_member_id = ArrayHelper::getValue($params, 'driver_member_id', null);
    	if($driver_member_id){
    		$query->andFilterWhere(['and', 'logistics_order.driver_member_id = '.$driver_member_id]);
    	}
    	//状态下拉框order_state
    	if($status=='50'){//已封车
//     		$query->andFilterWhere(['and','logistics_order.order_state = 50',['or','logistics_order.order_state = 70','logistics_order.state = 2']]);
    		$query->andFilterWhere(['and','logistics_order.order_state = 70','logistics_order.state = 6','logistics_order.same_city = 1']);
    	}elseif($status=='80'){//已完成
//     		$query->andFilterWhere(['and','logistics_order.order_state = 70','logistics_order.state = 6']);
    		$query->andFilterWhere(['and','logistics_order.order_state = 70','logistics_order.state = 2','logistics_order.same_city = 1']);
    	}else{
    		$query->andFilterWhere(['order_state' => $this->order_state]);
    	}
    
    	$query->andFilterWhere(['like', 'logistics_order.logistics_sn', $this->logistics_sn])
    	->andFilterWhere(['like', 'order_sn', $this->order_sn])
    	//->andFilterWhere(['like', 'add_time', $this->add_time])
    	->andFilterWhere(['like', 'member_name', $this->member_name])
    	->andFilterWhere(['like', 'logistics_order.member_phone', $this->member_phone])
    	->andFilterWhere(['like', 'receiving_name', $this->receiving_name])
    	->andFilterWhere(['like', 'receiving_phone', $this->receiving_phone])
    	->andFilterWhere(['&', 'freight_state', $this->freight_state])
    	->andFilterWhere(['&', 'goods_price_state', $this->goods_price_state])
    	->andFilterWhere(['like', 'receiving_name_area', $this->receiving_name_area]);
    
    	//司机user_truename
    	$query->join('LEFT JOIN','user as drivername','drivername.id = logistics_order.driver_member_id');
    	$query->andFilterWhere(['like','drivername.user_truename',$this->driverTrueName]);
    	$dataProvider->sort->attributes['driverTrueName']=[
    			'asc' =>['drivername.username'=>SORT_ASC],
    			'desc' =>['drivername.username'=>SORT_DESC],
    	];
    	//司机username
    	$query->join('LEFT JOIN','user as driverusername','driverusername.id = logistics_order.driver_member_id');
    	$query->andFilterWhere(['like','driverusername.username',$this->driverUserName]);
    	$dataProvider->sort->attributes['driverUserName']=[
    			'asc' =>['driverusername.username'=>SORT_ASC],
    			'desc' =>['driverusername.username'=>SORT_DESC],
    	];
    
    	if($type == 'delive'){
    		//         	$query->andFilterWhere(['like', 'receiving_phone', $this->receiving_phone]);
    		$query->andFilterWhere(['>', 'logistics_order.driver_member_id', 0]);
			//查询同城，上边注释一行11-14
//     		$query->andFilterWhere(['and', 'logistics_order.same_city = 1',['>', 'logistics_order.driver_member_id', 0]]);
    		$query->groupBy(['logistics_order.driver_member_id']);
    		$query->select(['logistics_order.*', "count(*) AS countnum"]);
    
    	}
    
    	return $dataProvider;
    }
    
    public function returnSearch($params,$type='')
    {
        $query = LogisticsOrder::find();
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
                //             'order_state' => $this->order_state,
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
    
        $query->andFilterWhere(['like', 'logistics_order.logistics_sn', $this->logistics_sn])
        ->andFilterWhere(['like', 'order_sn', $this->order_sn])
        ->andFilterWhere(['like', 'add_time', $this->add_time])
        ->andFilterWhere(['like', 'member_name', $this->member_name])
        ->andFilterWhere(['like', 'logistics_order.member_phone', $this->member_phone])
        ->andFilterWhere(['like', 'receiving_name', $this->receiving_name])
        ->andFilterWhere(['like', 'receiving_phone', $this->receiving_phone])
        ->andFilterWhere(['like', 'receiving_name_area', $this->receiving_name_area]);
    
        //会员号
        $query->join('INNER JOIN','user','user.id = logistics_order.member_id');
        $query->andFilterWhere(['like','user.username',$this->userName]);
        $dataProvider->sort->attributes['userName']=[
                'asc' =>['user.username'=>SORT_ASC],
                'desc' =>['user.username'=>SORT_DESC],
        ];
        //收货人市
        $query->join('INNER JOIN','area AS A','A.area_id = logistics_order.receiving_cityid');
        $query->andFilterWhere(['like','A.area_name',$this->receivingCityName]);
        $dataProvider->sort->attributes['receivingCityName']=[
                'asc' =>['A.area_name'=>SORT_ASC],
                'desc' =>['A.area_name'=>SORT_DESC],
        ];
        //发货人市
        $query->join('INNER JOIN','area AS B','B.area_id = logistics_order.member_cityid');
        $query->andFilterWhere(['like','B.area_name',$this->memberCityName]);
        $dataProvider->sort->attributes['memberCityName']=[
                'asc' =>['B.area_name'=>SORT_ASC],
                'desc' =>['B.area_name'=>SORT_DESC],
        ];
    
        // 下单员只能看见
        if($type=='over'){
            $query->andFilterWhere(['>=', 'order_state', 70]);
            $query->andFilterWhere(['and','collection = 1','return_logistics_sn = ""',['NOT',['&','state','4']]]);
        }elseif($type=='return'){
            $query->andFilterWhere(['and','return_logistics_sn <> ""']);
        }else{
            $query->andFilterWhere(['in', 'order_state', [5, 10]]);
        }
        return $dataProvider;
    }
    
    public function searchDriverOrder($params)
    {
        $query = LogisticsOrder::find();
        
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
            'order_id' => $this->order_id,
            'freight' => $this->freight,
            'goods_price' => $this->goods_price,
            'make_from_price' => $this->make_from_price,
            'goods_num' => $this->goods_num,
            'order_state' => $this->order_state,
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
        
        $query->andFilterWhere(['like', 'logistics_order.logistics_sn', $this->logistics_sn])
        ->andFilterWhere(['like', 'order_sn', $this->order_sn])
        ->andFilterWhere(['like', 'add_time', $this->add_time])
        ->andFilterWhere(['like', 'member_name', $this->member_name])
        ->andFilterWhere(['like', 'member_phone', $this->member_phone])
        ->andFilterWhere(['like', 'receiving_name', $this->receiving_name])
        ->andFilterWhere(['like', 'receiving_phone', $this->receiving_phone])
        ->andFilterWhere(['like', 'receiving_name_area', $this->receiving_name_area]);
        
        $query->innerJoin('logistics_car', 'logistics_order.logistics_route_id = logistics_car.logistics_route_id')
              ->andFilterWhere(['driver.member_id' => Yii::$app->user->id]);
        return $dataProvider;
    }
    
    public function searchMemberOrder($params, $type='')
    {
        $query = LogisticsOrder::find();
        
        // add conditions that should always apply here
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            $query->andFilterWhere(['and','order_state = 70','freight_state = 2',['or','logistics_order.state = 1','logistics_order.state = 2'],'return_logistics_sn = ""']);
        }elseif($this->order_state==72){
            $query->andFilterWhere(['and','order_state = 70',['or',['and','logistics_order.state != 1','logistics_order.state != 2'],'logistics_order.freight_state = 1'],'return_logistics_sn = ""']);
        }else{
            $query->andFilterWhere(['order_state' => $this->order_state]);
        }
        
        $query->andFilterWhere(['like', 'logistics_sn', $this->logistics_sn])
        ->andFilterWhere(['like', 'order_sn', $this->order_sn])
        ->andFilterWhere(['like', 'add_time', $this->add_time])
        ->andFilterWhere(['like', 'member_name', $this->member_name])
        ->andFilterWhere(['like', 'member_phone', $this->member_phone])
        ->andFilterWhere(['like', 'receiving_name', $this->receiving_name])
        ->andFilterWhere(['like', 'receiving_phone', $this->receiving_phone])
        ->andFilterWhere(['like', 'receiving_name_area', $this->receiving_name_area]);
        
        // 下单员只能看见
//         if($type=='over') {
//             $query->andFilterWhere(['>=', 'order_state', 70]);
//         }else{
//              $query->andFilterWhere(['in', 'order_state', [5, 10]]);
//         }
        
        $query->andFilterWhere(['member_id' => Yii::$app->user->id]);
        
        return $dataProvider;
    }
    
    public function BalanceEditSearch($params,$type='')
    {
        $query = LogisticsOrder::find();
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
            $query->where('0=1');
            return $dataProvider;
        }
    
        // grid filtering conditions
        $query->andFilterWhere([
                'order_id' => $this->order_id,
                'freight' => $this->freight,
                'goods_price' => $this->goods_price,
                'make_from_price' => $this->make_from_price,
                'goods_num' => $this->goods_num,
                //             'order_state' => $this->order_state,
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
    
        $query->andFilterWhere(['like', 'logistics_sn', $this->logistics_sn])
        ->andFilterWhere(['like', 'order_sn', $this->order_sn])
        //->andFilterWhere(['like', 'add_time', $this->add_time])
        ->andFilterWhere(['like', 'member_name', $this->member_name])
        ->andFilterWhere(['like', 'member_phone', $this->member_phone])
        ->andFilterWhere(['like', 'receiving_name', $this->receiving_name])
        ->andFilterWhere(['like', 'receiving_phone', $this->receiving_phone])
        ->andFilterWhere(['like', 'receiving_name_area', $this->receiving_name_area]);
    
        //会员号
        $query->join('INNER JOIN','user','user.id = logistics_order.member_id');
        $query->andFilterWhere(['like','user.username',$this->userName]);
        $dataProvider->sort->attributes['userName']=[
                'asc' =>['user.username'=>SORT_ASC],
                'desc' =>['user.username'=>SORT_DESC],
        ];
        
        $query->andFilterWhere(['>=','order_state',10]);
        $query->andFilterWhere(['or','return_logistics_sn = ""','return_logistics_sn is null']);
        $query->andFilterWhere(['&','goods_price_state',2]);
    
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
 /**
 *  2017-09-21
 *  用于统计返货数量页面 statisticas 
 *  xiaoyu
 **/
 public function search_new($params,$type='rece')
    {
        $query = LogisticsOrder::find()->where(['not', ['logistics_order.return_logistics_sn' => '']]);
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
      /*  $query->andFilterWhere([
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
            'driver_member_id' => $this->driver_member_id,
        ]);*/
        
        //开单时间
       /* $add_time = ArrayHelper::getValue($params, 'LogisticsOrderSearch.add_time', null);
        if($add_time){
            list($start, $end) = explode(' - ', $add_time);
            $start = strtotime($start);
            $end = strtotime($end)+60*60*24;
            
            $query->andFilterWhere(['between', 'logistics_order.add_time', $start , $end]);
        }*/


        //$query->andFilterWhere(['like', 'logistics_order.logistics_sn', $this->logistics_sn])
          //  ->andFilterWhere(['like', 'order_sn', $this->order_sn])
            //->andFilterWhere(['like', 'add_time', $this->add_time])
           // ->andFilterWhere(['like', 'member_name', $this->member_name])
           // ->andFilterWhere(['like', 'logistics_order.member_phone', $this->member_phone])
           // ->andFilterWhere(['like', 'receiving_name', $this->receiving_name])
		 if($type == 'rece')
		{
            $query->andFilterWhere(['like', 'receiving_phone', $this->receiving_phone]);
			$query->groupBy(['logistics_order.receiving_phone']);
		}
		 elseif($type == 'send')
		{
			$query->join('LEFT JOIN','user as username','username.id = logistics_order.member_id');
		    $query->andFilterWhere(['like', 'username.username',$this->userName]); 
			$query->groupBy(['logistics_order.member_id']);
		 }

         //   ->andFilterWhere(['&', 'freight_state', $this->freight_state])
          //  ->andFilterWhere(['&', 'goods_price_state', $this->goods_price_state])
          //  ->andFilterWhere(['like', 'receiving_name_area', $this->receiving_name_area]);
        
        //会员号
        //$query->join('LEFT JOIN','user as username','username.id = logistics_order.member_id');
      //  $query->andFilterWhere(['like','username.username',$this->userName]);
		

      //  $dataProvider->sort->attributes['userName']=[
       //         'asc' =>['username.username'=>SORT_ASC],
      //          'desc' =>['username.username'=>SORT_DESC],
       // ];
        return $dataProvider;
    }
}
