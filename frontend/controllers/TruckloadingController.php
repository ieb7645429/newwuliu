<?php

namespace frontend\controllers;

use Yii;
use common\models\LogisticsOrder;
use common\models\Area;
use common\models\User;
use common\models\UserAll;
use common\models\LogisticsOrderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\NoCsrf;
use yii\filters\VerbFilter;
use yii\base\Exception;
use common\models\OrderTime;
use common\models\Goods;
use common\models\ReturnGoods;
use common\models\AppUpgrade;
use yii\filters\AccessControl;
use common\models\StatisticalOrder;
use yii\helpers\ArrayHelper;
use common\models\LogisticsRoute;
use common\models\Terminus;
use common\models\Driver;
use common\models\LogisticsLines;
use common\models\AppLogin;
use common\models\LogisticsCar;

/**
 * TruckloadingController implements the CRUD actions for Goods model.
 */
class TruckloadingController extends Controller
{
    /**
     * @inheritdoc
     */	
    const  ReceTime = 30;//传递接口时间
	const  PosTime = 10;//传递接口时间 定位时间
	public function behaviors()
    {
        return [

            'csrf' => [
                'class' => NoCsrf::className(),
			    'controller' => $this,
                'actions' => [
                    'sendinterface',
			        'applogin',
			        'status',
				    'getsmallnum',
                		'getinfo',
				    'driverlogin',
				    'driver-change-status',
				    'getisstart',
				    'appupgrade-dreiver',
                    'driverlist',
                    'order-info',
                    'set-goods-state',
				    'get-route-info',//created by fenghuan
                    'get-route-view',//created by fenghuan
                ]
            ],
            'verbs' => [
            'class' => VerbFilter:: className(),
            'actions' => [
                 'applogin'  => ['post'],
				 'driverlogin' => ['post'],
                 'sendinterface' => ['post'],          //只允许用post方式访问
				 'status' => ['post'],
				 'getsmallnum' => ['post'],
                 'getinfo' => ['post'],
				 'driver-change-status' => ['post'],
				 'getisstart'=>['post'],
				 'driverlist'=>['post'],
				 'order-info'=>['post'],
                'set-goods-state'=>['post'],
                'get-route-info' => ['post'],
                'get-route-view' => ['post'],

            ],
             ],
        ];
    }
   /**
	* 有件网发货保存订单接口调用
	* 2017-07-21
	* 2017-09-06修改功能至反推订单接口
	* 王子强
	**/
/*	public function actionSendinterface(){
		$model = new LogisticsOrder();
		$modelOrderTime = new OrderTime();
	    $value =Yii::$app->request->post();
		if(empty($value) && is_array($value)){exit;}
		//验证数据是否正确
		if(@$value['key']!=$this->create_secrect($value)){echo('error');exit;}
		unset($value['key']);

		foreach($value as $key=>$val){
		 //if($key!='key')
		  $model->$key = $val;
		}
		$type = 'member';//录入类型
        $tr = Yii::$app->db->beginTransaction();
		$model = $model->appFillLogisticsInfo($model, $type);

		try {
          $model->save();//插入数据库获取order_id
		//如果保存不上订单请检查是否有goods_num，数据模型将此定义成必填
             $model->logistics_sn = $model->getLogisticsSn($model->order_id);//票号
//                 $modelBuyInfo->addBuyInfo($model);

           $model->save(); //根据order_id更新票号信息

              $modelOrderTime->order_id=$model->order_id;
             $modelOrderTime->save();//增加订单时间表
			  $tr -> commit();
			//  echo true;
			//2017-09-04返回订单编号给有件
			  echo($model->logistics_sn);
		} catch (Exception $e) {
			$tr->rollBack();
			Yii::$app->session->setFlash('error', '添加发货单失败！');
		}
	}*/
	/**
	* 接口加密验证
	* 2017-07-21
	* 王子强
	* 用途 app登陆接口验证
	**/
	private function create_secrect($data) {
		$url        ='';
		$mac        = "";
		$arg        = "";
		unset($data['key']);
		//while (list ($key, $val) = each ($data)) {
	//		   $mac.=$key."=".urlencode($this->form_strip($val))."&";		
	//	}
        $mac = 'user='.$data['user'].'&pwd='.$data['pwd'];
		//$mac = substr($mac, 0, -1);
		$url = strtoupper(md5($mac.'1813514w'));
		return $url;
	}
	
	//过滤
	private function form_strip($formvalue)
	{
	    return strip_tags(addslashes(trim($formvalue)));
	}
	//json
	private function get_jsoncode($code,$datas,$msg=''){
	    return json_encode(array('code'=>$code,'datas'=>$datas,'msg'=>$msg));
	}
	/**
	* @name   :  app登陆接口
	* @param  ： user,pwd,key
	* @date   ： 2017-07-27
	* @author :  xiaoyu
	* @modify :  2017-11-15司机登陆扫码不需要密码
	**/
	public function actionApplogin(){
	   //获取参数
       if(!Yii::$app->request->post('user') || !Yii::$app->request->post('pwd') || !Yii::$app->request->post('key'))
		{
			 return $this->get_jsoncode(10002,'','参数为空');
			 exit;
		 }
	 if($this->create_secrect(Yii::$app->request->post())!=Yii::$app->request->post('key')){
		     return $this->get_jsoncode(10004,'','参数非法');
			 exit;
		 }
		 $user        = new UserAll();
		 $arr['user'] = Yii::$app->request->post('user');
         $arr['pwd']  = Yii::$app->request->post('pwd');
		 $token       = $user->Check_App_Login($arr);
		 if($token){			
            return $this->get_jsoncode(10000,$token,'登陆成功');
		  }
		  else{
			echo $this->get_jsoncode(10001,'','用户名密码或保存错误');
		 }
	}
   /**
	* @name   :  app司机登陆接口
	* @param  ： user,pwd,key
	* @date   ： 2017-10-09
	* @author :  xiaoyu
	**/
    public function actionDriverlogin()
    {
        //获取参数
        if (!Yii::$app->request->post('user') || !Yii::$app->request->post('pwd') || !Yii::$app->request->post('key')) {
            return $this->get_jsoncode(10002, '', '参数为空');
            exit;
        }
        if ($this->create_secrect(Yii::$app->request->post()) != Yii::$app->request->post('key')) {
            return $this->get_jsoncode(10004, '', '参数非法');
            exit;
        }

        //判断是否为司机如果是司机执行下一步
        $user = new UserAll();
        $arr['user'] = Yii::$app->request->post('user');
        $arr['pwd'] = Yii::$app->request->post('pwd');
        $token = $user->Check_App_Login($arr, 'driver');
        if ($token) {
            $userId = $user->getMemberInfo(array('username' => $arr['user']));
            // echo($userId->id);
            $role = array_keys(Yii::$app->authManager->getRolesByUser($userId->id))[0];
            if (!strstr($role, '司机')) {

                return $this->get_jsoncode(10002, '', '登陆失败');
                exit;
            }

            /*
            当前applogin表status为1不允许登陆
            2017-10-10
            修改
            */
            if ($token == 'no') {
                return $this->get_jsoncode(10004, '', '其它司机正在送货中');
            }
            /*
             end
            */
            //查询线路
            /*
             根据地区查找对应线路
             2017-11-29
             xiaoyu
            */
            //先不分地区
//            switch ($userId->area) {
//                case 'sy':
//                    $driver = new Driver();
//                    break;
//                case 'hlj':
//                    $driver = new \frontend\modules\hlj\models\Driver();
//                    break;
//                case 'dl':
//                    $driver = new \frontend\modules\dl\models\Driver();
//                    break;
//                default:
//                    $driver = new Driver();
//            }

            $driver = new Driver();


            $info = $driver->getDriverRouteInfo($userId->id);
            $new_arr = array('UserId' => $userId->id,
                'Token' => $token,
                'RouteName' => empty($info['logistics_route_name']) ? '' : $info['logistics_route_name'],
                'SameCity' => empty($info['same_city']) ? '' : $info['same_city']
            );
            return $this->get_jsoncode(10000, $new_arr, '登陆成功');
        } else {
            return $this->get_jsoncode(10001, '', '用户名密码或保存错误');
        }
    }
	
	/**
	 * 扫码枪查询货号
	 * @return string
	 */
	public function actionOrderInfo()
	{
	    if(empty(Yii::$app->request->post('AppKey')))
	    {
	        return $this->get_jsoncode(10001,array(),'参数错误');
	    }
	    $model = new UserAll();
	    $user = $model->appLogin(Yii::$app->request->post('AppKey'), Yii::$app->request->post('appType'));
	    if($user === 10004)
	    {
	        return $this->get_jsoncode(10004,array(),'其它司机正在送货中');
	    }
	    if(empty($user))
	    {
	        return $this->get_jsoncode(10002,array(),'请重新登录');
	    }
	    $arr = array();
	    if(!empty($user))
	    {
	        switch ($user->area)
	        {
	            case 'sy':
	                $model = new LogisticsOrder();
	                $arr = $model->getOrderInfo(Yii::$app->request->post('orderSn'));
	                break;
	            case 'dl':
	                $model = new \frontend\modules\dl\models\LogisticsOrder();
	                $arr = $model->getOrderInfo(Yii::$app->request->post('orderSn'));
	                break;
	            default:$arr = false;
	                break;
	        }
	    }
	    if($arr === false)
	    {
	        return $this->get_jsoncode(10003,array(),'数据为空');
	    }
	    return $this->get_jsoncode(10000,$arr,'成功');
	}
	

	
	/**
	 * 扫码司机列表
	 * @return string
	 */
	public function actionDriverlist()
	{
	    $arr['con'] = Yii::$app->params['city'];
	    $arr['LogisticsRoute']['sameCity'] = array();
	    $arr['LogisticsRoute']['sameCity2'] = array();
	    $arr['user'] = array();
	    if(Yii::$app->request->post('cityId'))
	    {
	        switch (Yii::$app->request->post('cityId'))
	        {
	            case '024':
	                $model = new LogisticsRoute();
	                $res = $model->driverlist(Yii::$app->request->post('logRouteId'));
	                $arr['LogisticsRoute'] = $res['LogisticsRoute'];
	                $arr['user'] = $res['user'];
	                break;
	            case '0411':
	                $model = new \frontend\modules\dl\models\LogisticsRoute();
	                $res = $model->driverlist(Yii::$app->request->post('logRouteId'));
	                $arr['LogisticsRoute'] = $res['LogisticsRoute'];
	                $arr['user'] = $res['user'];
	                break;
	            default:
	                break;
	        }
	    }
	    return $this->get_jsoncode(10000,$arr,'成功');
	}
	
	/**
	 * 扫码枪处理货物状态
	 * @return string
	 */
	public function actionSetGoodsState()
	{
	    if(empty(Yii::$app->request->post('AppKey')) || empty(Yii::$app->request->post('goodsId')))
	    {
	        return $this->get_jsoncode(10001,array(),'参数错误');
	    }
	    
	    $model = new UserAll();
	    $user = $model->appLogin(Yii::$app->request->post('AppKey'), Yii::$app->request->post('appType'));
	    if($user === 10004)
	    {
	        return $this->get_jsoncode(10004,array(),'其它司机正在送货中');
	    }
	    if(empty($user))
	    {
	        return $this->get_jsoncode(10002,array(),'请重新登录');
	    }
	    switch ($user->area)
	    {
	        case 'sy':
	            $model = new Goods();
	            $res = $model->upGoodsState(Yii::$app->request->post('goodsId'),$user->id,'ie');
	            break;
	        case 'dl':
	            $model = new \frontend\modules\dl\models\Goods();
	            $res = $model->upGoodsState(Yii::$app->request->post('goodsId'),$user->id,'ie');
	            break;
	        default:
	            return $this->get_jsoncode(10003,array(),'处理失败');
	            break;
	    }
	    if($res === false)
	    {
	        return $this->get_jsoncode(10003,array(),'处理失败');
	    }
	    return $this->get_jsoncode(10000,array(),'成功');
	}
	
    /**
	* @Name  : 改变状态接口
	* @Param : token
	* @date  : 2017-07-21
	* @Author: xiaoyu
	**/
	public function actionStatus(){
	   if(!Yii::$app->request->post('gsn') || !Yii::$app->request->post('token'))
	   {
			 return $this->get_jsoncode(10002,array(),'参数为空');
			 exit;
	   }
       //获取token并验证
	   //根据
       $user   = New UserAll();
       $_token = $user->getMemberInfo(array('App_Key'=>Yii::$app->request->post('token')));
	   if(!$_token){
	      return $this->get_jsoncode(10003,array(),'数据有误');
		  exit;
	   }
	   //判断用户来源
	   //2017-10-25
	 //  $user = $user->findIdentity(Yii::$app->user->id);
       if ($_token->area == 'sy') {
	      $res = $this->_authority(Yii::$app->request->post('gsn'),$_token->id);
	   }
	   elseif($_token->area == 'dl'){
		   //进入dl页面
		    $module = \Yii::$app->controller->module->getModule('dl');
		    $msg =  $module->runAction('truckloading/status', array('userid'=>$_token->id));
			return $msg;
	   }
	   if($res === '102')
	   {
	   	return $this->get_jsoncode(10005,array(),'已扫过');
	   }
	   if($res === '101')
	   {
	   	return $this->get_jsoncode(10004,array(),'货物与物流线路不符');
	   }
	   if($res)
	   {
	     return $this->get_jsoncode(10000,array(),'更新成功');
	   }
	   else
	   {
	     return $this->get_jsoncode(10001,array(),'更新失败');
	   }
	}

    /**
     * 司机登录返回待扫码,待送货,已完成信息
     * @Author:Fenghuan
     */
	public function actionGetRouteInfo()
    {
        $request = Yii::$app->request;

        //总页数/第几页 初始为零
        $totalCount = $offset = 0;
        $limit = 10;

        $date = $request->post('date');//格式: 年-月-日

        $beginTime = strtotime(date('Y-m-d') . ' 00:00:00');
        $endTime = strtotime(date('Y-m-d') . ' 23:59:59');

        if (!empty($date)) {
            $beginTime = strtotime($date . ' 00:00:00');
            $endTime = strtotime($date . ' 23:59:59');
        }

        if(!empty($request->post('page') - 1)){
            $offset = ($request->post('page') - 1) * $limit;
        }

        $list = $arr = [];

        $order_state = $request->post('order_state');//订单状态
        $state = $request->post('state');//订单二级状态
        $token = $request->post('token');
        $goods_state = $request->post('goods_state');//已扫码状态下传(goods.goods_state状态)
        $model = new UserAll();
        $modelAppLogin = new AppLogin();

        //switch判断不同地区,实例化不同model

        $user = $model->appLogin($token, 'driver');

        if (!empty($user)) {
            switch ($user->area) {
                case 'sy':
                    $modelLogisticsOrder = new LogisticsOrder();
                    $modelLogisticsCar = new LogisticsCar();
                    $modelDriver = new Driver();
                    $param = Yii::$app->params['order_type'];
//                    $modelAppLogin = new AppLogin();
                    break;
                case 'hlj':
                    $modelLogisticsOrder = new \frontend\modules\hlj\models\LogisticsOrder();
                    $modelLogisticsCar = new \frontend\modules\hlj\models\LogisticsCar();
                    $modelDriver = new \frontend\modules\hlj\models\Driver();
                    $param = Yii::$app->params['order_type_hlj'];
//                    $modelAppLogin = new \frontend\modules\hlj\models\AppLogin();
                    break;
                case 'dl':
                    $modelLogisticsOrder = new \frontend\modules\dl\models\LogisticsOrder();
                    $modelLogisticsCar = new \frontend\modules\dl\models\LogisticsCar();
                    $modelDriver = new \frontend\modules\dl\models\Driver();
                    $param = Yii::$app->params['order_type_dl'];
//                    $modelAppLogin = new \frontend\modules\dl\models\AppLogin();
                    break;
                default:
                    break;
            }
        }

        if(empty($token)){
            echo json_encode(['code' => 16000, 'datas' => [], 'msg' => '没有秘钥']);die;
        }

        $res = $modelAppLogin->SearchInfo(['token' => $token]);


        if(empty($res)){
            echo json_encode(['code' => 16001, 'datas' => [], 'msg' => '数据异常']);die;
        }

        $userId = $res->user_id;
        $sameCity = $modelDriver->findOneModel(['member_id' => $userId])->logisticsCarInfo->car_type_id;


        //待扫码:根据key获取user_id->logistics_route_id
        if ((empty($order_state) || $order_state == 10) && empty($goods_state)) {
            //同城外阜一样
            //driver->logistics_car 获取线路id
            $res1 = $modelDriver->findOneModel(['member_id' => $userId])->logisticsCarInfo;

            if(!empty($res1)){
                if(empty($res1->logistics_route_id)){
                    echo json_encode(['code' => 16005, 'datas' => [], 'msg' => '没有线路']);die;
                }
            }
            $where = ['logistics_route_id' => $res1->logistics_route_id, 'order_state' => 10, 'goods_state' => 10];

            if($sameCity == 5)
            {
                $where = ['logistics_order.driver_member_id' => $userId, 'order_state' => 10, 'goods_state' => 10];
            }
            $ArrRes1 = $modelLogisticsOrder->getRouteArr(
                'logistics_sn',
                $where
            );

//            var_dump($ArrRes1);die;

            $routeArr =  ArrayHelper::getColumn($ArrRes1, 'logistics_sn');
            $condition = ['and',
                ['in', 'logistics_sn',$routeArr],
                ['order_state' => 10],
                ['between', 'add_time', $beginTime, $endTime],
                ['fast_route_id'=>0]
            ];
            if($sameCity == 5)
            {
                $condition = ['and',
                    ['in', 'logistics_sn',$routeArr],
                    ['order_state' => 10],
                    ['between', 'add_time', $beginTime, $endTime],
                    ['fast_route_id'=>1]
                ];
            }


        }
        //只要有商品扫码, 订单就进已扫码
        else if ($order_state == 10 && $goods_state == 70){
            //同城外阜一样
            //driver->logistics_car 获取线路id
            $res1 = $modelDriver->findOneModel(['member_id' => $userId])->logisticsCarInfo;

            if(!empty($res1)){
                if(empty($res1->logistics_route_id)){
                    echo json_encode(['code' => 16005, 'datas' => [], 'msg' => '没有线路']);die;
                }
            }
            $where = ['and', ['logistics_route_id' => $res1->logistics_route_id], ['order_state' => 10], ['in', 'goods_state', [50,70]]];

            if($sameCity == 5)
            {
                $where = ['and',['logistics_order.driver_member_id' => $userId], ['order_state' => 10], ['in', 'goods_state', [50,70]]];
            }
            $ArrRes1 = $modelLogisticsOrder->getRouteArr(
                'logistics_sn',
                $where
            );
            $routeArr =  ArrayHelper::getColumn($ArrRes1, 'logistics_sn');
            $condition = ['and',
                ['in', 'logistics_sn',$routeArr],
                ['order_state' => 10],
                ['between', 'add_time', $beginTime, $endTime],
                ['fast_route_id'=>0]
            ];
            if($sameCity == 5)
            {
                $condition = ['and',
                    ['in', 'logistics_sn',$routeArr],
                    ['order_state' => 10],
                    ['between', 'add_time', $beginTime, $endTime],
                    ['fast_route_id'=>1]
                ];
            }


        }
        //待送货,已完成:user_id->driver_member_id,
        else if($order_state == 70 && $state == 2){
            if($sameCity === 1){
                $condition = ['and',
                    ['driver_member_id' => $userId, 'order_state' => 70, 'state' => 2],
                    ['between', 'add_time', $beginTime, $endTime],//有票号:原返,退货;
                    ['return_logistics_sn' => ''],
                ];
            }
            else if($sameCity === 2){
                $condition = ['and',
                    ['driver_member_id' => $userId, 'order_state' => 50],
                    ['between', 'add_time', $beginTime, $endTime],
                    ['return_logistics_sn' => ''],
                ];
            }


        }
        else if($order_state == 70 && $state == 6){
            //同城外阜一样
            $condition = ['and',
                ['driver_member_id' => $userId, 'order_state' => 70, 'state' => 6],
                ['between', 'add_time', $beginTime, $endTime],
            ];
        }

        $resArr =  $modelLogisticsOrder->getOrders(
            $condition,
            [
                'order_id',
                'logistics_sn',
                'receiving_name',
                'receiving_phone',
                'order_state',
                'state',
                'driver_member_id',
                'logistics_route_id',
                'add_time',
                'order_type'
            ],
            $offset,
            $limit,
            ['order_id' => SORT_DESC]
        );

        if(!empty($resArr)){
            foreach ($resArr as $k => $v){
                $resArr[$k]['add_time'] = !empty($v['add_time']) ? date('Y-m-d H:i:s', $v['add_time']) : '';
                $resArr[$k]['order_type'] = !empty($v['order_type']) ? $param[$v['order_type']] : '';
            }
        }

        $totalCount =  $modelLogisticsOrder->getCountOrders(['order_id'], $condition);
        $totalCount = ceil($totalCount/10);

        $list['list'] = $resArr;
        $list['totalCount'] = $totalCount;

        echo json_encode(['code' => 200, 'datas' => $list, 'msg' => 'success']);

    }


    /**
     * get-route-info的详情页
     * @Author:Fenghuan
     */
    public function actionGetRouteView()
    {
        $request = Yii::$app->request;
        $model = new UserAll();
        $orderSn = $request->post('logistics_sn');

        if(empty($orderSn) || empty($request->post('token'))){
            echo json_encode(['code' => 16002, 'datas' => [], 'msg' => '缺少参数']);die;
        }

        $user = $model->appLogin($request->post('token'), 'driver');

        $arr = array();
        if (!empty($user)) {
            switch ($user->area) {
                case 'sy':
                    $modelOrder = new LogisticsOrder();
                    $arr = $modelOrder->getOrderInfo($orderSn);
                    break;
                case 'hlj':
                    $modelOrder = new \frontend\modules\hlj\models\LogisticsOrder();
                    $arr = $modelOrder->getOrderInfo($orderSn);
                    break;
                case 'dl':
                    $modelOrder = new \frontend\modules\dl\models\LogisticsOrder();
                    $arr = $modelOrder->getOrderInfo($orderSn);
                    break;
                default:
                    $arr = false;
                    break;
            }
        }

        if (empty($arr)) {
            echo json_encode(['code' => 16006, 'datas' => [], 'msg' => '没有数据']);die;
        }
        $arr['order']['add_time'] = !empty($arr['order']['add_time']) ? date('Y-m-d H:i:s', $arr['order']['add_time']) : '';


        echo json_encode(['code' => 200, 'datas' => $arr, 'msg' => 'success']);

    }
    


	/**
	 * 判断扫码权限
	 * @param unknown $goodsSn
	 * @param unknown $userId
	 * @return \common\models\true
	 */
	private  function _authority($goodsSn,$userId){
		$goodsSn = ltrim($goodsSn, 0);
		$role = array_keys(Yii::$app->authManager->getRolesByUser($userId))[0];
		if($role == '司机'){
			$goods  = New Goods();
			return $goods->upGoodsState($goodsSn, $userId,'app');
		}elseif ($role == '入库' || $role == '同城员' || $role == '西部退货组' || $role == '瑞胜退货组' || $role =='塔湾退货组')
		{
			$returnGoods = new ReturnGoods();
			return $returnGoods->upGoodsState($goodsSn, $userId,'app');
		}
	}
   /**
	* @Name  : app升级
	* @Param : token
	* @date  : 2017-07-21
	* @Author: xiaoyu
	**/
	public function actionAppupgrade(){
	   /*if(!Yii::$app->request->post('token'))
	   {
			 return $this->get_jsoncode(10002,array(),'参数为空');
			 exit;
	   }
       //获取token并验证
       $user   = New User();
       $_token = $user->getMemberInfo(array('App_Key'=>Yii::$app->request->post('token')));
	   if(!$_token){
	      return $this->get_jsoncode(10003,array(),'数据有误');
		  exit;
	   }*/
	   //获取升级数据
	   $upgrade  = New AppUpgrade();
	   $type = 1;
	   $datas    = $upgrade->GetInfo($type);
	   if($datas)
	   {
	     echo $this->get_jsoncode(10000,$datas,'获取数据成功');
	   }
	   else
	   {
	     echo $this->get_jsoncode(10001,array(),'获取数据失败');//没有数据也会返回失败
	   }
	}
	/**
	 * @Name  : 物流获取时时数据app升级
	 * @Param : token
	 * @date  : 2017-07-21
	 * @Author: xiaoyu
	 **/
	public function actionAppupgradeorder(){
		/*if(!Yii::$app->request->post('token'))
		 {
		 return $this->get_jsoncode(10002,array(),'参数为空');
		 exit;
		 }
		 //获取token并验证
		 $user   = New User();
		 $_token = $user->getMemberInfo(array('App_Key'=>Yii::$app->request->post('token')));
		 if(!$_token){
		 return $this->get_jsoncode(10003,array(),'数据有误');
		 exit;
		 }*/
		//获取升级数据
		$upgrade  = New AppUpgrade();
		$type = 2;
		$datas    = $upgrade->GetInfo($type);
		if($datas)
		{
			echo $this->get_jsoncode(10000,$datas,'获取数据成功');
		}
		else
		{
			echo $this->get_jsoncode(10001,array(),'获取数据失败');//没有数据也会返回失败
		}
	}
    /**
	 * @Name  : 司机app升级
	 * @Param : token
	 * @date  : 2017-10-19
	 * @Author: xiaoyu
	 **/
	public function actionAppupgradeDreiver(){
		/*if(!Yii::$app->request->post('token'))
		 {
		 return $this->get_jsoncode(10002,array(),'参数为空');
		 exit;
		 }
		 //获取token并验证
		 $user   = New User();
		 $_token = $user->getMemberInfo(array('App_Key'=>Yii::$app->request->post('token')));
		 if(!$_token){
		 return $this->get_jsoncode(10003,array(),'数据有误');
		 exit;
		 }*/
		//获取升级数据
		$upgrade  = New AppUpgrade();
		$type = 3;
		$datas    = $upgrade->GetInfo($type);
		if($datas)
		{
			echo $this->get_jsoncode(10000,$datas,'获取数据成功');
		}
		else
		{
			echo $this->get_jsoncode(10001,array(),'获取数据失败');//没有数据也会返回失败
		}
	}
  /**
   * app获取订单统计信息
   * 朱鹏飞
   * @return string
   */
  /**
   * app获取订单统计信息
   * 朱鹏飞
   * @return string
   */
  public function actionGetinfo()
  {
      //   	$model = new StatisticalOrder();
      //   	$modelLogisticsRoute = new LogisticsRoute();
      //   	$modelTerminus = new Terminus();
      $areaType = Yii::$app->request->post('areaType');
      if(empty($areaType))//将查询城市转换成数组
      {
          $type = '全国';
          $areaType = array('024', '0451','0411');
      }else{
          $areaType = array($areaType);
      }
      $times = Yii::$app->request->post('time');
      //查询条件整理
      $where[] = 'and';
      if(isset($times['bg']) && $times['en'])
      {
          $where[] = ['between','add_time', $times['bg'],$times['en']];
      }
      $datas = Yii::$app->request->post('data');
      if(isset($datas)){
          foreach ($datas as $k => $data)
          {
              $where[][$k] = $data;
          }
      }
      $sameCity = array();
      if(isset($datas['same_city']))//判断筛选线路
      {
          $sameCity = array('same_city'=>$datas['same_city']);
      }
      $info = $this->_areaType($areaType,$where, $sameCity);
      if(isset($type))//全国计算反货平均值
      {
          if(count($areaType) > 1)
          {
              $info['order']['whereInfo']['proportion']=$info['order']['whereInfo']['proportion']/(count($areaType)-$info['aa']);
          }else{
              $info['order']['whereInfo']['proportion']=$info['order']['whereInfo']['proportion']/count($areaType);
          }
          $info['order']['total']['proportion']=round ($info['order']['total']['proportion']/count($areaType),1);
      }
      //     	$info['order'] = $model->getTotalInfo($where);
      //     	$info['Terminus'] = $modelTerminus->find()->select('terminus_id,terminus_name')->asArray()->all();
      //     	$info['LogisticsRoute'] = $modelLogisticsRoute->find()->select('logistics_route_id,logistics_route_name')->asArray()->all();
      return json_encode(array('code'=>'10000','datas'=>$info,'msg'=>'成功'));
      die;
  }
  
  /**
   * 根本地区获取相对应的统计信息
   * @param str $areaType
   * @param arr $where
   * @return unknown[]|string[]|\yii\db\ActiveRecord[]|array[]|NULL[]
   */
  private function _areaType($areaType, $where, $sameCity){
      $whereInfo = array();
      $total = array();
      foreach ($areaType as $v){
          switch($v){
              case '024'://沈阳地区项目
                  $model = new StatisticalOrder();
                  $modelLogisticsRoute = new LogisticsRoute();
                  $modelTerminus = new Terminus();
                  break;
              case '0451'://黑龙江地址项目
                  $model = new \frontend\modules\hlj\models\StatisticalOrder();
                  $modelLogisticsRoute = new \frontend\modules\hlj\models\LogisticsRoute();
                  $modelTerminus = new \frontend\modules\hlj\models\Terminus();
                  break;
              case '0411'://大连地址项目
                  $model = new \frontend\modules\dl\models\StatisticalOrder();
                  $modelLogisticsRoute = new \frontend\modules\dl\models\LogisticsRoute();
                  $modelTerminus = new \frontend\modules\dl\models\Terminus();
                  break;
              default:
                  continue;
                  break;
          }
          $res = $this->getStatisticsInfo($model, $modelLogisticsRoute, $modelTerminus, $where, $sameCity);
          foreach ($res['order']['whereInfo'] as $k1 => $v1)
          {
              if(!isset($whereInfo[$k1]))
              {
                  $whereInfo[$k1] = $v1;
              }else{
                  $whereInfo[$k1] += $v1;
              }
              if($k1 == 'proportion' && $v1 == 0)
              {
                  if(!isset($res['aa']))
                  {
                      $res['aa'] = 1;
                  }else{
                      $res['aa'] += 1;
                  }
              }
          }
          foreach ($res['order']['total'] as $k2 => $v2)
          {
              if(!isset($total[$k2]))
              {
                  $total[$k2] = $v2;
              }else{
                  $total[$k2] += $v2;
              }
          }
          $res['order']['whereInfo']=$whereInfo;
          $res['order']['total']=$total;
      }
     
      return $res;
  }
  
  /**
   * 查询当地统计信息
   * @param object $model统计表对像
   * @param object $modelLogisticsRoute物流线路表对像
   * @param object $modelTerminus落地点对像
   * @param array $where统计条件
   * @param array $sameCity是否同城
   * @return array
   */
  private function getStatisticsInfo($model, $modelLogisticsRoute, $modelTerminus, $where, $sameCity)
  {
      $info['order'] = $model->getTotalInfo($where);
      $info['Terminus'] = array();
      if(isset($sameCity['same_city']) && ($sameCity['same_city'] == 2))
      {
          $info['Terminus']= $modelTerminus->find()->select('terminus_id,terminus_name')->all();
      }
      $info['LogisticsRoute'] = $modelLogisticsRoute->find()->select('logistics_route_id,logistics_route_name')->where($sameCity)->asArray()->all();
      $info['areaType'] = Yii::$app->params['city'];//app获取地区信息
      return $info;
  }
  /**
  * @name   :  app司机改变状态沈阳地区函数
  * @param  ： key,userid,category(on,状态变为2，off状态变为1)
  **/
  private function DriverChangeStatus($userid,$category,$key){
	  	$string = $this->get_jsoncode(20000,'','其它司机已登录');
        $applogin = new AppLogin();
		 if($category == 'on'){
	         $where    = array('driver_member_id' =>$userid,'state' => 0);
             $_value   =  array('state'=>2,'add_time'=>time());			
			 $_status  = 1;
			 $_val     = 0;
		 }
		 elseif($category == 'off'){
		     $where    = array('driver_member_id' =>$userid,'state' => 2);
             $_value   = array('state'=>1,'end_time'=>time());
			 $_status  = 0;
			 $_val     = 1;
		 }
		 $arr      = array('user_id'=> $userid,
			               'token'  => $key,
			               'status' => $_val);
		 $value    = $applogin->SearchInfo($arr);
         if(!empty($value)){
			 $status   = new LogisticsLines();
			 //增加事务
		    $tr = Yii::$app->db->beginTransaction();
			 try{
				 if(!$status->ChangeState($where,$_value)){
                     throw new Exception('没有当前订单', '1');
				 } 
				 //改变applogin中status值
				 $arr = array('user_id'=>$userid,
			                 'token' =>$key);
                 if(!$applogin->ChangeStatus($arr,$_status)){
				     throw new Exception('status处理失败','2');
				 }
				 $string = $this->get_jsoncode(10000,
						  array('ReceTime'=>TruckloadingController::ReceTime,
						 'PosTime'=>TruckloadingController::PosTime),
						 '成功');
				 $tr -> commit();
			  }catch (Exception $e){
				$tr->rollBack();
				$string = $this->get_jsoncode(10001,'',$e->getMessage());
			  }
		 }
			  return $string;
	}
  
	 /**
	* @name   :  app司机改变状态
	* @param  ： key,userid,category(on,状态变为2，off状态变为1)
	* @date   ： 2017-10-10
	* @author :  xiaoyu
	**/
	public function actionDriverChangeStatus(){
	   //获取参数
      if(!Yii::$app->request->post('userid') || !Yii::$app->request->post('key') || !Yii::$app->request->post('category'))
		{
			 return $this->get_jsoncode(10002,'','参数为空');
			 exit;
		 }
		/* if($this->create_secrect(Yii::$app->request->post())!=Yii::$app->request->post('key')){
		     return $this->get_jsoncode(10004,'','参数非法');
			 exit;
		 }		*/
		 //验证token并验证为0才能执行下面操作
		 $userid   = Yii::$app->request->post('userid');
		 $key      = Yii::$app->request->post('key');
		 $category = Yii::$app->request->post('category');
         /*
		 * ===start===
		 * 2017-10-26
		 * 根据用户id查询区域,调用不同地区方法
		 * xiaoyu
		 */
          $user = UserAll::findIdentity($userid);
            $str = '';
			if($user->area == 'sy')
			{
			  $str =  $this->DriverChangeStatus($userid,$category,$key);
			   return $str;
            }
			else if($user->area == 'dl')
		    {
			  //进入dl页面
			   $module = \Yii::$app->controller->module->getModule('dl');
		       $msg = $module->runAction('truckloading/driver-change-status','');
			   return $msg;
		    }else if($user->area == 'hlj')
		    {
		        //进入hlj页面
		        $module = \Yii::$app->controller->module->getModule('hlj');
		        $msg = $module->runAction('truckloading/driver-change-status','');
		        return $msg;
		    }
		 /*
		 *===end===
		 */
		
	}
    /**
	*  获取applogin开始状态值
	**/
    private function Getisstart($key,$userid){
		$string = $this->get_jsoncode(20000,'','其它司机已登录');
        //验证token并验证为0才能执行下面操作
         $applogin = new AppLogin();
		 $userid   = Yii::$app->request->post('userid');
		 $key      = Yii::$app->request->post('key');
		 $arr      = array('user_id'=> $userid,
			               'token'  => $key			             
			               );
		 $value    = $applogin->SearchInfo($arr);
         if(!empty($value)){
			 $status   = new LogisticsLines();
			 $string = $this->get_jsoncode(10000,array('status'=>$value->status,'ReceTime'=>TruckloadingController::ReceTime,
						 'PosTime'=>TruckloadingController::PosTime),'成功');
		 }
		 return $string;
	}
	/**
	*  获取applogin开始状态值
	*  xiaoyu
	*  2017-10-12
	**/
	public function actionGetisstart(){
	   //获取参数
      if(!Yii::$app->request->post('key') || !Yii::$app->request->post('userid'))
		{
			 return $this->get_jsoncode(10002,'','参数为空');
			 exit;
		 }
		 $userid   = Yii::$app->request->post('userid');
		 $key      = Yii::$app->request->post('key');
	   /*
		 * ===start===
		 * 2017-10-26
		 * 根据用户id查询区域,调用不同地区方法
		 * xiaoyu
		 */
          $user = UserAll::findIdentity($userid);
            $str = '';
			if($user->area == 'sy')
			{
			  $str =  $this->Getisstart($key,$userid);
			  return $str;
            }
			else if($user->area == 'dl')
		    {
			  //进入dl页面
			   $module = \Yii::$app->controller->module->getModule('dl');
		       $msg = $module->runAction('truckloading/getisstart', '');
			   return $msg;
		    }
			else if($user->area == 'hlj')
		    {
			  //进入hlj页面
			   $module = \Yii::$app->controller->module->getModule('hlj');
		       $msg = $module->runAction('truckloading/getisstart', '');
			   return $msg;
		    }
		 /*
		 *===end===
		 */
		
	}




	 /**
  * @Name  : 获取user表相关数据接口
  * @Param : 会员小号,电话,店铺名称
  * @date  : 2017-07-21 
  * @Author: xiaoyu
  **/
  public function actionGetsmallnum(){
	 $_Arr['contacts_phone'] = Yii::$app->request->post('contacts_phone');
     $_Arr['store_name']     = Yii::$app->request->post('store_name');
	 $_Arr['small_num']      = Yii::$app->request->post('small_num');
	 $_Arr['province_id']    = Yii::$app->request->post('province_id');
	 $area                   = Yii::$app->request->post('area');
	 if($area == 'sy')
	 {
		 $model  = new User();
		 $result = $model->getsearch($_Arr);
		 $code = 100;
		 $msg  = '没有相关数据';
		 $datas = array();
	 if($result){
		//echo '1';
		//var_dump($result);
		 $code = 200;
		 $msg  = '';
		 $datas = $result;
	 }  
	 return json_encode(array('code'=>$code,'datas'=>$datas,'msg'=>$msg));
	 }
	 elseif($area == 'dl')
	 {
		$module = \Yii::$app->controller->module->getModule('dl');
		$msg = $module->runAction('truckloading/getsmallnum', '');
		return $msg;
		
	 }
	
  }

}
