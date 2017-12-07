<?php

namespace frontend\modules\dl\controllers;

use Yii;
use yii\web\Controller;
use frontend\modules\dl\models\LogisticsReturnOrder;
use frontend\modules\dl\models\LogisticsReturnOrderSearch;
use frontend\modules\dl\models\LogisticsOrder;
use frontend\modules\dl\models\ReturnGoods;
use mdm\admin\components\MenuHelper;
use yii\data\Pagination;
use backend\modules\dl\models\ReturnIncomeDealer;
use frontend\modules\dl\models\User;
use backend\modules\dl\models\ReturnOrderRemark;

class ReturnCompleteController extends \yii\web\Controller
{
/**
     * 已退货订单列表
     * 靳健
     */
    public function actionIndex(){
        $returnGoods = new ReturnGoods();
        $return = new LogisticsReturnOrder(['scenario' => 'search']);
        $add_time = $this->getAddTime(Yii::$app->request->get('LogisticsReturnOrder')['add_time']);
        $where = $return->getReturnWhere(Yii::$app->params['returnOrderStateDriver'],$add_time);
        $orderList = $return->returnList(Yii::$app->request->get('LogisticsReturnOrder')['logistics_sn'],\Yii::$app->request->get('ReturnGoods')['goods_sn'],$where);
        //分页
        $pages = new Pagination(['totalCount' =>count($orderList), 'pageSize' => Yii::$app->params['page_size']]);
        $model = array_slice($orderList,$pages->offset,$pages->limit);
        return $this->render('index',
                [
                        'returnGoods'=>$returnGoods,
                        'return'=>$return,
                        'order_sn'=>Yii::$app->request->get('LogisticsReturnOrder')['logistics_sn'],
                        'goods_sn'=>Yii::$app->request->get('ReturnGoods')['goods_sn'],
                        'add_time'=>$add_time,
                        'orderList'=>$model,
                        'pages' => $pages,
                        'menus' => $this->_getMenus(),
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
        $where = ['and','order_state ='.Yii::$app->params['returnOrderStateDelivery'],'state = 2',['or','return_type=1','shipping_type=1']];
        $orderList = $return->returnList(Yii::$app->request->get('LogisticsReturnOrder')['logistics_sn'],\Yii::$app->request->get('ReturnGoods')['goods_sn'],$where);
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
        $add_time = $this->getAddTime(Yii::$app->request->get('LogisticsReturnOrder')['add_time']);
        $where = $return->getReturnWhere(Yii::$app->params['returnOrderStateDelivery'],$add_time,'collection_time',['or','logistics_return_order.state = 1','return_type=2','shipping_type=2','shipping_type=3']);
        $orderList = $return->returnList(Yii::$app->request->get('LogisticsReturnOrder')['logistics_sn'],\Yii::$app->request->get('ReturnGoods')['goods_sn'],$where);
        //分页
        $pages = new Pagination(['totalCount' =>count($orderList), 'pageSize' => Yii::$app->params['page_size']]);
        $model = array_slice($orderList,$pages->offset,$pages->limit);
        $model = $this->getSender($model);
        return $this->render('return-over',
                [
                        'returnGoods'=>$returnGoods,
                        'return'=>$return,
                        'orderList'=>$model,
                        'order_sn'=>Yii::$app->request->get('LogisticsReturnOrder')['logistics_sn'],
                        'goods_sn'=>Yii::$app->request->get('ReturnGoods')['goods_sn'],
                        'add_time'=>$add_time,
                        'pages' => $pages,
                        'menus' => $this->_getMenus(),
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
        $order_arr = Yii::$app->request->post('order_arr');
        $where = ['and','order_state ='.Yii::$app->params['returnOrderStateDriver'],['in','logistics_return_order.order_id',$order_arr]];
        $orderList = $return->returnList(Yii::$app->request->post('LogisticsReturnOrder')['logistics_sn'],\Yii::$app->request->post('ReturnGoods')['goods_sn'],$where);
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
            'index' => ['menu' => '/dl/return-complete/index', 'item' => '/dl/return-complete/index'],
            'return-over' => ['menu' => '/dl/return-complete/index', 'item' => '/dl/return-complete/return-over'],
            'pay' => ['menu' => '/dl/return-complete/pay', 'item' => false],
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

}
