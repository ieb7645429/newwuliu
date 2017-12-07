<?php

namespace frontend\modules\dl\controllers;

use Yii;
use frontend\modules\dl\models\LogisticsOrder;
use frontend\modules\dl\models\Area;
use frontend\modules\dl\models\User;
use frontend\modules\dl\models\LogisticsOrderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\NoCsrf;
use yii\filters\VerbFilter;
use yii\base\Exception;
use frontend\modules\dl\models\OrderTime;
use frontend\modules\dl\models\Goods;
use frontend\modules\dl\models\ReturnGoods;
use frontend\modules\dl\models\AppUpgrade;
use yii\filters\AccessControl;
use frontend\modules\dl\models\StatisticalOrder;
use yii\helpers\ArrayHelper;
use frontend\modules\dl\models\LogisticsRoute;
use frontend\modules\dl\models\Terminus;
use frontend\modules\dl\models\Driver;
use frontend\modules\dl\models\LogisticsLines;
use frontend\modules\dl\models\AppLogin;

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
		 $user        = new User();
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
	* @Name  : 改变状态接口
	* @Param : token
	* @date  : 2017-07-21
	* @Author: xiaoyu
	**/
	public function actionStatus($userid){
	   if(!Yii::$app->request->post('gsn') || !Yii::$app->request->post('token'))
	   {
			 return $this->get_jsoncode(10002,array(),'参数为空');
			 exit;
	   }

       //获取token并验证
      // $user   = New User();
      // $_token = $user->getMemberInfo(array('App_Key'=>Yii::$app->request->post('token')));
	 //  if(!$_token){
	  //    return $this->get_jsoncode(10003,array(),'数据有误');
	//	  exit;
	  // }
	   $res = $this->_authority(Yii::$app->request->post('gsn'),$userid);
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
	 * 判断扫码权限
	 * @param unknown $goodsSn
	 * @param unknown $userId
	 * @return \common\models\true
	 */
	public  function _authority($goodsSn,$userId){
		$goodsSn = ltrim($goodsSn, 0);
		$role = array_keys(Yii::$app->authManager->getRolesByUser($userId))[0];
		if($role == '大连司机'){
			$goods  = New Goods();
			return $goods->upGoodsState($goodsSn, $userId,'app');
		}elseif ($role == '入库' || $role == '同城员'){
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
  * @Name  : 获取user表相关数据接口
  * @Param : 会员小号,电话,店铺名称
  * @date  : 2017-07-21 
  * @Author: xiaoyu
  **/
  public function actionGetsmallnum(){
     $model  = new User();
	 $_Arr['contacts_phone'] = Yii::$app->request->post('contacts_phone');
     $_Arr['store_name']     = Yii::$app->request->post('store_name');
	 $_Arr['small_num']      = Yii::$app->request->post('small_num');
	 $_Arr['province_id']    = Yii::$app->request->post('province_id');
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
      if(empty($areaType))
      {
          $areaType = '024';
      }
      $times = Yii::$app->request->post('time');
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
      $info = $this->_areaType($areaType,$where);
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
  private function _areaType($areaType, $where){
      switch($areaType){
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
          default:
      }
      $info['order'] = $model->getTotalInfo($where);
      $info['Terminus'] = $modelTerminus->find()->select('terminus_id,terminus_name')->asArray()->all();
      $info['LogisticsRoute'] = $modelLogisticsRoute->find()->select('logistics_route_id,logistics_route_name')->asArray()->all();
      $info['areaType'] = array(array('id'=>'024', 'name'=>'沈阳'),array('id'=>'0451','name'=>'哈尔滨'));//app获取地区信息
      return $info;
  }
   /**
	* @name   :  app司机登陆接口
	* @param  ： user,pwd,key
	* @date   ： 2017-10-09
	* @author :  xiaoyu
	**/
	public function actionDriverlogin(){
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
		 
       //判断是否为司机如果是司机执行下一步
	   	 $user   = new User();
         $userId = $user->getMemberInfo(array('username'=>Yii::$app->request->post('user')));
		// echo($userId->id);
         $role = array_keys(Yii::$app->authManager->getRolesByUser($userId->id))[0]; 
         if($role != '司机'){
			 return $this->get_jsoncode(10002,'','登陆失败');
			 exit;
		 }
		 $arr['user'] = Yii::$app->request->post('user');
         $arr['pwd']  = Yii::$app->request->post('pwd');
		 $token       = $user->Check_App_Login($arr,'driver');
		 if($token){
			  /*
			  当前applogin表status为1不允许登陆
			  2017-10-10
			  修改
			  */
			  if($token == 'no'){
			    return $this->get_jsoncode(10004,'','其它司机正在送货中');
			  }
			  /*
			   end
			  */
			  //查询线路
			  $driver  = new Driver();
			  $info    = $driver->getDriverRouteInfo($userId->id);
			  $new_arr = array('UserId'=>$userId->id,
				  'Token'=>$token,
				  'RouteName'=>$info['logistics_route_name'],
				  'SameCity'=>$info['same_city']				
				  );
  			  return $this->get_jsoncode(10000,$new_arr,'登陆成功');
		  }
		  else{
			 return $this->get_jsoncode(10001,'','用户名密码或保存错误');
		 }
	}
	 /**
	* @name   :  app司机改变状态
	* @param  ： key,userid,category(on,状态变为2，off状态变为1)
	* @date   ： 2017-10-10
	* @author :  xiaoyu
	**/
	public function actionDriverChangeStatus(){
		$string = $this->get_jsoncode(20000,'','其它司机已登录');
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
         $applogin = new AppLogin();
		 $userid   = Yii::$app->request->post('userid');
		 $key      = Yii::$app->request->post('key');
		 if(Yii::$app->request->post('category')=='on'){
	         $where    = array('driver_member_id' =>$userid,'state' => 0);
             $_value   =  array('state'=>2,'add_time'=>time());			
			 $_status  = 1;
			 $_val     = 0;
		 }
		 elseif(Yii::$app->request->post('category')=='off'){
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
	*  获取applogin开始状态值
	*  xiaoyu
	*  2017-10-12
	**/
	public function actionGetisstart(){
	  	$string = $this->get_jsoncode(20000,'','其它司机已登录');
	   //获取参数
      if(!Yii::$app->request->post('key') || !Yii::$app->request->post('userid'))
		{
			 return $this->get_jsoncode(10002,'','参数为空');
			 exit;
		 }
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
}
