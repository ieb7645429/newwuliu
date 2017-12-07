<?php

namespace common\models;
use common\models\LogisticsRoute;
use Yii;
use yii\base\Object;
use backend\models\OrderAdvance;
use backend\models\ReturnOrderRemark;

/**
 * This is the model class for table "logistics_return_order".
 *
 * @property int $order_id
 * @property string $logistics_sn 物流单号
 * @property string $ship_logistics_sn 发货物流单号
 * @property string $goods_sn 物流单号
 * @property string $order_sn 订单编号
 * @property string $freight 运费
 * @property string $goods_price 商品价钱
 * @property string $make_from_price 制单费
 * @property int $goods_num 商品数量
 * @property int $order_state 订单一级状态(0取消，5用户开单，10开单，20分拣，30摆渡车，40货站分拣，50封车，60落地，70送货，80完成)
 * @property int $state 订单二级状态(1买断2不买断,4已收款,8 已付款)
 * @property int $abnormal 挂起(1挂起，2正常)
 * @property int $collection 是否代要货款(1代要,2不代要)
 * @property string $collection_poundage_two 代收的手续费2
 * @property int $order_type 订单类型（1线上，2友件）
 * @property int $return_type 退货类型（1返货，2退货）
 * @property int $return_all 是否全返(1:是，2否)
 * @property string $add_time 生成时间(时间戳)
 * @property string $member_name 发货人
 * @property int $member_id 发货人id
 * @property int $member_cityid 发货人市级id
 * @property string $member_phone 发货人电话
 * @property string $receiving_name 收货人
 * @property string $receiving_phone 收货人电话
 * @property string $receiving_name_area 收货人详细地址
 * @property int $receiving_provinceid 收货人省Id
 * @property int $receiving_cityid 收货人市级id
 * @property int $receiving_areaid 收货人区级id
 * @property int $terminus_id 落地点
 * @property int $shipping_type 运费付款方式
 */
class LogisticsReturnOrder extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_CREATE_2 = 'create2';
    const SCENARIO_SEARCH = 'search';
    
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['freight', 'goods_price', 'make_from_price', 'collection_poundage_two','goods_num', 'order_state', 'state', 'abnormal', 'collection', 'order_type', 'return_type', 'return_all', 'add_time', 'member_id', 'member_cityid', 'receiving_provinceid', 'receiving_cityid',  'terminus_id', 'shipping_type','add_time', 'member_phone', 'member_name', 'member_cityid', 'goods_num', 'receiving_phone', 'receiving_name', 'make_from_price', 'freight','logistics_sn', 'ship_logistics_sn', 'goods_sn', 'order_sn', 'member_name', 'member_phone', 'receiving_name', 'receiving_phone'];
        $scenarios[self::SCENARIO_CREATE_2] = ['member_phone','member_name','member_cityid','goods_num','goods_price', 'order_type','receiving_phone','receiving_name','receiving_name_area','shipping_type','make_from_price','freight'];
        $scenarios[self::SCENARIO_SEARCH] = [];
        return $scenarios;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'logistics_return_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['freight', 'goods_price', 'make_from_price', 'collection_poundage_two'], 'number'],
            [['goods_num', 'order_state', 'state', 'abnormal', 'collection', 'order_type', 'return_type', 'return_all', 'add_time', 'member_id', 'member_cityid', 'receiving_provinceid', 'receiving_cityid', 'receiving_areaid', 'terminus_id', 'shipping_type'], 'integer'],
            [['add_time', 'member_phone', 'member_name', 'member_cityid', 'goods_num', 'receiving_phone', 'receiving_name', 'receiving_areaid', 'receiving_name_area', 'make_from_price', 'freight'], 'required'],
            [['logistics_sn', 'ship_logistics_sn', 'goods_sn', 'order_sn', 'member_name', 'member_phone', 'receiving_name', 'receiving_phone', 'receiving_name_area'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'logistics_sn' => '票号',
            'ship_logistics_sn' => '发货票号',
            'goods_sn' => '货号',
            'order_sn' => '友件网编号',
            'freight' => '运费',
            'goods_price' => '代收款',
            'make_from_price' => '制单费',
            'goods_num' => '商品数量',
            'order_state' => '订单一级状态',
            'state' => '订单二级状态',
            'abnormal' => '挂起',
            'collection' => '代要货款',
            'collection_poundage_two' => '代收手续费',
            'order_type' => '订单类型',
            'return_type' => '退货类型',
            'return_all' => '返货类型',
            'add_time' => '生成时间',
            'member_name' => '退货人',
            'member_id' => '发货人id',
            'member_cityid' => '发货人城市',
            'member_phone' => '退货人电话',
            'receiving_name' => '收货人',
            'receiving_phone' => '收货人电话',
            'receiving_name_area' => '收货人详细地址',
            'receiving_provinceid' => '收货人省',
            'receiving_cityid' => '收货人市',
            'receiving_areaid' => '收货人区',
            'terminus_id' => '落地点',
            'shipping_type' => '运费付款方式',
        ];
    }

    /**
     * @desc 取得代要货款名列表
     * @author 暴闯
     * @return string[]
     */
    public static function getCollectionList() {
        return [
            '1' => '代要',
            '2' => '不代要',
        ];
    }
    /**
     * 退货单
     */
    public static function getReturnCollectionList() {
        return [
                '2' => '不代要',
        ];
    }

    /**
     * @desc 取得返货类型列表
     * @author 暴闯 
     * @return string[]
     */
    public static function getReturnAllList() {
        return [
            '1' => '全返',
            '2' => '拆单返',
        ];
    }

    /**
     * 填补退货物流信息
     * 朱鹏飞
     */
    public function fillLogisticsInfo($data){
    	$modelUser = new User();
    	$modelTerminusUser = new TerminusUser();
    	$modelAuthAssignment  = new AuthAssignment();
    	$data->add_time = time();
    	$role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];//获取权限
    	if($role == Yii::$app->params['roleTerminus'])
    	{
    		$terminusId= $modelTerminusUser->findOne(['user_id'=>Yii::$app->user->id]);//获取落地点id
    		$data->terminus_id = $terminusId->terminus_id;//落地点id
    	}
    	$data->receiving_provinceid = 6;
    	$data->order_state = Yii::$app->params['returnOrderStateEmployee'];//订单状态默认为10
    	$data->employee_id = yii::$app->user->id;//开单员id
    	return $data;
    }
    
    /**
     * 补全订单信息
     * @param unknown $model
     */
    public function orderInfo(&$model)
    {
    	$this->_sameCity($model);
    	$this->_shippingSale($model);
    	$this->_scale($model);
    }
    
    /**
     * 判断是否同城
     * @param unknown $model
     */
    private  function _sameCity(&$model)
    {
    	if($model->member_cityid == $model->receiving_cityid)
    	{
    		$model->same_city=1;
    	}else{
    		$model->same_city=2;
    	}
    }
    
    /**
     * 获取运费制单费优惠捡钱
     * @param unknown $model
     */
    private function _shippingSale(&$model)
    {
    	$model->shipping_sale = $model->make_from_price;
    	if($model->return_type == 1 && $model->shipping_type != 3)
    	{
    		$model->shipping_sale = $model->freight + $model->make_from_price;
    	}
    }
    
    /**
     * 运费分配比例
     * @param unknown $model
     */
    private function _scale(&$model)
    {
    	if($model->terminus_id>0)
    	{
    		$modelTerminus = new Terminus();
    		$terminusInfo = $modelTerminus::findOne($model->terminus_id);
    		$model->scale = $terminusInfo->receiving_scale;
    	}else{
    		$model->scale = 0;
    		$model->terminus_id = 0;
    	}
    }
    /**
     * 生成退货号
     * 朱鹏飞
     */
    public function getReturnGoodsSn($str = 'T')
    {
    	
    	$today = strtotime(date('Y-m-d', time()));
    	$num = $this->find()->where('add_time >= :add_time', [':add_time' => $today])->count();
    	return $str.date('Ymd').'-'.str_pad($num,6,"0",STR_PAD_LEFT);
    }
    
    /**
     * 生成退货物流单号
     * 朱鹏飞
     */
    public function getReturnLogisticsSn($order_id, $str = 'T')
    {
    	return $str.Yii::$app->params['city_num'].str_pad($order_id,8,"0",STR_PAD_LEFT);
    }
    
    /**
     * 退货状态修改
     * @param unknown $data
     * @return boolean
     */
    public function setReturnOrderState($data)
    {
    	$role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
    	switch ($role)
    	{
    		case '入库':
    			if($data->order_state == Yii::$app->params['returnOrderStateFerry'])//30
    			{
    				$r1 = $this->_setOrderState_1($data, Yii::$app->params['returnOrderStateDriver'], $role);//50
    				$r2= $this->_upStorageId($data);
    				if($r1 && $r2)
    				{
    					return true;
    				}
    			}
    			return false;
    			break;
    		case '退货员':
    			if($data->order_state == Yii::$app->params['returnOrderStateDriver'])//50
    			{
    				$r1 = $this->_setOrderState_1($data, Yii::$app->params['returnOrderStateDelivery'], $role);//70
    				$r2 = $this->_upReturnManageId($data);
    				if($data->state == 2 && ($data->return_type == 1 || $data->shipping_type == 1))
    				{
    					$res3 = $this->_collection($data);
    					if($res3 == false)
    					{
    						return false;
    					}
    				}
    				if($r1 && $r2)
    				{
    					return true;
    				}
    			}
    			return false;
    			break;
			case '瑞胜退货组':
			case '塔湾退货组':
			    if($data->order_state == Yii::$app->params['returnOrderStateFerry'])//30
			    {
			        $r1 = $this->_setOrderState_1($data, Yii::$app->params['returnOrderStateDriver'], $role);//50
			        $r2= $this->_upStorageId($data);
			        if($r1 && $r2)
			        {
			            return true;
			        }
			    }
			    if($data->order_state == Yii::$app->params['returnOrderStateDriver'])//50
			    {
			        $r1 = $this->_setOrderState_1($data, Yii::$app->params['returnOrderStateDelivery'], $role);//70
			        $r2 = $this->_upReturnManageId($data);
			        if($data->state == 2 && ($data->return_type == 1 || $data->shipping_type == 1))
			        {
			            $res3 = $this->_collection($data);
			            if($res3 == false)
			            {
			                return false;
			            }
			        }
			        if($r1 && $r2)
			        {
			            return true;
			        }
			    }
			    return false;
			    break;
    		case '同城员':
		    case '西部退货组':
    			if($data->order_state == Yii::$app->params['returnOrderStateEmployee'])//10
    			{
    				$r1 = $this->_setOrderState_1($data, Yii::$app->params['returnOrderStateDivide'], $role);//20
    				$r2= $this->_upStorageId($data);
    				if($r1 && $r2)
    				{
    					return true;
    				}
    			}
    			if($data->order_state == Yii::$app->params['returnOrderStateDivide'])//20
    			{
    			    $r1 = $this->_setOrderState_1($data, Yii::$app->params['returnOrderStateFerry'], $role);//30
    			    $r2= $this->_upStorageId($data);
    			    if($r1 && $r2)
    			    {
    			        return true;
    			    }
    			}
    			if($data->order_state == Yii::$app->params['returnOrderStateFerry'])//30
    			{
    			    $r1 = $this->_setOrderState_1($data, Yii::$app->params['returnOrderStateDriver'], $role);//50
    			    $r2= $this->_upStorageId($data);
    			    if($r1 && $r2)
    			    {
    			        return true;
    			    }
    			}
    			if($data->order_state == Yii::$app->params['returnOrderStateDriver'])//50
    			{
    				$r1 = $this->_setOrderState_1($data, Yii::$app->params['returnOrderStateDelivery'], $role);//70
    				$r2 = $this->_upReturnManageId($data);
    				if($data->state == 2 && ($data->return_type == 1 || $data->shipping_type == 1))
    				{
    					$res3 = $this->_collection($data);
    					if($res3 == false)
    					{
    						return false;
    					}
    				}
    				if($r1 && $r2)
    				{
    					return true;
    				}
    			}
    			return false;
    			break;
    		default: return false;
    	}
    }
    
    /**
     * 判断是否代收
     * @param Object $data 订单对像
     * @return true false
     */
    private function _collection($data)
    {
    	$r1 = $this->_stateSwitch($data);
    	$modelOrderTime = new ReturnOrderTime();
    	$r2 = $modelOrderTime->orderTimeswitch('collection', $data);
    	if($r1 && $r2){
    		return true;
    	}
    	return false;
    }
    
    /**
     * 判断订单二级状态
     * 朱鹏飞
     * @param Object $data 订单对像
     * @return true false
     */
    private  function _stateSwitch($data)
    {
    	return $this->_stateEdit($data, Yii::$app->params['returnOrderPayment']);

    }
    
    /**
     * 修改订单二级状态
     * 朱鹏飞
     * @param Object $data订单对像
     * @param int $state 要修成的值
     * return true false
     */
    private function _stateEdit($data, $state){
    	$data->state = $state;
    	return $data->save();
    }
    
    /**
     * 修改入库人id
     * @param Object $data 订单对像
     *  @return true false 
     */
    private function _upStorageId($data)
    {
    	$data->storage_id = Yii::$app->user->id;
    	return $data->save();
    }
    
    /**
     * 修改退货员id
     * @param Object $data 订单对像
     * @return true false 
     */
    private function _upReturnManageId($data)
    {
    	$data->return_manage_id = Yii::$app->user->id;
    	return $data->save();
    }
    /**
     * 订单标签打印
     * @param $order_id
     * 靳健
     */
    public function tagOrderPrint($order_id){
        $area = new Area();
        $data = $this::find()->joinWith('goodsInfo')->where(['logistics_return_order.order_id'=>$order_id])->asArray()->all();
        $data[0]['province'] = $area::findOne($data[0]['receiving_provinceid'])->area_name;
        $data[0]['city'] = $area::findOne($data[0]['receiving_cityid'])->area_name;
// 		$data[0]['district'] = $area::findOne($data[0]['receiving_areaid'])->area_name;
		$data[0]['from_city'] = $area::findOne($data[0]['member_cityid'])->area_name;
        return $data;
    }
    /**
     * 联合查询
     * 靳健
     */
    public function getGoodsInfo(){
        return $this->hasMany(ReturnGoods::className(), ['order_id' => 'order_id']);
    }
    public function getMemberCity(){
        return $this->hasOne(Area::className(), ['area_id' => 'member_cityid']);
    }
    public function getShippingType(){
        return $this->hasOne(ShippingTpye::className(), ['id' => 'shipping_type']);
    }
    public function getAdvance()
    {
        return $this->hasOne(OrderAdvance::className(), ['logistics_sn' => 'ship_logistics_sn']);
    }
    
    public function getAdvanceShow($ship_logistics_sn)
    {
        $model = OrderAdvance::findOne(['logistics_sn'=>$ship_logistics_sn]);
        if(!empty($model->state)){
            if($model->state==1){
                return '已追回';
            }else if($model->state==2){
                return '已垫付';
            }
        }else{
            return '';
        }
    }
    public function getReturnOrderTime(){
        return $this->hasOne(ReturnOrderTime::className(), ['order_id' => 'order_id']);
    }
    public function getTrueName()
    {
        return $this->hasOne(User::className(), ['id' => 'employee_id']);
    }
    public function getSenderName(){
        return $this->hasOne(ReturnOrderRemark::className(), ['order_id' => 'order_id']);
    }
    

    /**
     * 联合查询
     * 靳健
     */
    public function getRouetInfo(){
        return $this->hasMany(logisticsRoute::className(), ['logistics_route_id' => 'logistics_route_id']);
    }
    /**
     * 修改退货状态
     * 靳健
     * @param $order_id
     */
    public function upOrderState($order_id){
        $data = $this::findOne($order_id);
        if($data->order_state==50){//判断为待收货状态
            return $this->setReturnOrderState($data);
        }
        return true;
    }

    /**
     * 司机可退货订单查询
     * @param $logistics_sn  物流编号
     * @param $goods_sn  商品编号
     * @param $where 
     * 靳健
     */
    public function returnList($params,$where){
        $condition = $this->getOrderCondition($params);
        $identityWhere = empty($params['identity'])?array():array('order_type'=>$params['identity']);
        $orderList = $this::find()->joinWith('memberCity')->joinWith('shippingType')->joinWith('returnOrderTime')->where(['and',$identityWhere,$where,$condition])->orderBy('order_id desc');
//         $orderList = $this->returnCompleteButton($orderList,1);
//         $orderList = $this->buttonType($orderList,1);
        return $orderList;
    }
    /**
     * 订单checkbox是否已经处理
     * @param  $state  未处理订单状态
     * @param  $type
     * 1,选中 2,不选中
     * 靳健
     */
    public function isTreatment($list,$goods_state){
        foreach($list as $key => $value){
            $arr = ReturnGoods::find()->where(['and','order_id ='.$value['order_id'],'goods_state = '.$goods_state])->asArray()->all();
            if(count($arr)>0){
                $list[$key]['checkbox'] = 1;
            }else{
                $list[$key]['checkbox'] = 0;
            }
        }
        return $list;
    }
    /**
     * 不代收且不提付不显示按钮
     * 靳健
     */
    public function returnCompleteButton($orderList){
        foreach($orderList as $key => $value){
            if($value['collection']==2&&$value['shipping_type']!=1&&$value['return_type']==2){
                $orderList[$key]['button'] = 0;
            }else{
                $orderList[$key]['button'] = 1;
            }
        }
        return $orderList;
    }
    /**
     * 商品按钮类型
     * @param $orderList
     * buttonType 1未处理  2已处理 0无
     * 靳健
     */
    public function buttonType($orderList,$type){
        if($type==1){
            $goods_state_01 = Yii::$app->params['returnOrderStateEmployee'];//10
        }else if($type == 2){
            $goods_state_01 = Yii::$app->params['returnOrderStateFerry'];//30
        }
        foreach($orderList as $key => $value){
            foreach($value['returnGoods'] as $k => $v){
                if($v['goods_state']==$goods_state_01){
                    $orderList[$key]['returnGoods'][$k]['buttonType'] = 1;
                }else{
                    $orderList[$key]['returnGoods'][$k]['buttonType'] = 2;
                }
            }
        }
        return $orderList;
    }
    /**
     * 退货打印
     * @param $goods_sn  搜索条件 
     * @param $order_sn  搜索条件 
	 * @param $source    来源 creat退货申请,list退货列表
     * 靳健
     */
    public function orderPrint($order_sn,$goods_sn,$where,$source){
        $params['logistics_sn'] = $order_sn;
        $params['goods_sn'] = $goods_sn;
        $list = $this->returnList($params,$where)->asArray()->all();
        //$list = $this->removeOrder($list,Yii::$app->params['returnOrderStateEmployee']);
       // $list = array_merge($list,$list);
	    if($source=='list'){
		//增加收货人省市区
		$list = $this->GetAreaInfo($list);
		}
        return $list;
    }
    /**
     * 退货订单处理
     * 靳健
     * $params $order_arr 处理订单id数组
     * $params $state 处理订单状态
     */
    public function ajaxReturnOrderEdit($order_arr,$order_state){
        $userBalance = new UserBalance();
        foreach($order_arr as $v){
            $return_order_model = $this::findOne($v);
            if(!empty($return_order_model->ship_logistics_sn)){
                if(!$userBalance->returnBalanceInfo($v)){
                    return false;
                }
            }
            if($return_order_model->order_state == $order_state){//判断待入库状态
                if($this->isAbnormal($return_order_model,$order_state)){//判断是否异常
                    $r1 = $this->setReturnOrderState($return_order_model);//修改订单状态
                    if(!$r1){
                        return false;
                    }
                }else{
                    $r2 = $this->orderAbnormal($return_order_model->order_id);//挂起
                    if(!$r2){
                        return false;
                    }
                }
            }
        }
        return true;
    }
    /**
     * 删去商品没有处理的订单
     * @param $list
     * 靳健
     */
    public function removeOrder($list,$goods_state){
        $goods = new ReturnGoods();
        foreach($list as $key=>$value){
            $arr = $goods::find()->where(['and','order_id ='.$value['order_id'],'goods_state ='.$goods_state])->asArray()->all();
           if(count($arr)>0){
               unset($list[$key]);
           }
        }
        return $list;
    }
    /**
     * 物流信息联合查询
     * 靳健
     * */
    public function getReturnGoods(){
        return $this->hasMany(ReturnGoods::className(), ['order_id' => 'order_id']);
    }

    /**
     * 订单查询条件
     * @param unknown $logistics_sn
     * @param unknown $goods_sn
     * 靳健
     */
    public function getOrderCondition($params){
        $condition = '';
        if(!empty($params['logistics_sn'])){
            $condition = "logistics_sn like '%".$params['logistics_sn']."%'";
        }
        if(!empty($params['goods_sn'])){
            $condition .= "logistics_order.order_id = '{$this::getOrderId($params['goods_sn'])}'";
        }
        
        
        return $condition;
    }
    /**
     * 获取订单id
     * @param $goods_sn 商品单号
     * 靳健
     * */
    public function getOrderId($goods_sn){
        $returnGoods = new ReturnGoods();
        $order_id = $returnGoods->find()->select('order_id')->where('goods_sn = "'.$goods_sn.'"')->asArray()->one()['order_id'];
        return $order_id;
    }

    /**
     * @desc 取得订单状态名
     * @author 暴闯
     * @param unknown $orderState
     * @return string
     */
    public function getOrderStateName($orderState) {
        switch ($orderState) {
            case Yii::$app->params['returnOrderStateCancel']:
                return '取消';
            case Yii::$app->params['returnOrderStateMember']:
                return '用户下单（未确认）';
            case Yii::$app->params['returnOrderStateEmployee']:
                return '已开单';
            case Yii::$app->params['returnOrderStateDivide']:
                return '物流点已分拣';
            case Yii::$app->params['returnOrderStateFerry']:
                return '已上摆渡车';
            case Yii::$app->params['returnOrderStateDivide2']:
                return '货站已分拣';
            case Yii::$app->params['returnOrderStateDriver']:
                return '已封车';
            case Yii::$app->params['returnOrderStateTerminus']:
                return '已到物流点';
            case Yii::$app->params['returnOrderStateDelivery']:
                return '已送货';
            case Yii::$app->params['returnOrderStateComplete']:
                return '已完成';
        }
    }

    /**
     * @desc 取得是否代要货款
     * @author 暴闯
     * @param unknown $collection
     * @return string
     */
    public function getCollectionName($collection) {
        switch ($collection) {
            case '1':
                return '代要';
            case '2':
                return '不代要';
        }
    }

    /**
     * @desc 返回退货类型
     * @author 暴闯
     * @param unknown $returnType
     * @return string
     */
    public function getReturnTypeName($returnType) {
        switch ($returnType) {
            case '1':
                return '返货';
            case '2':
                return '退货';
        }
    }
    /**
     * 退货查询条件
     * 靳健
     */
    public function getReturnWhere($order_state,$abnormal,$add_time=null,$time_type = 'add_time',$other=null){
        $searchTime = $this->getSearchTime($add_time,$time_type);
        //$member = !empty($member_id)?$member ='storage_id ='. $member_id:"";
        $other = !empty($other)?$other:"";
        $where = [
                'and',
                'order_state ='.$order_state,
                'abnormal = '.$abnormal,
                $other,
                $searchTime
        ];
        return $where;
    }
    /**
     * 查询时间添加
     * 靳健
     * @param unknown $add_time
     * @param unknown $search_name
     */
    public function getSearchTime($add_time,$search_name){
        if(!empty($add_time)){
            if($search_name == 'add_time'){//装车查询订单生成时间
                $where = ['between','logistics_return_order.add_time',$add_time['start'],$add_time['end']];
                return $where;
            }else if($search_name == 'collection_time'){
                $where = ['between','return_order_time.collection_time',$add_time['start'],$add_time['end']];
                return $where;
            }
        }
        return array();
    }
    /**
     * 原返信息
     * 靳健
     * @param $order_id 
     * @param $model
     */
    public function getReturnCreate($order_id,$model){
        $data = LogisticsOrder::findOne($order_id);
        if(empty($data)){
            return $model;
        }
        $memberInfo = User::findOne($data->member_id);
        $model->member_phone = $data->receiving_phone;
        $model->member_name = $data->receiving_name;
        $model->member_cityid = $data->receiving_cityid;
        //$model->goods_num = $data->goods_num;
        $model->goods_price = $data->goods_price;
        $model->receiving_phone = $data->member_phone;
        $model->receiving_name = $data->member_name;
        $model->receiving_provinceid = $memberInfo->member_provinceid;
        $model->receiving_cityid = $data->member_cityid;
        //$model->receiving_areaid = $memberInfo->member_areaid;
        $model->receiving_name_area = $memberInfo->member_areainfo;
        $model->ship_logistics_sn = $data->logistics_sn;
        $model->shipping_type = $data->shipping_type;
//         $model->make_from_price = $data->make_from_price;
        $model->order_type = $data->order_type;
        $model->make_from_price = 0;
        $model->freight = $data->freight;
        $model->collection_poundage_two = $data->collection_poundage_two;
        return $model;
    }
    /**
     * 是否存在订单
     * 靳健
     * @param $order_id;
     */
    public function isExistOrder($order_id){
        if(LogisticsOrder::findOne($order_id)){
            return true;
        }
        return false;
    }
    
    /**
     * @author 暴闯
     * @param unknown $collection
     * @return string
     */
    public function getFreightStateName($freightState) {
        $return = [];
        if ($freightState & 1) {
            $return[] = '已收款';
        }
        if ($freightState & 2) {
            $return[] = '未收款';
        }
        if ($freightState & 4) {
            $return[] = '已结款';
        }
        return implode("/", $return);
    }

    /**
     * @author 暴闯
     * @param unknown $collection
     * @return string
     */
    public function getGoodsPriceStateName($goodsPriceState) {
        $return = [];
        if ($goodsPriceState & 1) {
            $return[] = '已收款';
        }
        if ($goodsPriceState & 2) {
            $return[] = '未收款';
        }
        if ($goodsPriceState & 4) {
            $return[] = '已结款';
        }
        return implode("/", $return);
    }
    
    /**
     * 获取退货组对应order_type
     * 分拣中心 order_type = 0
     */
    public function getIdentity(){
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        switch ($role)
        {
            case '西部退货组':
                return 1;
                break;
            case '瑞胜退货组':
                return 3;
                break;
            case '塔湾退货组':
                return 4;
                break;
            default:
                return 0;
        }
    }

    /**
     * 订单列表添加返货id
     * @param unknown $orderList
     */
    public function orderGetReturnId($orderList){
        foreach($orderList as $key => $value){
            $orderList[$key]['return_id'] = $this->getReturnInfo(['logistics_sn'=>$value['return_logistics_sn']])->order_id;
        }
        return $orderList;
    }
    
    /**
     * 返货订单关联
     * 靳健
     * @param $logistics_sn
     */
    public function getReturnInfo($where){
        return $this::find()->where($where)->one();
    }
    
    /**
     * 取得退货单信息
     * @param unknown $sn
     * @return \yii\db\ActiveRecord|array|NULL
     */
    public function getOrderByLogisticsSn($sn) {
        $query = self::find();
        return $query->where('logistics_sn = :logistics_sn', ['logistics_sn' => $sn])
                     ->asArray()
                     ->one();

    }
    /**
     * 判断退货单是否有送货员
     * @param unknown $order_id
     */
    public function isSender($order_id){
        $returnRemark = new ReturnOrderRemark();
        if(empty($returnRemark::findOne($order_id)))  return true;//备注不存在 未送货
        if(empty($returnRemark::findOne($order_id)->sender)){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * 朱鹏飞
     * 判断订单是否异常
     * @param Object $data
     * @return true 成功 false 失败
     */
    public  function isAbnormal($data,$order_state){
        $modelGoods = new ReturnGoods();
        $goodsData = $modelGoods::find()->where(['order_id'=>$data->order_id])->select('goods_state')->distinct()->count();
        if($goodsData>1){
            return false;
        }
        $goodsData = $modelGoods::find()->where(['order_id'=>$data->order_id, 'goods_state' => $order_state])->asArray()->all();
        if(!empty($goodsData))
        {
            return false;
        }
        return true;
    }
    
    /**
     * 朱鹏飞
     * 订单标记异常
     * @param Object $data
     * @return true 成功 false 失败
     */
    public function orderAbnormal($order_id){
        $model = $this::findOne($order_id);
        $model->scenario = 'search';
        $model->abnormal = 1;
        return $model->save();
    }
    
    /**
     * 靳健异常恢复
     * @param unknown $order_id  订单id
     * @param unknown $order_state  恢复状态
     */
    public function recoverOrder($order_id,$order_state){
        $modelOrder = $this::findOne($order_id);
        $modelOrder->scenario = 'search';
        $modelOrder->order_state = $order_state;
        $modelOrder->abnormal = 2;
        return $modelOrder->save();
    }
    
    
    public function batchGoodsEdit($from_status,$order_arr){
        $goods = new ReturnGoods();
        $goodsModel = $goods->find()->where(['in','order_id',$order_arr])->all();
        foreach($goodsModel as $key => $value){
            if($value->goods_state==$from_status){
                $re = $goods->switchUpGoodsState($value,Yii::$app->user->id);
                if(!$re){
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 改变退货订单状态
     * 朱鹏飞
     * @param unknown $data 订单信息对像
     * @param unknown $orderState 需要改变成多少状态
     *  @param unknown $role 权限
     * @return bool true 成功 false 失败
     */
    private  function _setOrderState_1($data, $orderState, $role)
    {
        $timeArray = array('退货员'=>'signed_for_time', '入库'=>'ruck_time', '收款员');
        $model= new ReturnOrderTime();
        $modelReturnLog = new ReturnLog();
        $r1 = $model->orderTimeswitch($orderState, $data);
        $r2 = $this->_cityWideReturnOrder($data,$orderState);//修改订单状态
        $r3 = $modelReturnLog->addReturnLogInfo($data, $orderState);//增加log日记
        if($r1 && $r2 && $r3){
            return true;
        }
        return false;
    }
    
    
    /**
     * 判断是否同城
     * 朱鹏飞
     * @param unknown $data
     * @param unknown $orderState
     * @return true 成功 false 失败
     */
    private function _cityWideReturnOrder($data, $orderState){
//         if(($data->member_cityid == $data->receiving_cityid) && ($data->order_state ==Yii::$app->params['returnOrderStateEmployee']))//订单状态为10时，判断是否同城
//         {
//             return $this->_upReturnOrderState($data,Yii::$app->params['returnGoodsStateComplete']);//同城订单状态改为80
//         }else{
            return $this->_upReturnOrderState($data,$orderState);//修改订单状态
//         }
    }

    /**
     * 修改退货订单状态
     * 朱鹏飞
     * @param unknown $data
     * @param unknown $orderState
     */
    private function _upReturnOrderState($data,$orderState){
    	$data->scenario='create';
        $data->order_state = $orderState;
        return $data->save();
    }
	/**
	*  功能:加入区域信息
	*  用途:为打印收据地址提供省市区信息
	*  作者:小雨
	**/
	private function GetAreaInfo($orderList){
	   if(!empty($orderList) && count($orderList)>0)
	   {
			$area = array();	
			for($i=0;$i<count($orderList);$i++)
			{
			  $orderList[$i]['province'] = Area::getAreaNameById($orderList[$i]['receiving_provinceid']);
			  $orderList[$i]['city'] = Area::getAreaNameById($orderList[$i]['receiving_cityid']);
			  if(!empty($orderList[$i]['receiving_areaid'])){
			   $orderList[$i]['district']= Area::getAreaNameById($orderList[$i]['receiving_areaid']);
			  }
			  else{
			   $orderList[$i]['district']='';
			  }
			}
		}
		return $orderList;
	}
}
