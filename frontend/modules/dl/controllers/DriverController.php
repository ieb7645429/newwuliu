<?php
namespace frontend\modules\dl\controllers;

use Yii;
use frontend\modules\dl\models\LogisticsOrderSearch;
use yii\web\Controller;
use frontend\modules\dl\models\Goods;
use frontend\modules\dl\models\OrderTime;
use frontend\modules\dl\models\Driver;
use frontend\modules\dl\models\OrderPrintLog;
use yii\filters\VerbFilter;
use frontend\modules\dl\models\LogisticsOrder;
use mdm\admin\components\MenuHelper;
use yii\data\Pagination;
use frontend\models\DriverPay;
use frontend\modules\dl\models\SmallPrint;
use frontend\modules\dl\models\LogisticsLines;
use frontend\modules\dl\models\AppLogin;
use frontend\modules\dl\models\LogisticsCar;

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
        $params['goods_sn'] = Yii::$app->request->get('Goods')['goods_sn'];
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
                        'menus' => $this->_getMenus(),
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
        $params['goods_sn'] = Yii::$app->request->get('Goods')['goods_sn'];
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
                    'menus' => $this->_getMenus(),
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
        $params['goods_sn'] = Yii::$app->request->get('Goods')['goods_sn'];
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
                        'pages' => $pages,
                        'menus' => $this->_getMenus(),
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
        $params['goods_sn'] = Yii::$app->request->get('Goods')['goods_sn'];
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
        //获取查询订单列表
        $type = 5;
        $add_time = $this->getAddTime(Yii::$app->request->get('OrderTime')['ruck_time']);
        //参数
        $params['time_type'] = Yii::$app->request->get('LogisticsOrder')['add_time'];
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('Goods')['goods_sn'];
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
                    'pages' => $pages,
                    'orderPrintLog' => $orderPrintLog,
                    'menus' => $this->_getMenus(),
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
        $params['goods_sn'] = Yii::$app->request->get('Goods')['goods_sn'];
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
                        'pages' => $pages,
                        'menus' => $this->_getMenus(),
                        'num' => $count,
                        'order_sn_id' => $order_sn_id,
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
        if($list = $LogisticsOrder->orderPrint(array(),Yii::$app->request->post('order_arr'),Yii::$app->request->post('loading'))){
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
        $tr = Yii::$app->db->beginTransaction();
        try{
            $res = $order->ajaxOrderStateDriverEdit(Yii::$app->request->post('order_arr'));
            if($res===false){
                throw new Exception('处理失败', '1');
            }
            
            $res2 = $routeLine->addLines(Yii::$app->request->post('order_arr'));
            if($res2===false){
                throw new Exception('line插入失败', '1');
            }
//             $appLogin->updateStateOne();
            $result = ['error'=>0,'message'=>'处理成功'];
            $tr -> commit();
        }catch (Exception $e){
            $tr->rollBack();
            $result = ['error'=>1,'message'=>'处理失败'];
        }
        return json_encode($result);
        
    }
    /**
     * 同城打印
     * 靳健
     */
    public function actionCityWidePrint(){
        $LogisticsOrder = new LogisticsOrder();
        if($list = $LogisticsOrder->getOrderList(array(),8,Yii::$app->request->post('order_arr'))){
            $list = $LogisticsOrder->getGoodsPrice($list,'driver');
            $orderPrintLog = new OrderPrintLog();
            $orderPrintLog->saveTerminusPrintLog(Yii::$app->request->post('order_arr'));
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
        $data = $order->getSmallMemo();
        if(empty($data)){
            $result = ['code'=>404,'message'=>'不存在符合条件订单'];
        }
        else{
            $smallPrint->addSmallHistory($data);
            $result = ['code'=>200,'data'=>$data];
        }
        return json_encode($result);
    }
    /**
     * 批量扫码ajax处理
     */
    public function actionAjaxBatchScan(){
        $goods = new Goods();
        $order_arr =Yii::$app->request->post('order_arr');
        $goods_arr = $goods->getGoodsArr($order_arr);
        $tr = Yii::$app->db->beginTransaction();
        try{
            $res = true;
            if(!empty($goods_arr)){
                foreach($goods_arr as $key => $value){
                    if(!$goods->upGoodsState($value)) $res = false;
                }
            }
            if($res===false){
                throw new Exception('处理失败', '1');
            }
            $result = ['error'=>0,'message'=>'处理成功'];
            $tr -> commit();
        }catch (Exception $e){
            $tr->rollBack();
            $result = ['error'=>1,'message'=>'处理失败'];
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
                            if($item['url'][0] == '/dl/driver/myself') {
                                unset($menu['items'][$key]);
                                continue;
                            }
                        }
                        if ($carType == 2) {
                            if($item['url'][0] == '/dl/driver/city-wide'||$item['url'][0] == '/dl/driver/over') {
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
            'index' => ['menu' => '/dl/driver/index', 'item' => '/dl/driver/index'],
            'index-another' => ['menu' => '/dl/driver/index', 'item' => '/dl/driver/index-another'],
            'myself' => ['menu' => '/dl/driver/index', 'item' => '/dl/driver/myself'],
            'city-wide' => ['menu' => '/dl/driver/index', 'item' => '/dl/driver/city-wide'],
            'over' => ['menu' => '/dl/driver/index', 'item' => '/dl/driver/over'],
            'abnormal' => ['menu' => '/dl/driver/index', 'item' => '/dl/driver/abnormal'],
            'pay' => ['menu' => '/dl/driver/pay', 'item' => false],
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
}