<?php

namespace frontend\modules\hlj\controllers;

use Yii;
use frontend\modules\hlj\models\LogisticsOrder;
use frontend\modules\hlj\models\LogisticsOrderDelete;
use frontend\modules\hlj\models\LogisticsLines;
use frontend\modules\hlj\models\LogisticsOrderEdit;
use frontend\modules\hlj\models\LogisticsRoute;
use frontend\modules\hlj\models\LogisticsOrderSearch;
use frontend\modules\hlj\models\Area;
use frontend\modules\hlj\models\User;
use frontend\modules\hlj\models\BuyInfo;
use frontend\modules\hlj\models\BuyOut;
use frontend\modules\hlj\models\Goods;
use frontend\modules\hlj\models\OrderTime;
use frontend\modules\hlj\models\Terminus;
use frontend\modules\hlj\models\GoodsInfo;
use frontend\modules\hlj\models\StatisticalOrder;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Exception;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use mdm\admin\components\MenuHelper;
use yii\data\Pagination;
use frontend\modules\hlj\models\OrderRemark;
use common\models\UserAll;
use frontend\modules\hlj\models\UserConfig;

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
        $userConfig = new UserConfig();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if (Yii::$app->request->get('download_type', '0')) {
            return $this->_downloadExcel($dataProvider);
        }
        //统计代码
        $countModel = $searchModel->search(Yii::$app->request->queryParams);
        $count['order_num'] = $searchModel->getEmployeeOrderNum($countModel);
        $count['goods_num'] = $searchModel->getEmployeeGoodsNum($countModel);
        $count['price'] = $searchModel->getEmployeePrice($countModel);
        $count['price_count'] = $searchModel->getEmployeePriceCount($searchModel->search(Yii::$app->request->queryParams));
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus(),
            'count' => $count,
            'is_print' => $userConfig->isPrint(),
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
        $modelBuyInfo = new BuyInfo();
        $modelBuyOut = new BuyOut();
        $modelOrderTime = new OrderTime;
        $modelGoodsInfo = new GoodsInfo();
        $modelStatisticalOrder = new StatisticalOrder();
        $user = new User();
        $orderRemark = new OrderRemark();
        $userConfig = new UserConfig();

        $request = Yii::$app->request->post();

        if ($model->load($request)) {
            try {
            	$tr = Yii::$app->db_hlj->beginTransaction();
            	$tr2 = Yii::$app->db->beginTransaction();

//                if(floatval($request['LogisticsOrder']['goods_num'] * 0.5) > floatval($request['LogisticsOrder']['freight'])){
//                    throw new Exception('数量或运费有误', '122');
//                }

            	$addTime = $model->find()
            	->where(['employee_id'=>Yii::$app->user->id])
            	->max('add_time');
            	if(time()-$addTime < 10)
            	{
            		throw new Exception('同一用户不可连续生成订单', '408');
            	}
                $type = 'logistics';//录入类型
                $res1 = $model->fillLogisticsInfo($model, $type, Yii::$app->request->post('User')['username'], Yii::$app->request->post('ismodify'));//填补物流信息
                if($res1 === false){
                    throw new Exception('会员号注册失败', '400');
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
//                 $OrderSn = array_filter(Yii::$app->request->post()['OrderSn']);
// 				if(!empty($OrderSn) && count($OrderSn)>0){
// 					$new_order_sn = implode(',',Yii::$app->request->post()['OrderSn']);
// 			     	$model->order_sn  = serialize($new_order_sn);
// 				}
                $model->goods_sn =$goodsSn.'_'.$model->goods_num;//货号
                if($model->collection ==1) {//判断是否代收
                    $r3 = $modelBuyOut->addBuyOutInfo($model);//增加买断信息
                    if(!$r3) {
                        throw new Exception('买断信息添加失败', '403');
                    }
                }
                $model->orderInfo($model);
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
//                 if($model->order_sn)
//                 {
//                    //$OrderSn = explode(',',str_replace('，',',',Yii::$app->request->post()['OrderSn']));
// 				   foreach($OrderSn as $key => $val){
// 					 if(!empty($val)){
// 						 $res =$model->editYoujianOrderState(array('orderSn'=>$val));//修改友件网订单状态
// 						 if($res === '206')
// 						 {
// 							throw new Exception('友件网信息错误！', '405');
// 						 }
// 						 if($res === '204')
// 						 {
// 						 	throw new Exception('友件网订单已发货！', '406');
// 						 }
// 						 if($res === '205')
// 						 {
// 						 	throw new Exception('友件网订单已完成！', '407');
// 						 }
// 					 }
					
// 				   } 
//                 }
                $orderRemark->addEditRemark(['order_id'=>$model->order_id,'edit_content'=>Yii::$app->request->post('OrderRemark')['edit_content']]);
                $modelStatisticalOrder->add($model);
                $isPrint = $userConfig->isPrint();
                $tr -> commit();
                $tr2-> commit();
                if($isPrint==1){
                    return $this->redirect(['view','id' => $model->order_id,'print'=>$isPrint]);
                }else{
                    Yii::$app->getSession()->setFlash('success', '订单保存成功');
                    return $this->redirect(['create']);
//                     $moren = UserAll::findIdentity($_SESSION['__id']);
//                     $td = ['kd0451001','kd0451002'];
//                     $xh = ['kd0451003','kd0451004','kd0451005'];
//                     // 			if($user->username == 'kd0451001' || $user->username == 'kd0451002'){
//                     // 				$model->order_type = 1;
//                     // 			}
//                     if(in_array($moren->username,$td)){
//                         $model->order_type = 1;
//                     }
//                     if(in_array($moren->username,$xh)){
//                         $model->order_type = 3;
//                     }
//                     $logisticsRouteInfo = $modelLogisticsRoute->getLogisticsRoute(130, 1);
//                     $area = new Area();
//                     return $this->render('create', [
//                     'model' => $model,
//                     'area' => $area,
//                     'user' => $user,
//                     'logisticsRouteInfo' => $logisticsRouteInfo,
// //                     'terminus' => $terminusInfo,
//                     'areaName' => '哈尔滨市',
//                     'orderRemark' => $orderRemark,
//                     'menus' => $this->_getMenus(),
//                 ]);
                }
                
            } catch (Exception $e) {
                $tr->rollBack();
                $tr2->rollBack();
                if ($e->getCode() == '405' || $e->getCode() == '400' || $e->getCode() == '406' || $e->getCode() == '407' || $e->getCode() == '408' || $e->getCode() == '122') {
                    Yii::$app->getSession()->setFlash('error', $e->getMessage());
                } else {
                    Yii::$app->getSession()->setFlash('error', '保存失败,信息不全');
                }

                $logisticsRouteInfo = $modelLogisticsRoute->getLogisticsRoute(130, 1);
//                 $terminusInfo = $modelTerminus->getTerminus();
                $area = new Area();
                return $this->render('create', [
                    'model' => $model,
                    'area' => $area,
                    'user' => $user,
                    'logisticsRouteInfo' => $logisticsRouteInfo,
//                     'terminus' => $terminusInfo,
                    'areaName' => '哈尔滨市',
                    'orderRemark' => $orderRemark,
                    'menus' => $this->_getMenus(),
                ]);
            }
        } else {
            $roles = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id));
            if (in_array('瑞胜开单员', $roles)) {
                $model->order_type = 3;
            }
            $logisticsRouteInfo = $modelLogisticsRoute->getLogisticsRoute(130, 1);
            $area = new Area();
			//通过id获取账号名字-2017-10-27-表单001，002账号默认通达 003,004,005默认宣化-齐经纬
			$moren = UserAll::findIdentity($_SESSION['__id']);
			$td = ['kd0451001','kd0451002'];
			$xh = ['kd0451003','kd0451004','kd0451005'];
// 			if($user->username == 'kd0451001' || $user->username == 'kd0451002'){
// 				$model->order_type = 1;
// 			}
			if(in_array($moren->username,$td)){
				$model->order_type = 1;
			}
			if(in_array($moren->username,$xh)){
				$model->order_type = 3;
			}

            return $this->render('create', [
                'model' => $model,
                'area' => $area,
                'user' => $user,
                'logisticsRouteInfo' => $logisticsRouteInfo,
                'areaName' => '哈尔滨市',
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
                $tr = Yii::$app->db_hlj->beginTransaction();
                $modelGoods = new Goods();
                $modelStatisticalOrder = new StatisticalOrder();
                $orderRemark = new OrderRemark();
                $orderEdit = new LogisticsOrderEdit();
                $modelBuyInfo = new BuyInfo();
                
                $goodsSn = $modelGoods->getGoodsSn(Yii::$app->request->post()['LogisticsOrder']['logistics_route_id'], $model->add_time);//生成货号
                $model->goods_sn = $goodsSn;
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
                $model->goods_sn = $goodsSn.'_'.$model->goods_num;
                if(empty($model->employee_id)){
                     $model->employee_id = Yii::$app->user->id;
                }else{
                    //订单封车后只允许大开单员修改
                    if($model->order_state>10&&$role!=Yii::$app->params['roleEmployeeDelete']){
                        Yii::$app->getSession()->setFlash('error', '订单已封车,不可修改！');
                        return $this->redirect(['index']);
                    }
                }
                
                $user_model = User::findOne(['user_truename'=>$model->member_name, 'member_phone' => $model->member_phone]);
//                 $user_model = User::findOne(['id'=>$model->member_id]);
                if(empty($user_model)){//判断修改的会员账号是否存在
                    $user_model = User::findOne(['id'=>$model->member_id]);
                    $model->member_id = $model->member_id;
                    $user_model->user_truename = $model->member_name;
                    $user_model->member_phone= $model->member_phone;
                    $user_model->save();
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
     * 开单自动打印保存
     */
    public function actionAjaxPrintChange(){
        $userConfig = new UserConfig();
        $result = $userConfig->editPrint();
        if($result['boolean']){
            $result = ['code'=>200,'status'=>$result['status'],'message'=>'修改成功'];
        }else{
            $result = ['code'=>400,'message'=>'修改失败'];
        }
        return json_encode($result);
    }

    /**
     * 营业员更新用户下的运单
     * @param unknown $model
     */
    private function _updateMemberOrder(&$model) {
    	$goods = new Goods();
    	$modelStatisticalOrder = new StatisticalOrder();
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
            $modelGoods = new Goods();
            $modelGoods->delGoodsByOrderId($model->order_id);
            $goodsSn = $modelGoods->getGoodsSn(Yii::$app->request->post()['LogisticsOrder']['logistics_route_id']);//生成货号
            $modelGoods->addGoodsInfo($model->order_id, $model->goods_sn, Yii::$app->request->post('LogisticsOrder')['goods_num']);
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
            $tr = Yii::$app->db_hlj->beginTransaction();
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
            'index' => ['menu' => '/hlj/employee/index', 'item' => '/hlj/employee/index'],
            'index-over' => ['menu' => '/hlj/employee/index', 'item' => '/hlj/employee/index-over'],
            'create' => ['menu' => '/hlj/employee/index', 'item' => false],
            'view' => ['menu' => '/hlj/employee/index', 'item' => false],
            'pay' => ['menu' => '/hlj/employee/pay', 'item' => false],
        );
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        if($role==Yii::$app->params['roleEmployeeDelete']){
            $arr['update'] = ['menu' => '/hlj/balance-edit/index', 'item' => false];
        }else{
            $arr['update'] = ['menu' => '/hlj/employee/index', 'item' => false];
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
        if(empty(Yii::$app->request->post('member_name'))){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['code' => 400];			
		}
		//判断是否是数字，如果位数字搜索电话号码
        if(is_numeric(Yii::$app->request->post('member_name'))){
		  $where = ['like', 'member_phone',Yii::$app->request->post('member_name')] ;
		}
		else{
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
    
    private function _downloadExcel($dataProvider) {
        $size = 5000;
        $count = $dataProvider->query->count();
        if($count > $size && !Yii::$app->request->get('page')) {
            $logisticsOrderSearch = new LogisticsOrderSearch();
            $page = ceil($count/$size);
            $result = array();
            for($i=0;$i<$page;$i++) {
                $temp = array();
                $begin = $i * $size + 1;
                $end = ($i+1) * $size > $count ? $count : ($i+1) * $size;
                $temp['content'] = '（' . $begin. '--' . $end. '）';
                $temp['url'] = $logisticsOrderSearch->_getObjectUrlParameter('hlg/employee/index', ['page'=>$i+1]);
                $result[] = $temp;
            }
            return $this->render('order_download', [
                'datas' => $result,
                'menus' => $this->_getMenus(),
            ]);
        }
        
        // Create new PHPExcel object
        $objPHPExcel = new \PHPExcel();
        
        // Set document properties
        $objPHPExcel->getProperties()
        ->setCreator("wuliu.youjian8.com")
        ->setLastModifiedBy("wuliu.youjian8.com")
        ->setTitle("youjian logistics order")
        ->setSubject("youjian logistics order")
        ->setDescription("youjian logistics order")
        ->setKeywords("youjian logistics order")
        ->setCategory("youjian logistics order");
        if (yii::$app->request->get('page')) {
            $dataProvider->query->limit($size)->offset((yii::$app->request->get('page') - 1) * $size);
        }
        $datas = $dataProvider->query->all();
        if ($datas) {
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '单号')
            ->setCellValue('B1', '开票时间')
            ->setCellValue('C1', '收件人')
            ->setCellValue('D1', '订单类型')
            ->setCellValue('E1', '线路')
            ->setCellValue('F1', '件数')
            ->setCellValue('G1', '代收')
            ->setCellValue('H1', '运费')
            ->setCellValue('I1', '合计')
            ->setCellValue('J1', '寄件人');
            $i = 2;
            $count = 0;
            $amount = 0;
            $freight = 0;
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('E')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('F')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('G')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            foreach ($datas as $model) {
                // Add some data
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, $model->return_logistics_sn?$model->logistics_sn."(已原返)":$model->logistics_sn);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, date('Y-m-d H:i:s',$model->add_time));
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, $model->receiving_name);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, LogisticsOrder::getOrderType($model->order_type));
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, $model->routeName->logistics_route_name);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, $model->goods_num);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$i, $model->goods_price);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$i, $model->freight);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$i, $model->goods_price+$model->freight);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$i, $model->member_name);
                $count += $model->goods_num;
                $amount += $model->goods_price;
                $freight += $model->freight;
                $i++;
            }
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, '总和：');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, $count);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$i, $amount);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$i, $freight);
        }
        
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('友件-物流发货单');
        
        
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        
        
        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="友件-物流发货单.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
}

