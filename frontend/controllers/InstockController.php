<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\models\LogisticsOrder;
use common\models\LogisticsReturnOrder;
use common\models\ReturnGoods;
use common\models\Area;
use mdm\admin\components\MenuHelper;
use yii\data\Pagination;

class InstockController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $returnGoods = new ReturnGoods();
        $area = new Area();
        $return = new LogisticsReturnOrder(['scenario' => 'search']);
        $order_status = 30;//显示订单状态
        $abnormal = 2;//未挂起;
        $identity = $this->_getIdentity();//获取账户身份
        $add_time = $this->getAddTime(Yii::$app->request->get('LogisticsReturnOrder')['add_time']);
        $cityWhere = empty(Yii::$app->request->get('LogisticsReturnOrder')['member_cityid'])?' 1 ':'member_cityid = '.Yii::$app->request->get('LogisticsReturnOrder')['member_cityid'];
        $where = $return->getReturnWhere($order_status,$abnormal,$add_time,'add_time',$cityWhere);
        //参数
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsReturnOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('ReturnGoods')['goods_sn'];
        $params['identity'] = $identity;
        $dataSql = $return->returnList($params,$where);
        $count = $dataSql->count();
        $pages = new Pagination(['totalCount' =>$count, 'pageSize' => Yii::$app->params['page_size']]);
        $orderList = $dataSql->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $orderList = $this->_putInOrder($orderList,$order_status);
        
        return $this->render('index',
                [
                        'returnGoods'=>$returnGoods,
                        'return'=>$return,
                        'orderList'=>$orderList,
			            'params'=>$params,
                        'add_time'=>$add_time,
                        'pages' => $pages,
                        'cityList' => $area->getRegion(6),
                        'count' => $count,
                        'city_id' => Yii::$app->request->get('LogisticsReturnOrder')['member_cityid'],
                        'identity' => $identity,
                        'menus' => $this->_getMenus(),
                ]
                );
    }
    /**
     * 入库历史订单
     * 靳健
     */
    public function actionHistoryList(){
        $returnGoods = new ReturnGoods();
        $area = new Area();
        $return = new LogisticsReturnOrder(['scenario' => 'search']);
        $order_status = 50;//显示订单状态
        $abnormal = 2;//未挂起;
        $identity = $this->_getIdentity();//获取账户身份
        $add_time = $this->getAddTime(Yii::$app->request->get('LogisticsReturnOrder')['add_time']);
        $cityWhere = empty(Yii::$app->request->get('LogisticsReturnOrder')['member_cityid'])?' 1 ':'member_cityid = '.Yii::$app->request->get('LogisticsReturnOrder')['member_cityid'];
        $where = $return->getReturnWhere($order_status,$abnormal,$add_time,'add_time',['and','storage_id ='.Yii::$app->user->id,$cityWhere]);
        //参数
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsReturnOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('ReturnGoods')['goods_sn'];
        $params['identity'] = $identity;
        $dataSql = $return->returnList($params,$where);
        $count = $dataSql->count();
        $pages = new Pagination(['totalCount' =>$count, 'pageSize' => Yii::$app->params['page_size']]);
        $orderList = $dataSql->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $orderList = $this->_putInOrder($orderList,$order_status);
        return $this->render('history-list',
                [
                        'returnGoods'=>$returnGoods,
                        'return'=>$return,
                        'orderList'=>$orderList,
                        'params'=>$params,
                        'add_time'=>$add_time,
                        'pages' => $pages,
                        'cityList' => $area->getRegion(6),
                        'count' => $count,
                        'city_id' => Yii::$app->request->get('LogisticsReturnOrder')['member_cityid'],
                        'identity' => $identity,
                        'menus' => $this->_getMenus(),
                ]
                );
    }
    /**
     * ajax
     * 退款商品状态修改
     * 靳健
     */
    public function actionReturnEdit(){
        $returnGoods = new ReturnGoods();
        if($returnGoods->upGoodsState(Yii::$app->request->post('goods_id'))){
            $result = [
                    'error'=>0
            ];
        }else{
            $result = [
                    'message'=>'处理失败',
                    'error'=>1
            ];
        }
        return json_encode($result);
    }
    
    
    public function actionAbnormal()
    {
        $returnGoods = new ReturnGoods();
        $area = new Area();
        $return = new LogisticsReturnOrder(['scenario' => 'search']);
        $order_status = 30;//显示订单状态
        $abnormal = 1;//未挂起;
        $identity = $this->_getIdentity();//获取账户身份
        $add_time = $this->getAddTime(Yii::$app->request->get('LogisticsReturnOrder')['add_time']);
        $cityWhere = empty(Yii::$app->request->get('LogisticsReturnOrder')['member_cityid'])?' 1 ':'member_cityid = '.Yii::$app->request->get('LogisticsReturnOrder')['member_cityid'];
        $where = $return->getReturnWhere($order_status,$abnormal,$add_time,'add_time',$cityWhere);
        //参数
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsReturnOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('ReturnGoods')['goods_sn'];
        $params['identity'] = $identity;
        $dataSql = $return->returnList($params,$where);
        $count = $dataSql->count();
        $pages = new Pagination(['totalCount' =>$count, 'pageSize' => Yii::$app->params['page_size']]);
        $orderList = $dataSql->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $orderList = $this->_putInOrder($orderList,$order_status);
    
        return $this->render('abnormal',
                [
                        'returnGoods'=>$returnGoods,
                        'return'=>$return,
                        'orderList'=>$orderList,
                        'params'=>$params,
                        'add_time'=>$add_time,
                        'pages' => $pages,
                        'cityList' => $area->getRegion(6),
                        'count' => $count,
                        'city_id' => Yii::$app->request->get('LogisticsReturnOrder')['member_cityid'],
                        'identity' => $identity,
                        'menus' => $this->_getMenus(),
                ]
                );
    }
    /**
     * ajax
     * 商品打印
     * 靳健
     */
    public function actionGoodsPrint(){
        $order = new LogisticsOrder();
        $return = new LogisticsReturnOrder();
        $where = ['and','order_state'=>Yii::$app->params['returnOrderStateEmployee'],['in','logistics_return_order.order_id',Yii::$app->request->post('order_arr')]];
        if($list = $return->orderPrint(Yii::$app->request->post('order_sn'),Yii::$app->request->post('goods_sn'),$where,'list')){
            $list = $order->getGoodsPrice($list,'return');
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
     * 订单状态修改
     * 靳健
     */
    public function actionOrderEdit(){
        $return = new LogisticsReturnOrder();
        $tr = Yii::$app->db->beginTransaction();
        try{
            $res = $return->ajaxReturnOrderEdit(Yii::$app->request->post('order_arr'),30);
            if($res===false){
                throw new Exception('处理失败', '1');
            }
            $result = ['error'=>0,'message'=>'处理成功'];
            $tr -> commit();
        }catch(Exception $e){
            $tr->rollBack();
            $result = ['error'=>1,'message'=>'处理失败'];
        }
        return json_encode($result);
    }
    
    /**
     * 订单异常恢复
     */
    public function actionRecoverEdit(){
        $returnOrder = new LogisticsReturnOrder();
        if($returnOrder->recoverOrder(Yii::$app->request->post('order_id'),30)){
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
     * 商品批量扫码处理
     */
    public function actionGoodsBatchEdit(){
        $model = new LogisticsReturnOrder();
        if($model->batchGoodsEdit(30,Yii::$app->request->post('order_arr'))){
            $result = [
                    'code'=>200,
                    'message'=>'处理成功',
            ];
        }else{
            $result = [
                    'code'=>400,
                    'message'=>'处理失败',
            ];
        }
        return json_encode($result);
    }
    
    /**
     * 获取退货组对应order_type  
     * 分拣中心 order_type = 0
     */
    private function _getIdentity(){
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        switch ($role)
        {
            case '西部退货组':
                return 1;
                break;
            case '瑞胜退货组':
                return 3;
                break;
            case '塔湾退货组':
                return 4;
                break;
            default:
                return 0;
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
            $add_time['end'] = strtotime($end)+60*60*24;
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
            'index' => ['menu' => '/instock/index', 'item' => '/instock/index'],
            'history-list' => ['menu' => '/instock/index', 'item' => '/instock/history-list'],
            'abnormal' => ['menu' => '/instock/index', 'item' => '/instock/abnormal'],
        );
        
        return $arr[Yii::$app->controller->action->id];
    }
    
    /**
     * 整理订单显示内容
     * @param unknown $orderList
     * @param $order_status 显示订单状态
     */
    private function _putInOrder($orderList,$order_status){
        $return = new LogisticsReturnOrder();
        $orderList = $this->addReturnGoods($orderList);//添加商品
        $orderList = $return->returnCompleteButton($orderList,1);//判断按钮显示
        $orderList = $return->buttonType($orderList,2);//商品是否处理按钮
        $orderList = $return->isTreatment($orderList,50);//checkbox是否选中
        return $orderList;
    }
    
    private function addReturnGoods($orderList){
        if(!empty($orderList)){
            foreach($orderList as $key=>$value){
                $orderList[$key]['returnGoods'] = $this->getGoodsInfo($value['order_id']);
            }
        }
        return $orderList;
    }
    //获取商品信息
    private function getGoodsInfo($order_id){
        $goods = new ReturnGoods();
        $goodsList = $goods->find()->where(['order_id'=>$order_id])->asArray()->all();
        return $goodsList;
    }
}
