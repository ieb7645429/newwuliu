<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\models\LogisticsReturnOrder;
use common\models\ReturnGoods;
use common\models\Area;
use mdm\admin\components\MenuHelper;
use yii\data\Pagination;

class SortingController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $returnGoods = new ReturnGoods();
        $area = new Area();
        $return = new LogisticsReturnOrder(['scenario' => 'search']);
        $order_status = 10;//显示订单状态
        $abnormal = 2;//未挂起;
        $add_time = $this->getAddTime(Yii::$app->request->get('LogisticsReturnOrder')['add_time']);
        $cityWhere = empty(Yii::$app->request->get('LogisticsReturnOrder')['member_cityid'])?' 1 ':'member_cityid = '.Yii::$app->request->get('LogisticsReturnOrder')['member_cityid'];
        $where = $return->getReturnWhere($order_status,$abnormal,$add_time,'add_time',$cityWhere);
        //参数
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsReturnOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('ReturnGoods')['goods_sn'];
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
                        'menus' => $this->_getMenus(),
                ]
                );
    }
    
    public function actionIndexSecond()
    {
        $returnGoods = new ReturnGoods();
        $area = new Area();
        $return = new LogisticsReturnOrder(['scenario' => 'search']);
        $order_status = 20;//显示订单状态
        $abnormal = 2;//未挂起;
        $add_time = $this->getAddTime(Yii::$app->request->get('LogisticsReturnOrder')['add_time']);
        $cityWhere = empty(Yii::$app->request->get('LogisticsReturnOrder')['member_cityid'])?' 1 ':'member_cityid = '.Yii::$app->request->get('LogisticsReturnOrder')['member_cityid'];
        $where = $return->getReturnWhere($order_status,$abnormal,$add_time,'add_time',$cityWhere);
        //参数
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsReturnOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('ReturnGoods')['goods_sn'];
        $dataSql = $return->returnList($params,$where);
        $count = $dataSql->count();
        $pages = new Pagination(['totalCount' =>$count, 'pageSize' => Yii::$app->params['page_size']]);
        $orderList = $dataSql->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $orderList = $this->_putInOrder($orderList,$order_status);
        
        return $this->render('index-second',
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
                        'menus' => $this->_getMenus(),
                ]);
    }
    
    public function actionAbnormal(){
        $returnGoods = new ReturnGoods();
        $area = new Area();
        $return = new LogisticsReturnOrder(['scenario' => 'search']);
        $order_status = 10;//显示订单状态
        $abnormal = 1;//未挂起;
        $add_time = $this->getAddTime(Yii::$app->request->get('LogisticsReturnOrder')['add_time']);
        $cityWhere = empty(Yii::$app->request->get('LogisticsReturnOrder')['member_cityid'])?' 1 ':'member_cityid = '.Yii::$app->request->get('LogisticsReturnOrder')['member_cityid'];
        $where = $return->getReturnWhere($order_status,$abnormal,$add_time,'add_time',$cityWhere);
        //参数
        $params['logistics_sn'] = Yii::$app->request->get('LogisticsReturnOrder')['logistics_sn'];
        $params['goods_sn'] = Yii::$app->request->get('ReturnGoods')['goods_sn'];
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
                        'menus' => $this->_getMenus(),
                ]);
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
            $res = $return->ajaxReturnOrderEdit(Yii::$app->request->post('order_arr'),Yii::$app->request->post('order_state'));
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
    
    /**
     * 订单异常恢复
     */
    public function actionRecoverEdit(){
        $returnOrder = new LogisticsReturnOrder();
        if($returnOrder->recoverOrder(Yii::$app->request->post('order_id'),10)){
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
        if($model->batchGoodsEdit(10,Yii::$app->request->post('order_arr'))){
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
    /**
     * 整理订单显示内容
     * @param unknown $orderList
     * @param $order_status 显示订单状态
     */
    private function _putInOrder($orderList,$order_status){
        $return = new LogisticsReturnOrder();
        $orderList = $this->addReturnGoods($orderList);//添加商品 、订单类型
        $orderList = $return->returnCompleteButton($orderList,1);//判断按钮显示
        $orderList = $return->buttonType($orderList,1);//商品是否处理按钮
        $orderList = $return->isTreatment($orderList,30);//checkbox是否选中
        return $orderList;
    }
    
    
    private function _getActiveMenu() {
        $arr = array(
                'index' => ['menu' => '/sorting/index', 'item' => '/sorting/index'],
                'index-second' => ['menu' => '/sorting/index', 'item' => '/sorting/index-second'],
                'abnormal' => ['menu' => '/sorting/index', 'item' => '/sorting/abnormal'],
        );
    
        return $arr[Yii::$app->controller->action->id];
    }
    private function addReturnGoods($orderList){
        if(!empty($orderList)){
            foreach($orderList as $key=>$value){
                $orderList[$key]['returnGoods'] = $this->getGoodsInfo($value['order_id']);
                if($value['order_type']==1){
                    $orderList[$key]['order_type_name'] = '西部';
                }
                if($value['order_type']==3){
                    $orderList[$key]['order_type_name'] = '瑞胜';
                }
                if($value['order_type']==4){
                    $orderList[$key]['order_type_name'] = '塔湾';
                }
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
