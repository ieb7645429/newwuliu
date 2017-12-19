<?php

namespace frontend\controllers;

use Yii;
//use common\models\LogisticsOrderSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use common\models\Goods;
use common\models\User;
use common\models\Driver;
use common\models\OrderPrintLog;
use common\models\OrderTime;
use common\models\LogisticsOrder;
use yii\data\Pagination;
use mdm\admin\components\MenuHelper;
use common\models\LogisticsRoute;
use common\models\LogisticsCar;
use common\models\DriverConfig;
use common\models\AuthAssignment;

class DriverManagerController extends \yii\web\Controller
{
    public $layout_data;
    public $layout_driver_id;
    //待扫码
    public function actionIndex()
    {
        $driver = new Driver();
        $goods = new Goods();
        $LogisticsOrder = new LogisticsOrder(['scenario' => 'search']);
        //左侧司机列表
        $driverConfig = new DriverConfig();
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        $same_city = 2;
        if($role == Yii::$app->params['roleDriverManagerCityWide']){
            $same_city = 1;
        }
        $this->layout_data = $driver->getDriverList($same_city);//同城司机左侧列表
        $this->layout_driver_id = $driver_id = $driverConfig->getDriverManagerStatus();//左侧选中司机
        //获取查询订单列表
        $type = 7;
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        $add_time = $this->getAddTime(Yii::$app->request->get('LogisticsOrder')['add_time']);
        //参数
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('Goods')['goods_sn'];
        $where = $this->getSearchDriver($driver_id);
        $orderList = $LogisticsOrder->getDriverManagerList($params,$type,null,$add_time,$where);
        //分页
        $pages = new Pagination(['totalCount' =>count($orderList), 'pageSize' => Yii::$app->params['page_size']]);
        $model = array_slice($orderList,$pages->offset,$pages->limit);
        
        return $this->render('index',
                [
                        'goods'=>$goods,
                        'driver'=>$driver,
                        'params'=>$params,
                        'LogisticsOrder'=>$LogisticsOrder,
                        'orderList'=>$model,
                        'pages' => $pages,
                        'add_time' => $add_time,
                        'driver_id'=>$driver_id,
                        'menus' => $this->_getMenus(),
                        'count' => $this->_CookieClear($orderList,'arr'),
                        'order_arr' => $this->_GetOrderArr(),
                        'rule' => Yii::$app->request->queryParams['r'],
                ]
                );
    }

    //待封车
    public function actionIndexAnother()
    {
        $route = new LogisticsRoute();
    	$goods = new Goods();
        $LogisticsOrder = new LogisticsOrder(['scenario' => 'search']);
        //左侧司机列表
        $driver = new Driver();
        $driverConfig = new DriverConfig();
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        $same_city = 2;
        if($role == Yii::$app->params['roleDriverManagerCityWide']){
            $same_city = 1;
        }
        $this->layout_data = $driver->getDriverList($same_city);//同城司机左侧列表
        $this->layout_driver_id = $driver_id = $driverConfig->getDriverManagerStatus();//左侧选中司机
        //获取查询订单列表
        $type = 1;
        $add_time = $this->getAddTime(Yii::$app->request->get('LogisticsOrder')['add_time']);
        //参数
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('Goods')['goods_sn'];
        $where = $this->getSearchWhere($driver_id,'goods');
        $orderList = $LogisticsOrder->getDriverManagerList($params,$type,null,$add_time,$where);
        //分页
        $pages = new Pagination(['totalCount' =>count($orderList), 'pageSize' => Yii::$app->params['page_size']]);
        $model = array_slice($orderList,$pages->offset,$pages->limit);
        return $this->render('index-another',
                [
                    'goods'=>$goods,
                    'route'=>$route,
                    'params'=>$params,
                    'LogisticsOrder'=>$LogisticsOrder,
                    'orderList'=>$model,
                    'pages' => $pages,
                    'add_time' => $add_time,
                    'driver_id'=>$driver_id,
                    'menus' => $this->_getMenus(),
                    'count' => $this->_CookieClear($orderList,'arr'),
                    'order_arr' => $this->_GetOrderArr(),
                    'rule' => Yii::$app->request->queryParams['r'],
                ] 
               );
    }
    //已封车
    public function actionMyself(){
        $route = new LogisticsRoute();
        $goods = new Goods();
        $driver = new Driver();
        $driverConfig = new DriverConfig();
        $OrderTime = new OrderTime(['scenario' => 'search']);
        $LogisticsOrder = new LogisticsOrder(['scenario' => 'search']);
        $this->layout_data = $driver->getDriverList(2);//同城司机左侧列表
        $this->layout_driver_id = $driver_id = $driverConfig->getDriverManagerStatus();//左侧选中司机
        //获取查询订单列表
        $type = 2;
        $add_time = $this->getAddTime(Yii::$app->request->get('OrderTime')['ruck_time']);
        //参数
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('Goods')['goods_sn'];
        $where['logistics_order.driver_member_id'] = $driver_id;
        //分页
        $dataSql = $LogisticsOrder->getDriverManagerList($params,$type,null,$add_time,$where);
        $count = $dataSql->count();
        $pages = new Pagination(['totalCount' =>$count, 'pageSize' => Yii::$app->params['page_size']]);
        $orderList = $dataSql->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $orderList = $this->addGoodsInfo($orderList);
        return $this->render('myself',
                [
                        'goods'=>$goods,
                        'route'=>$route,
                        'params'=>$params,
                        'LogisticsOrder'=>$LogisticsOrder,
                        'orderList'=>$orderList,
                        'orderTime'=>$OrderTime,
                        'pages' => $pages,
                        'add_time' => $add_time,
                        'menus' => $this->_getMenus(),
                        'count' => $this->_CookieClear(null,'none'),
                        'order_arr' => $this->_GetOrderArr(),
                        'rule' => Yii::$app->request->queryParams['r'],
                ]
                );
    }
    
    //待收货
    public function actionCityWide(){
        $route = new LogisticsRoute();
        $driver = new Driver();
        $goods = new Goods();
        $OrderTime = new OrderTime(['scenario' => 'search']);
        $LogisticsOrder = new LogisticsOrder(['scenario' => 'search']);
        $orderPrintLog = new OrderPrintLog();
        $driverConfig = new DriverConfig();
        //左侧司机列表
        $this->layout_data = $driver->getDriverList(1);//同城司机左侧列表
        $this->layout_driver_id = $driver_id = $driverConfig->getDriverManagerStatus();//左侧选中司机
        //获取查询订单列表
        $type = 4;
        $same_city = 1;
        $add_time = $this->getAddTime(Yii::$app->request->get('OrderTime')['ruck_time']);
        //参数
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('Goods')['goods_sn'];
        $params['print'] = Yii::$app->request->get('OrderPrintLog')['terminus'];
        $where['logistics_order.driver_member_id'] = $driver_id;
        //分页
        $dataSql = $LogisticsOrder->getDriverManagerList($params,$type,null,$add_time,$where);
        $count = $dataSql->count();
        $pages = new Pagination(['totalCount' =>$count, 'pageSize' => Yii::$app->params['page_size']]);
        $orderList = $dataSql->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $orderList = $this->addGoodsInfo($orderList);
        $orderList = $LogisticsOrder->stateButtonType($orderList,1);
        return $this->render('city-wide',
                [
                        'goods'=>$goods,
                        'route'=>$route,
                        'params'=>$params,
                        'LogisticsOrder'=>$LogisticsOrder,
                        'orderList'=>$orderList,
                        'orderTime'=>$OrderTime,
                        'pages' => $pages,
                        'routeList' => $route->getRouteDropList($same_city),
                        'add_time' => $add_time,
                		'orderPrintLog' => $orderPrintLog,
                        'menus' => $this->_getMenus(),
                        'is_print'=>$driverConfig->getSmallPrintStatus(),
                        'driver_id'=>$driver_id,
                        'count' => $this->_CookieClear($LogisticsOrder->getDriverManagerList($params,$type,null,$add_time,$where),'obj'),
                        'order_arr' => $this->_GetOrderArr(),
                        'rule' => Yii::$app->request->queryParams['r'],
                ]
                );
        
        
    }
    //已完成
    public function actionOver(){
        $route = new LogisticsRoute();
        $driver = new Driver();
        $goods = new Goods();
        $OrderTime = new OrderTime(['scenario' => 'search']);
        $LogisticsOrder = new LogisticsOrder(['scenario' => 'search']);
        //左侧司机列表
        $driverConfig = new DriverConfig();
        $this->layout_data = $driver->getDriverList(1);//同城司机左侧列表
        $this->layout_driver_id = $driver_id = $driverConfig->getDriverManagerStatus();//左侧选中司机
        //获取查询订单列表
        $type = 5;
        $same_city = 1;
        $add_time = $this->getAddTime(Yii::$app->request->get('OrderTime')['ruck_time']);
        //参数
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('Goods')['goods_sn'];
        $order_sn_id = Yii::$app->request->get('LogisticsOrder')['order_sn'];
        $where['logistics_order.driver_member_id'] = $driver_id;
        //分页
        $dataSql = $LogisticsOrder->getDriverManagerList($params,$type,null,$add_time,$where);
        $count = $dataSql->count();
        $pages = new Pagination(['totalCount' =>$count, 'pageSize' => Yii::$app->params['page_size']]);
        $orderList = $dataSql->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $orderList = $this->addGoodsInfo($orderList);
        $orderList = $LogisticsOrder->stateButtonType($orderList,1);
        $orderList = $this->orderOver($orderList,$order_sn_id);
       
        return $this->render('over',
                [
                        'goods'=>$goods,
                        'params'=>$params,
                        'route'=>$route,
                        'LogisticsOrder'=>$LogisticsOrder,
                        'orderList'=>$orderList,
                        'orderTime'=>$OrderTime,
                        'pages' => $pages,
                        'add_time' => $add_time,
                        'order_sn_id' => $order_sn_id,
                        'menus' => $this->_getMenus(),
                        'count' => $this->_CookieClear(null,'none'),
                        'order_arr' => $this->_GetOrderArr(),
                        'rule' => Yii::$app->request->queryParams['r'],
                ]
                );
    }
    
    //异常
    public function actionAbnormal(){
        $driver = new Driver();
        $goods = new Goods();
        $OrderTime = new OrderTime(['scenario' => 'search']);
        $LogisticsOrder = new LogisticsOrder(['scenario' => 'search']);
        //左侧司机列表
        $driverConfig = new DriverConfig();
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        $same_city = 2;
        if($role == Yii::$app->params['roleDriverManagerCityWide']){
            $same_city = 1;
        }
        $this->layout_data = $driver->getDriverList($same_city);//同城司机左侧列表
        $this->layout_driver_id = $driverConfig->getDriverManagerStatus();//左侧选中司机
        
        
        //获取查询订单列表
        $type = 3;
        $add_time = $this->getAddTime(Yii::$app->request->get('LogisticsOrder')['add_time']);
        //参数
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('Goods')['goods_sn'];
        $orderList = $LogisticsOrder->getDriverManagerList($params,$type,null,$add_time,['logistics_order.same_city'=>$same_city]);
        //分页
        $pages = new Pagination(['totalCount' =>count($orderList), 'pageSize' => Yii::$app->params['page_size']]);
        $model = array_slice($orderList,$pages->offset,$pages->limit);
        return $this->render('abnormal',
                [
                        'goods'=>$goods,
                        'params'=>$params,
                        'LogisticsOrder'=>$LogisticsOrder,
                        'orderList'=>$model,
                        'orderTime'=>$OrderTime,
                        'pages' => $pages,
                        'add_time' => $add_time,
                        'menus' => $this->_getMenus(),
                        'rule' => Yii::$app->request->queryParams['r'],
                ]
                );
    }
    
    public function actionNotEnclosed(){
        $route = new LogisticsRoute();
        $goods = new Goods();
        $LogisticsOrder = new LogisticsOrder(['scenario' => 'search']);
        //左侧司机列表
        $driver = new Driver();
        $driverConfig = new DriverConfig();
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        $same_city = 2;
        if($role == Yii::$app->params['roleDriverManagerCityWide']){
            $same_city = 1;
        }
        $this->layout_data = $driver->getDriverList($same_city);//同城司机左侧列表
        $this->layout_driver_id = $driver_id = $driverConfig->getDriverManagerStatus();//左侧选中司机
        //获取查询订单列表
        $type = 6;
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        $add_time = $this->getAddTime(Yii::$app->request->get('LogisticsOrder')['add_time']);
        //参数
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('Goods')['goods_sn'];
        $where['goods.driver_member_id'] = $driver_id;
        $orderList = $LogisticsOrder->getDriverManagerList($params,$type,null,$add_time,$where);
        $orderList = $LogisticsOrder->getGoodsScan($orderList);
        //分页
        $pages = new Pagination(['totalCount' =>count($orderList), 'pageSize' => Yii::$app->params['page_size']]);
        $model = array_slice($orderList,$pages->offset,$pages->limit);
        return $this->render('not-enclosed',
                [
                        'goods'=>$goods,
                        'route'=>$route,
                        'params'=>$params,
                        'LogisticsOrder'=>$LogisticsOrder,
                        'orderList'=>$model,
                        'driverList' => $this->getDriverList(Yii::$app->request->get('LogisticsRoute')['logistics_route_id']),
                        'pages' => $pages,
                        'add_time' => $add_time,
                        'route_id' => Yii::$app->request->get('LogisticsRoute')['logistics_route_id'],
                        'menus' => $this->_getMenus(),
                        'rule' => Yii::$app->request->queryParams['r'],
                ]
                );
    }
    /**
     * 订单异常恢复
     */
    public function actionRecoverEdit(){
        $order = new LogisticsOrder();
        if($order->recoverOrder(Yii::$app->request->post('order_id'),10)){
            $result = [
                    'error'=>200,
                    'message'=>'恢复成功',
            ];
        }else{
            $result = [
                    'error'=>400,
                    'message'=>'恢复失败',
            ];
        }
        return json_encode($result);
    }
    
    /**
     * ajax扫码
     */
    public function actionGoodsEdit(){
        $goods = new Goods();
        if($goods->upGoodsState(Yii::$app->request->post('goods_id'),Yii::$app->request->post('user_id'))){
            $result = [
                    'goods_id'=>Yii::$app->request->post('goods_id'),
                    'error'=>0,
                    'message'=>'处理成功'
            ];
            return json_encode($result);
        }
    }
    /**
     * ajax 获取路线下对应的司机下拉框
     */
    public function actionAjaxGetDriverList(){
        $route_id = Yii::$app->request->post('route_id');
        $list = $this->getDriverList($route_id);
        return $this->getNewOption($list);
    }
    
    /**
     * ajax 改变选中司机
     */
    public function actionAjaxDriverChange(){
        $driverConfig = new DriverConfig();
        if($driverConfig->editDriverManagerStatus(Yii::$app->request->post('driver_id'))){
            $cookies = Yii::$app->request->cookies;//清楚cookie
            if(isset($cookies['checkbox'])){
                $checkbox = $cookies->get('checkbox');
                Yii::$app->response->cookies->remove($checkbox);
            }
            $result = ['code'=>200,'message'=>'司机切换成功'];
        }else{
            $result = ['code'=>400,'message'=>'司机切换失败'];
        }
        return json_encode($result);
    }
    
    
    /**
     * 添加时间筛选条件
     * @param unknown $time
     * @return unknown|string
     */
    private function getAddTime($time){
        $time = empty($time)?date('Y-m-d H:i:s',strtotime(date('Y-m-d'))) .' - ' . date('Y-m-d H:i:s'):$time;
            list($start, $end) = explode(' - ', $time);
            $add_time['start'] = strtotime($start);
            //$add_time['end'] = strtotime($end)+60*60*24;
            $add_time['end'] = strtotime($end);
            $add_time['date'] = $time;
            //print_r($add_time);die;
            return $add_time;
    }
    private function getSearchWhere($driver_id,$driver_table = 'order'){
        $where = array();
        if($driver_table=='goods'){
                $where['goods.driver_member_id'] = $driver_id;
        }else{
            $where['logistics_order.driver_member_id'] = $driver_id;
        }
        return $where;     
    }
    private function getSearchDriver($driver_id){
        if(!empty($driver_id)){
            $car_id = Driver::findOne(['member_id'=>$driver_id])->logistics_car_id;
            $route_id = LogisticsCar::findOne($car_id)->logistics_route_id;
//             print_r($route_id);die;
            $where['logistics_order.logistics_route_id'] = $route_id;
            return $where;
        }
        return '0 = 1';
    }
    private function getSearchRoute($logistics_route_id,$same_city){
        $where = array();
        if(!empty($logistics_route_id)){
            $where['logistics_order.logistics_route_id'] = $logistics_route_id;
            return $where;
        }
        return '0 = 1';
    }
    
    /**
     * 路线司机
     * @param unknown $logistics_route_id
     */
    private function getDriverArr($logistics_route_id){
        $car = new LogisticsCar();
        $driver = new Driver();
        $res = $car::find()->where(['logistics_route_id'=>$logistics_route_id])->asArray()->all();
        $driver_arr = array();
        foreach($res as $key=>$value){
            if(!empty($driver::findOne(['logistics_car_id'=>$value['logistics_car_id']])->member_id)){
                $driver_arr[] = $driver::findOne(['logistics_car_id'=>$value['logistics_car_id']])->member_id;
            }
        }
        return $driver_arr;
    }
    
    /**
     * 路线对应司机下拉列表
     */
    private function getDriverList($route_id){
        $user = new User();
        $driver_arr = $this->getDriverArr($route_id);
        $all = $user->find()->where(['in','id',$driver_arr])->all();
        $driver_list = array();
        foreach($all as $key => $value){
            $driver_list[$value['id']] = $value['user_truename'].'-'.$value['username'];
        }
        return $driver_list;
    }
    
    
    /**
     * 取得menus
     * @return array[]|string[]
     */
    private function _getMenus()
    {
        $menus = MenuHelper::getAssignedMenu(Yii::$app->user->id);
        $items = array();
    
        $activeMenus = $this->_getActiveMenu();
    
        foreach ($menus as &$menu) {
            if ($menu['url'][0] == $activeMenus['menu']) {
                $menu['active'] = 'active';
                if($activeMenus['item'] !== false && isset($menu['items'])) {
                    foreach ($menu['items'] as &$item) {
                        if($item['url'][0] == $activeMenus['item']) {
                            $item['active'] = 'active';
                            break;
                        }
                    }
                    $items = $menu['items'];
                }
            }
        }
        return ['menus' => $menus, 'items' => $items];
    }
    
    private function _getActiveMenu() {
        $arr = array(
                'index' => ['menu' => '/driver-manager/index', 'item' => '/driver-manager/index'],
                'index-another' => ['menu' => '/driver-manager/index', 'item' => '/driver-manager/index-another'],
                'myself' => ['menu' => '/driver-manager/index', 'item' => '/driver-manager/myself'],
                'city-wide' => ['menu' => '/driver-manager/index', 'item' => '/driver-manager/city-wide'],
                'over' => ['menu' => '/driver-manager/index', 'item' => '/driver-manager/over'],
                'abnormal' => ['menu' => '/driver-manager/index', 'item' => '/driver-manager/abnormal'],
                'not-enclosed' => ['menu' => '/driver-manager/index', 'item' => '/driver-manager/not-enclosed'],
        );
    
        return $arr[Yii::$app->controller->action->id];
    }
    /**
     * 获取订单来源
     * @param unknown $list
     * @param unknown $order_sn_id 1,线上 2,线下
     */
    private function orderOver($orderList,$order_sn_id){
        $list = array();
        foreach($orderList as $key=>$value){
            if($value['stateButtonType']!=0||$value['freight_state']!=2){//排除特殊情况
                if($order_sn_id==1){
                    if(!empty($value['order_sn']))
                        $list[$key] = $value;
                }else if($order_sn_id==2){
                    if(empty($value['order_sn']))
                        $list[$key] = $value;
                }else{
                        $list[$key] = $value;
                }
            }
        }
        return $list;
    }
    
    /**
     * 获取订单来源
     * @param unknown $list
     * @param unknown $order_sn_id 1,线上 2,线下
     */
    
    private function getNewOption($list){
        $str = '';
        foreach($list as $key => $value){
            $str .= '<option value='.$key.'>'.$value.'</option>';
        }
        return $str;
    }
    
    //添加商品列表
    private function addGoodsInfo($orderList){
        if(!empty($orderList)){
            foreach($orderList as $key=>$value){
                $orderList[$key]['goodsInfo'] = $this->getGoodsInfo($value['order_id']);
            }
        }
        return $orderList;
    }
    //获取商品信息
    private function getGoodsInfo($order_id){
        $goods = new Goods();
        $carInfo = new LogisticsCar();
        $goodsList = $goods->find()->where(['order_id'=>$order_id])->asArray()->all();
        if(empty($goodsList)) return $goodsList;
        foreach($goodsList as $key => $value){
            $goodsList[$key]['carInfo'] = $carInfo::findOne(['logistics_car_id'=>$value['car_id']]);
        }
        return $goodsList;
    }
    /**
     * 清除cookie
     * @param unknown $data
     * @param string $type  arr数组  obj对象 none默认不选中
     */
    private function _CookieClear($data,$type = 'obj'){
        $cookies = Yii::$app->request->cookies;
        $count = 0;
        if(empty(Yii::$app->request->queryParams['page'])){
                //删除cookie
                if(isset($cookies['checkbox'])){
                    $checkbox = $cookies->get('checkbox');
                    Yii::$app->response->cookies->remove($checkbox);
                }
                //默认不选中
                if($type=='none'){
                    return $count;
                }
                //默认cookie全部选中
                if($type=='arr'){
                    $order_arr = ArrayHelper::getColumn($data,'order_id');
                }else if($type=='obj'){
                    $order_arr = ArrayHelper::getColumn($data->asArray()->all(),'order_id');
                }
                $order_str = implode('-',$order_arr);
                //添加新cookie
                Yii::$app->response->cookies->add(new \yii\web\Cookie([
                        'name' => 'checkbox',
                        'value' => $order_str,
                    ])
                );
                $count = count($order_arr);
        }else{
            if(isset($cookies['checkbox'])){
                $count = count(explode('-',$cookies->get('checkbox')));
            }
        }
        return  $count;
    }
    //获取cookie
    private function _GetOrderArr(){
        $cookies = Yii::$app->request->cookies;
        if(isset($cookies['checkbox'])){
            return explode('-',$cookies->get('checkbox'));
        }
        return array();
    }
    
}
