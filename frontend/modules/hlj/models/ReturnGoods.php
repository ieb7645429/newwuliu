<?php

namespace frontend\modules\hlj\models;

use Yii;
use yii\base\Object;

/**
 * This is the model class for table "return_goods".
 *
 * @property int $goods_id
 * @property int $order_id
 * @property string $goods_sn 商品单号(时间戳+线路+第几件货)
 * @property int $goods_state 货状态(0取消，10开单，20分拣，30摆渡车，40货站分拣，50封车，60落地，70送货，80完成)
 * @property string $update_time 更新时间
 * @property int $car_id 物流车id
 * @property int $lose_goods 是否丢失货物(1丢失，2没丢，3已找到)
 * @property int $sorting_id 分捡人id
 * @property int $send_id 派送人id
 * @property int $driver_member_id 司机的member_id
 */
class ReturnGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'return_goods';
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
            [['order_id', 'update_time'], 'required'],
            [['order_id', 'goods_state', 'update_time', 'car_id', 'lose_goods', 'sorting_id', 'send_id', 'driver_member_id'], 'integer'],
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
            'car_id' => 'Car ID',
            'lose_goods' => 'Lose Goods',
            'sorting_id' => 'Sorting ID',
            'send_id' => 'Send ID',
            'driver_member_id' => 'Driver Member ID',
        ];
    }
    
    /**
     * 增加退货商品表信息
     * @param unknown $order_id
     * @param unknown $logisticsRoute
     * @param unknown $goodsNum
     */
    public function addReturnGoodsInfo($order_id, $goodsSn, $goodsNum){
    	for($i =1;$i<=$goodsNum; $i++){
    		$a = array('order_id'=>$order_id, 'goods_state'=>Yii::$app->params['returnOrderStateEmployee'], 'goods_sn'=>$goodsSn.'_'.$i.'/'.$goodsNum, 'update_time'=>time());
    		$b = $this->addReturnGoods($a);
    		if(!$b) {
    			return false;
    		}
    	}
    	return true;
    }
    
    /**
     * 增加
     */
    public function addReturnGoods($upda){
    	$model = new ReturnGoods();
        $model->setAttributes($upda);
        return $model->save();
    }
    
    /**
     * 删除货品
     * @param unknown $orderId
     */
    public function delReturnGoodsByOrderId($orderId) {
    	return $this::deleteAll(['order_id' => $orderId]);
    }
    
    /**
     * 修改订单时,更新商品
     * @param Object 退货物流订单对像
     * @param string 退货商品goods_sn
     * @param int 退货数量
     * @return true false;
     */
    public function isUpdateReturnGoods($model, $goodsSn){
    	$r1 =$this->delReturnGoodsByOrderId($model->order_id);
    	$r2 = $this->addReturnGoodsInfo($model->order_id, $goodsSn, $model->goods_num);
    	if($r1 && $r2){
    		return true;
    	}
    	return false;
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
    		if($data->goods_state !=Yii::$app->params['returnGoodsStateEmployee'])
    		{
    			return '102';
    		}
    		if(!empty($data) && is_object($data)){
    			return $this->switchUpGoodsState($data, $userId);
    		}
    	}elseif ($terminal == 'ie')//电脑
    	{
    		$data = self::findOne($goodsInfo);
    		if(!empty($data) && is_object($data)){
    			return $this->switchUpGoodsState($data, Yii::$app->user->id);
    		}
    	}
    	return false;
    }
    
    /**
     * 修改商品状态
     * 朱鹏飞
     * @param unknown $goodsId
     */
    public function switchUpGoodsState($data, $userId){
    	$role = array_keys(Yii::$app->authManager->getRolesByUser($userId))[0];//获取当前用户权限
    	switch ($role)
    	{
    		case '入库':
    			if($data->goods_state == Yii::$app->params['returnGoodsStateEmployee'])//状态为10
    			{
    				$r1 = $this->goodsStateCityEdit($data, Yii::$app->params['returnGoodsStateDriver']);
    				if($r1)
    				{
    					return true;
    				}
    			}
    			return false;
    			break;
			case '同城员':
			    if($data->goods_state == Yii::$app->params['returnGoodsStateEmployee'])//状态为10
			    {
    				$r1 = $this->goodsStateCityEdit($data, Yii::$app->params['returnGoodsStateDriver']);
    				if($r1)
    				{
    				    return true;
    				}
			    }
			    return false;
			    break;
    		default:
    	}
    }
    
    /**
     * 判断是否为同一城市修改商品状态
     * 朱鹏飞
     * @param 商品id $goodsId
     * @param 修改成的状态 $goods_state
     */
    public function goodsStateCityEdit($data, $goodsState){
    	if($data->goods_state == Yii::$app->params['returnGoodsStateEmployee'])//判断状态是否为10
    	{
    		$modelLogisticsOrder = new LogisticsReturnOrder();
    		$LogisticsOrderInfo = $modelLogisticsOrder::findOne($data->order_id);
    		if($LogisticsOrderInfo->member_cityid == $LogisticsOrderInfo->receiving_cityid){//判断是否为同一城市
    			return $this->goodsStateEdit($data, Yii::$app->params['returnGoodsStateDelivery']);//同一城市状态修改为80
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
}
