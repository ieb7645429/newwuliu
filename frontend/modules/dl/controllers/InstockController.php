<?php

namespace frontend\modules\dl\controllers;

use Yii;
use yii\web\Controller;
use frontend\modules\dl\models\LogisticsOrder;
use frontend\modules\dl\models\LogisticsReturnOrder;
use frontend\modules\dl\models\ReturnGoods;
use frontend\modules\dl\models\Area;
use mdm\admin\components\MenuHelper;
use yii\data\Pagination;

class InstockController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $returnGoods = new ReturnGoods();
        $area = new Area();
        $return = new LogisticsReturnOrder(['scenario' => 'search']);
        $add_time = $this->getAddTime(Yii::$app->request->get('LogisticsReturnOrder')['add_time']);
        $cityWhere = empty(Yii::$app->request->get('LogisticsReturnOrder')['member_cityid'])?' 1 ':'member_cityid = '.Yii::$app->request->get('LogisticsReturnOrder')['member_cityid'];
        $where = $return->getReturnWhere(Yii::$app->params['returnOrderStateEmployee'],$add_time,$cityWhere);
        $orderList = $return->returnList(Yii::$app->request->get('LogisticsReturnOrder')['logistics_sn'],\Yii::$app->request->get('ReturnGoods')['goods_sn'],$where);
        $orderList = $return->isTreatment($orderList,Yii::$app->params['returnOrderStateEmployee']);
        //分页
        $pages = new Pagination(['totalCount' =>count($orderList), 'pageSize' => Yii::$app->params['page_size']]);
        $model = array_slice($orderList,$pages->offset,$pages->limit);
        return $this->render('index',
                [
                        'returnGoods'=>$returnGoods,
                        'return'=>$return,
                        'orderList'=>$model,
			            'order_sn'=>Yii::$app->request->get('LogisticsReturnOrder')['logistics_sn'],
			            'goods_sn'=>Yii::$app->request->get('ReturnGoods')['goods_sn'],
                        'add_time'=>$add_time,
                        'pages' => $pages,
                        'cityList' => $area->getRegion(6),
                        'count' => count($orderList),
                        'city_id' => Yii::$app->request->get('LogisticsReturnOrder')['member_cityid'],
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
        $add_time = $this->getAddTime(Yii::$app->request->get('LogisticsReturnOrder')['add_time']);
        $cityWhere = empty(Yii::$app->request->get('LogisticsReturnOrder')['member_cityid'])?' 1 ':'member_cityid = '.Yii::$app->request->get('LogisticsReturnOrder')['member_cityid'];
        $where = $return->getReturnWhere(Yii::$app->params['returnOrderStateDriver'],$add_time,['and','storage_id ='.Yii::$app->user->id,$cityWhere]);
        $orderList = $return->returnList(Yii::$app->request->get('LogisticsReturnOrder')['logistics_sn'],\Yii::$app->request->get('ReturnGoods')['goods_sn'],$where);
        //分页
        $pages = new Pagination(['totalCount' =>count($orderList), 'pageSize' => Yii::$app->params['page_size']]);
        $model = array_slice($orderList,$pages->offset,$pages->limit);
        return $this->render('history-list',
                [
                        'returnGoods'=>$returnGoods,
                        'return'=>$return,
                        'orderList'=>$model,
                        'order_sn'=>Yii::$app->request->get('LogisticsReturnOrder')['logistics_sn'],
                        'goods_sn'=>Yii::$app->request->get('ReturnGoods')['goods_sn'],
                        'add_time'=>$add_time,
                        'pages' => $pages,
                        'cityList' => $area->getRegion(6),
                        'count' => count($orderList),
                        'city_id' => Yii::$app->request->get('LogisticsReturnOrder')['member_cityid'],
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
            $res = $return->ajaxReturnOrderEdit(Yii::$app->request->post('order_arr'));
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
            'index' => ['menu' => '/dl/instock/index', 'item' => '/dl/instock/index'],
            'history-list' => ['menu' => '/dl/instock/index', 'item' => '/dl/instock/history-list'],
        );
        
        return $arr[Yii::$app->controller->action->id];
    }
}
