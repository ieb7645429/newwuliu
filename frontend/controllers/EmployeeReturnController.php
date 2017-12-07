<?php

namespace frontend\controllers;

use Yii;
use common\models\LogisticsOrder;
use common\models\UserBalance;
use common\models\Terminus;
use common\models\LogisticsRoute;
use common\models\Goods;
use common\models\LogisticsOrderSearch;
use common\models\OrderTime;
use common\models\BuyInfo;
use common\models\BuyOut;
use common\models\GoodsInfo;
use common\models\Area;
use common\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Exception;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use mdm\admin\components\MenuHelper;
use common\models\BalanceLog;
use backend\models\IncomeEmployee;
use backend\models\ReturnIncomeEmployee;
use yii\data\Pagination;

/**
 * LogisticsOrderController implements the CRUD actions for LogisticsOrder model.
 */
class EmployeeReturnController extends Controller
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
     * Lists all LogisticsOrder models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LogisticsOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $type = '';
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus(),
            'indexOver'=>$type
        ]);
    }
    /**
     * Lists all LogisticsOrder models.
     * @return mixed
     */
    public function actionIndexOver()
    {
        $searchModel = new LogisticsOrderSearch();
        $type = 'over';
        $dataProvider = $searchModel->returnSearch(Yii::$app->request->queryParams,$type);
    
        return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'menus' => $this->_getMenus(),
                'indexOver'=>$type
        ]);
    }
    
    public function actionIndexReturn(){
        $searchModel = new LogisticsOrderSearch();
        $type = 'return';
        $dataProvider = $searchModel->returnSearch(Yii::$app->request->queryParams,$type);
        return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'menus' => $this->_getMenus(),
                'indexOver'=>$type
        ]);
    }

    /**
     * Displays a single LogisticsOrder model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        return $this->render('view', [
            'model' => $this->findModel($id),
            'menus' => $this->_getMenus(),
            'print' => Yii::$app->request->get('print'),
            'role' => $role,
        ]);
    }

    /**
     * Creates a new LogisticsOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new LogisticsOrder();
        $modelGoods = new Goods();
        $modelLogisticsRoute = new LogisticsRoute();
        $modelTerminus = new Terminus();
        $modelBuyOut = new BuyOut();
        $modelOrderTime = new OrderTime();
        $modelBuyInfo = new BuyInfo();
        $modelGoodsInfo = new GoodsInfo();
        $user = new User();
        $modelUserBalance = new UserBalance();
        if ($model->load(Yii::$app->request->post())) {
            try {
                $tr = Yii::$app->db->beginTransaction();
                $type = 'logistics';//录入类型
                $model->fillLogisticsInfo($model, $type, Yii::$app->request->post('User')['username']);//填补物流信息
                if($model == false){
                    throw new Exception('用户注册失败', '400');
                }
                $model->employee_id = Yii::$app->user->id;
                $r1= $model->save();
                $modelBuyInfo->addBuyInfo($model);
                if(!$r1){
                    throw new Exception('订单生成失败', '401');
                }
                $goodsSn = $modelGoods->getGoodsSn(Yii::$app->request->post()['LogisticsOrder']['logistics_route_id']);//生成货号
                $modelGoods->addGoodsInfo($model->order_id, $goodsSn, Yii::$app->request->post()['LogisticsOrder']['goods_num']);//增加商品表
                $model->logistics_sn = $model->getLogisticsSn($model->order_id);//票号
                $model->goods_sn =$goodsSn.'_'.$model->goods_num;//货号
                if($model->collection ==1) {//判断是否代收
                    $r3 = $modelBuyOut->addBuyOutInfo($model);//增加买断信息
                    if(!$r3) {
                        throw new Exception('买断信息添加失败', '403');
                    }
                }
                $terminusId= ArrayHelper::getValue(Yii::$app->request->post('LogisticsOrder'), 'terminus_id', 0);
                $model->orderInfo($model, $terminusId);
                $model->save();
                $modelOrderTime->order_id=$model->order_id;
                $modelOrderTime->price_time=time();
                if(Yii::$app->request->post()['GoodsInfo']['name'] && Yii::$app->request->post()['GoodsInfo']['number'] && Yii::$app->request->post()['GoodsInfo']['price']){
                	$r4 = $modelGoodsInfo->addGoodsInfo($model->order_id, Yii::$app->request->post()['GoodsInfo']['name'], Yii::$app->request->post()['GoodsInfo']['number'], Yii::$app->request->post()['GoodsInfo']['price']);
                	if(!$r4)
                	{
                		throw new Exception('商品详细信息添加失败', '404');
                	}
                }
                $modelOrderTime->save();//增加订单时间表
                if($model->order_sn && $$model->order_type == 5)
                {
                	$res =$model->editYoujianOrderState(array('orderSn'=>$model->order_sn));//修改友件网订单状态
                	if($res === false)
                	{
                		throw new Exception('商品详细信息添加失败', '404');
                	}
                }
                $tr -> commit();
                return $this->redirect(['view','id' => $model->order_id,'print'=>1]);
            } catch (Exception $e) {
                $tr->rollBack();
                Yii::$app->getSession()->setFlash('error', '保存失败,信息不全');

                $logisticsRouteInfo = $modelLogisticsRoute->getLogisticsRoute();
                $terminusInfo = $modelTerminus->getTerminus();
                $area = new Area();
                return $this->render('create', [
                    'model' => $model,
                    'area' => $area,
                    'user' => $user,
                    'logisticsRouteInfo' => $logisticsRouteInfo,
                    'terminus' => $terminusInfo,
                    'menus' => $this->_getMenus(),
                ]);
            }
        } else {
            $logisticsRouteInfo = $modelLogisticsRoute->getLogisticsRoute();
            $terminusInfo = $modelTerminus->getTerminus();
            $area = new Area();
            return $this->render('create', [
                'model' => $model,
                'area' => $area,
                'user' => $user,
                'logisticsRouteInfo' => $logisticsRouteInfo,
                'terminus' => $terminusInfo,
                'menus' => $this->_getMenus(),
            ]);
        }
    }

    /**
     * Updates an existing LogisticsOrder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            try {
                $tr = Yii::$app->db->beginTransaction();
                $modelGoods = new Goods();
                $goodsSn = $modelGoods->getGoodsSn(Yii::$app->request->post()['LogisticsOrder']['logistics_route_id']);//生成货号
                $model->goods_sn = $goodsSn;
                if ($model->getOldAttribute('order_state') == 5) {
                    $this->_updateMemberOrder($model);
                } else {
                    $this->_updateOrder($model);
                }
                $model->goods_sn = $goodsSn.'_'.$model->goods_num;
                $model->employee_id = Yii::$app->user->id;
                $terminusId= ArrayHelper::getValue(Yii::$app->request->post('LogisticsOrder'), 'terminus_id', 0);
                $model->orderInfo($model, $terminusId);
                $model->save();
                $tr->commit();
                return $this->redirect(['view', 'id' => $model->order_id]);
            } catch (Exception $e) {
                $tr->rollBack();
            }
        } else {
            $modelLogisticsRoute = new LogisticsRoute();
            $modelTerminus = new Terminus();
            //$user = new User();
            if($model->logistics_route_id)
            {
            	$logisticsRouteInfo = $modelLogisticsRoute->getLogisticsRouteData($model->logistics_route_id);
            }else{
                if($model->receiving_areaid==0){
                    $city_id = $model->receiving_cityid;
                    $city_type = 1;
                }else{
                    $city_id = $model->receiving_areaid;
                    $city_type = 2;
                }
            	$logisticsRouteInfo = $modelLogisticsRoute->getLogisticsRoute($city_id,$city_type);
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
            
            $user = User::findOne($model->member_id);
            return $this->render('update', [
                'model' => $model,
                'user' => $user,
                'logisticsRouteInfo' => $logisticsRouteInfo,
                'terminus' => $terminusInfo,
                'area' => new Area(),
                'menus' => $this->_getMenus(),
            ]);
        }
    }
    /**
     * ajax
     * 订单标签tag打印
     * 靳健
     */
    public function actionOrderPrint(){
        $LogisticsOrder = new LogisticsOrder();
        $list = $LogisticsOrder->tagOrderPrint(Yii::$app->request->post('order_id'));
        return json_encode($list);
    }

    /**
     * @author 暴闯
     * @desc ajax 取得发件人信息
     * @return json
     */
    public function actionMemberInfo() {
        $user = new User();
        if(!empty(Yii::$app->request->post('phone'))){
            $where = ['member_phone'=>Yii::$app->request->post('phone')];
        }else if(!empty(Yii::$app->request->post('name'))){
            $where = ['username'=>Yii::$app->request->post('name')];
        }
        $userInfo = $user->getMemberInfo($where);
        $return = array();
        if($userInfo) {
            $return['username'] = $userInfo->username;
            $return['member_phone'] = $userInfo->member_phone;
            $return['user_truename'] = $userInfo->user_truename;
            $return['member_cityid'] = $userInfo->member_cityid;
            $return['member_areainfo'] = $userInfo->member_areainfo;
        }else{
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['code' => 400];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['code' => 200, 'msg' => '成功', 'datas' => $return];
    }
    /**
     * ajax
     * 发货更新城市获取路线信息
     * 靳健
     */
    public function actionRouteOption(){
        $route = new LogisticsRoute();
        $terminus = new Terminus();
        $route_str = $route->ajaxLogisticsRoute(Yii::$app->request->post('city_id'));
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $route_str;
    }
    
    /**
     * ajax
     * 发货更新线路对应落地点信息
     * 朱鹏飞
     */
    public function actionTerminusOption()
    {
    	$terminus = new Terminus();
    	if(Yii::$app->request->post('route_id')==0||LogisticsRoute::findOne(Yii::$app->request->post('route_id'))->same_city==1){
    	    $result['sameCity'] = 1;
    	}else{
    	    $result['sameCity'] = 2;
    	}
    	$terminus_str = $terminus->ajaxTerminus(Yii::$app->request->post('route_id'));
    	$result['terminus_str'] = $terminus_str;
    	Yii::$app->response->format = Response::FORMAT_JSON;
    	return $result;
    }
    
    public function actionPay() {
        $model = new IncomeEmployee();
        $returnModel = new ReturnIncomeEmployee();
        
        $params = Yii::$app->request->queryParams;
        $params['employee_id'] = Yii::$app->user->id;
        
        $orderList = $model->formatDetailsData($model->getLogisticsOrderList($params));
        $returnOrderList = $returnModel->formatDetailsData($returnModel->getLogisticsOrderList($params));
        
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
            'menus' => $this->_getMenus(),
            'pages' => $pages,
            'returnPages' => $returnPages,
        ]);
    }

    /**
     * 营业员更新用户下的运单
     * @param unknown $model
     */
    private function _updateMemberOrder(&$model) {
    	$goods = new Goods();
    	$modelUserBalance = new UserBalance();
        $goods->addGoodsInfo($model->order_id, $model->goods_sn, Yii::$app->request->post('LogisticsOrder')['goods_num']);//增加商品表
        //判断是否代收
        if($model->collection ==1) {
            $modelBuyOut = new BuyOut();
            //增加买断信息
            $result = $modelBuyOut->addBuyOutInfo($model);
            if(!$result) {
                throw new Exception();
            }
        }
        // 修改订单状态
        if(!$model -> setOrderState($model)){
            throw new Exception();
        }
    }

    /**
     * 营业员更新运单
     * @param unknown $model
     */
    private function _updateOrder(&$model) {
        // 商品数量变化
        if ($model->getOldAttribute('goods_num') != $model->goods_num) {
            // 增加商品表
            $modelGoods = new Goods();
            $modelGoods->delGoodsByOrderId($model->order_id);
            $goodsSn = $modelGoods->getGoodsSn(Yii::$app->request->post()['LogisticsOrder']['logistics_route_id']);//生成货号
            $modelGoods->addGoodsInfo($model->order_id, $model->goods_sn, Yii::$app->request->post('LogisticsOrder')['goods_num']);
        }

        // 代收变化
        if ($model->getOldAttribute('collection') != $model->collection) {
            $buyOut = new BuyOut();
            if($model->getOldAttribute('collection') == 1) {
                $buyOut -> deleteByOrderId($model->order_id);
                $model -> state = 2;
            }
            if($model->collection == 1) {
                //增加买断信息
                $result = $buyOut->addBuyOutInfo($model);
                if(!$result) {
                    throw new Exception();
                }
            }
        }
    }

    /**
     * Deletes an existing LogisticsOrder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
            'index-over' => ['menu' => '/employee-return/index-over', 'item' => '/employee-return/index-over'],
            'index-return' => ['menu' => '/employee-return/index-over', 'item' => '/employee-return/index-return'],
            'view' => ['menu' => '/employee-return/index-over', 'item' => false],
        );

        return $arr[Yii::$app->controller->action->id];
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
}