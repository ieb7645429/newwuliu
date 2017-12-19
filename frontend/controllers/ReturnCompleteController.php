<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use common\models\LogisticsReturnOrder;
use common\models\LogisticsReturnOrderSearch;
use common\models\LogisticsOrder;
use common\models\ReturnGoods;
use mdm\admin\components\MenuHelper;
use yii\data\Pagination;
use backend\models\ReturnIncomeDealer;
use common\models\User;
use backend\models\ReturnOrderRemark;

class ReturnCompleteController extends \yii\web\Controller
{
/**
     * 已退货订单列表
     * 靳健
     */
    public function actionIndex(){
        $returnGoods = new ReturnGoods();
        $return = new LogisticsReturnOrder(['scenario' => 'search']);
        $order_status = 50;//订单显示状态
        $abnormal = 2;//未挂起;
        $identity = $this->_getIdentity();//获取账户身份
        $add_time = $this->getAddTime(Yii::$app->request->get('LogisticsReturnOrder')['add_time']);
        $where = $return->getReturnWhere(Yii::$app->params['returnOrderStateDriver'],$abnormal,$add_time);
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
                        'params'=>$params,
                        'add_time'=>$add_time,
                        'orderList'=>$orderList,
                        'pages' => $pages,
                        'menus' => $this->_getMenus(),
                        'identity' => $identity,
                        'check_count' => $this->_CookieClear($return->returnList($params,$where),'obj'),
                        'order_arr' => $this->_GetOrderArr(),
                ]
                );
    }
    /**
     * 退货完成列表
     * 靳健
     */
    public function actionReturnOk(){
        $returnGoods = new ReturnGoods();
        $return = new LogisticsReturnOrder();
        $where = ['and','order_state ='.$order_status,'state = 2',['or','return_type=1','shipping_type=1']];
        //参数
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsReturnOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('ReturnGoods')['goods_sn'];
        $orderList = $return->returnList($params,$where);
        return $this->render('return-ok',
                [
                        'returnGoods'=>$returnGoods,
                        'return'=>$return,
                        'orderList'=>$orderList,
                        'menus' => $this->_getMenus(),
                ]
                );
    }
    /**
     * 退货完成列表
     * 靳健
     */
    public function actionReturnOver(){
        $returnGoods = new ReturnGoods();
        $user = new User();
        $return = new LogisticsReturnOrder(['scenario' => 'search']);
        $order_status = 70;//订单显示状态
        $abnormal = 2;//未挂起;
        $identity = $this->_getIdentity();//获取账户身份
        $add_time = $this->getAddTime(Yii::$app->request->get('LogisticsReturnOrder')['add_time']);
        $where = $return->getReturnWhere(Yii::$app->params['returnOrderStateDelivery'],$abnormal,$add_time,'collection_time',['or','logistics_return_order.state = 1','return_type=2','shipping_type=2','shipping_type=3']);
        //参数
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsReturnOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('ReturnGoods')['goods_sn'];
        $params['identity'] = $identity;
        $dataSql = $return->returnList($params,$where);
        $count = $dataSql->count();
        $pages = new Pagination(['totalCount' =>$count, 'pageSize' => Yii::$app->params['page_size']]);
        $orderList = $dataSql->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $orderList = $this->_putInOrder($orderList,$order_status);
        $orderList = $this->getSender($orderList);
        return $this->render('return-over',
                [
                        'returnGoods'=>$returnGoods,
                        'return'=>$return,
                        'orderList'=>$orderList,
                        'params'=>$params,
                        'add_time'=>$add_time,
                        'pages' => $pages,
                        'menus' => $this->_getMenus(),
                        'identity' => $identity,
                        'check_count' => $this->_CookieClear(null,'none'),
                        'order_arr' => $this->_GetOrderArr(),
                ]
                );
    }
    /**
     * ajax
     * 退款订单状态修改
     * 靳健
     */
    public function actionReturnOrderEdit(){
        $return = new LogisticsReturnOrder();
        $returnOrderRemark = new ReturnOrderRemark();
        try{
            $tr = Yii::$app->db->beginTransaction();
            if($return->upOrderState(Yii::$app->request->post('order_id'))&&$returnOrderRemark->addSender(Yii::$app->request->post())){
                $result = ['error'=>0,'message'=>'处理成功'];
            }else{
                $result = ['message'=>'处理失败','error'=>1];
            }
            $tr -> commit();
        }catch(Exception $e){
            $tr->rollBack();
            $result = ['message'=>'处理失败','error'=>1];
        }
        
        return json_encode($result);
    }
    /**
     * 退货打印
     * 靳健
     */
    public function actionReturnPrint(){
        $order = new LogisticsOrder();
        $returnGoods = new ReturnGoods();
        $return = new LogisticsReturnOrder();
        $cookies = Yii::$app->request->cookies->get('checkbox');
        $order_arr = explode('-',$cookies);
        $where = ['in','logistics_return_order.order_id',$order_arr];
        $orderList = $return->returnList(array(),$where)->asArray()->all();
        $orderList = $order->getGoodsPrice($orderList,'return');
        if($orderList){
            $result = [
                    'error'=>0,
                    'data'=>$orderList
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
        $model = new ReturnIncomeDealer();
        
        $params = Yii::$app->request->queryParams;
        $params['return_manage_id'] = Yii::$app->user->id;
        
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
            'searchModel' => new LogisticsReturnOrderSearch(),
            'orderList' => $orderList,
            'total' => $total,
            'menus' => $this->_getMenus(),
            'pages' => $pages
        ]);
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
            'index' => ['menu' => '/return-complete/index', 'item' => '/return-complete/index'],
            'return-over' => ['menu' => '/return-complete/index', 'item' => '/return-complete/return-over'],
            'pay' => ['menu' => '/return-complete/pay', 'item' => false],
        );
        
        return $arr[Yii::$app->controller->action->id];
    }
    /**
     * 获取送货人
     */
    private function getSender($orderList){
       
        if(!empty($orderList)){
            $returnOrderRemark = new ReturnOrderRemark();
            foreach($orderList as $key => $value){
                $orderList[$key]['sender'] = empty($returnOrderRemark::findOne($value['order_id']))?'':$returnOrderRemark::findOne($value['order_id'])->sender;
            }
        }
        return $orderList;
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
        $orderList = $return->buttonType($orderList,1);//商品是否处理按钮
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
