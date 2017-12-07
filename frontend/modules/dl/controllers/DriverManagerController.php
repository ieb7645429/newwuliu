<?php

namespace frontend\modules\dl\controllers;

use Yii;
//use common\models\LogisticsOrderSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use frontend\modules\dl\models\Goods;
use frontend\modules\dl\models\User;
use frontend\modules\dl\models\Driver;
use frontend\modules\dl\models\OrderTime;
use frontend\modules\dl\models\LogisticsOrder;
use yii\data\Pagination;
use mdm\admin\components\MenuHelper;
use frontend\modules\dl\models\LogisticsRoute;
use frontend\modules\dl\models\LogisticsCar;

class DriverManagerController extends \yii\web\Controller
{
    //待扫码
    public function actionIndex()
    {
        $driver = new Driver();
        $goods = new Goods();
        $LogisticsOrder = new LogisticsOrder(['scenario' => 'search']);
        //获取查询订单列表
        $type = 7;
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        $same_city = 2;
        if($role == '大连司机同城领队'){
            $same_city = 1;
        }
        $add_time = $this->getAddTime(Yii::$app->request->get('LogisticsOrder')['add_time']);
        //参数
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('Goods')['goods_sn'];
        $where = $this->getSearchDriver(Yii::$app->request->get('Driver')['driver_id'],$same_city);
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
                        'driverList' => $driver->getDriverDropList($same_city),
                        'add_time' => $add_time,
                        'driver_id' => Yii::$app->request->get('Driver')['driver_id'],
                        'menus' => $this->_getMenus(),
                ]
                );
    }
    //待封车
    public function actionIndexAnother()
    {
        $route = new LogisticsRoute();
    	$goods = new Goods();
        $LogisticsOrder = new LogisticsOrder(['scenario' => 'search']);
        //获取查询订单列表
        $type = 1;
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        $same_city = 2;
        if($role == '大连司机同城领队'){
            $same_city = 1;
        }
        $add_time = $this->getAddTime(Yii::$app->request->get('LogisticsOrder')['add_time']);
        //参数
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('Goods')['goods_sn'];
        $params['driver_member_id'] = Yii::$app->request->get('LogisticsOrder')['driver_member_id'];
        $params['route_id'] = Yii::$app->request->get('LogisticsRoute')['logistics_route_id'];
        $where = $this->getSearchWhere(Yii::$app->request->get('LogisticsRoute')['logistics_route_id'],'goods',$same_city);
        if(!empty($params['driver_member_id'])){
            $where['goods.driver_member_id'] = $params['driver_member_id'];
        }
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
                    'driverList' => $this->getDriverList(Yii::$app->request->get('LogisticsRoute')['logistics_route_id']),
                    'pages' => $pages,
                    'routeList' => $route->getRouteDropList($same_city),
                    'add_time' => $add_time,
                    'menus' => $this->_getMenus(),
                ] 
               );
    }
    //已封车
    public function actionMyself(){
        $route = new LogisticsRoute();
        $goods = new Goods();
        $OrderTime = new OrderTime(['scenario' => 'search']);
        $LogisticsOrder = new LogisticsOrder(['scenario' => 'search']);
        //获取查询订单列表
        $type = 2;
        $same_city = 2;
        $driver_member_id = Yii::$app->request->get('LogisticsOrder')['driver_member_id'];
        $add_time = $this->getAddTime(Yii::$app->request->get('OrderTime')['ruck_time']);
        //参数
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('Goods')['goods_sn'];
        $where = $this->getSearchWhere(Yii::$app->request->get('LogisticsRoute')['logistics_route_id'],'order',$same_city);
        if(!empty($driver_member_id)){
            $where['logistics_order.driver_member_id'] = $driver_member_id;
        }
        $orderList = $LogisticsOrder->getDriverManagerList($params,$type,null,$add_time,$where);
        //分页
        $pages = new Pagination(['totalCount' =>count($orderList), 'pageSize' => Yii::$app->params['page_size']]);
        $model = array_slice($orderList,$pages->offset,$pages->limit);
        return $this->render('myself',
                [
                        'goods'=>$goods,
                        'route'=>$route,
                        'params'=>$params,
                        'LogisticsOrder'=>$LogisticsOrder,
                        'orderList'=>$model,
                        'driver_member_id' => $driver_member_id,
                        'driverList' => $this->getDriverList(Yii::$app->request->get('LogisticsRoute')['logistics_route_id']),
                        'orderTime'=>$OrderTime,
                        'pages' => $pages,
                        'routeList' => $route->getRouteDropList($same_city),
                        'add_time' => $add_time,
                        'route_id' => Yii::$app->request->get('LogisticsRoute')['logistics_route_id'],
                        'menus' => $this->_getMenus(),
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
        //获取查询订单列表
        $type = 4;
        $same_city = 1;
        $driver_member_id = Yii::$app->request->get('LogisticsOrder')['driver_member_id'];
        $add_time = $this->getAddTime(Yii::$app->request->get('OrderTime')['ruck_time']);
        //参数
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('Goods')['goods_sn'];
        $where = $this->getSearchWhere(Yii::$app->request->get('LogisticsRoute')['logistics_route_id'],'order',$same_city);
        if(!empty($driver_member_id)){
            $where['logistics_order.driver_member_id'] = $driver_member_id;
        }
        $orderList = $LogisticsOrder->getDriverManagerList($params,$type,null,$add_time,$where);
        //分页
        $pages = new Pagination(['totalCount' =>count($orderList), 'pageSize' => Yii::$app->params['page_size']]);
        $model = array_slice($orderList,$pages->offset,$pages->limit);
        return $this->render('city-wide',
                [
                        'goods'=>$goods,
                        'route'=>$route,
                        'params'=>$params,
                        'LogisticsOrder'=>$LogisticsOrder,
                        'orderList'=>$model,
                        'orderTime'=>$OrderTime,
                        'driver_member_id' => $driver_member_id,
                        'driverList' => $this->getDriverList(Yii::$app->request->get('LogisticsRoute')['logistics_route_id']),
                        'pages' => $pages,
                        'routeList' => $route->getRouteDropList($same_city),
                        'add_time' => $add_time,
                        'route_id' => Yii::$app->request->get('LogisticsRoute')['logistics_route_id'],
                        'menus' => $this->_getMenus(),
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
        //获取查询订单列表
        $type = 5;
        $same_city = 1;
        $add_time = $this->getAddTime(Yii::$app->request->get('OrderTime')['ruck_time']);
        //参数
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('Goods')['goods_sn'];
        $driver_member_id = Yii::$app->request->get('LogisticsOrder')['driver_member_id'];
        $order_sn_id = Yii::$app->request->get('LogisticsOrder')['order_sn'];
        $where = $this->getSearchWhere(Yii::$app->request->get('LogisticsRoute')['logistics_route_id'],'order',$same_city);
        if(!empty($driver_member_id)){
            $where['logistics_order.driver_member_id'] = $driver_member_id;
        }
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
                        'routeList' => $route->getRouteDropList($same_city),
                        'driverList' => $this->getDriverList(Yii::$app->request->get('LogisticsRoute')['logistics_route_id']),
                        'add_time' => $add_time,
                        'route_id' => Yii::$app->request->get('LogisticsRoute')['logistics_route_id'],
                        'driver_member_id' => $driver_member_id,
                        'order_sn_id' => $order_sn_id,
                        'menus' => $this->_getMenus(),
                        'count' => $count,
                ]
                );
    }
    
    //异常
    public function actionAbnormal(){
        $driver = new Driver();
        $goods = new Goods();
        $OrderTime = new OrderTime(['scenario' => 'search']);
        $LogisticsOrder = new LogisticsOrder(['scenario' => 'search']);
        
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        $same_city = 2;
        if($role == '大连司机同城领队'){
            $same_city = 1;
        }
        
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
                ]
                );
    }
    
    public function actionNotEnclosed(){
        $route = new LogisticsRoute();
        $goods = new Goods();
        $LogisticsOrder = new LogisticsOrder(['scenario' => 'search']);
        //获取查询订单列表
        $type = 6;
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        $same_city = 2;
        if($role == '大连司机同城领队'){
            $same_city = 1;
        }
        $driver_member_id = Yii::$app->request->get('LogisticsOrder')['driver_member_id'];
        $add_time = $this->getAddTime(Yii::$app->request->get('LogisticsOrder')['add_time']);
        //参数
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('Goods')['goods_sn'];
        $where = $this->getSearchRoute(Yii::$app->request->get('LogisticsRoute')['logistics_route_id'],$same_city);
        if(!empty($driver_member_id)){
            $where['goods.driver_member_id'] = $driver_member_id;
        }
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
                        'driver_member_id' => $driver_member_id,
                        'driverList' => $this->getDriverList(Yii::$app->request->get('LogisticsRoute')['logistics_route_id']),
                        'pages' => $pages,
                        'routeList' => $route->getRouteDropList($same_city),
                        'add_time' => $add_time,
                        'route_id' => Yii::$app->request->get('LogisticsRoute')['logistics_route_id'],
                        'menus' => $this->_getMenus(),
                ]
                );
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
    private function getSearchWhere($logistics_route_id,$driver_table,$same_city = 0){

        if(!empty($logistics_route_id)){
            $driver_arr = $this->getDriverArr($logistics_route_id);
            $where = array();
            if($driver_table=='goods'){
                $where['goods.driver_member_id'] = $driver_arr;
            }else{
                $where['logistics_order.driver_member_id'] = $driver_arr;
            }
        }else{
            //不选中路线无查询条件
            $where['logistics_order.order_id'] = 0;
        }
        
        if(!empty($same_city)){
            $where['logistics_order.same_city'] = $same_city;
        }
        return $where;     
    }
    private function getSearchDriver($driver_id,$same_city){
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
                'index' => ['menu' => '/dl/driver-manager/index', 'item' => '/dl/driver-manager/index'],
                'index-another' => ['menu' => '/dl/driver-manager/index', 'item' => '/dl/driver-manager/index-another'],
                'myself' => ['menu' => '/dl/driver-manager/index', 'item' => '/dl/driver-manager/myself'],
                'city-wide' => ['menu' => '/dl/driver-manager/index', 'item' => '/dl/driver-manager/city-wide'],
                'over' => ['menu' => '/dl/driver-manager/index', 'item' => '/dl/driver-manager/over'],
                'abnormal' => ['menu' => '/dl/driver-manager/index', 'item' => '/dl/driver-manager/abnormal'],
                'not-enclosed' => ['menu' => '/dl/driver-manager/index', 'item' => '/dl/driver-manager/not-enclosed'],
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
    
}
