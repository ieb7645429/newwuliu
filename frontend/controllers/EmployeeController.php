<?php

namespace frontend\controllers;

use Yii;
use common\models\LogisticsOrder;
use common\models\LogisticsOrderDelete;
use common\models\LogisticsOrderEdit;
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
use common\models\StatisticalOrder;
use common\models\OrderPriceEditLog;
use backend\models\OrderRemark;
use common\models\LogisticsLines;
use common\models\DateNum;

/**
 * LogisticsOrderController implements the CRUD actions for LogisticsOrder model.
 */
class EmployeeController extends Controller
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
        $statisticalOrder = new StatisticalOrder();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        //统计代码
        if(empty(Yii::$app->request->queryParams['LogisticsOrderSearch'])){//无搜索条件
            $count = $statisticalOrder->getEmployeeCount();
        }else{//有搜索条件
            $countModel = $searchModel->search(Yii::$app->request->queryParams);
            $count['order_num'] = $searchModel->getEmployeeOrderNum($countModel);
            $count['goods_num'] = $searchModel->getEmployeeGoodsNum($countModel);
            $count['price'] = $searchModel->getEmployeePrice($countModel);
            $count['price_count'] = $searchModel->getEmployeePriceCount($searchModel->search(Yii::$app->request->queryParams));
            $count['same_city_order'] = $searchModel->getEmployeeSameCityOrder($countModel);
            $count['same_city_goods'] = $searchModel->getEmployeeSameCityGoods($countModel);
            $count['same_city_price'] = $searchModel->getEmployeeSameCityPrice($countModel);
            $count['same_city_price_count'] = $searchModel->getEmployeeSameCityPriceCount($searchModel->search(Yii::$app->request->queryParams));
        }

        $type = '';
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus(),
            'indexOver'=>$type,
            'count' => $count,
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
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$type);
    
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
        $modelStatisticalOrder = new StatisticalOrder();
        $user = new User();
        $modelUserBalance = new UserBalance();
        $orderRemark = new OrderRemark();
        $modelDateNum= new DateNum();
        if ($model->load(Yii::$app->request->post())) {
            try {
            	$tr = Yii::$app->db->beginTransaction();
            	$tr2 = Yii::$app->db2->beginTransaction();
            	$addTime = $model->find()
            	->where(['employee_id'=>Yii::$app->user->id])
            	->max('add_time');
            	if(time()-$addTime < 10)
            	{
            		throw new Exception('同一用户不可连续生成订单', '408');
            	}
                $type = 'logistics';//录入类型
                $res1 = $model->fillLogisticsInfo($model, $type, Yii::$app->request->post('User')['username']);//填补物流信息
                if($res1 === false){
                    throw new Exception('请使用已注册的会员号', '400');
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
                $OrderSn = array_filter(Yii::$app->request->post()['OrderSn']);
				if(!empty($OrderSn) && count($OrderSn)>0){
					$new_order_sn = implode(',',Yii::$app->request->post()['OrderSn']);
			     	$model->order_sn  = serialize($new_order_sn);
				}
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
                if($model->order_sn)
                {
                   //$OrderSn = explode(',',str_replace('，',',',Yii::$app->request->post()['OrderSn']));
				   foreach($OrderSn as $key => $val){
					 if(!empty($val)){
						 $res =$model->editYoujianOrderState(array('orderSn'=>$val));//修改友件网订单状态
						 if($res === '206')
						 {
							throw new Exception('友件网信息错误！', '405');
						 }
						 if($res === '204')
						 {
						 	throw new Exception('友件网订单已发货！', '406');
						 }
						 if($res === '205')
						 {
						 	throw new Exception('友件网订单已完成！', '407');
						 }
					 }
					
				   } 
                }
                $orderRemark->addEditRemark(['order_id'=>$model->order_id,'edit_content'=>Yii::$app->request->post('OrderRemark')['edit_content']]);
                $modelStatisticalOrder->add($model);
                $tr -> commit();
                $tr2-> commit();
                return $this->redirect(['view','id' => $model->order_id,'print'=>1]);
            } catch (Exception $e) {
                $tr->rollBack();
                $tr2->rollBack();
                if ($e->getCode() == '405' || $e->getCode() == '400' || $e->getCode() == '406' || $e->getCode() == '407' || $e->getCode() == '408') {
                    Yii::$app->getSession()->setFlash('error', $e->getMessage());
                } else {
                    Yii::$app->getSession()->setFlash('error', '保存失败,信息不全');
                }

                $logisticsRouteInfo = $modelLogisticsRoute->getLogisticsRoute();
                $terminusInfo = $modelTerminus->getTerminus();
                $area = new Area();
                return $this->render('create', [
                    'model' => $model,
                    'area' => $area,
                    'user' => $user,
                    'logisticsRouteInfo' => $logisticsRouteInfo,
                    'terminus' => $terminusInfo,
                    'areaName' => '沈阳市',
                    'orderRemark' => $orderRemark,
                    'menus' => $this->_getMenus(),
                ]);
            }
        } else {
            $roles = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id));
            if (in_array('瑞胜开单员', $roles)) {
                $model->order_type = 3;
            }elseif(in_array('塔湾开单员', $roles))
            {
                $model->order_type = 4;
            }
            $logisticsRouteInfo = $modelLogisticsRoute->getLogisticsRoute();
            $terminusInfo = $modelTerminus->getTerminus();
            $area = new Area();
            return $this->render('create', [
                'model' => $model,
                'area' => $area,
                'user' => $user,
                'logisticsRouteInfo' => $logisticsRouteInfo,
                'terminus' => $terminusInfo,
                'areaName' => '沈阳市',
                'orderRemark' => $orderRemark,
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
                $modelStatisticalOrder = new StatisticalOrder();
                $orderRemark = new OrderRemark();
                $orderEdit = new LogisticsOrderEdit();
                $modelBuyInfo = new BuyInfo();
                
                
                
                
                
                //$goodsSn = $modelGoods->getGoodsSn(Yii::$app->request->post()['LogisticsOrder']['logistics_route_id'], $model->add_time);//生成货号
                $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
                //封车后代收款改变记录log
                if($model->getOldAttribute('goods_price')!=$model->goods_price&&$model->order_state>10){
                    $orderPriceEditLog = new OrderPriceEditLog();
                    $params = ['model'=>$model,'before_amount'=>$model->getOldAttribute('goods_price'),'after_amount'=>$model->goods_price];
                    $orderPriceEditLog->addOrderPriceEditLog($params);
                }
                //封车后同城外阜不可相互修改
                if($model->order_state>10&&$this->_isSameCity($model->getOldAttribute('logistics_route_id'))!=$this->_isSameCity($model->logistics_route_id)){
                    Yii::$app->getSession()->setFlash('error', '同城外阜路线不可相互修改!');
                    return $this->redirect(['update','id'=>$id]);
                }
                //线上订单
                $OrderSn = array_filter(Yii::$app->request->post('OrderSn',array()));
                if(!empty($OrderSn) && count($OrderSn)>0){
                    $new_order_sn = implode(',',Yii::$app->request->post('OrderSn'));
                    $model->order_sn  = serialize($new_order_sn);
                }
                if($model->order_sn)
                {
                    //$OrderSn = explode(',',str_replace('，',',',Yii::$app->request->post()['OrderSn']));
                    foreach($OrderSn as $key => $val){
                        if(!empty($val)){
                            $res =$model->editYoujianOrderState(array('orderSn'=>$val));//修改友件网订单状态
                            if($res === '206')
                            {
                                throw new Exception('友件网信息错误！', '405');
                            }
                            if($res === '204')
                            {
                                throw new Exception('友件网订单已发货！', '406');
                            }
                            if($res === '205')
                            {
                                throw new Exception('友件网订单已完成！', '407');
                            }
                        }
                        	
                    }
                }
                
                
                if ($model->getOldAttribute('order_state') == 5) {
                    $this->_updateMemberOrder($model);
                } else {
                    $this->_updateOrder($model);
                }
                
                if(empty($model->employee_id)){
                     $model->employee_id = Yii::$app->user->id;
                }else{
                    //订单封车后只允许大开单员修改
                    if($model->order_state>10&&$role!=Yii::$app->params['roleEmployeeDelete']){
                        Yii::$app->getSession()->setFlash('error', '订单已封车,不可修改！');
                        return $this->redirect(['index']);
                    }
                }
                
                $user_model = User::findOne(['username'=>Yii::$app->request->post('User')['username']]);
                if(empty($user_model)){//判断修改的会员账号是否存在
                    Yii::$app->getSession()->setFlash('error', '会员号不存在!');
                    return $this->redirect(['update','id'=>$id]);
                }else{
                    $model->member_id = $user_model->id;
                }
                $terminusId= ArrayHelper::getValue(Yii::$app->request->post('LogisticsOrder'), 'terminus_id', 0);
                $model->orderInfo($model, $terminusId);
                
                //修改订单log记录
                $orderEdit->addOrderEditLog($model,['edit_content'=>Yii::$app->request->post('OrderRemark')['edit_content']]);
                $modelBuyInfo->addBuyInfo($model);
                $model->save();
                //备注
                $orderRemark->addEditRemark(['order_id'=>$id,'edit_content'=>Yii::$app->request->post('OrderRemark')['edit_content']]);
                
                $tr->commit();
                $modelStatisticalOrder->edit($model->logistics_sn,$model);
                return $this->redirect(['view', 'id' => $model->order_id]);
            } catch (Exception $e) {
                $tr->rollBack();
            }
        } else {
            $modelLogisticsRoute = new LogisticsRoute();
            $modelTerminus = new Terminus();
            $orderRemark = new OrderRemark();
            
            //$user = new User();
//             if($model->logistics_route_id)
//             {
//             	$logisticsRouteInfo = $modelLogisticsRoute->getLogisticsRouteData($model->logistics_route_id);
//             }else{
                if($model->receiving_areaid==0){
                    $city_id = $model->receiving_cityid;
                    $city_type = 1;
                }else{
                    $city_id = $model->receiving_areaid;
                    $city_type = 2;
                }
            	$logisticsRouteInfo = $modelLogisticsRoute->getLogisticsRoute($city_id,$city_type);
//             }
            foreach ($logisticsRouteInfo as $k => $v)
            {
            	break;
            }
//             if($model->terminus_id)
//             {
//             	$terminusInfo = $modelTerminus->Terminus($model->terminus_id);
//             }else{
                $terminusInfo = $modelTerminus->getCityTerminus($model->logistics_route_id);
//             }
            
            $user = User::findOne($model->member_id);
            return $this->render('update', [
                'model' => $model,
                'user' => $user,
                'logisticsRouteInfo' => $logisticsRouteInfo,
                'orderRemark'=>empty($orderRemark::findOne($id))?$orderRemark:$orderRemark::findOne($id),
                'terminus' => $terminusInfo,
                'area' => new Area(),
                'areaName' => Area::getAreaNameById($model->receiving_areaid?$model->receiving_areaid:$model->receiving_cityid),
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
		else if(!empty(Yii::$app->request->post('smallname'))){
            $where = ['small_num'=>Yii::$app->request->post('smallname')];
        }
		else if(!empty(Yii::$app->request->post('membername'))){
			if(is_numeric(Yii::$app->request->post('membername'))){
		      $where = ['id'=>Yii::$app->request->post('membername')];
			}
			else{
			  $where = ['user_truename'=>Yii::$app->request->post('membername')];
			}
		}
        $userInfo = $user->getMemberInfo($where);
        $return = array();
        if($userInfo) {
            $return['username'] = $userInfo->username;
            $return['member_phone'] = $userInfo->member_phone;
            $return['user_truename'] = $userInfo->user_truename;
            $return['member_cityid'] = $userInfo->member_cityid;
            $return['member_areainfo'] = $userInfo->member_areainfo;
			$return['small_num'] = $userInfo->small_num;
			$return['id'] = $userInfo->id;
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
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $route = new LogisticsRoute();
        $route_str = $route->ajaxLogisticsRoute(Yii::$app->request->post('city_id'), Yii::$app->request->post('city_type'));
        
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
            'returnPages' => $returnPages,
        ]);
    }

    /**
     * 营业员更新用户下的运单
     * @param unknown $model
     */
    private function _updateMemberOrder(&$model) {
    	$goods = new Goods();
    	$goodsSn = $goods->getGoodsSn($model->logistics_route_id);
    	$modelStatisticalOrder = new StatisticalOrder();
    	$modelUserBalance = new UserBalance();
    	$goods->addGoodsInfo($model->order_id, $goodsSn, Yii::$app->request->post('LogisticsOrder')['goods_num']);//增加商品表
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
        if($model->order_state == 5)
        {
        	$modelStatisticalOrder->add($model);
        }
    }

    /**
     * 营业员更新运单
     * @param unknown $model
     */
    private function _updateOrder(&$model) {
        // 商品数量变化
        if ($model->getOldAttribute('goods_num') != $model->goods_num||$model->getOldAttribute('logistics_route_id') != $model->logistics_route_id) {
            // 增加商品表
            $arr = explode('-', $model->goods_sn);
            $arr1= explode('_',$arr['2']);
            $modelLogisticsRoute= new LogisticsRoute();
            $logisticsRouteName = $modelLogisticsRoute->findOne($model->logistics_route_id);
            $goodsSn = $arr['0'].'-'.$logisticsRouteName->logistics_route_code.$logisticsRouteName->logistics_route_no.'-'.$arr1['0'];
            $modelGoods = new Goods();
            $modelGoods->delGoodsByOrderId($model->order_id);
//             $goodsSn = $modelGoods->getGoodsSn(Yii::$app->request->post()['LogisticsOrder']['logistics_route_id']);//生成货号
            $modelGoods->addGoodsInfo($model->order_id, $goodsSn, Yii::$app->request->post('LogisticsOrder')['goods_num']);
            $model->goods_sn = $goodsSn.'_'.$model->goods_num;
            //订单状态恢复初始
            $model->order_state = 10;
            $model->driver_member_id = 0;
            if($model->state==5) $model->state = 1;
            if($model->state==6) $model->state = 2;
            //删除lines
            $modelLines = new LogisticsLines();
            $modelLines->delLines($model->order_id);
        }

        // 代收变化
        if ($model->getOldAttribute('collection') != $model->collection) {
            $buyOut = new BuyOut();
            if($model->getOldAttribute('collection') == 1) {
                $buyOut -> deleteByOrderId($model->order_id);
                $model -> state = 2;
                $model -> goods_price = 0;//不买断代收款改为0
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
        $order = new LogisticsOrder();
        $model = $order::findOne($id);
        $modelStatisticalOrder = new StatisticalOrder();
        try{
            $tr = Yii::$app->db->beginTransaction();
            $result = $order->orderDelete($model);
            if($result===false){
                Yii::$app->getSession()->setFlash('error', '删除失败');
                return $this->redirect(['index']);
            }
            Yii::$app->getSession()->setFlash('success', '删除成功');
            $modelStatisticalOrder->del($model->logistics_sn);
            $tr -> commit();
        }catch (Exception $e){
            Yii::$app->getSession()->setFlash('error', '删除失败');
            $tr->rollBack();
        }

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
            'index' => ['menu' => '/employee/index', 'item' => '/employee/index'],
            'index-over' => ['menu' => '/employee/index', 'item' => '/employee/index-over'],
            'create' => ['menu' => '/employee/index', 'item' => false],
            'view' => ['menu' => '/employee/index', 'item' => false],
            'pay' => ['menu' => '/employee/pay', 'item' => false],
        );
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        if($role==Yii::$app->params['roleEmployeeDelete']){
            $arr['update'] = ['menu' => '/balance-edit/index', 'item' => false];
        }else{
            $arr['update'] = ['menu' => '/employee/index', 'item' => false];
        }

        return $arr[Yii::$app->controller->action->id];
    }
    /**
     * 判断路线是否为同城
     * @param unknown $route_id
     */
    private function _isSameCity($route_id){
        return LogisticsRoute::findOne($route_id)->same_city;
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
     * $logisticOrder=>$statisticalOrder
     * @Author:冯欢
     */
    public function actionTran()
    {
        set_time_limit(0);
        $model = new LogisticsOrder();
        $Goods = new Goods();
        $StatisticalOrder = new StatisticalOrder();
        $datas = $model->find()->asArray()->all();
        foreach ($datas as $data)
        {
        	$info = array();
        	$info = $StatisticalOrder->find()->where(['logistics_sn'=>$data['logistics_sn']])->one();
        	if(!empty($info)){
        		$info->collection = $data['collection'] == 1?1:0;
        		$info->goods_num = $data['goods_num'];
        		$info->save();
        	}
        	unset($info);
        }
        echo 'ok';
//         ini_set('memory_limit','512M');

//         $logisticOrder = (new \yii\db\Query())->from('logistics_order')->all();
//         $statisticalOrder = Yii::$app->db->createCommand();
//         foreach($logisticOrder as $key => $value){
//             $save = $statisticalOrder->insert('statistical_order',[
//                 'logistics_sn' => $value['logistics_sn'],
//                 'add_time' => $value['add_time'],
//                 'freight' => $value['freight'],
//                 'goods_price' => $value['goods_price'],
//                 'shipping_sale' => $value['shipping_sale'],
//                 'same_city' => $value['same_city'],
//                 'terminus_id' => $value['terminus_id'],
//                 'logistics_route_id' => $value['logistics_route_id'],
//             ])->execute();
//             if(!$save){
//                 echo $key.'==='.$value.'插入失败!!';die;
//             }

//         }

//         echo 'ok';
    }
	 /**
     * @author 小雨
     * @desc ajax 获取发货人列表
     * @return json
     */
    public function actionMemberNameInfo() {
        $user = new User();
        if(!empty(Yii::$app->request->post('member_name'))){
			$where = ['like', 'user_truename',Yii::$app->request->post('member_name')] ;
		}
        $userInfo = $user->getMemberInfoAll($where);
        $return = array();
        if($userInfo) {
            $return = $userInfo;
        }else{
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['code' => 400];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['code' => 200, 'msg' => '成功', 'datas' => $return];
    }

}

