<?php

namespace frontend\modules\hlj\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\AuthAssignment;
use common\models\Log;
use frontend\modules\hlj\models\LogisticsRoute;
use yii\base\Exception;
use frontend\modules\hlj\models\User;
use common\models\UserAll;


/**
 * This is the model class for table "logistics_order".
 *
 * @property int $order_id
 * @property string $logistics_sn 物流单号
 * @property string $order_sn 订单编号
 * @property string $freight 运费
 * @property string $goods_price 商品价钱
 * @property string $make_from_price 制单费
 * @property int $goods_num 商品数量
 * @property int $order_state 订单一级状态(0取消，5用户开单，10开单，20分拣，30摆渡车，40货站分拣，50封车，60落地，70送货，80完成)
 * @property int $state 订单二级状态(1买断2不买断,4已收款,8 已付款)
 * @property int $abnormal 挂起(1挂起，2正常)
 * @property int $collection 是否代收货款(1代收,2不代收)
 * @property string $collection_poundage_one 代收的手续费1
 * @property string $collection_poundage_two 代收的手续费2
 * @property int $order_type 订单类型（1线上，2友件）
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
 * @property int $logistics_route_id 物流线路id
 * @property int $shipping_type 运费付款方式 
 */
class LogisticsOrder extends \yii\db\ActiveRecord
{
    const SCENARIO_SEARCH = 'search';
    
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_SEARCH] = [];
        return $scenarios;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'logistics_order';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_hlj');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['freight', 'goods_price', 'make_from_price', 'collection_poundage_one', 'collection_poundage_two' ,'add_time'], 'number'],
            [['order_state', 'state', 'abnormal', 'collection', 'order_type', 'member_id', 'member_cityid','receiving_provinceid', 'receiving_cityid', 'receiving_areaid', 'terminus_id', 'logistics_route_id', 'shipping_type','pay_for'], 'integer'],
            [['add_time','logistics_route_id', 'member_cityid', 'member_name', 'member_phone', 'receiving_phone', 'receiving_name'], 'required'],
            [['order_sn','member_name', 'receiving_name', 'receiving_phone', 'receiving_name_area'], 'string', 'max' => 255],
            ['member_phone', 'number'],
            
            ['goods_num', 'required'],
            ['goods_num', 'integer', 'min'=>1],
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
            'order_sn' => '订单编号',
            'freight' => '运费',
            'goods_price' => '代收款',
            'make_from_price' => '制单费',
            'goods_num' => '数量',
            'order_state' => '状态',
            'state' => '订单二级状态',
            'abnormal' => '挂起',
            'collection' => '代收货款',
            'collection_poundage_one' => '会员费',
            'collection_poundage_two' => '代收手续费',
            'order_type' => '订单类型',
            'add_time' => '生成时间',
            'member_name' => '发货人',
            'member_id' => '发货人id',
            'member_cityid' => '发货人城市',
            'member_phone' => '发货人电话',
            'receiving_name' => '收货人',
            'receiving_phone' => '收货人电话',
            'receiving_name_area' => '收货人详细地址',
            'receiving_provinceid' => '收货人省',
            'receiving_cityid' => '收货人市',
            'receiving_areaid' => '收货人区',
            'terminus_id' => '落地点',
            'logistics_route_id' => '物流线路',
            'shipping_type' => '运费付款方式',
            'driver_member_id' => '司机',
            'freight_state' => '运费状态',
            'goods_price_state' => '代收款状态',
            'pay_for' => '运费垫付',
            'driver_get' => '司机收取',
        ];
    }
    

    
    /**
     * 生成物流单号
     * 朱鹏飞
     */
    public function getLogisticsSn($order_id)
    {
        $module = \Yii::$app->getModule('hlj');
        return $module->params['city_num'].str_pad($order_id,8,"0",STR_PAD_LEFT);
    }

    /**
     * 填补物流信息
     * 朱鹏飞
     */
    public function fillLogisticsInfo(&$model ,$type='member', $username = null, $ismodify = 0){
//         $userAll = new UserAll();
//         $hljModelUser = new User();
        $modelAuthAssignment  = new AuthAssignment();
        $model->add_time = time();
        if($type == 'member'){
            $model->member_id = Yii::$app->user->id;
        }elseif ($type == 'logistics'){
            $hlj_user_info = $this->userInfo($model, $ismodify);
            
            
            
//             $hlj_user_info = $hljModelUser->getMemberInfo(array('username' =>$username));
//             if(empty($hlj_user_info)) {
//                 $user_info = $userAll->getMemberInfo(array('username' =>$username));
//                 if(empty($user_info)){//物流表中是否存在
//                     $user_info = $userAll->userInfo($username,'hlj');//用户为空，添加新用户
//                     if($user_info == null){
//                         return false;
//                     }
//                 }else{
//                     return false;
//                 }
//                 $hlj_user_info = $hljModelUser->userInfo($model, $username,$user_info->id);//添加黑龙江用户
//                 if($hlj_user_info == null){
//                     return false;
//                 }
                
//                 $r = $modelAuthAssignment->saveMember($hlj_user_info->id ,'黑龙江用户');//添加权限表
//                 if(!$hlj_user_info || !$r){
//                     return false;
//                 }
//             }else{
//                 $hlj_user_info->user_truename=$model->member_name;
//                 $hlj_user_info->member_phone=$model->member_phone;
//                 $hlj_user_info->save();
//             }
            $model->member_id = $hlj_user_info->id;
        }
//         if($model->order_sn && $model->order_type == 5)
//         {
//         	$model->order_type = 20;//友件订单
//         }
        $model->receiving_provinceid = 8;
        $model->order_state = Yii::$app->params['orderStateMember'];//订单状态默认为5
        $model->state = '2';//二级订单状态，不代收默认为2
        if($type == 'logistics'){
            $model->order_state = Yii::$app->params['orderStateEmployee'];//物流录入物流单，状态为10
        }
    }
    
    private function userInfo($model, $ismodify)
    {
        $userAll = new UserAll();
        $hljModelUser = new User();
        if($ismodify > 0)
        {
            $hlj_user_info= $hljModelUser::findOne($ismodify);
            $hlj_user_info->user_truename= $model->member_name;
            $hlj_user_info->member_phone= $model->member_phone;
            $hlj_user_info->save();
        }else{
            $hlj_user_info = $hljModelUser->find()->where(['user_truename'=>$model->member_name, 'member_phone'=>$model->member_phone])
            ->one();
            if(empty($hlj_user_info))
            {
                $username_id = $userAll::find()
                ->max('id');
                $user_info = $userAll->userInfo('0451'.$username_id,'hlj');//用户为空，添加新用户,username 为0451+最新的userall 子增id
                if($user_info == null){
                    return false;
                }
                $hlj_user_info = $hljModelUser->userInfo($model, '0451'.$username_id,$user_info->id);//添加黑龙江用户
                if($hlj_user_info == null){
                    return false;
                }
            }
        }
        return $hlj_user_info;
    }

    /**
     * 填补物流信息
     * 朱鹏飞
     */
    public function appFillLogisticsInfo($data,$type='member'){
    	$modelUser = new User();
    	$modelAuthAssignment  = new AuthAssignment();
    	$data->add_time = time();
    		$user_info = $modelUser->getMemberInfo(array('username' =>$data->member_phone));
    		if(empty($user_info)) {
    			$user_info = $modelUser->userInfo($data,$data->member_phone);//用户为空，添加新用户
    			$r = $modelAuthAssignment->saveMember($user_info->id);//添加权限表
    			if(!$user_info || !$r){
    				return false;
    			}
    		}
    	$data->member_id = $user_info->id;
        $data->shipping_type = 2;
    	$data->receiving_provinceid = 6;
    	$data->order_state = Yii::$app->params['orderStateMember'];//订单状态默认为5
    	$data->state = $user_info->is_buy_out;//二级订单状态，1代收,2不代收,
    	return $data;
    
    }
    
    /**
     * 补全订单信息
     * @param unknown $model
     */
    public function orderInfo(&$model, $terminusId = 0)
    {
    	$this->_sameCity($model);
    	$this->_shippingSale($model);
    	$this->_scale($model, $terminusId);
    }
    
    /**
     * 判断是否同城
     * @param unknown $model
     */
    private  function _sameCity(&$model)
    {
    	$model->collection_poundage_one = 0;
    	$model->collection_poundage_two = 0;
    	$model->same_city=2;
//     	if(($model->member_cityid == $model->receiving_cityid))
//     	{
    		$modelLogisticsRoute = new LogisticsRoute();
    		$LogisticsRoute = $modelLogisticsRoute->getLogisticsRouteFindOne($model->logistics_route_id);
    		if($LogisticsRoute->same_city == 1)
    		{
    			$model->same_city=1;
    		}
//     	}
    }
    
    /**
     * 获取运费制单费优惠捡钱
     * @param unknown $model
     */
    private function _shippingSale(&$model)
    {
    	$model->shipping_sale = 0;
//     	if($model->order_sn)//线上订单免运费
//     	{
//     		$model->shipping_sale = $model->freight;
//     	}
//     	if($model->same_city == 1)//同城订单免运费
//     	{
//     		$model->shipping_sale = $model->freight;
//     	}
//     	$logistics_route_id = array(5,9,12,17,32,14,11,31);//免运费落地点
//     	if(in_array($model->logistics_route_id, $logistics_route_id))
//     	{
//     	    $model->shipping_sale = $model->freight;
//     	}
//     	if($model->logistics_route_id == 14 || $model->logistics_route_id == 9 || $model->logistics_route_id == 11 || $model->logistics_route_id==1 || $model->logistics_route_id == 4 || $model->logistics_route_id==10 || $model->logistics_route_id ==13 || $model->logistics_route_id==18 || $model->logistics_route_id==27)//新民,丹东,阜新线路免运费
//     	{
//     		$model->shipping_sale = $model->freight;
//     	}
//     	$model->shipping_sale = $model->make_from_price + $model->freight;
    }
    
    /**
     * 运费分配比例
     * @param unknown $model
     */
    private function _scale(&$model, $terminusId)
    {
    	if($terminusId>0)
    	{
    		$modelTerminus = new Terminus();
    		$terminusInfo = $modelTerminus::findOne($model->terminus_id);
    		$model->scale = $terminusInfo->shipping_scale;
    	}else{
    		$model->scale = 0.5;//代扣运费0.5元
    		$model->terminus_id = 0;
    	}
    }
    /**
     * 物流信息联合查询
     * 靳健
     * */
    public function getGoodsInfo(){
        return $this->hasMany(Goods::className(), ['order_id' => 'order_id'])->joinWith('carInfo');
    }
    public function getDriverGoods(){
        return $this->hasMany(Goods::className(), ['order_id' => 'order_id'])->joinWith('carInfo')
        ->where(['and','goods.driver_member_id ='.Yii::$app->user->id,['or','goods_state ='.Yii::$app->params['orderStateDriver'],'goods_state ='.Yii::$app->params['orderStateDelivery']]]);
    }
    public function getDriverGoodsTen(){
        return $this->hasMany(Goods::className(), ['order_id' => 'order_id'])->joinWith('carInfo')
        ->where(['and','goods_state ='.Yii::$app->params['orderStateEmployee']]);
    }
    public function getDriverManagerGoods(){
        return $this->hasMany(Goods::className(), ['order_id' => 'order_id'])->joinWith('carInfo')
        ->where(['or','goods_state ='.Yii::$app->params['orderStateDriver'],'goods_state ='.Yii::$app->params['orderStateDelivery']]);
    }
    public function getDriverGoodsInfo(){
        return $this->hasMany(Goods::className(), ['order_id' => 'order_id'])->joinWith('carInfo')
            ->where(['and',['or','goods_state ='.Yii::$app->params['orderStateDelivery'],'goods_state ='.Yii::$app->params['orderStateDriver']],'goods.driver_member_id ='.Yii::$app->user->id]);
    }
    public function getTerminusGoodsInfo(){
        return $this->hasMany(Goods::className(), ['order_id' => 'order_id'])
        ->where(['goods_state'=>Yii::$app->params['orderStateDelivery']]);
    }
    public function getSendGoodsInfo(){
        return $this->hasMany(Goods::className(), ['order_id' => 'order_id'])
        ->where(['goods_state'=>Yii::$app->params['orderStateDelivery'],'send_id'=>Yii::$app->user->id]);
    }
    public function getRouteInfo(){
        return $this->hasOne(logisticsRoute::className(), ['logistics_route_id' => 'logistics_route_id']);
    }
    public function getTerminusGoodsTwo(){
        return $this->hasMany(Goods::className(), ['order_id' => 'order_id'])
        ->where(['goods_state'=>Yii::$app->params['orderStateDelivery']]);
    }
    public function getTerminusGoodsThree(){
        return $this->hasMany(Goods::className(), ['order_id' => 'order_id']);
    }
    public function getTerminus(){
    	return $this->hasOne(Terminus::className(), ['terminus_id'=>'terminus_id']);
    }
    public function getOrderTime(){
        return $this->hasOne(OrderTime::className(), ['order_id'=>'order_id']);
    }
    public function getOrderPrintLog(){
        return $this->hasOne(OrderPrintLog::className(), ['order_id'=>'order_id']);
    }
    /****联合查询结束******/
    
    public static function getCollectionList() {
        return [
            '1' => '代收',
            '2' => '不代收',
        ];
    }

    /**
     * 订单类型
     * @Author:Fenghuan
     * @param $order_type
     * @return string
     */
    public static function getOrderType($order_type)
    {
        if ($order_type == 1) {
            return '通达';
        }
        else if ($order_type == 3) {
            return '宣化';
        }
    }
    
    /**
     * 取得发货单信息
     * @param unknown $sn
     * @return \yii\db\ActiveRecord|array|NULL
     */
    public function getOrderByLogisticsSn($sn) {
        $query = self::find();
        return $query->where('logistics_sn = :logistics_sn', ['logistics_sn' => $sn])
                     ->asArray()
                     ->one();
    }
    
    public function getOrderStateName($orderState,$model = null) {
        switch ($orderState) {
            case Yii::$app->params['orderStateCancel']:
                return '取消';
            case Yii::$app->params['orderStateMember']:
                return '用户下单（未确认）';
            case Yii::$app->params['orderStateEmployee']:
                return '已开单';
            case Yii::$app->params['orderStateDivide']:
                return '物流点已分拣';
            case Yii::$app->params['orderStateFerry']:
                return '已上摆渡车';
            case Yii::$app->params['orderStateDivide2']:
                return '货站已分拣';
            case Yii::$app->params['orderStateDriver']:
                return '已封车';
            case Yii::$app->params['orderStateTerminus']:
                return '已到物流点';
            case Yii::$app->params['orderStateDelivery']:
//                 if(!empty($model)&&!($model->state&4)&&$model->collection==1&&empty($model->return_logistics_sn))
                if(!empty($model)&&($model->state==1||$model->state==2)&&$model->freight_state==2&&empty($model->return_logistics_sn))
            return '待送货';
                else
                return '已送货';
            case Yii::$app->params['orderStateComplete']:
                return '已完成';
        }
    }

    /**
     * 取得代收名称
     * @author 暴闯
     * @param unknown $collection
     * @return string
     */
    public function getCollectionName($collection) {
        switch ($collection) {
            case '1':
                return '代收';
            case '2':
                return '不代收';
        }
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
     * @author 暴闯
     * @param unknown $collection
     * @return string
     */
    public function getStateName($state) {
        if ($state& 1) {
            return '买断';
        } else if ($state& 2) {
            return '未买断';
        }
    }
    
    public function getDriverName($driverId = '') {
        if(!$driverId) {
            $driverId = $this->driver_member_id;
        }
        if(!$driverId) {
            return '';
        }
        return User::findOne($driverId)->user_truename;
    }
    
    public function getMemberCityName()
    {
        return $this->hasOne(Area::className(), ['area_id' => 'member_cityid']);
    }
    
    public function getUserName()
    {
        return $this->hasOne(User::className(), ['id' => 'member_id']);
    }
    public function getDriverTrueName()
    {
        return $this->hasOne(User::className(), ['id' => 'driver_member_id']);
    }
    public function getTrueName()
    {
        return $this->hasOne(User::className(), ['id' => 'employee_id']);
    }
    public function getAdvance()
    {
        return $this->hasOne(OrderAdvance::className(), ['order_id' => 'order_id']);
    }
    public function getRouteName()
    {
        return $this->hasOne(LogisticsRoute::className(), ['logistics_route_id' => 'logistics_route_id']);
    }

    public function getReceivingCityName()
    {
        return $this->hasOne(Area::className(), ['area_id' => 'receiving_cityid']);
    }

    public function getReceivingAreaName()
    {
        return $this->hasOne(Area::className(), ['area_id' => 'receiving_areaid']);
    }
    
    public function getAdvanceShow($order_id)
    {
        $model = OrderAdvance::findOne(['order_id'=>$order_id]);
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
    /**
     * 大司机订单
     * $type 1,待封车 2,已封车 3,异常 4,待收款 5,已完成 6,未封车 7,待扫码
     */
    public function getDriverManagerList($logistics_sn,$goods_sn,$type,$order_arr=null,$add_time = null,$where = null){
        $modelLogisticsRoute = new LogisticsRoute();
        $condition = $this->getOrderCondition($logistics_sn,$goods_sn,$add_time);
        switch ($type){
            case 1:
                $where = empty($where)?' 1 ':$where;
                $searchTime = $this->getSearchTime($add_time,'add_time');
                $orderList = $this::find()->joinWith('driverManagerGoods')->joinWith('routeInfo')
                ->where(['and','order_state = '.Yii::$app->params['orderStateEmployee'],'abnormal = 2',$where,$condition,$searchTime])
                ->orderBy('order_state,logistics_order.order_id desc')
                ->asArray()->all();
                return $orderList;
                break;
            case 2:
                $where = empty($where)?' 1 ':$where;
                $searchTime = $this->getSearchTime($add_time,'ruck_time');
                $orderList = $this::find()->joinWith('driverManagerGoods')->joinWith('routeInfo')->joinWith('orderTime')
                ->where(['and',['>=','order_state',Yii::$app->params['orderStateDriver']],'abnormal = 2',$where,$condition,$searchTime])->orderBy('logistics_order.order_id desc')
                ->asArray()->all();
                return $orderList;
                break;
            case 3:
                $searchTime = $this->getSearchTime($add_time,'add_time');
                $orderList = $this::find()->joinWith('goodsInfo')->joinWith('routeInfo')
                ->where(['and','order_state = '.Yii::$app->params['orderStateEmployee'],'abnormal = 1',$where,$condition,$searchTime])->orderBy('logistics_order.order_id desc')
                ->asArray()->all();
                return $orderList;
                break;
            case 4:
                $where = empty($where)?' 1 ':$where;
                $searchTime = $this->getSearchTime($add_time,'ruck_time');
                $orderList = $this::find()->joinWith('driverManagerGoods')->joinWith('routeInfo')->joinWith('orderTime')
                ->where(['and','order_state = '.Yii::$app->params['orderStateDelivery'],'abnormal = 2',['or','state = 1','state = 2'],'freight_state = 2',$where,$condition,$searchTime])->orderBy('logistics_order.order_id desc')
                ->asArray()->all();
                $orderList = $this->stateButtonType($orderList,1);
                return $orderList;
                break;
            case 5:
                $where = empty($where)?' 1 ':$where;
                $searchTime = $this->getSearchTime($add_time,'ruck_time');
                $orderList = $this::find()->joinWith('driverManagerGoods')->joinWith('routeInfo')->joinWith('orderTime')
                ->where(['and','order_state = '.Yii::$app->params['orderStateDelivery'],'abnormal = 2',['or',['and','collection = 2','shipping_type=3'],['and','state <> 1','state <> 2']],$where,$condition,$searchTime])->orderBy('logistics_order.order_id desc')
                ->asArray()->all();
                $orderList = $this->stateButtonType($orderList,1);
                return $orderList;
                break;
            case 6:
                $where = empty($where)?' 1 ':$where;
                $searchTime = $this->getSearchTime($add_time,'add_time');
                $orderList = $this::find()->joinWith('goodsInfo')->joinWith('routeInfo')
                ->where(['and','order_state = '.Yii::$app->params['orderStateEmployee'],'abnormal = 2',$where,$condition,$searchTime])
                ->orderBy('order_state,logistics_order.order_id desc')
                ->asArray()->all();
                return $orderList;
                break;
            case 7:
                $where = empty($where)?' 1 ':$where;
                $searchTime = $this->getSearchTime($add_time,'add_time');
                $orderList = $this::find()->joinWith('driverGoodsTen')->joinWith('routeInfo')
                ->where(['and','order_state = '.Yii::$app->params['orderStateEmployee'],'abnormal = 2',$where,$condition,$searchTime])
                ->orderBy('order_state,logistics_order.order_id desc')
                ->asArray()->all();
                return $orderList;
                break;
            default:
                $orderList = array();
                return $orderList;
                break;
        }
    }
    /**
     * 商品扫码状态
     * @param unknown $orderList
     */
    public function getGoodsScan($orderList){
        foreach($orderList as  $key => $value){
            foreach($value['goodsInfo'] as $k => $v){
                if($v['goods_state']==50||$v['goods_state']==70){
                    $orderList[$key]['goodsInfo'][$k]['scan'] = 1;
                }else{
                    $orderList[$key]['goodsInfo'][$k]['scan'] = 0;
                }
                
            }
        }
        return $orderList;
    }
    
    
    public function idToUserName($id){
        if(!empty(User::findOne($id)->username))
        return User::findOne($id)->username;
        return null;
    }
    
    
    /**
     * 装车获取订单信息
     * @param $logistics_sn  物流编号
     * @param $goods_sn  商品编号
     * @param $order_arr 打印checkbox选择id
     * @param $type 查询类型 1、路线 2、自己 3、挂起 4、改变状态 5、同城 6、完成  8、打印  9、大司机 10、待扫码
     * 靳健
     * */
    public function getOrderList($logistics_sn,$goods_sn,$type,$order_arr=null,$add_time = null,$where = null,$print = null){
        $modelLogisticsRoute = new LogisticsRoute();
        $condition = $this->getOrderCondition($logistics_sn,$goods_sn,$add_time);
        $route_id = $modelLogisticsRoute->getDriverRouteId(Yii::$app->user->id);
        $route_id = !empty($route_id)?$route_id:0;
        switch ($type){
            case 1:
            $searchTime = $this->getSearchTime($add_time,'add_time');
            $orderList = $this::find()->joinWith('driverGoods')->joinWith('routeInfo')->joinWith('orderTime')
            ->where(['and','logistics_order.logistics_route_id = '.$route_id,'order_state = '.Yii::$app->params['orderStateEmployee'],'abnormal = 2',$condition,$searchTime])
            ->orderBy('order_state,logistics_order.order_id desc')
            ->asArray()->all();
            $orderList = $this->buttonType($orderList,1);
			$orderList = $this->isTreatment($orderList,Yii::$app->params['orderStateEmployee']);
                return $orderList;
                break;
            case 2:
            $searchTime = $this->getSearchTime($add_time,'ruck_time');
            $orderList = $this::find()->joinWith('driverGoodsInfo')->joinWith('routeInfo')->joinWith('orderTime')
            ->where(['and',['>=','order_state',Yii::$app->params['orderStateDriver']],'abnormal = 2','logistics_order.logistics_route_id ='.$route_id,$condition,$searchTime])->orderBy('logistics_order.order_id desc')
            ->asArray()->all();
                return $orderList;
                break;
            case 3:
            $searchTime = $this->getSearchTime($add_time,'ruck_time');
            $orderList = $this::find()->joinWith('goodsInfo')->joinWith('routeInfo')->joinWith('orderTime')
            ->where(['and','order_state = '.Yii::$app->params['orderStateEmployee'],'logistics_order.logistics_route_id =  '.$route_id,'abnormal = 1',$condition,$searchTime])->orderBy('logistics_order.order_id desc')->asArray()->all();
                return $orderList;
                break;
            case 4:
                $orderList = $this::find()->joinWith('driverGoodsInfo')->joinWith('routeInfo')
                ->where(['and','logistics_order.logistics_route_id = '.$route_id,'order_state = '.Yii::$app->params['orderStateEmployee'],'abnormal = 2',$condition])->orderBy('order_state,logistics_order.order_id desc')
                ->asArray()->all();
                return $orderList;
                break;
            case 5:
                $searchTime = $this->getSearchTime($add_time,'ruck_time');
                $orderList = self::find()->joinWith('goodsInfo')->joinWith('routeInfo')->joinWith('orderTime')->joinWith('orderPrintLog')
                ->where(['and','logistics_order.same_city = 1',['or','state = 1','state = 2'],'freight_state = 2','order_state = '.Yii::$app->params['orderStateDelivery'],'abnormal = 2','logistics_order.driver_member_id = '.Yii::$app->user->id,$condition,$searchTime])
                ->orderBy('order_state,logistics_order.order_id desc')
                ->asArray();
                if ($print) {
                    if($print == '1'){
                        $orderList->andFilterWhere(['order_print_log.terminus'=>$print]);
                    } else if($print == '2') {
                        $orderList->andFilterWhere(['or', ['order_print_log.terminus'=>$print], 'order_print_log.terminus is null']);
                    }
                }
                $orderList = $orderList->all();
                $orderList = $this->stateButtonType($orderList,1);
                return $orderList;
                break;
            case 6:
                $orderList = $this::find()->joinWith('goodsInfo')->joinWith('routeInfo')
                ->where(['and','logistics_order.same_city = 1','order_state = '.Yii::$app->params['orderStateDelivery'],'abnormal = 2','logistics_order.driver_member_id = '.Yii::$app->user->id,$condition])
                ->orderBy('state,logistics_order.order_id desc')
                ->asArray()->all();
                $orderList = $this->stateButtonType($orderList,1);
                return $orderList;
                break;
            case 7:
                $where = empty($where)?' 1 ':$where;
                $searchTime = $this->getSearchTime($add_time,'ruck_time');
                $orderList = $this::find()->joinWith('goodsInfo')->joinWith('routeInfo')->joinWith('orderTime')
                ->where(['and','logistics_order.same_city = 1',['or',['and','collection = 2','shipping_type=3'],['and','state <> 1','state <> 2']],'order_state = '.Yii::$app->params['orderStateDelivery'],'abnormal = 2','logistics_order.driver_member_id = '.Yii::$app->user->id,$where,$condition,$searchTime])
                ->orderBy('order_state,logistics_order.order_id desc')
                ->asArray()->all();
                $orderList = $this->stateButtonType($orderList,1);
                return $orderList;
                break;
            case 8:
                $orderList = $this::find()->joinWith('goodsInfo')->joinWith('routeInfo')
                ->where(['and',['in','logistics_order.order_id',$order_arr],$condition])
                ->orderBy('logistics_order.order_id asc')
                ->asArray()->all();
                $orderList = $this->GetAreaInfo($orderList);
                return $orderList;
                break;
            case 9:
                $where = empty($where)?' 1 ':$where;
                $searchTime = $this->getSearchTime($add_time,'add_time');
                $orderList = $this::find()->joinWith('driverManagerGoods')->joinWith('routeInfo')
                ->where(['and','order_state = '.Yii::$app->params['orderStateEmployee'],'abnormal = 2',$where,$condition,$searchTime])
                ->orderBy('order_state,logistics_order.order_id desc')
                ->asArray()->all();
                return $orderList;
                break;
            case 10:
                $searchTime = $this->getSearchTime($add_time,'add_time');
                $orderList = $this::find()->joinWith('driverGoodsTen')->joinWith('routeInfo')->joinWith('orderTime')
                ->where(['and','logistics_order.logistics_route_id = '.$route_id,'order_state = '.Yii::$app->params['orderStateEmployee'],'abnormal = 2',$condition,$searchTime])
                ->orderBy('order_state,logistics_order.order_id desc')
                ->asArray()->all();
                return $orderList;
                break;
            default:
            $orderList = array();
                return $orderList;
                break;
        }
    }
    /**
     * 订单是否已经处理
     * 1,选中 2,不选中
     * 靳健
     */
    public function isTreatment($list,$state){
        foreach($list as $key => $value){
            $arr = Goods::find()->where(['and','order_id ='.$value['order_id'],'goods_state ='.$state])->asArray()->all();
            if(count($arr)==$value['goods_num']){
                $list[$key]['checkbox'] = 0;
            }else{
                $list[$key]['checkbox'] = 1;
            }
        }
        return $list;
    }
    /**
     * 判断装车列表显示按钮
     * $param $orderlist 
     * $param $type 1、装货  2、落地点 
     * buttonType 1未处理  2已处理 3同城 0无
     * 靳健
     */
    public function buttonType($orderList,$type=1){
        $goods_state_01 = $goods_state_02 = $goods_state_03 = 0;
        if($type==1){
            $goods_state_01 = Yii::$app->params['orderStateEmployee'];
            $goods_state_02 = Yii::$app->params['orderStateDriver'];
            $goods_state_03 = Yii::$app->params['orderStateDelivery'];
        }else if($type==2){
            $goods_state_01 = Yii::$app->params['orderStateDriver'];
            $goods_state_02 = 200;
        }else if($type==3){
            $goods_state_01 = Yii::$app->params['orderStateTerminus'];
            $goods_state_02 = Yii::$app->params['orderStateDelivery'];
        }
        if(!empty($orderList)){
            foreach($orderList as $key => $value){
               if($type==1){
                   $goods_name = 'driverGoods';//装车联合查询修改后 区分
               }else{
                   $goods_name = 'goodsInfo';
               }
               foreach($value[$goods_name] as $k => $v){
                   if($v['goods_state']==$goods_state_01){
                       $orderList[$key][$goods_name][$k]['buttonType'] = 1;
                   }else if($v['goods_state']==$goods_state_02){
                       $orderList[$key][$goods_name][$k]['buttonType'] = 2;
                   }else if($v['goods_state']==$goods_state_03){
                       $orderList[$key][$goods_name][$k]['buttonType'] = 2;
                   }else{
                       $orderList[$key][$goods_name][$k]['buttonType'] = 0;
                   }
               }
            }
        }
        return $orderList;
    }
    /**
     * 落地点列表查询
     * @param $logistics_sn  物流编号
     * @param $goods_sn  商品编号
     * @param $type 查询类型 1、路线 2、自己 3、挂起 4、状态修改 5、送货打印 6、已完成 7、多选打印 8、司机挂起 9、原返订单
     * 靳健
     */
    public function getTerminusList($logistics_sn,$goods_sn,$type,$order_arr = 0,$add_time = null,$driver_id = 0, $print=null){
        $condition = $this->getOrderCondition($logistics_sn,$goods_sn);
        $terminus_id = $this->getUserTerminusId(Yii::$app->user->id);
        switch ($type){
            case 1:
                $searchTime = $this->getSearchTime($add_time,'ruck_time');
                $orderList = $this::find()->joinWith('goodsInfo')->joinWith('routeInfo')->joinWith('orderTime')
                ->where(['and','terminus_id = '.$terminus_id,'order_state = '.Yii::$app->params['orderStateDriver'],'abnormal = 2','logistics_order.driver_member_id = '.$driver_id,$condition,$searchTime])->orderBy('order_state,logistics_order.order_id desc')
                ->asArray()->all();
                $orderList = $this->buttonType($orderList,2);
                return $orderList;
                break;
            case 2:
                $searchTime = $this->getSearchTime($add_time,'ruck_time');
                $orderList = $this::find()->joinWith('goodsInfo')->joinWith('routeInfo')->joinWith('orderTime')->joinWith('orderPrintLog')
                ->where(['and','order_state = '.Yii::$app->params['orderStateDelivery'],'abnormal = 2','return_logistics_sn = ""',['or','state = 1','state = 2'],'terminus_id = '.$terminus_id,$condition,$searchTime])
                ->asArray();
                if ($print) {
                    if($print == '1'){
                        $orderList->andFilterWhere(['order_print_log.terminus'=>$print]);
                    } else if($print == '2') {
                        $orderList->andFilterWhere(['or', ['order_print_log.terminus'=>$print], 'order_print_log.terminus is null']);
                    }
                }
                $orderList = $orderList->all();
                $orderList = $this->stateButtonType($orderList,1);
                return $orderList;
                break;
            case 3:
                $searchTime = $this->getSearchTime($add_time,'ruck_time');
                $orderList = $this::find()->joinWith('goodsInfo')->joinWith('routeInfo')->joinWith('orderTime')
                ->where(['and','order_state = '.Yii::$app->params['orderStateDriver'],'terminus_id = '.$terminus_id,'abnormal = 1',$condition,$searchTime])->orderBy('logistics_order.order_id desc')->asArray()->all();
                return $orderList;
                break;
            case 4:
                $orderList = $this::find()->joinWith('goodsInfo')->joinWith('routeInfo')
                ->where(['and','terminus_id = '.$terminus_id,'order_state = '.Yii::$app->params['orderStateDriver'],'abnormal = 2','logistics_order.driver_member_id ='.$driver_id,$condition])->orderBy('order_state,logistics_order.order_id desc')
                ->asArray()->all();
                return $orderList;
                break;
            case 5:
                $orderList = $this::find()->where(['in','order_id',$order_arr])->asArray()->all();
                return $orderList;
                break;
            case 6:
                $searchTime = $this->getSearchTime($add_time,'ruck_time');
                $orderList = $this::find()->joinWith('goodsInfo')->joinWith('routeInfo')->joinWith('orderTime')
                ->where(['and','order_state = '.Yii::$app->params['orderStateDelivery'],'abnormal = 2',['or',['and','state != 1','state != 2']],'terminus_id = '.$terminus_id,$condition,$searchTime])
                ->asArray()->all();
                return $orderList;
                break;
            case 7:
                $orderList = $this::find()->joinWith('terminusGoodsThree')->joinWith('routeInfo')
                ->where(['and',['in','logistics_order.order_id',$order_arr],$condition])
                ->asArray()->all();
                return $orderList;
                break;
            case 8:
                $searchTime = $this->getSearchTime($add_time,'ruck_time');
                $orderList = $this::find()->joinWith('goodsInfo')->joinWith('routeInfo')->joinWith('orderTime')
                ->where(['and','order_state = '.Yii::$app->params['orderStateEmployee'],'abnormal = 1','terminus_id = '.$terminus_id,$condition,$searchTime])
                ->asArray()->all();
                return $orderList;
                break;
            case 9:
                $searchTime = $this->getSearchTime($add_time,'ruck_time');
                $orderList = $this::find()->joinWith('goodsInfo')->joinWith('routeInfo')->joinWith('orderTime')
                ->where(['and','order_state = '.Yii::$app->params['orderStateDelivery'],'abnormal = 2','return_logistics_sn <> ""',['or','state = 1','state = 2'],'terminus_id = '.$terminus_id,$condition,$searchTime])
                ->asArray()->all();
                return $orderList;
                break;
            default:
                $orderList = array();
                return $orderList;
                break;
        }
    }
    /**
     * 即将到达车辆
     * 靳健
     */
    public function getArriveCar(){
        $logisticsCar = new LogisticsCar();
        $terminus_id = $this->getUserTerminusId(Yii::$app->user->id);
        $driver_arr = $this::find()->where(['order_state'=>50,'terminus_id'=>$terminus_id,'abnormal'=>2])->groupBy('driver_member_id')->asArray()->all();
        $car = array();
        foreach($driver_arr as $key => $value){
            $car[$key] = $logisticsCar->getCarInfo($value['driver_member_id']);
            $car[$key]['ruck_time'] = date('Y-m-d',OrderTime::findOne($value['order_id'])->ruck_time);
            $car[$key]['driver_name'] = User::findOne($value['driver_member_id'])->username;
            $car[$key]['route'] = LogisticsRoute::findOne($value['logistics_route_id'])->logistics_route_name;
        }
        return $car;
    }
    /**
     * 送货员订单列表查询
     * @param $logistics_sn  物流编号
     * @param $goods_sn  商品编号
     * @param $type 查询类型 1、路线 2、自己 3、挂起 4、状态修改
     * 靳健
     */
    public function getSendList($logistics_sn,$goods_sn,$type){
        $condition = $this->getOrderCondition($logistics_sn,$goods_sn);
        $terminus_id = $this->getUserTerminusId(Yii::$app->user->id);
        switch ($type){
            case 1:
                $orderList = $this::find()->joinWith('goodsInfo')->joinWith('routeInfo')
                ->where(['and','terminus_id = '.$terminus_id,'order_state = '.Yii::$app->params['orderStateTerminus'],'abnormal = 2',$condition])->orderBy('order_state,logistics_order.order_id desc')
                ->asArray()->all();
                $orderList = $this->buttonType($orderList,3);
                return $orderList;
                break;
            case 2:
                $orderList = $this::find()->joinWith('sendGoodsInfo')->joinWith('routeInfo')
                ->where(['and','order_state = '.Yii::$app->params['orderStateDelivery'],'abnormal = 2','terminus_id = '.$terminus_id,$condition])
                ->asArray()->all();
                return $orderList;
                break;
            case 3:
                $orderList = $this::find()->joinWith('goodsInfo')->joinWith('routeInfo')
                ->where(['and','order_state = '.Yii::$app->params['orderStateTerminus'],'terminus_id = '.$terminus_id,'abnormal = 1',$condition])->orderBy('logistics_order.order_id desc')->asArray()->all();
                return $orderList;
                break;
            case 4:
                $orderList = $this::find()->joinWith('sendGoodsInfo')->joinWith('routeInfo')
                ->where(['and','terminus_id = '.$terminus_id,'order_state = '.Yii::$app->params['orderStateTerminus'],'abnormal = 2',$condition])->orderBy('order_state,logistics_order.order_id desc')
                ->asArray()->all();
                return $orderList;
                break;
            default:
                $orderList = array();
                return $orderList;
                break;
        }
        
    }
    /**
     * 二级状态判断按钮
     * @param $orderList
     * 靳健
     */
    public function stateButtonType($orderList,$return=0){
        foreach($orderList as $key=>$value){
            //不代收已付不显示按钮
          if($value['collection']==2&&$value['shipping_type']==3){
              $orderList[$key]['stateButtonType'] = 0;
          }else{
            if($value['state']==1||$value['state']==2){
                $orderList[$key]['stateButtonType'] = 1;
            }else{
                $orderList[$key]['stateButtonType'] = 2;
            }
          }
          //判断是否满足原返条件
          if($return==1){
              $orderList[$key]['returnButton'] = $this->chooseReturn($value['state']);
          }
        }
        return $orderList;
    }
    /**
     * 订单是否原返
     * @param $state
     * 靳健
     */
    public function chooseReturn($state){
        if(!($state & 4)){
            return 1;
        }
            return 0;
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
                $where = ['between','logistics_order.add_time',$add_time['start'],$add_time['end']];
                return $where;
            }else if($search_name == 'ruck_time'){
                $where = ['between','order_time.ruck_time',$add_time['start'],$add_time['end']];
                return $where;
            }
        }
        return array();
    } 
    /**
     * 订单查询条件
     * @param unknown $logistics_sn
     * @param unknown $goods_sn
     * 靳健;
     */
    public function getOrderCondition($logistics_sn,$goods_sn){
// modified by fenghuan
// $condition = array();
        $condition = '';
        if(!empty($logistics_sn)){
// $condition['logistics_sn'] = $logistics_sn;
            $condition = "logistics_sn like '%".$logistics_sn."%'";

        }
        if(!empty($goods_sn)){
// $condition['logistics_order.order_id'] = $this::getOrderId($goods_sn);
            $condition .= "logistics_order.order_id = '{$this::getOrderId($goods_sn)}'";
        }
        return $condition;
    }
    /**
     * 获取落地点用户落地点ID
     * @param 用户id  $userId
     * 靳健
     */
    public function getUserTerminusId($userId){
        $terminusUser = new TerminusUser();
        return $terminusUser::find()->where(['user_id'=>$userId])->one()->terminus_id;
    }
    
    /**
     * 装车打印
     * $param $loading 1,装车打印
     * 靳健
     */
    public function orderPrint($order_sn,$goods_sn,$order_arr,$loading){
        $list = $this->getOrderList($order_sn,$goods_sn,8,$order_arr);
        if($loading){
            $list = $this->hasError($list);
        }
        return $list;
    }
    /**
     * 封车90分钟内 小码单打印
     * 靳健
     */
    public function getSmallMemo(){
        $data = $this::find()->joinWith('orderTime')->where([
                'and',
                'same_city = 1',
                ['or','state = 1','state = 2'],
                'freight_state = 2',
                'order_state = '.Yii::$app->params['orderStateDelivery'],
                'abnormal = 2',
                'driver_member_id = '.Yii::$app->user->id,
               ['>=','order_time.ruck_time',time()-60*60],
        ])->asArray()->all();
        return $data;
    }
    
    /**
     * 判断订单是否有异常
     * @param $list 订单列表   error 1异常
     * 靳健
     */
    public function hasError($list){
        foreach($list as $key => $value){//判断是否有挂起(异常)订单
            $all = Goods::find()->groupBy(['goods_state'])->where(['order_id'=>$value['order_id']])->asArray()->all();
            if(count($all)!=1){
                $list[$key]['error'] = 1;
            }
        }
        return $list;
    }
    /**
     * 修改提交订单状态
     * 靳健
     */
    public function ajaxOrderStateDriverEdit($order_arr){
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        if($order_arr){
            foreach($order_arr as $v){
                if(!$this->orderStateDriverEdit($v)){
                    return false;
                }else{
                    $this->isDriverManager($role,$v);
                }
            }
        }else{
            if(!$this->orderStateDriverEdit($order_arr)){
                return false;
            }else{
                    $this->isDriverManager($role,$order_arr);
                }
        }
        return true;
    }
    /**
     * 判断是否为大司机
     * 靳健
     */
    
    public function isDriverManager($role,$order_id){
        $goods = new Goods();
        if($role==Yii::$app->params['roleDriverManagerCityWide']||$role==Yii::$app->params['roleDriverManager']){
            $driver_member_id = $goods::find()->where(['and','order_id ='.$order_id,['<>','driver_member_id',0]])->asArray()->all()[0]['driver_member_id'];
            $order_obj = $this::findOne($order_id);
            $order_obj->driver_member_id = $driver_member_id;
            $re = $order_obj->save();
            if(!$re){
                return false;
            }
        }
    }
    /**
     * 修改提交订单状态
     * 靳健
     */
    public function ajaxOrderStateEdit($order_arr){
        if($order_arr){
            foreach($order_arr as $v){
                if(!$this->orderStateEdit($v)){
                    return false;
                }
            }
        }else{
            if(!$this->orderStateEdit($order_arr)){
                return false;
            }
        }
        return true;
    }
    /**
     * 删去商品没有处理的订单
     * @param $list
     * @param $goods_state 
     * 靳健
     */
    public function removeOrder($list,$goods_state){
        $goods = new Goods();
        $array = array();
        foreach($list as $key=>$value){
            $arr = $goods::find()->where(['and','order_id ='.$value['order_id'],'goods_state ='.$goods_state])->asArray()->all();
            if(count($arr)!=$value['goods_num']){
                $array[] = $list[$key];
            }
        }
        return $array;
    }
    /**
     * 订单数组添加价格
     * 靳健
     */
    public function getGoodsPrice($list,$type=null){
        if(empty($type)){
            foreach($list as $key => $value){
                $list[$key]['all_amount'] = 0;
            }
            return $list;
        }else if($type=='driver'){
            $price  =  new IncomeDriver();
        }else if($type=='terminus'){
            $price  =  new IncomeTerminus();
        }else if($type=='return'){
            $price  =  new ReturnIncomeDealer();
        }else if($type=='member'){
            foreach($list as $key => $value){
                $list[$key]['all_amount'] = $value['goods_price'];
            }
            return $list;
        }
        foreach($list as $key => $value){
            $list[$key]['all_amount'] = $price->_getAmount($value)['all_amount'];
        }
        return $list;
    }
    /**
     * 订单标签打印
     * @param $order_id 
     * 靳健
	 * 增加user关联表,显示小号,2017-09-07 xiaoyu
     */
    public function tagOrderPrint($order_id){
        $area = new Area();
		$user = new User();
		$remark = new OrderRemark();
        $data = $this::find()->joinWith('routeInfo')->where(['logistics_order.order_id'=>$order_id])->asArray()->all();
        $data[0]['province'] = $area::getAreaNameById($data[0]['receiving_provinceid']);
        $data[0]['city'] = $area::getAreaNameById($data[0]['receiving_cityid']);
        $data[0]['district'] = $area::getAreaNameById($data[0]['receiving_areaid']);
        $data[0]['from_city'] = $area::getAreaNameById($data[0]['member_cityid']);
		$data[0]['small_num'] = $user->getMemberInfo(array('id'=>$data[0]['member_id']))->small_num;
        $data[0]['employee_name'] = $user->getMemberInfo(array('id'=>$data[0]['employee_id']))->user_truename;
		$data[0]['remark'] = empty($remark::findOne($order_id))?'':$remark::findOne($order_id)->edit_content;
		return $data;
    }
    /**
     * 批量修改落地点状态修改
     * 靳健
     */
    public function orderStateEdit($order_id){
        $userBalance = new UserBalance();
        $data = $this::findOne(['order_id'=>$order_id, 'abnormal'=>2]);//abnormal:1挂起,2未挂起
        if($data->order_state==50){//判断落地单入库
            if($this->isAbnormal($data)){//判断是否异常
                $r1 = $this->setOrderState($data);//修改订单状态
                if(!$r1){
                    return false;
                }
            }else{
                $r2 = $this->orderAbnormal($data);//挂起
                if(!$r2){
                    return false;
                }
            }
        }
        return true;
    }
    /**
     * 批量修改封车状态修改
     * 靳健
     */
    public function orderStateDriverEdit($order_id){
        $userBalance = new UserBalance();
        $data = $this::findOne(['order_id'=>$order_id, 'abnormal'=>2]);//abnormal:1挂起,2未挂起
        if($data->order_state==10){//待封车状态
            if($this->isAbnormal($data)){//判断是否异常
                $r1 = $this->setOrderState($data);//修改订单状态
                $r2 = $userBalance->addUserBalanceInfo($order_id);//余额处理
                if(!$r1||!$r2){
                    return false;
                }
            }else{
                $r2 = $this->orderAbnormal($data);//挂起
                if(!$r2){
                    return false;
                }
            }
        }
        return true;
    }
    /**
     * 派送状态修改
     * @param unknown $order_sn
     * @param unknown $goods_sn
     */
    public function sendPrint($order_sn,$goods_sn){
        $list = $this->getSendList($order_sn,$goods_sn,4);
        foreach($list as $v){
            if(!$this->orderStateEdit($v['order_id'])){
                return false;
            }
        }
        return true;
    }
    /**
     * 获取订单id
     * @param $goods_sn 商品单号
     * 靳健
     * */
    public function getOrderId($goods_sn){
        $goods = new Goods();
        $order_id = $goods->find()->select('order_id')->where('goods_sn = "'.$goods_sn.'"')->asArray()->one()['order_id'];
        return $order_id;
    }
    /**
     * 获取订单id
     * @param $goods_id 商品id
     * 靳健
     * */
    public function gdGetOrderId($goods_id){
        $goods = new Goods();
        $order_id = $goods->find()->select('order_id')->where('goods_id = "'.$goods_id.'"')->asArray()->one()['order_id'];
        return $order_id;
    }
    /**
     * 待处理完成
     * @param $order_id 订单id
     * 靳健
     */
    public function ajaxOrderEdit($order_arr){
        foreach($order_arr as $k => $v){
            $data = $this::findOne($v);
            if($data->order_state==70){//判断为待收货状态
                if(!$this->setOrderState($data)){
                    return false;
                }
            }
        }
        
        return true;
    }
    /**
     * 修改代收款
     * @param  $order_id 订单id
     * @param  $now_goods_price 目前货值
     * @param  $goods_price 修改后货值
     */
    public function balanceEdit($model,$now_goods_price,$post){
        if($post['collection']==2){//不代收货值为零
            $post['goods_price'] = 0;
        }
        
        if($now_goods_price != intval($post['goods_price'])){//判断是否需要修改货值
            $re2 = $this->orderOtherPriceEdit($model,$post);
            $result = $this->computeAmount($model,$post);
            $orderPriceEditLog = new OrderPriceEditLog();
            $re1 = $orderPriceEditLog->addOrderPriceEditLog($result,$now_goods_price,intval($post['goods_price']),'修改');
            if(!$re1||!$re2){
                return false;
            }
            return $this->logHandle($result,'修改');
        }else{
            return $this->orderOtherPriceEdit($model,$post);
        }
    }
    /**
     * 删除商品 代收款修改
     * @param  $order_id 订单id
     * @param  $now_goods_price 目前货值
     * @param  $goods_price 修改后货值
     */
    public function balanceDel($model){
        if($model->goods_price!=0){//代收款为零不记录log
            $orderPriceEditLog = new OrderPriceEditLog();
            $params = ['model'=>$model,'before_amount'=>$model->goods_price,'after_amount'=>0];
            $re1 = $orderPriceEditLog->addOrderPriceEditLog($params,'删除');
            if(!$re1){
                return false;
            }
        }
        return $this->orderDelete($model);
    }
    /**
     * 计算余额修改
     * @param unknown $order_id
     * @param unknown $post
     */
    
    public function computeAmount($model,$post){
        $modelLogisticsOrder = new LogisticsOrder();
        $modelPayDealer = new PayDealer();
        $modelBefore = $modelLogisticsOrder->findOne($model->order_id);
        $before_amount = $modelPayDealer->_getAmount(ArrayHelper::toArray($modelBefore), 1)['all_amount'];
        //修改订单价钱
        $this->orderPriceEdit($model,$post);
        $modelAfter = $modelLogisticsOrder->findOne($model->order_id);
        $after_amount = $modelPayDealer->_getAmount(ArrayHelper::toArray($modelAfter), 1)['all_amount'];
        $result['before_amount'] = $before_amount;
        $result['after_amount'] = $after_amount;
        $result['amount'] = $after_amount - $before_amount;
        $result['model'] = $model;
        return $result;
    }
    
    /**
     * 靳健
     * 代收款修改
     * @param unknown $order_id
     * @param unknown $goods_price
     */
    public function orderPriceEdit($model,$post){
        $model->goods_price = intval($post['goods_price']);
        if($post['collection']==1){
            $model->collection = $post['collection'];
        }
        return $model->save();
    }
    /**
     * 靳健
     * 修改除了代收款外其他信息
     * @param unknown $model
     * @param unknown $post
     */
    public function orderOtherPriceEdit($model,$post){
        if($model->getOldAttribute('logistics_route_id') != $post['logistics_route_id']){
            $res = $this->goodsInfoUpdate($model,$post);
            if(!$res){
                return false;
            }
        }
        $model->terminus_id = empty($post['terminus_id'])?0:$post['terminus_id'];
        $model->freight = intval($post['freight']);
        $model->make_from_price = intval($post['make_from_price']);
        $model->collection = $post['collection'];
        $model->abnormal = $post['abnormal'];
        return $model->save();
    }
    
    /**
     * 订单修改更新商品表信息
     */
    public function goodsInfoUpdate($model,$post){
        $modelGoods = new Goods();
        try{
            $tr = Yii::$app->db->beginTransaction();
            if($model->order_state == 10){//未封车删除商品
                $modelGoods->delGoodsByOrderId($model->order_id);
                $goodsSn = $modelGoods->getGoodsSn($post['logistics_route_id']);//生成货号
                $modelGoods->addGoodsInfo($model->order_id, $model->goods_sn,$model->goods_num);
                $model->receiving_cityid = $post['receiving_cityid'];
                $model->logistics_route_id = $post['logistics_route_id'];
                $model->same_city = LogisticsRoute::findOne($post['logistics_route_id'])->same_city;
                $result_order = $model->save();
                if(!$result_order){
                    return false;
                }
            }else if($model->order_state>10){//封车后修改商品对应司机信息
                $goodsDriver = $this->getRouteDriverInfo($post['logistics_route_id']);
                $model->receiving_cityid = $post['receiving_cityid'];
                $model->logistics_route_id = $post['logistics_route_id'];
                $same_city = LogisticsRoute::findOne($post['logistics_route_id'])->same_city;
                if($same_city==1){
                    $model->order_state = 70;
                }else{
                    $model->order_state = 50;
                }
                $model->same_city = $same_city;
                $model->driver_member_id = $goodsDriver['driver_id'];
                $result_order = $model->save();
                if(!$result_order){
                    return false;
                }
                $goods = Goods::find()->where(['order_id'=>$model->order_id])->all();
                foreach($goods as $key => $value){
                    $value->car_id = $goodsDriver['car_id'];
                    $value->driver_member_id = $goodsDriver['driver_id'];
                    $result_goods = $value->save();
                    if(!$result_goods){
                        return false;
                    }
                }
            }
            $tr->commit();
            return true;
        }catch(Exception $e){
            $tr->rollBack();
            return false;
        }
    }
    
    /**
     * 靳健
     * 获取路线对应司机信息
     * @param unknown $route_id
     */
    public function getRouteDriverInfo($route_id){
        $carInfo = LogisticsCar::find()->where(['logistics_route_id'=>$route_id])->asArray()->all();
        $driverInfo = Driver::findOne(['logistics_car_id'=>$carInfo[0]['logistics_car_id']]);
        return ['car_id'=>$carInfo[0]['logistics_car_id'],'driver_id'=>$driverInfo->member_id];
    }
    
    public function orderDelete($model){
        $delete = new LogisticsOrderDelete();
        foreach($model as $key => $value){
            if($delete->hasProperty($key,true)){
                $delete->$key = $value;
            }
    
        }
        //如果有订单编号 友件网状态修改
//         if(!empty($model->order_sn)){
//             if(!is_numeric($model->order_sn)){
//                $order_sn = unserialize($model->order_sn);
//             }else{
//                 $order_sn = $model->order_sn;
//             }
//             //调用接口修改友件订单状态
//             $youjian = $this->editYoujianOrderStateToTen(array('orderSn'=>$order_sn));
//             if(!$youjian){
//                 return false;
//             }
//         }
        $delete->delete_member_id = Yii::$app->user->id;
        if($delete->save()){
            return $model->delete();
        }
        return false;
    }
    /**
     * 账号信息修改买断处理
     * @param unknown $order_id
     * @param unknown $post
     */
    public function buyOutHandle(&$model){
        if ($model->getOldAttribute('collection') != $model->collection) {
            $buyOut = new BuyOut();
            if($model->getOldAttribute('collection') == 1) {
                $buyOut -> deleteByOrderId($model->order_id);
                $model -> state = 2;
                $model -> goods_price = 0;//不买断代收款改为0
            }
            if($model->collection == 1) {
                //增加买断信息
                $result = $buyOut->addBuyOutInfo($model);
                if(!$result) {
                    return false;
                }
            }
            return true;
        }
        return true;
    }
    
    /**
     * 靳健
     * 订单添加到已提现表withdrawal_order
     */
    public function orderToWithdrawal(){
        
        $list = $this->find()->where(['member_id'=>Yii::$app->user->id,'goods_price_state'=>1])->asArray()->all();
        try{
            foreach($list as $key => $value){
                $withdrawalOrder = new WithdrawalOrder();
                if(!$withdrawalOrder->addWithdrawalOrder($value['logistics_sn'],1)){
                    return false;
                }
            }
        }catch(Exception $e){
            return false;
        }
        return true;
    }
    /**
     * 订单提现后状态修改
     * @param unknown $order_sn
     */
    public function goodsPriceStateToFive($order_sn){
        $order = $this::findOne(['logistics_sn'=>$order_sn]);
        $order->goods_price_state = $order->goods_price_state|4;
        return $order->save();
    }
    
    /**
     * 靳健
     * 用户提现方式改变,修改原订单状态
     */
    public function orderWithdrawalPriceState(){
        $list = $this->find()->where(['member_id'=>Yii::$app->user->id,'goods_price_state'=>1])->asArray()->all();
        try{
            foreach($list as $key => $value){
                $order = $this::findOne($value['order_id']);
                $order->goods_price_state = $order->goods_price_state | 4;
                if(!$order->save()){
                    return false;
                }
            }
        }catch(Exception $e){
            return false;
        }
        return true;
    }
    
    /**
     * 其他log添加
     * @param unknown $result
     * @param string $content
     */
    public function logHandle($params,$content = '修改'){
        $userBalance = new UserBalance();
        $balanceLog = new BalanceLog();
    
        $re2 = $balanceLog->editBalancelLog($params,$content);
        $re3 = $userBalance->editUserAmount($params);
        if(!$re2||!$re3){
            return false;
        }
        return true;
    }
    /**
     * 更新物流单表
     * 朱鹏飞
     * @param unknown $upda
     * @param array $condition
     */
    public function upOrder($upda = array(), $condition = array()){ 
    	try {
    		$this::updateAll($upda, $condition);
    	}catch (Exception $e ) {
    		return false;
    	}
        return true;
    }
    /**
     * 修改订单状态
     * 朱鹏飞
     * @param unknown $data
     * @param unknown $orderState
     */
    private function _upOrderState($data,$orderState){
    	$data->order_state = $orderState;
    	return $data->save();
    }
    
    /**
     * 朱鹏飞
     * 订单状态相关操作
     * @param unknown $orderId
     */
    public function setOrderState($data){
    	$role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
	    switch ($role)
	    {
	    	case '开单员':
	    		if($data->order_state == Yii::$app->params['orderStateMember'])
	    		{
	    			return $this->_setOrderState_1($data, Yii::$app->params['orderStateEmployee'], $role);
	    		}
	    		return false;
	    	break;
	    	case '司机':
	    		if($data->order_state == Yii::$app->params['orderStateEmployee'])//状态为10
	    		{
	    			$r1 = $this->_setOrderState_1($data, Yii::$app->params['orderStateDriver'], $role);//修改为50
	    			$r2 = $this->upOrder(array('driver_member_id'=>Yii::$app->user->id), array('order_id'=>$data->order_id));
	    			if($r1 && $r2){
	    				return true;
	    			}
	    			return false;
	    		}
	    		if($data->order_state == Yii::$app->params['orderStateDelivery'])//状态为70
	    		{
	    			return $this->collection($data);//修改二级订单状态
	    		}
	    		return false;
	    	break;  
	    	case '落地点':
	    		if($data->order_state == Yii::$app->params['orderStateDriver'])//状态为50
	    		{
	    			return $this->_setOrderState_1($data, Yii::$app->params['orderStateDelivery'], $role);
	    		}
	    		if($data->order_state == Yii::$app->params['orderStateDelivery'])//状态为70
	    		{
	    			return $this->collection($data);//修改二级订单状态
	    		}
	    		return false;
	    	break;
	    	case '司机领队':
	    		if($data->order_state == Yii::$app->params['orderStateEmployee'])//状态为10
	    		{
	    			$r1 = $this->_setOrderState_1($data, Yii::$app->params['orderStateDriver'], $role);//修改为50
	    			$r2 = $this->upOrder(array('driver_member_id'=>Yii::$app->user->id), array('order_id'=>$data->order_id));
	    			if($r1 && $r2){
	    				return true;
	    			}
	    			return false;
	    		}
	    		if($data->order_state == Yii::$app->params['orderStateDelivery'])//状态为70
	    		{
	    			return $this->collection($data);//修改二级订单状态
	    		}
	    		return false;

            // 财务修改订单状态
            case Yii::$app->params['roleTeller']:
            case Yii::$app->params['roleTellerIncomeLeader']:
            case Yii::$app->params['roleTellerIncome']:
               if($data->order_state == Yii::$app->params['orderStateDriver'])//状态为50
                {
                    if(!$this->_setOrderState_1($data, Yii::$app->params['orderStateDelivery'], $role)) {
                        return false;
                    }
                }
                if(!($data->state & Yii::$app->params['orderReceived']))//状态为70
                {
                    //修改二级订单状态
                    if(!$this->collection($data)){
                        return false;
                    }
                }
                return true;
    		case Yii::$app->params['roleDriverManagerCityWide']://司机领队同城
    		    if($data->order_state == Yii::$app->params['orderStateEmployee'])//状态为10
    		    {
    		        $r1 = $this->_setOrderState_1($data, Yii::$app->params['orderStateDriver'], $role);//修改为50
    		        $r2 = $this->upOrder(array('driver_member_id'=>Yii::$app->user->id), array('order_id'=>$data->order_id));
    		        if($r1 && $r2){
    		            return true;
    		        }
    		        return false;
    		    }
    		    if($data->order_state == Yii::$app->params['orderStateDelivery'])//状态为70
    		    {
    		        return $this->collection($data);//修改二级订单状态
    		    }
    		    return false;
    		    break;
	    	default:return false;
		}
    }
    
    /**
     * 判断是否代收
     * @param Object $data 订单对像
     * @return true false
     */
    public function collection($data)
    {
        // 修改友件订单状态
        if($data->order_sn) {
            $orderSn = is_numeric($data->order_sn) ? $data->order_sn : unserialize($data->order_sn);
            if(!$this->editYoujianOrder('/mobile/index.php?act=login&op=update_order_state', array('orderSn'=>$orderSn))) {
                return false;
            }
        }
    	$r1 = $this->_stateSwitch($data);
    	$modelOrderTime = new OrderTime();
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
    	switch ($data->state)
    	{
    		case Yii::$app->params['orderBuyOut']:
    			return $this->_stateEdit($data, $data->state|Yii::$app->params['orderReceived']);
    		break;
    		case Yii::$app->params['orderNotBuyOut']:
    			return $this->_stateEdit($data, $data->state|Yii::$app->params['orderReceived']);
    		break;
    		default:
    		return false;
    	}
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
     * 改变订单状态
     * 朱鹏飞
     * @param unknown $data 订单信息对像
     * @param unknown $orderState 需要改变成多少状态
     *  @param unknown $role 权限
     * @return bool true 成功 false 失败
     */
    private  function _setOrderState_1($data, $orderState, $role)
    {
    	$model= new OrderTime();
    	$modelLog = new Log();
    	$r1 = $model->orderTimeswitch($orderState, $data);//更新时间
    	$r2 = $this->_cityWideOrder($data,$orderState);//修改订单状态
    	$r3 = $modelLog->addLogInfo($data, $orderState);//增加log日记
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
    private function _cityWideOrder($data, $orderState){
    	if(($data->same_city==1) && ($data->order_state ==Yii::$app->params['orderStateEmployee']))//订单状态为10时，判断是否同城
    	{
    		return $this->_upOrderState($data,Yii::$app->params['orderStateDelivery']);//同城订单状态改为70
    	}else{
    		return $this->_upOrderState($data,$orderState);//修改订单状态
    	}
    }
    /**
     * 朱鹏飞
     * 判断订单是否异常
     * @param Object $data
     * @return true 成功 false 失败
     */
    public  function isAbnormal($data){
    	$modelGoods = new Goods();
    	$goodsData = $modelGoods::find()->where(['order_id'=>$data->order_id])->select('goods_state')->distinct()->count();
    	if($goodsData>1){
    		return false;
    	}
    	$goodsData = $modelGoods::find()->where(['order_id'=>$data->order_id, 'goods_state' => 200])->asArray()->all();
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
    public function orderAbnormal($data){
    	$data->abnormal=1;
    	return $data->save();
    }
	    /**
     * 财务开单元收款确认
     * xiaoyu
	 * 2017-07-29
     */
    public function FinanceConfirmCollectionSum($data){
        $model = new OrderTime();
            $data->freight_state = $data->freight_state==6?5:$data->freight_state;
            $data->freight_state = $data->freight_state==2?1:$data->freight_state;
            if(!$model->orderTimeswitch('price',$data)){
                return false;
            }
            $data->freight_state = $data->freight_state==2?1:$data->freight_state;
            $data->freight_state = $data->freight_state==6?5:$data->freight_state;
            if(!$model->orderTimeswitch('freight',$data)){
                return false;
            }

        return $data->save();
    }
    /**
     * 获取商品司机车牌号
     * 靳健
     */
    public function carNumber($driver_id){
        $logistics_car_id = Driver::find()->where(['member_id'=>$driver_id])->one()->logistics_car_id;
        return LogisticsCar::findOne($logistics_car_id)->car_number;
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
			  $orderList[$i]['district']= Area::getAreaNameById($orderList[$i]['receiving_areaid']);
			}
		}
		return $orderList;
	}
	/**
	*  用户表打印
	*  小雨
	**/
	public function UserPrint($where){
        $orderList = $this::find()->joinWith('goodsInfo')->where($where)->asArray()->all();
        return $orderList;
	}
	/**
	 * 修改youjian网订单状态 70 to 10
	 * @param array $params
	 */
	public function editYoujianOrderStateToTen($params=array())
	{
	    //$url = "www.youjian8.com/mobile/index.php?act=login&op=update_order_status_a";
	    $url = "www.youjian8.com/mobile/index.php?act=login&op=update_order_status_a";
	    if (empty($params)) {
	        return false;
	    }
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
	    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	    //return curl_exec($ch);
	    $res = json_decode(curl_exec($ch),true);
	    if($res['code'] == '200')
	    {
	        curl_close($ch);
	        return true;
	    }else{
	        curl_close($ch);
	        return false;
	    }
	}
	
	/**
	 * 朱鹏飞
	 * 修改youjian网订单状态
	 * @param array $params
	 */
	public function editYoujianOrderState($params=array())
	{
		$url = "www.youjian8.com/mobile/index.php?act=login&op=edit_order_state";
		if (empty($params)) {
			return false;
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		//return curl_exec($ch);
		$res = json_decode(curl_exec($ch),true);
		if($res['code'] == '200')
		{
			curl_close($ch);
			return true;
		}else{
			if($res['code'] == '204')
			{
				curl_close($ch);
				return '204';
			}
			if($res['code'] == '205')
			{
				curl_close($ch);
				return '205';
			}
		curl_close($ch);
		return '206';
		}
	}
	
	/**
	 * 修改youjian网订单状态
	 * @param array $params
	 */
	public function editYoujianOrder($url, $params=array())
	{
	    $baseUrl = "www.youjian8.com/";
	    if (empty($url) || empty($params)) {
	        return false;
	    }
	    $url = $baseUrl.$url;
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
	    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	    //return curl_exec($ch);
	    $res = json_decode(curl_exec($ch),true);
// 	    if($res['code'] == '200')
// 	    {
	        curl_close($ch);
	        return true;
// 	    }
// 	    curl_close($ch);
// 	    return false;
	}
	
	/**
	 * 朱鹏飞
	 * 反回当天订单数量与同城订单数量
	 * @return array() 
	 */
	public function getOrderNum()
	{
		$today = strtotime(date('Y-m-d', time()));
		$data = self::find()
		->select('same_city, count(*) as orderNum,sum(goods_price) as todayPrice')
// 		->where(['>','add_time',$today])
		->groupBy(['same_city'])
		->indexBy('same_city')
		->asArray()
		->all();
		return $data;
	}
	public function getOrderNumTwo(){
	    $today = strtotime(date('Y-m-d', time()));
	    $data = self::find()
	    ->select('count(*) as totalPrice')
	    ->where(['and','same_city = 1',['>','goods_price',0]])
	    ->asArray()
	    ->one();
	    return $data;
	}
	//开单统计
	public function getEmployeeOrderNum($dataProvider){
	    $order_num = $dataProvider->query->count();
	    return $order_num;
	}
	public function getEmployeeGoodsNum($dataProvider){
	    $goods_num = $dataProvider->query->sum('goods_num');
	    return $goods_num;
	}
	public function getEmployeePrice($dataProvider){
	     $price = $dataProvider->query->sum('goods_price');
	     return $price;
	}
	public function getEmployeePriceCount($dataProvider){
	    $price_count = $dataProvider->query->andWhere(['>','goods_price',0])->count();
	    return $price_count;
	}
	public function getEmployeeSameCityOrder($dataProvider){
	    $same_city_order = $dataProvider->query->andWhere(['logistics_order.same_city'=>1])->count();
	    return $same_city_order;
	}
	public function getEmployeeSameCityGoods($dataProvider){
	    $same_city_goods = $dataProvider->query->andWhere(['logistics_order.same_city'=>1])->sum('goods_num');
	    return $same_city_goods;
	}
	public function getEmployeeSameCityPrice($dataProvider){
	    $same_city_price = $dataProvider->query->andWhere(['logistics_order.same_city' => 1])->sum('goods_price');
	    return $same_city_price;
	}
	public function getEmployeeSameCityPriceCount($dataProvider){
	    $same_city_price_count = $dataProvider->query->andWhere(['and','logistics_order.same_city = 1',['>','goods_price',0]])->count();
	    return $same_city_price_count;
	}
	

    /**
     * 计算总额
     * @param unknown $data
     * @return number[]|unknown[]
     */
    public function _getAmount($data) {
        $return = array(
            'all_amount' => 0,
            'finished_amount' => 0,
            'unfinished_amount' => 0,
        );
        
        $return['all_amount'] += $data['freight'];
        $return['all_amount'] += $data['make_from_price'];
        $return['all_amount'] -=  $data['shipping_sale'];
        // 代收时 收货款
        if ($data['freight_state'] & 1) {
            $return['finished_amount'] = $return['all_amount'];
        } else if($data['freight_state'] & 2) {
            $return['unfinished_amount'] = $return['all_amount'];
        }
        return $return;
    }
		/**
	*  统计收货人订单返货总数量
	*  2017-09-21
	*  小雨
	* type 类型 send为发货，rece为收货
	*  phone收货人电话
	**/
	public function GetCount_Return_Rece($phone=array(),$type='rece'){
	   $key = $this->OutPutCategory($type);
	   $phone = $this->OutPutPhone($phone,$key);
	   return $this->find()->where(['not', ['return_logistics_sn' => '']])->andwhere("$key =:phone",[':phone'=>$phone])->count();
	 
	}
	/**
	*  统计收货人订单总数量
	*  2017-09-21
	*  小雨
	* type 类型 send为发货，rece为收货
	*  phone收货人电话
	**/
	public function GetCount_Rece($phone=array(),$type='rece'){
	   $key   = $this->OutPutCategory($type);
	   $phone = $this->OutPutPhone($phone,$key);
	   return $this->find()->where("$key =:phone",[':phone'=>$phone])->count();
	}
	private function OutPutCategory($type){
	    $keyword = 'receiving_phone';
	   if($type == 'send')
	   {
	      $keyword = 'member_id';
	   }
	  /* elseif($type == 'rece'){
	      $keyword = 'receiving_phone';
	   }*/
	   return $keyword;
	}
	private function OutPutPhone($phone,$key){
	   $_phone = $phone[0];
	   if($key == 'member_id'){
 	     $_phone = $phone[1];
 	   }
	  /* elseif($key == 'receiving_phone'){
 	     $phone = $phone[0];
 	   }*/

	   return $_phone;
	}

    /**
     * @Author:Fenghuan
     * @param $field
     * @param string $phone
     * @return string
     */
    public function statisLogisticsOrder($field, $phone = '')
    {
        $phones = self::find()->where(['receiving_phone' => $phone])->select($field)->count();

        $sns = self::find()
            ->where(['and','receiving_phone = '. $phone,['<>','return_logistics_sn' , '']])
            ->select($field)->count();

        if ($phones && $sns) {
            $rate = round($sns * 100 / $phones, 2) . '%';
        } else {
            $rate = '0';
        } 

        return $rate;
    }


    /*0.0
    *订单二级状态  State
   */
    public static function getStateList($State)
    {
        switch ($State)
        {
            case '1':
                return '买断';
            case '2':
                return '不买断';
            case '3':
                return '已收款';
        }
    }


    /*0.0
    *挂起  Abnormal
   */
    public static function getAbnormalList($Abnormal)
    {
        switch ($Abnormal)
        {
            case '1':
                return '挂起';
            case '2':
                return '正常';
        }
    }

    /*0.0
    *是否代收货款  Collection
   */
    public static function getCollectionList1($Collection)
    {
        switch ($Collection)
        {
            case '1':
                return '代收';
            case '2':
                return '不代收';
        }
    }

    /*0.0
    *订单类型  OrderType
   */
    public static function getOrderTypeList($OrderType)
    {
        switch ($OrderType)
        {
            case '1':
                return '线上';
            case '3':
                return '瑞胜';
        }
    }

    /*0.0
    *是否为测试
   */
    public static function getTestList($GoodsPriceState)
    {
        switch ($GoodsPriceState)
        {
            case '1':
                return '测试数据';
            case '2':
                return '正常';
        }
    }

    /*0.0
    *是否同城  SameCity
   */
    public static function getSameCityList($GoodsPriceState)
    {
        switch ($GoodsPriceState)
        {
            case '1':
                return '是';
            case '2':
                return '不是';
        }
    }

    /*0.0
    *买家确认 BuyConfirm
   */
    public static function getBuyConfirmList($BuyConfirm)
    {
        switch ($BuyConfirm)
        {
            case '0':
                return '未确认';
            case '1':
                return '已确认';
        }
    }

    /*0.0
     * 封装个函数得到时间表字段，将时间戳设置成时间显示
     */
    public static function getTableTimeValue($a)
    {
        if ($a==0)
        {
            return '时间未设置';
        }
        return date('Y-m-d H:i:s',$a);
    }
}
