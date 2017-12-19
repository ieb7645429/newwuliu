<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\ArrayHelper;
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
                        'check_count' => $this->_CookieClear($return->returnList($params,$where),'obj'),
                        'order_arr' => $this->_GetOrderArr(),
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
                        'check_count' => $this->_CookieClear($return->returnList($params,$where),'obj'),
                        'order_arr' => $this->_GetOrderArr(),
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
        $cookies = Yii::$app->request->cookies->get('checkbox');
        $order_arr = explode('-',$cookies);
        $where = ['and','order_state'=>Yii::$app->params['returnOrderStateEmployee'],['in','logistics_return_order.order_id',$order_arr]];
        if($list = $return->orderPrint('','',$where,'list')){
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
        $cookies = Yii::$app->request->cookies->get('checkbox');
        $order_arr = explode('-',$cookies);
        try{
            $res = $return->ajaxReturnOrderEdit($order_arr,30);
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
        $cookies = Yii::$app->request->cookies->get('checkbox');
        $order_arr = explode('-',$cookies);
        if($model->batchGoodsEdit(30,$order_arr)&&!empty($order_arr)){
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
