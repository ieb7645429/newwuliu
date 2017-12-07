<?php

namespace frontend\controllers;
use yii\web\Controller;
use yii\web\Response;
use common\models\Goods;
use common\models\OrderTime;
use common\models\TerminusUser;
use common\models\OrderPrintLog;
use yii\filters\VerbFilter;
use common\models\LogisticsOrder;
use common\models\LogisticsOrderSearch;
use Yii;
use mdm\admin\components\MenuHelper;
use common\models\LogisticsReturnOrder;
use frontend\models\TerminusPay;
use backend\models\ReturnIncomeTerminus;
use yii\data\Pagination;
use backend\models\PayTerminus;
use backend\models\ReturnPayTerminus;
use backend\models\OrderRemark;
use backend\models\ReturnOrderRemark;
use common\models\BankInfo;
use common\models\LogisticsCar;

class TerminusController extends Controller
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
     * 订单落地点
     */
    public function actionIndex()
    {   
        $goods = new Goods();
        $LogisticsOrder = new LogisticsOrder();
        //获取查询订单列表
        
        $carList = $LogisticsOrder->getArriveCar();
        return $this->render('index',
                [
                    'carList'=>$carList,
                    'menus' => $this->_getMenus(),
                ] 
               );
    }
    public function actionList($driver)
    {
        $goods = new Goods();
        $OrderTime = new OrderTime(['scenario' => 'search']);
        $LogisticsOrder = new LogisticsOrder();
        //获取查询订单列表
        $type = 1;
        $driver_id = Yii::$app->request->get('driver');
        $add_time = $this->getAddTime(Yii::$app->request->get('OrderTime')['ruck_time']);
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('Goods')['goods_sn'];
        //分页
        $dataSql = $LogisticsOrder->getTerminusList($params,$type,null,$add_time,$driver_id);
        $count = $dataSql->count();
        $pages = new Pagination(['totalCount' =>$count, 'pageSize' => Yii::$app->params['page_size']]);
        $orderList = $dataSql->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $orderList = $this->addGoodsInfo($orderList);
        $orderList = $LogisticsOrder->buttonType($orderList,2);
        return $this->render('list',
                [
                        'goods'=>$goods,
                        'params' => $params,
                        'LogisticsOrder'=>$LogisticsOrder,
                        'orderList'=>$orderList,
                        'driver_id'=>$driver_id,
                        'orderTime'=>$OrderTime,
                        'add_time' => $add_time,
                        'pages' => $pages,
                        'menus' => $this->_getMenus(),
                ]
                );
    }
    /**
     * 落地点我的订单
     * 靳健
     */
    public function actionMyself(){
        $goods = new Goods();
        $OrderTime = new OrderTime(['scenario' => 'search']);
        $LogisticsOrder = new LogisticsOrder();
        $orderPrintLog = new OrderPrintLog();
        //获取查询订单列表
        $type = 2;
        $add_time = $this->getAddTime(Yii::$app->request->get('OrderTime')['ruck_time']);
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('Goods')['goods_sn'];
        //分页
        $dataSql = $LogisticsOrder->getTerminusList($params,$type,null,$add_time,0,Yii::$app->request->get('OrderPrintLog')['terminus']);
        $count = $dataSql->count();
        $pages = new Pagination(['totalCount' =>$count, 'pageSize' => Yii::$app->params['page_size']]);
        $orderList = $dataSql->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $orderList = $this->addGoodsInfo($orderList);
        $orderList = $LogisticsOrder->stateButtonType($orderList,1);
        return $this->render('myself',
                [
                        'goods'=>$goods,
                        'params' => $params,
                        'LogisticsOrder'=>$LogisticsOrder,
                        'orderList'=>$orderList,
                        'pages'=>$pages,
                        'orderTime'=>$OrderTime,
                        'add_time' => $add_time,
                        'orderPrintLog' => $orderPrintLog,
                        'menus' => $this->_getMenus(),
                ]
                );
    }
    /**
     * 已完成订单
     * 靳健
     */
    public function actionOver(){
        $goods = new Goods();
        $OrderTime = new OrderTime(['scenario' => 'search']);
        $LogisticsOrder = new LogisticsOrder();
        //获取查询订单列表
        $type = 6;
        $add_time = $this->getAddTime(Yii::$app->request->get('OrderTime')['ruck_time']);
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('Goods')['goods_sn'];
        //分页
        $dataSql = $LogisticsOrder->getTerminusList($params,$type,null,$add_time);
        $count = $dataSql->count();
        $pages = new Pagination(['totalCount' =>$count, 'pageSize' => Yii::$app->params['page_size']]);
        $orderList = $dataSql->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $orderList = $this->addGoodsInfo($orderList);
        
        return $this->render('over',
                [
                        'goods'=>$goods,
                        'params' => $params,
                        'LogisticsOrder'=>$LogisticsOrder,
                        'orderList'=>$orderList,
                        'orderTime'=>$OrderTime,
                        'add_time' => $add_time,
                        'pages' => $pages,
                        'menus' => $this->_getMenus(),
                ]
                );
    }
    /**
     * 原返列表
     * 靳健
     */
    public function actionReturn(){
        $goods = new Goods();
        $OrderTime = new OrderTime(['scenario' => 'search']);
        $LogisticsOrder = new LogisticsOrder();
        $return = new LogisticsReturnOrder();
        //获取查询订单列表
        $type = 9;
        $add_time = $this->getAddTime(Yii::$app->request->get('OrderTime')['ruck_time']);
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('Goods')['goods_sn'];
        //分页
        $dataSql = $LogisticsOrder->getTerminusList($params,$type,null,$add_time);
        $count = $dataSql->count();
        $pages = new Pagination(['totalCount' =>$count, 'pageSize' => Yii::$app->params['page_size']]);
        $orderList = $dataSql->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $orderList = $this->addGoodsInfo($orderList);
        $orderList = $return->orderGetReturnId($orderList);
       
        return $this->render('return',
                [
                        'goods'=>$goods,
                        'params' => $params,
                        'LogisticsOrder'=>$LogisticsOrder,
                        'orderList'=>$orderList,
                        'orderTime'=>$OrderTime,
                        'add_time' => $add_time,
                        'pages' => $pages,
                        'menus' => $this->_getMenus(),
                ]
                );
    }
    /**
     * 落地点挂起
     * 靳健
     */
    public function actionAbnormal(){
        $goods = new Goods();
        $OrderTime = new OrderTime(['scenario' => 'search']);
        $LogisticsOrder = new LogisticsOrder();
        //获取查询订单列表
        $type = 3;
        $add_time = $this->getAddTime(Yii::$app->request->get('OrderTime')['ruck_time']);
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('Goods')['goods_sn'];
        //分页
        $dataSql = $LogisticsOrder->getTerminusList($params,$type,null,$add_time);
        $count = $dataSql->count();
        $pages = new Pagination(['totalCount' =>$count, 'pageSize' => Yii::$app->params['page_size']]);
        $orderList = $dataSql->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $orderList = $this->addGoodsInfo($orderList);
        return $this->render('abnormal',
                [
                        'goods'=>$goods,
                        'params' => $params,
                        'LogisticsOrder'=>$LogisticsOrder,
                        'orderList'=>$orderList,
                        'orderTime'=>$OrderTime,
                        'add_time' => $add_time,
                        'pages' => $pages,
                        'menus' => $this->_getMenus(),
                ]
                );
    }
    /**
     * 落地点司机挂起
     * 靳健
     */
    public function actionDriverAbnormal(){
        $goods = new Goods();
        $OrderTime = new OrderTime(['scenario' => 'search']);
        $LogisticsOrder = new LogisticsOrder();
        //获取查询订单列表
        $type = 8;
        $add_time = $this->getAddTime(Yii::$app->request->get('OrderTime')['ruck_time']);
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('Goods')['goods_sn'];
        //分页
        $dataSql = $LogisticsOrder->getTerminusList($params,$type,null,$add_time);
        $count = $dataSql->count();
        $pages = new Pagination(['totalCount' =>$count, 'pageSize' => Yii::$app->params['page_size']]);
        $orderList = $dataSql->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $orderList = $this->addGoodsInfo($orderList);
        return $this->render('driver-abnormal',
                [
                        'goods'=>$goods,
                        'params' => $params,
                        'LogisticsOrder'=>$LogisticsOrder,
                        'orderList'=>$orderList,
                        'orderTime'=>$OrderTime,
                        'add_time' => $add_time,
                        'pages' => $pages,
                        'menus' => $this->_getMenus(),
                ]
                );
    }
    /**
     * ajax
     * 落地点打印信息
     */
    public function actionGoodsPrint(){
        $LogisticsOrder = new LogisticsOrder();
        if($list = $LogisticsOrder->getTerminusList(array('logistics_sn'=>'','goods_sn'=>''),7,Yii::$app->request->post('order_arr'))){
           $list = $LogisticsOrder->getGoodsPrice($list,'terminus');
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
     * ajax
     * 站点打印
     * 靳健
     */
    public function actionGoodsPrintOther(){
        $LogisticsOrder = new LogisticsOrder();
        if($list = $LogisticsOrder->getTerminusList(array('logistics_sn'=>'','goods_sn'=>''),5,Yii::$app->request->post('order_arr'))){
           $list = $LogisticsOrder->getGoodsPrice($list,'terminus');
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
    /**
     * ajax
     * 落地点商品处理
     */
    public function actionGoodsEdit(){
    $goods = new Goods();
        if($goods->upGoodsState(Yii::$app->request->post('goods_id'))){
            $result = [
                'goods_id'=>Yii::$app->request->post('goods_id'),
                'error'=>0
            ];
            return json_encode($result);
        }
    }
    /**
     * ajax
     * 落地点订单状态修改
     */
    public function actionStateEdit(){
        $LogisticsOrder = new LogisticsOrder();
        if($LogisticsOrder->ajaxOrderStateEdit(Yii::$app->request->post('order_arr'))){
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
     * ajax
     * 订单状态修改
     * 靳健
     */
    public function actionOrderEdit(){
        $order = new LogisticsOrder();
        //调用修改订单状态函数
        if($order->ajaxOrderEdit([Yii::$app->request->post('order_id')])){
            $result = [
                    'error'=>0
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
     * ajax
     * 落地点二级状态修改
     * 靳健
     */
    public function actionLastEdit(){
        $order = new LogisticsOrder();
        //调用修改订单状态函数
        if($order->ajaxOrderEdit([Yii::$app->request->post('order_id')])){
            $data = $order::findOne(Yii::$app->request->post('order_id'));
            $returnButton = $order->chooseReturn($data->state);
            $result = [
                    'error'=>0,
                    'message'=>'处理成功',
                    'returnButton'=>$returnButton
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
     * ajax
     * 待收货批量处理
     * 靳健
     */
    public function actionAjaxAllOver(){
        $LogisticsOrder = new LogisticsOrder();
        if($LogisticsOrder->ajaxOrderEdit(Yii::$app->request->post('order_arr'))){
            $result = [
                    'error'=>0,
                    'message'=>'处理成功',
            ];
        }else{
            $result = [
                    'error'=>1,
                    'message'=>'处理失败'
            ];
        }
        return json_encode($result);
    }
    public function actionPay() {
        $model = new TerminusPay();
        $returnModel = new ReturnIncomeTerminus();
        
        $terminusUser = new TerminusUser();
        $info = $terminusUser->getById(array('user_id'=>Yii::$app->user->id));
        
        $params = Yii::$app->request->queryParams;
        $params['terminus_id'] = $info['terminus_id'];
        
        $orderList = $model->formatDetailsData($model->getLogisticsOrderList($params));
        if(!empty($orderList)) {
            $total = $orderList['total'];
            unset($orderList['total']);
        } else {
            $total = array(
                'all_amount' => 0,
                'finished_amount' => 0,
                'unfinished_amount' => 0,
                'freight_amount' => 0,
                'goods_amount' => 0,
            );
        }
        
        $returnOrderList = $returnModel->formatDetailsData($returnModel->getLogisticsOrderList($params));
        if(!empty($returnOrderList)) {
            $returnTotal = $returnOrderList['total'];
            unset($returnOrderList['total']);
        } else {
            $returnTotal = array(
                'all_amount' => 0,
                'finished_amount' => 0,
                'unfinished_amount' => 0,
                'freight_amount' => 0,
                'goods_amount' => 0,
            );
        }
        
        //分页
        $pages = new Pagination(['totalCount' =>count($orderList), 'pageSize' => Yii::$app->params['page_size']]);
        $orderList = array_slice($orderList, $pages->offset, $pages->limit);
        
        //分页
        $returnPages = new Pagination(['totalCount' =>count($returnOrderList), 'pageSize' => Yii::$app->params['page_size'], 'pageParam' => 'repage']);
        $returnOrderList = array_slice($returnOrderList, $returnPages->offset, $returnPages->limit);
        
        return $this->render('pay', [
            'searchModel' => new LogisticsOrderSearch(),
            'orderList' => $orderList,
            'returnOrderList' => $returnOrderList,
            'total' => $total,
            'returnTotal' => $returnTotal,
            'menus' => $this->_getMenus(),
            'pages' => $pages,
            'returnPages' => $returnPages
        ]);
    }
    
    public function actionFreight(){
        $model = new PayTerminus();
        $returnModel = new ReturnPayTerminus();
        $terminus_id = TerminusUser::findOne(['user_id'=>Yii::$app->user->id])->terminus_id;
        $params = Yii::$app->request->queryParams;
        $params['terminus_id'] = $terminus_id;
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
        $params = Yii::$app->request->queryParams;
        $params['terminus_id'] = $terminus_id;
        $returnOrderList = $returnModel->formatDetailsData($returnModel->getLogisticsOrderList($params));
        if(!empty($returnOrderList)) {
            $returnTotal = $returnOrderList['total'];
            unset($returnOrderList['total']);
        } else {
            $returnTotal = array(
                'all_amount' => 0,
                'finished_amount' => 0,
                'unfinished_amount' => 0,
            );
        }
        
        $bankModel = empty(BankInfo::getBankInfoByTerminus($terminus_id))?null:BankInfo::getBankInfoByTerminus($terminus_id);
        
        //分页
        $pages = new Pagination(['totalCount' =>count($orderList), 'pageSize' => Yii::$app->params['page_size']]);
        $orderList = array_slice($orderList, $pages->offset, $pages->limit);
        
        //分页
        $returnPages = new Pagination(['totalCount' =>count($returnOrderList), 'pageSize' => Yii::$app->params['page_size'], 'pageParam' => 'repage']);
        $returnOrderList = array_slice($returnOrderList, $returnPages->offset, $returnPages->limit);
        
        return $this->render('freight', [
            'bankModel' => $bankModel,
            'searchModel' => new LogisticsOrderSearch(),
            'orderList' => $orderList,
            'returnOrderList' => $returnOrderList,
            'total' => $total,
            'returnTotal' => $returnTotal,
            'menus' => $this->_getMenus(),
            'pages' => $pages,
            'returnPages' => $returnPages
        ]);
    }
    
    /**
     * 订单备注保存
     * @return number[]|string[]
     */
    public function actionOrderRemark() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params['order_id'] = Yii::$app->request->post('orderId');
        $params['content'] = Yii::$app->request->post('content');
        
        $terminusUser = new TerminusUser();
        $info = $terminusUser->getById(array('user_id'=>Yii::$app->user->id));
        $params['terminus_id'] = $info['terminus_id'];
        
        $orderRemark = OrderRemark::findOne($params['order_id']);
        if(!$orderRemark) {
            $orderRemark = new OrderRemark();
        }
        $result = $orderRemark->addTerminusRemark($params);
        if($result) {
            return ['code' => 200, 'msg' => '保存成功'];
        } else {
            return ['code' => 300, 'msg' => '保存失败'];
        }
    }
    
    /**
     * 返货订单备注保存
     * @return number[]|string[]
     */
    public function actionReturnOrderRemark() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params['order_id'] = Yii::$app->request->post('orderId');
        $params['content'] = Yii::$app->request->post('content');
        
        $terminusUser = new TerminusUser();
        $info = $terminusUser->getById(array('user_id'=>Yii::$app->user->id));
        $params['terminus_id'] = $info['terminus_id'];
        
        $returnOrderRemark= ReturnOrderRemark::findOne($params['order_id']);
        if(!$returnOrderRemark) {
            $returnOrderRemark= new ReturnOrderRemark();
        }
        $result = $returnOrderRemark->addTerminusRemark($params);
        if($result) {
            return ['code' => 200, 'msg' => '保存成功'];
        } else {
            return ['code' => 300, 'msg' => '保存失败'];
        }
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
            'index' => ['menu' => '/terminus/index', 'item' => '/terminus/index'],
            'myself' => ['menu' => '/terminus/index', 'item' => '/terminus/myself'],
            'abnormal' => ['menu' => '/terminus/index', 'item' => '/terminus/abnormal'],
            'list' => ['menu' => '/terminus/index', 'item' => '/terminus/index'],
            'driver-abnormal' => ['menu' => '/terminus/index', 'item' => '/terminus/driver-abnormal'],
            'over' => ['menu' => '/terminus/index', 'item' => '/terminus/over'],
            'return' => ['menu' => '/terminus/index', 'item' => '/terminus/return'],
            'pay' => ['menu' => '/terminus/pay', 'item' => false],
            'freight' => ['menu' => '/terminus/freight', 'item' => false],
        );
        
        return $arr[Yii::$app->controller->action->id];
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