<?php

namespace common\models;

use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "goods".
 *
 * @property int $goods_id
 * @property int $order_id
 * @property string $goods_sn 商品单号(时间戳+线路+第几件货)
 * @property int $goods_state 货状态(0取消，10开单，20分拣，30摆渡车，40货站分拣，50封车，60落地，70送货，80完成) 
 * @property string $update_time 更新时间 
 */
class Goods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id','update_time'], 'required'],
            [['order_id', 'goods_state', 'update_time'], 'integer'],
            [['goods_sn'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'goods_id' => 'Goods ID',
            'order_id' => 'Order ID',
            'goods_sn' => 'Goods Sn',
            'goods_state' => 'Goods State',
            'update_time' => 'Update Time', 
        ];
    }
    
    /**
     * 生成货号
     * 朱鹏飞
     * @param int $logisticsRouteId 物流线路id
     * @return bool true false;
     */
    public function getGoodsSn($logisticsRouteId, $time = null)
    {
    	$modelLogisticsRoute = new LogisticsRoute();
    	$model = new LogisticsOrder();
    	$modelDateNum= new DateNum();
    	$logisticsRouteInfo = $modelLogisticsRoute->getLogisticsRouteInfo(array('logistics_route_id'=>$logisticsRouteId));
    	if($time)
    	{
//     	    $num = $modelDateNum::findOne(date('Y-m-d', $time));
    		$today = strtotime(date('Y-m-d', $time));
     		$num = $model->find()->where('add_time >= :add_time and add_time <=:time', [':add_time' => $today, ':time' => $time])->count();
    	}else{
//     		$today = strtotime(date('Y-m-d', time()));
//     		$num = $model->find()->where('add_time >= :add_time', [':add_time' => $today])->count();
    	    $num = $modelDateNum->dateNumInfo(time());
    	}
    	return date('ymd').'-'.$logisticsRouteInfo['0']['logistics_route_code'].$logisticsRouteInfo['0']['logistics_route_no'].'-'.str_pad($num,4,"0",STR_PAD_LEFT);
    }

    /**
     * 判断省
     * @return string
     */
    public function getMemberProvinceId()
    {
    	$userId = Yii::$app->user->id;
    	$modelUser = new User();
    	$data = $modelUser->findOne($userId);
    	$arr = array('6'=>'024');
    	return $arr[$data->member_provinceid];
    }
    
    /**
     * 增加商品表信息
     * @param unknown $order_id
     * @param unknown $logisticsRoute
     * @param unknown $goodsNum
     */
    public function addGoodsInfo($order_id, $goodsSn, $goodsNum){
        for($i =1;$i<=$goodsNum; $i++){
            $a = array('order_id'=>$order_id, 'goods_state'=>'10', 'goods_sn'=>$goodsSn.'_'.$i.'/'.$goodsNum, 'update_time'=>time());
            if(!$this->addGoods($a)) {
                throw new Exception('添加商品失败！');
            }
        }
    }
    
    /**
     * 增加
     */
    public function addGoods($upda){
        $model = new Goods();
        $model->setAttributes($upda);
        return $model->save();
    }
    
    /**
     * 删除货品
     * @param unknown $orderId
     */
    public function delGoodsByOrderId($orderId) {
        return $this::deleteAll('order_id = :order_id', [':order_id' => $orderId]);
    }
    /**
     * 判断是否为同一城市修改商品状态
     * 朱鹏飞
     * @param 商品id $goodsId
     * @param 修改成的状态 $goods_state
     */
    public function goodsStateCityEdit($data, $goodsState){
    	if($data->goods_state == Yii::$app->params['goodsStateEmployee'])//判断状态是否为10
    	{
    		$modelLogisticsOrder = new LogisticsOrder();
    		$LogisticsOrderInfo = $modelLogisticsOrder::findOne($data->order_id);
    		if($LogisticsOrderInfo->member_cityid == $LogisticsOrderInfo->receiving_cityid){//判断是否为同一城市
    			return $this->goodsStateEdit($data, Yii::$app->params['goodsStateDelivery']);//同一城市状态修改为70
    		}
    	}
    	return $this->goodsStateEdit($data, $goodsState);
    }
    
    /**
     * 修改商品状态
     * 朱鹏飞
     * @param 商品id $goodsId
     * @param 修改成的状态 $goods_state
     */
    public function goodsStateEdit($data, $goodsState){
    	$data->goods_state = $goodsState;
    	return $data->save();
    }
    
    /**
     * 修改商品其它信息
     * 朱鹏飞
     * @param unknown $data
     * @param unknown $arr
     * @return bool true 成功 false 失败
     */
    public function goodsInfoEdit($data, $arr){
    	foreach ($arr as $k =>$v)
    	{
    		$data->$k = $v;
    	}
    	return $data->save();
    }
    
    /**
     * 判断访问修改商品状态终端
     * @param string $goodsInfo 电脑传goods_id  , app 传goods_sn
     * @param string $terminal 终端 app传app
     * @param string $userId用户id app传用户id
     * @return true false
     */
    public function upGoodsState($goodsInfo, $userId = 0, $terminal='ie'){
    	if($terminal == 'app')//扫码
    	{
    		if($userId<=0)
    		{
    			return false;
    		}
    		$data = self::findOne(['goods_sn'=>$goodsInfo]);
    		if(empty($data)){
    			return false;
    		}
    		if($data->goods_state !=Yii::$app->params['goodsStateEmployee'])
    		{
    			return '102';
    		}
    		$res = $this->logisticsRoute($data->order_id, $userId);
    		if(!$res){
    			return '101';
    		}
    		if(!empty($data) && is_object($data)){
    			return $this->switchUpGoodsState($data, $userId);
    		}
    	}elseif ($terminal == 'ie')//电脑
    	{
    		$data = self::findOne($goodsInfo);
    		if(!empty($data) && is_object($data)){
    		    if(empty($userId)){
    		        $userId = Yii::$app->user->id;
    		    }
    			return $this->switchUpGoodsState($data,$userId);
    		}
    	}
    	return false;
    }
    
    /**
     * 判断司机是否能扫当前订单
     * @param unknown $orderId
     * @param unknown $userId
     * @return boolean
     */
    public function logisticsRoute($orderId, $userId){
    	$modelOrder = new LogisticsOrder();
    	$query = LogisticsCar::find();
    	$query->innerJoin('driver','driver.logistics_car_id = logistics_car.logistics_car_id')
    	->where(['driver.member_id'=>$userId]);
    	$orderData = $modelOrder->findOne($orderId);
    	if($orderData->same_city == 2)//判断是否同城，
    	{
    	    $logisticsRouteInfo = $query->andFilterWhere(['logistics_car.logistics_route_id'=>$orderData->logistics_route_id])
    	    ->asArray()
    	    ->all();
    	    if(empty($logisticsRouteInfo)){
    	        return false;
    	    }
    	}elseif ($orderData->same_city == 1){
    	    $logisticsRouteInfo = $query
    	    ->all();
    	    if(!empty($logisticsRouteInfo)){
	            foreach ($logisticsRouteInfo as $k => $v)
	            {
	                if($v->car_type_id == 1)//如果同城司机，可以扫所有同城订单
	                {
	                    return true;
	                }
	            }
    	        return false;
    	    }
    	}
    	return true;
    }
    /**
     * 修改商品与订单状态
     * 朱鹏飞
     * @param unknown $goodsId
     */
    public function switchUpGoodsState($data, $userId){
    	$role = array_keys(Yii::$app->authManager->getRolesByUser($userId))[0];//获取当前用户权限
    	switch ($role)
    	{
    		case '司机':
    				if($data->goods_state ==Yii::$app->params['goodsStateEmployee'] )
    				{
    					$r1 = $this->goodsStateCityEdit($data, Yii::$app->params['orderStateDriver']);//修改商品状态
    					$modelDriver = new Driver();
    					$driver = $modelDriver::findOne(array('member_id'=>$userId));
    					$r2 = $this->goodsInfoEdit($data, array(//修改物流车id,司机id
    							'driver_member_id'=>$userId,
    							'car_id'=>$driver->logistics_car_id
    					));
    					if($r1 && $r2){
    						return true;
    					}
    				}
    				return false;
    			break;
			case '司机领队':
			case '司机同城领队':
			    if($data->goods_state ==Yii::$app->params['goodsStateEmployee'] )
			    {
			        $r1 = $this->goodsStateCityEdit($data, Yii::$app->params['orderStateDriver']);//修改商品状态
			        $modelDriver = new Driver();
			        $driver = $modelDriver::findOne(array('member_id'=>$userId));
			        $r2 = $this->goodsInfoEdit($data, array(//修改物流车id,司机id
			                'driver_member_id'=>$userId,
			                'car_id'=>$driver->logistics_car_id
			        ));
			        if($r1 && $r2){
			            return true;
			        }
			    }
			    return false;
			    break;
    		case '落地点':
    				$r1 = $this->goodsStateCityEdit($data, Yii::$app->params['goodsStateAbnormal']);
    				if($r1)
    				{
    					return true;
    				}
    				return false;
    			break;
    		case '送货员'://暂时没有,不用
    			if($data->goods_state == Yii::$app->params['orderStateTerminus'])
    			{
    				$r1 = $this->goodsStateCityEdit($data, Yii::$app->params['orderStateDelivery']);
    				$r2 = $this->goodsInfoEdit($data, array(//修改派送人id
    						'send_id'=>Yii::$app->user->id
    				));
    				if($r1 && $r2){
    					return true;
    				}
    				return false;
    			}
    			if($data->goods_state == Yii::$app->params['orderStateDelivery'])
    			{
    				$r1 = $this->goodsStateCityEdit($data, Yii::$app->params['orderStateComplete']);
    				if($r1)
    				{
    					return true;;
    				}
    				return false;
    			}
    			break;
    		default:
    	}
    }
    
    /**
     * 根据订单获取商品id
     * @param unknown $arr
     */
    public function getGoodsArr($arr){
        $goods = $this::find()->select('goods_id')->where(['in','order_id',$arr])->asArray()->all();
        $goods_arr = array();
        foreach($goods as $key => $value){
            $goods_arr[] = $value['goods_id'];
        }
        return $goods_arr;
    }
    
    /**
     * 联合查询用表
     * 靳健
     */
    public function getCarInfo(){
        return $this->hasOne(LogisticsCar::className(), ['logistics_car_id' => 'car_id']);
    }
    public function getGoodsInfo(){
        return $this->hasMany(Goods::className(), ['order_id' => 'order_id']);
    }
}
