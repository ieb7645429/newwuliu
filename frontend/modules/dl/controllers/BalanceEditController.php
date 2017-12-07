<?php

namespace frontend\modules\dl\controllers;
use Yii;
use frontend\modules\dl\models\LogisticsOrder;
use frontend\modules\dl\models\LogisticsOrderSearch;
use mdm\admin\components\MenuHelper;
use backend\modules\dl\models\OrderRemark;
use yii\helpers\ArrayHelper;
use frontend\modules\dl\models\Terminus;
use frontend\modules\dl\models\LogisticsRoute;
use frontend\modules\dl\models\LogisticsLines;
use frontend\modules\dl\models\Area;
use frontend\modules\dl\models\StatisticalOrder;

class BalanceEditController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $searchModel = new LogisticsOrderSearch();
        $dataProvider = $searchModel->BalanceEditSearch(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus(),
        ]);
    }
    
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
       
        $now_price = $model->goods_price;
        $order = new LogisticsOrder();
        $orderRemark = new OrderRemark();
        $modelStatisticalOrder = new StatisticalOrder();
        if($model->load(Yii::$app->request->post())) {
            try {
                $tr = Yii::$app->db->beginTransaction();
                $post = Yii::$app->request->post('LogisticsOrder');
                $result_validate = $this->validate($model,$post);
                if($result_validate['code']==400){
                    Yii::$app->getSession()->setFlash('error', $result_validate['message']);
                    return $this->redirect(['index']);
                }
                
                $collection_result = $order->buyOutHandle($model);//代收修改买断处理
                if(!$collection_result){
                    Yii::$app->getSession()->setFlash('error', '买断信息处理失败');
                    return $this->redirect(['index']);
                }
                $re = $order->balanceEdit($model,intval($now_price),$post);
                $remark = $orderRemark->addEditRemark(['order_id'=>$id,'edit_content'=>Yii::$app->request->post('OrderRemark')['edit_content']]);
                if(!$re||!$remark){
                    Yii::$app->getSession()->setFlash('error', '修改失败');
                    return $this->redirect(['index']);
                }
                if(!empty(Yii::$app->request->post()['OrderSn'])){
                    $OrderSn = array_filter(Yii::$app->request->post()['OrderSn']);
                    if(!empty($OrderSn) && count($OrderSn)>0){
                        $new_order_sn = implode(',',Yii::$app->request->post()['OrderSn']);
                        $model->order_sn  = serialize($new_order_sn);
                        $model->orderInfo($model, $model->terminus_id);
                        $ress = $model->save();
                        if(!$ress){
                            Yii::$app->getSession()->setFlash('error', '订单编号添加失败！');
                            return $this->redirect(['index']);
                        }
                    }
                    if($model->order_sn)
                    {
                        //$OrderSn = explode(',',str_replace('，',',',Yii::$app->request->post()['OrderSn']));
                        foreach($OrderSn as $key => $val){
                            if(!empty($val)){
                                $res =$model->editYoujianOrderState(array('orderSn'=>$val));//修改友件网订单状态
                                if($res === '206')
                                {
                                    Yii::$app->getSession()->setFlash('error', '友件网信息错误！');
                                    return $this->redirect(['index']);
                                }
                                if($res === '204')
                                {
                                    Yii::$app->getSession()->setFlash('error', '友件网订单已发货！');
                                    return $this->redirect(['index']);
                                }
                                if($res === '205')
                                {
                                    Yii::$app->getSession()->setFlash('error', '友件网订单已完成！');
                                    return $this->redirect(['index']);
                                }
                            }
                            	
                        }
                    }
                 }
                $modelStatisticalOrder->edit($model->logistics_sn,$model);
                Yii::$app->getSession()->setFlash('success', '修改成功');
                $tr->commit();
                return $this->redirect(['index']);
            } catch (Exception $e) {
                $tr->rollBack();
                Yii::$app->getSession()->setFlash('error', '修改失败');
                return $this->redirect(['index']);
            }
        } else {
            //路线相关
            $modelLogisticsRoute = new LogisticsRoute();
            $modelTerminus = new Terminus();
            //$user = new User();
            if($model->logistics_route_id)
            {
                $logisticsRouteInfo = $modelLogisticsRoute->getLogisticsRouteData($model->logistics_route_id);
            }else{
                $logisticsRouteInfo = $modelLogisticsRoute->getLogisticsRoute($model->receiving_cityid);
            }
            foreach ($logisticsRouteInfo as $k => $v)
            {
                break;
            }
            if($model->terminus_id)
            {
                $terminusInfo = $modelTerminus->Terminus($model->terminus_id);
            }else{
                $terminusInfo = $modelTerminus->getCityTerminus($k);
            }
            $markModel = $orderRemark->findOne($id);
            return $this->render('update',
                    [
                        'goods_price' => $model->goods_price,
                        'order'=>$order,'model'=>$model,
                        'markModel'=>$markModel,
                        'orderRemark'=>$orderRemark,
                        'menus' => $this->_getMenus(),
                        'logisticsRouteInfo' => $logisticsRouteInfo,
                        'terminus' => $terminusInfo,
                        'areaName' => Area::getAreaNameById($model->receiving_areaid?$model->receiving_areaid:$model->receiving_cityid),
                            
                    ]);
        }
    }
    
    public function actionDelete($id){
        $model = $this->findModel($id);
        $order = new LogisticsOrder();
        $modelStatisticalOrder = new StatisticalOrder();
        try {
            $tr = Yii::$app->db->beginTransaction();
            $goods_price = 0;
            $re = $order->balanceDel($model);
            if(!$re){
                Yii::$app->getSession()->setFlash('error', '删除失败');
                return $this->redirect(['index']);
            }
            $modelStatisticalOrder->del($model->logistics_sn);
            if($model->order_state>10){
                $lineModel = new LogisticsLines();
                $lineModel -> delLines($model->order_id);
            }
            Yii::$app->getSession()->setFlash('success', '删除成功');
            $tr->commit();
            return $this->redirect(['index']);
        } catch (Exception $e) {
            $tr->rollBack();
            Yii::$app->getSession()->setFlash('error', '删除失败');
            return $this->redirect(['index']);
        }
        
    }
    
    
    
    /**
     * Finds the LogisticsOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LogisticsOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LogisticsOrder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
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
                'index' => ['menu' => '/dl/balance-edit/index', 'item' => '/dl/balance-edit/index'],
                'update' => ['menu' => '/dl/balance-edit/index', 'item' => false],
        );
    
        return $arr[Yii::$app->controller->action->id];
    }
    
    private function validate($model,$post){
        if(intval($post['goods_price'])<0||intval($post['freight'])<0||intval($post['make_from_price'])<0){
            return ['code'=>400,'message'=>'输入金额不能小于零'];
        }
        if($model->order_state>10&&LogisticsRoute::findOne($model->getOldAttribute('logistics_route_id'))->same_city!=LogisticsRoute::findOne($post['logistics_route_id'])->same_city){
            return ['code'=>400,'message'=>'封车后只允许修改同城路线'];
        }
        
    }

}
