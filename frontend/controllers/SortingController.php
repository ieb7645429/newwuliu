<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\ArrayHelper;
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
                        'check_count' => $this->_CookieClear($return->returnList($params,$where),'obj'),
                        'order_arr' => $this->_GetOrderArr(),
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
                        'check_count' => $this->_CookieClear($return->returnList($params,$where),'obj'),
                        'order_arr' => $this->_GetOrderArr(),
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
        $cookies = Yii::$app->request->cookies->get('checkbox');
        $order_arr = explode('-',$cookies);
        try{
            $res = $return->ajaxReturnOrderEdit($order_arr,Yii::$app->request->post('order_state'));
            if($res===false||empty($order_arr)){
                throw new Exception('处理失败', '1');
            }
            $checkbox = Yii::$app->request->cookies->get('checkbox');
            Yii::$app->response->cookies->remove($checkbox);
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
        $cookies = Yii::$app->request->cookies->get('checkbox');
        $order_arr = explode('-',$cookies);
        if($model->batchGoodsEdit(10,$order_arr)&&!empty($order_arr)){
//             $checkbox = Yii::$app->request->cookies->get('checkbox');
//             Yii::$app->response->cookies->remove($checkbox);
            $result = [
                    'code'=>200,
                    'message'=>'处理成功',
                    'data'=>$order_arr,
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
