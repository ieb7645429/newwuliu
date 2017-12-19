<?php
namespace frontend\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\LogisticsOrderSearch;
use yii\base\Exception;
use yii\web\Controller;
use common\models\Goods;
use common\models\OrderTime;
use common\models\Driver;
use common\models\OrderPrintLog;
use yii\filters\VerbFilter;
use common\models\LogisticsOrder;
use mdm\admin\components\MenuHelper;
use yii\data\Pagination;
use frontend\models\DriverPay;
use common\models\SmallPrint;
use common\models\LogisticsLines;
use common\models\AppLogin;
use common\models\LogisticsCar;
use common\models\DriverConfig;
use common\models\UserAll;

class DriverController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    /**
     * 待扫码
     */
    public function actionIndex(){
        $goods = new Goods();
        $LogisticsOrder = new LogisticsOrder(['scenario' => 'search']);
        //获取查询订单列表
        $type = 10;
        $add_time = $this->getAddTime(Yii::$app->request->get('LogisticsOrder')['add_time']);
        //参数
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['employee_id'] = Yii::$app->request->get('LogisticsOrder')['employee_id'];
        $params['order_type'] = Yii::$app->request->get('LogisticsOrder')['order_type'];
        $orderList = $LogisticsOrder->getOrderList($params,$type,null,$add_time);
        //分页
        $pages = new Pagination(['totalCount' =>count($orderList), 'pageSize' => Yii::$app->params['page_size']]);
        $model = array_slice($orderList,$pages->offset,$pages->limit);
        return $this->render('index',
                [
                        'goods'=>$goods,
                        'params' => $params,
                        'LogisticsOrder'=>$LogisticsOrder,
                        'orderList'=>$model,
                        'pages' => $pages,
                        'add_time' => $add_time,
                        'emoloyeeList'=> $this->_getEmployeeList(),
                        'orderTypeList'=> ['1'=>'西部','3'=>'瑞胜','4'=>'塔湾'],
                        'menus' => $this->_getMenus(),
                        'count' => $this->_CookieClear($orderList,'arr'),
                        'order_arr' => $this->_GetOrderArr(),
                ]
                );
    }
    
    /**
     * 订单装车
     */
    public function actionIndexAnother() {
        $goods = new Goods();
        $LogisticsOrder = new LogisticsOrder(['scenario' => 'search']);
        //获取查询订单列表
        $type = 1;
        $add_time = $this->getAddTime(Yii::$app->request->get('LogisticsOrder')['add_time']);
        //参数
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['employee_id'] = Yii::$app->request->get('LogisticsOrder')['employee_id'];
        $params['order_type'] = Yii::$app->request->get('LogisticsOrder')['order_type'];
        $orderList = $LogisticsOrder->getOrderList($params,$type,null,$add_time);
        //分页
        $pages = new Pagination(['totalCount' =>count($orderList), 'pageSize' => Yii::$app->params['page_size']]);
        $model = array_slice($orderList,$pages->offset,$pages->limit);
        return $this->render('index-another',
                [
                    'goods'=>$goods,
                    'LogisticsOrder'=>$LogisticsOrder,
                    'params'=>$params,
                    'orderList'=>$model,
                    'pages' => $pages,
                    'add_time' => $add_time,
                    'emoloyeeList'=> $this->_getEmployeeList(),
                    'orderTypeList'=> ['1'=>'西部','3'=>'瑞胜','4'=>'塔湾'],
                    'menus' => $this->_getMenus(),
                    'count' => $this->_CookieClear($orderList,'arr'),
                    'order_arr' => $this->_GetOrderArr(),
                ] 
               );
    }
    /**
     * 司机已处理订单
     * jinjian
     */
    public function actionMyself(){
        $goods = new Goods();
        $LogisticsOrder = new LogisticsOrder();
        $OrderTime = new OrderTime(['scenario' => 'search']);
        //获取查询订单列表
        $type = 2;
        $add_time = $this->getAddTime(Yii::$app->request->get('OrderTime')['ruck_time']);
        //参数
        $params['time_type'] = Yii::$app->request->get('LogisticsOrder')['add_time'];
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['employee_id'] = Yii::$app->request->get('LogisticsOrder')['employee_id'];
        $params['order_type'] = Yii::$app->request->get('LogisticsOrder')['order_type'];
        //分页
        $dataSql = $LogisticsOrder->getOrderList($params,$type,null,$add_time);
        $count = $dataSql->count();
        $pages = new Pagination(['totalCount' =>$count, 'pageSize' => Yii::$app->params['page_size']]);
        $orderList = $dataSql->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $orderList = $this->addGoodsInfo($orderList);
        return $this->render('myself',
                [
                        'goods'=>$goods,
                        'LogisticsOrder'=>$LogisticsOrder,
                        'params' => $params,
                        'orderList'=>$orderList,
                        'orderTime'=>$OrderTime,
                        'add_time' => $add_time,
                        'emoloyeeList'=> $this->_getEmployeeList(),
                        'orderTypeList'=> ['1'=>'西部','3'=>'瑞胜','4'=>'塔湾'],
                        'pages' => $pages,
                        'menus' => $this->_getMenus(),
                        'count' => $this->_CookieClear(null,'none'),
                        'order_arr' => $this->_GetOrderArr(),
                ]
                );
        
    }
    
    
    /**
     * 装车挂起订单
     * jinjian
     */
    public function actionAbnormal(){
        $goods = new Goods();
        $LogisticsOrder = new LogisticsOrder();
        $OrderTime = new OrderTime(['scenario' => 'search']);
        //获取查询订单列表
        $type = 3;
        $add_time = $this->getAddTime(Yii::$app->request->get('OrderTime')['ruck_time']);
        //参数
        $params['time_type'] = Yii::$app->request->get('LogisticsOrder')['add_time'];
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['employee_id'] = Yii::$app->request->get('LogisticsOrder')['employee_id'];
        $params['order_type'] = Yii::$app->request->get('LogisticsOrder')['order_type'];
        //分页
        $dataSql =  $LogisticsOrder->getOrderList($params,$type,null,$add_time);
        $count = $dataSql->count();
        $pages = new Pagination(['totalCount' =>$count, 'pageSize' => Yii::$app->params['page_size']]);
        $orderList = $dataSql->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $orderList = $this->addGoodsInfo($orderList);
        return $this->render('abnormal',
                [
                    'goods'=>$goods,
                    'LogisticsOrder'=>$LogisticsOrder,
                    'params' => $params,
                    'orderList'=>$orderList,
                    'orderTime'=>$OrderTime,
                    'add_time' => $add_time,
                    'emoloyeeList'=> $this->_getEmployeeList(),
                    'orderTypeList'=> ['1'=>'西部','3'=>'瑞胜','4'=>'塔湾'],
                    'pages' => $pages,
                    'menus' => $this->_getMenus(),
                ]
                );
    }
    /**
     * 同城订单
     * 靳健
     */
    public function actionCityWide(){
        $goods = new Goods();
        $LogisticsOrder = new LogisticsOrder(['scenario' => 'search']);
        $OrderTime = new OrderTime(['scenario' => 'search']);
        $orderPrintLog = new OrderPrintLog();
        $driverConfig = new DriverConfig();
        //获取查询订单列表
        $type = 5;
        $add_time = $this->getAddTime(Yii::$app->request->get('OrderTime')['ruck_time']);
        //参数
        $params['time_type'] = Yii::$app->request->get('LogisticsOrder')['add_time'];
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['employee_id'] = Yii::$app->request->get('LogisticsOrder')['employee_id'];
        $params['order_type'] = Yii::$app->request->get('LogisticsOrder')['order_type'];
        $params['print'] = Yii::$app->request->get('OrderPrintLog')['terminus'];
        //分页
        $dataSql = $LogisticsOrder->getOrderList($params,$type,null,$add_time,null);
        $count = $dataSql->count();
        $pages = new Pagination(['totalCount' =>$count, 'pageSize' => Yii::$app->params['page_size']]);
        $orderList = $dataSql->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $orderList = $this->addGoodsInfo($orderList);
        $orderList = $LogisticsOrder->stateButtonType($orderList,1);
        return $this->render('cityWide',
                [
                    'goods'=>$goods,
                    'params'=>$params,
                    'LogisticsOrder'=>$LogisticsOrder,
                    'orderList'=>$orderList,
                    'orderTime'=>$OrderTime,
                    'add_time' => $add_time,
                    'emoloyeeList'=> $this->_getEmployeeList(),
                    'orderTypeList'=> ['1'=>'西部','3'=>'瑞胜','4'=>'塔湾'],
                    'pages' => $pages,
                    'orderPrintLog' => $orderPrintLog,
                    'menus' => $this->_getMenus(),
                    'is_print'=>$driverConfig->getSmallPrintStatus(),
                    'count' => $this->_CookieClear($LogisticsOrder->getOrderList($params,$type,null,$add_time,null),'obj'),
                    'order_arr' => $this->_GetOrderArr(),
                    'rule' => Yii::$app->request->queryParams['r'],
                ]
                );
    }
    /**
     * 已原返
     * 靳健
     */
    public function actionReturned(){
        $goods = new Goods();
        $LogisticsOrder = new LogisticsOrder(['scenario' => 'search']);
        $OrderTime = new OrderTime(['scenario' => 'search']);
        $orderPrintLog = new OrderPrintLog();
        $driverConfig = new DriverConfig();
        //获取查询订单列表
        $type = 11;
        $add_time = $this->getAddTime(Yii::$app->request->get('OrderTime')['ruck_time']);
        //参数
        $params['time_type'] = Yii::$app->request->get('LogisticsOrder')['add_time'];
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['employee_id'] = Yii::$app->request->get('LogisticsOrder')['employee_id'];
        $params['order_type'] = Yii::$app->request->get('LogisticsOrder')['order_type'];
        $params['print'] = Yii::$app->request->get('OrderPrintLog')['terminus'];
        //分页
        $dataSql = $LogisticsOrder->getOrderList($params,$type,null,$add_time,null);
        $count = $dataSql->count();
        $pages = new Pagination(['totalCount' =>$count, 'pageSize' => Yii::$app->params['page_size']]);
        $orderList = $dataSql->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $orderList = $this->addGoodsInfo($orderList);
        $orderList = $LogisticsOrder->stateButtonType($orderList,1);
        return $this->render('cityWide',
                [
                    'goods'=>$goods,
                    'params'=>$params,
                    'LogisticsOrder'=>$LogisticsOrder,
                    'orderList'=>$orderList,
                    'orderTime'=>$OrderTime,
                    'add_time' => $add_time,
                    'emoloyeeList'=> $this->_getEmployeeList(),
                    'orderTypeList'=> ['1'=>'西部','3'=>'瑞胜','4'=>'塔湾'],
                    'pages' => $pages,
                    'orderPrintLog' => $orderPrintLog,
                    'menus' => $this->_getMenus(),
                    'is_print'=>$driverConfig->getSmallPrintStatus(),
                    'count' => $this->_CookieClear($LogisticsOrder->getOrderList($params,$type,null,$add_time,null),'obj'),
                    'order_arr' => $this->_GetOrderArr(),
                    'rule' => Yii::$app->request->queryParams['r'],
                ]
                );
    }
    public function actionOver(){
        $goods = new Goods();
        $LogisticsOrder = new LogisticsOrder();
        $OrderTime = new OrderTime(['scenario' => 'search']);
        //获取查询订单列表
        $type = 7;
        $order_sn_id = Yii::$app->request->get('LogisticsOrder')['order_sn'];
        $where = null;
        if(!empty($order_sn_id)){
            if($order_sn_id == 1){
                $where = ['and','logistics_order.order_sn <> ""','logistics_order.order_sn is not null'];
            }else if($order_sn_id == 2){
                $where = ['or','logistics_order.order_sn = ""','logistics_order.order_sn is null'];
            }
        }
        $add_time = $this->getAddTime(Yii::$app->request->get('OrderTime')['ruck_time']);
        //参数
        $params['time_type'] = Yii::$app->request->get('LogisticsOrder')['add_time'];
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['employee_id'] = Yii::$app->request->get('LogisticsOrder')['employee_id'];
        $params['order_type'] = Yii::$app->request->get('LogisticsOrder')['order_type'];
        //分页
        $dataSql = $LogisticsOrder->getOrderList($params,$type,null,$add_time,$where);
        $count = $dataSql->count();
        $pages = new Pagination(['totalCount' =>$count, 'pageSize' => Yii::$app->params['page_size']]);
        $orderList = $dataSql->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $orderList = $this->addGoodsInfo($orderList);
        $orderList = $LogisticsOrder->stateButtonType($orderList,1);
        return $this->render('over',
                [
                        'goods'=>$goods,
                        'LogisticsOrder'=>$LogisticsOrder,
                        'params' => $params,
                        'orderList'=>$orderList,
                        'orderTime'=>$OrderTime,
                        'add_time' => $add_time,
                        'emoloyeeList'=> $this->_getEmployeeList(),
                        'orderTypeList'=> ['1'=>'西部','3'=>'瑞胜','4'=>'塔湾'],
                        'pages' => $pages,
                        'menus' => $this->_getMenus(),
                        'num' => $count,
                        'order_sn_id' => $order_sn_id,
                        'count' => $this->_CookieClear(null,'none'),
                        'order_arr' => $this->_GetOrderArr(),
                ]
                );
    }
    /**
     * ajax修改订单状态
     * jinjian
     */
    public function actionGoodsEdit(){
        $goods = new Goods();
        if($goods->upGoodsState(Yii::$app->request->post('goods_id'))){
            $result = [
                'goods_id'=>Yii::$app->request->post('goods_id'),
                'error'=>0,
                'message'=>'处理成功'
            ];
            return json_encode($result);
        }
    }
    /**
     * ajax打印装车订单
     * 靳健
     */
    public function actionGoodsPrint(){
        $LogisticsOrder = new LogisticsOrder();
        $cookies = Yii::$app->request->cookies->get('checkbox');
        $order_arr = explode('-',$cookies);
        if($list = $LogisticsOrder->orderPrint(array(),$order_arr,0)){
            $list = $LogisticsOrder->getGoodsPrice($list,'driver');
            $result = [
                    'error'=>0,
                    'data'=>$list
            ];
        }else{
            $result = [
                    'error'=>1,
                    'message'=>'打印失败'
            ];
        }
            return json_encode($result);
    }
    /**
     * ajax订单修改
     * 靳健
     */
    public function actionOrderEdit(){
        $order = new LogisticsOrder();
        //调用修改订单状态函数
        if($order->ajaxOrderEdit([Yii::$app->request->post('order_id')])){
            $result = [
                    'error'=>0,
                    'message'=>'处理成功'
            ];
        }else{
            $result = [
                    'error'=>1,
                    'message'=>'处理失败'
            ];
        }
        return json_encode($result);
    }
    /**
     * ajax同城订单确定
     * 靳健
     */
    public function actionOrderSecondaryEdit(){
        $order = new LogisticsOrder();
        //调用修改订单状态函数
        if($order->ajaxOrderEdit([Yii::$app->request->post('order_id')])){
            $result = [
                    'error'=>0,
                    'message'=>'处理成功'
            ];
        }else{
            $result = [
                    'error'=>1,
                    'message'=>'处理失败'
            ];
        }
        return json_encode($result);
    }
   
    /**
     * 司机装车
     */
    public function actionShip() {
        $goodsSn = Yii::$app->request->get('goods_sn');
        Yii::$app->db->beginTransaction();
        $goodsSn = new Goods();
    }
    /**
     * 装车订单状态处理
     * 靳健
     */
    public function actionOrderManage(){
        $order = new LogisticsOrder();
        $routeLine = new LogisticsLines();
        $appLogin = new AppLogin();
        $driverConfig = new DriverConfig();
        $tr = Yii::$app->db->beginTransaction();
        try{
            $cookies = Yii::$app->request->cookies->get('checkbox');
            $order_arr = explode('-',$cookies);
            $res = $order->ajaxOrderStateDriverEdit($order_arr);
            if($res===false){
                throw new Exception('处理失败', '1');
            }
            if(!empty(Yii::$app->request->post('driver_id'))){//司机领队处理
                if(Yii::$app->request->post('driver_id') != $driverConfig::findOne(Yii::$app->user->id)->driver_manager_status){
                    throw new Exception('选中司机与当前不符', '1');
                }
            }
            
            $res2 = $routeLine->addLines($order_arr);
            if($res2===false){
                throw new Exception('line插入失败', '1');
            }
            $checkbox = Yii::$app->request->cookies->get('checkbox');
            Yii::$app->response->cookies->remove($checkbox);
//             $appLogin->updateStateOne();
            $result = ['error'=>0,'message'=>'处理成功'];
            $tr -> commit();
        }catch (Exception $e){
            $tr->rollBack();
            $result = ['error'=>1,'message'=>$e->getMessage()];
        }
        return json_encode($result);
        
    }
    /**
     * 同城打印
     * 靳健
     */
    public function actionCityWidePrint(){
        $LogisticsOrder = new LogisticsOrder();
        $cookies = Yii::$app->request->cookies->get('checkbox');
        $order_arr = explode('-',$cookies);
        if($list = $LogisticsOrder->getOrderList(array(),8,$order_arr)){
            $list = $LogisticsOrder->getGoodsPrice($list,'driver');
            $orderPrintLog = new OrderPrintLog();
            $orderPrintLog->saveTerminusPrintLog($order_arr);
            $result = [
                    'error'=>0,
                    'data'=>$list
            ];
        }else{
            $result = [
                    'error'=>1,
                    'message'=>'打印失败'
            ];
        }
        return json_encode($result);
    }
    
    public function actionPay() {
        $model = new DriverPay();
        
        $params = Yii::$app->request->queryParams;
        $params['driver_member_id'] = Yii::$app->user->id;
        
        $orderList = $model->formatDetailsData($model->getLogisticsOrderList($params));
        if(!empty($orderList)) {
            $total = $orderList['total'];
            unset($orderList['total']);
        } else {
            $total = array(
                'all_amount' => 0,
                'finished_amount' => 0,
                'unfinished_amount' => 0,
            );
        }
        
        //分页
        $pages = new Pagination(['totalCount' =>count($orderList), 'pageSize' => Yii::$app->params['page_size']]);
        $orderList = array_slice($orderList, $pages->offset, $pages->limit);
        
        return $this->render('pay', [
            'searchModel' => new LogisticsOrderSearch(),
            'orderList' => $orderList,
            'total' => $total,
            'menus' => $this->_getMenus(),
            'pages' => $pages
        ]);
    }
    
    /**
     * 小码单打印
     */
    public function actionSmallPrint(){
        $order = new LogisticsOrder();
        $smallPrint = new SmallPrint();
        $printLog = new OrderPrintLog();
        $driver_id = empty($_POST['driver_id'])?Yii::$app->user->id:$_POST['driver_id'];
        $data = $order->getSmallMemo($driver_id);
        $data = $printLog->smallPrintEdit($data);
        if(empty($data)){
            $result = ['code'=>404,'message'=>'不存在符合条件订单'];
        }
        else{
            $smallPrint->addSmallHistory($data,$driver_id);
            $result = ['code'=>200,'data'=>$data];
        }
        return json_encode($result);
    }
    /**
     * 批量扫码ajax处理
     */
    public function actionAjaxBatchScan(){
        $goods = new Goods();
        $cookies = Yii::$app->request->cookies->get('checkbox');
        $order_arr = explode('-',$cookies);
        $user_id = empty(Yii::$app->request->post('user_id'))?0:Yii::$app->request->post('user_id');
        $goods_arr = $goods->getGoodsArr($order_arr);
        $tr = Yii::$app->db->beginTransaction();
        try{
            $res = true;
            if(!empty($goods_arr)){
                foreach($goods_arr as $key => $value){
                        if(!$goods->upGoodsState($value,$user_id)) $res = false;
                }
            }
            if($res===false){
                throw new Exception('处理失败', '1');
            }
            $checkbox = Yii::$app->request->cookies->get('checkbox');
            Yii::$app->response->cookies->remove($checkbox);
            $result = ['error'=>0,'message'=>'处理成功'];
            $tr -> commit();
        }catch (Exception $e){
            $tr->rollBack();
            $result = ['error'=>1,'message'=>'处理失败'];
        }
        return json_encode($result);
    }
    
    /**
     * 小码单是否全部打印
     */
    public function actionAjaxPrintChange(){
        $driverConfig = new DriverConfig();
        $result = $driverConfig->editSmallPrint();
        if($result['boolean']){
            $result = ['code'=>200,'status'=>$result['status'],'message'=>'修改成功'];
        }else{
            $result = ['code'=>400,'message'=>'修改失败'];
        }
        return json_encode($result);
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
     * 靳健
     * 添加时间筛选条件
     * @param unknown $time
     * @return unknown|string
     */
    private function getAddTime($time){
        if(!empty($time)){
            list($start, $end) = explode(' - ', $time);
            $add_time['start'] = strtotime($start);
//             $add_time['end'] = strtotime($end)+60*60*24;
            $add_time['end'] = strtotime($end);
            $add_time['date'] = $time;
            //print_r($add_time);die;
            return $add_time;
        }
        return '';
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
        $driver = new Driver();
        $carType = $driver -> getCarTypeId(Yii::$app->user->id)['car_type_id'];
        foreach ($menus as &$menu) {
            if ($menu['url'][0] == $activeMenus['menu']) {
                $menu['active'] = 'active';
                if($activeMenus['item'] !== false && isset($menu['items'])) {
                    foreach ($menu['items'] as $key => &$item) {
                        if($carType == 1){
                            if($item['url'][0] == '/driver/myself') {
                                unset($menu['items'][$key]);
                                continue;
                            }
                        }
                        if ($carType == 2) {
                            if($item['url'][0] == '/driver/city-wide'||$item['url'][0] == '/driver/returned'||$item['url'][0] == '/driver/over') {
                                unset($menu['items'][$key]);
                                continue;
                            }
                        }
                        if($item['url'][0] == $activeMenus['item']) {
                            $item['active'] = 'active';
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
            'index' => ['menu' => '/driver/index', 'item' => '/driver/index'],
            'index-another' => ['menu' => '/driver/index', 'item' => '/driver/index-another'],
            'myself' => ['menu' => '/driver/index', 'item' => '/driver/myself'],
            'city-wide' => ['menu' => '/driver/index', 'item' => '/driver/city-wide'],
            'returned' => ['menu' => '/driver/index', 'item' => '/driver/returned'],
            'over' => ['menu' => '/driver/index', 'item' => '/driver/over'],
            'abnormal' => ['menu' => '/driver/index', 'item' => '/driver/abnormal'],
            'pay' => ['menu' => '/driver/pay', 'item' => false],
        );
        
        return $arr[Yii::$app->controller->action->id];
    }
    //完成确定按钮
    private function orderOver($orderList){
        $list = array();
        foreach($orderList as $key=>$value){
            if($value['stateButtonType']!=0||$value['freight_state']!=2){
                $list[$key] = $value;
            }
        }
        return $list;
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
    //开单员列表
    private function _getEmployeeList(){
        $userAll = new UserAll();
        $userList = $userAll->find()->where(['like','username','kd0240%',false])->asArray()->all();
        return ArrayHelper::map($userList, 'id', 'username');
    }
}