<?php

namespace frontend\controllers;

use common\models\LogisticsReturnOrderDelete;
use Yii;
use common\models\LogisticsReturnOrder;
use common\models\UserBalance;
use common\models\LogisticsOrder;
use common\models\LogisticsReturnOrderSearch;
use common\models\ReturnOrderTime;
use common\models\ReturnInfo;
use common\models\WithdrawalOrder;
use common\models\WithdrawalLog;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\ReturnGoods;
use yii\base\Exception;
use common\models\User;
use yii\web\Response;
use mdm\admin\components\MenuHelper;
use backend\models\ReturnOrderRemark;
use backend\models\OrderAdvance;

/**
 * ReturnController implements the CRUD actions for LogisticsReturnOrder model.
 */
class ReturnController extends Controller
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
     * Lists all LogisticsReturnOrder models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new LogisticsReturnOrderSearch();
//        var_dump($searchModel->return_manage_id);exit();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->_getIdentity());
        //统计
        if (Yii::$app->request->get('download_type', '0')) {
            if($dataProvider->query->count()==0){
                Yii::$app->getSession()->setFlash('error', '当前没有导出数据');
                return $this->redirect(['index']);
            }
            return $this->_downloadExcel($dataProvider);
        }
        $count['order_num'] = $dataProvider->query->count();
        $count['order_price'] = $dataProvider->query->sum('goods_price');
        $count['freight'] = $dataProvider->query->sum('freight');
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus(),
            'count' => $count,
            'identity' => $this->_getIdentity(),
        ]);
    }
    
    /**
     * Lists all LogisticsReturnOrder models.
     * @return mixed
     */
    public function actionIndexOver()
    {
        $searchModel = new LogisticsReturnOrderSearch();
        $type = 'over';
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $type);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus(),
            'type' => $type
        ]);
    }

    /**
     * Displays a single LogisticsReturnOrder model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $searchModel = new ReturnInfo();
        $dataProvider = $searchModel->getReturnInfoByOrderId($id);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider,
            'print' => Yii::$app->request->get('print'),
            'menus' => $this->_getMenus(),
        ]);
    }
    /**
     * Displays a single LogisticsReturnOrder model.
     * @param integer $id
     * @return mixed
     */
    public function actionView2($id)
    {
        $searchModel = new ReturnInfo();
        $dataProvider = $searchModel->getReturnInfoByOrderId($id);
    
        return $this->render('view2', [
                'model' => $this->findModel($id),
                'dataProvider' => $dataProvider,
                'print' => Yii::$app->request->get('print'),
                'menus' => $this->_getMenus(),
        ]);
    }

    /**
     * 反货生成订单
     * 朱鹏飞
     * Creates a new LogisticsReturnOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
    	try {
    		$model = new LogisticsReturnOrder(['scenario' => 'create']);
    		$modelReturnGoods = new ReturnGoods();
    		$modelReturnOrderTime = new ReturnOrderTime();
    		$modelReturnInfo = new ReturnInfo();
    		$modelLogisticsOrder = new LogisticsOrder();
    		$modelUserBalance = new UserBalance();
    		$orderRemark = new ReturnOrderRemark();
    		if(!empty($modelLogisticsOrder::findOne($_GET['order_id'])->return_logistics_sn)){
    		    Yii::$app->getSession()->setFlash('error', '订单已原返!');
    		    return $this->redirect(['terminus/myself']);
    		}
    		if ($model->load(Yii::$app->request->post())) {
    			$tr = Yii::$app->db->beginTransaction();
    			$model = $model->fillLogisticsInfo($model);//填补物流信息
    			$model->return_type = 1;
    			$r1= $model->save();
    			if(!$r1){
    				throw new Exception('订单生成失败', '401');
    			}
    			$goodsSn = $model->getReturnGoodsSn('F');//生成货号
    			
    			$r2 = $modelReturnGoods->addReturnGoodsInfo($model->order_id, $goodsSn, Yii::$app->request->post()['LogisticsReturnOrder']['goods_num']);//增加商品表
    			
    			if(!$r2)
    			{
    				throw new Exception('订单生成失败', '402');
    			}
    			$model->logistics_sn = $model->getReturnLogisticsSn($model->order_id, 'F');//票号
    			$model->goods_sn =$goodsSn.'_'.$model->goods_num;//货号
    			$r4 = $modelReturnInfo->setReturnInfo($model->order_id, Yii::$app->request->post()['ReturnInfo']['name'], Yii::$app->request->post()['ReturnInfo']['number'], Yii::$app->request->post()['ReturnInfo']['price']);
    			if($r4 === false)
    			{
    				throw new Exception('订单生成失败', '404');
    			}
    			if($r4 >0)
    			{
    				$model->goods_price = $r4;//部分反货商品价钱由反货详细信息生成
    				$model->return_all = 2;
    			}
    			if($model->collection==2){
    			    $model->goods_price = 0;//不代收，代收款为零
    			}
				
    			$model->orderInfo($model);
    			$model->save();
    			$modelReturnOrderTime->order_id=$model->order_id;
    			$modelReturnOrderTime->price_time=time();
    			$r3 = $modelReturnOrderTime->save();//增加订单时间表
    			if(!$r3){
    				throw new Exception('订单生成失败', '403');
    			}
    			$orderInfo = $modelLogisticsOrder->findOne(['logistics_sn' =>$model->ship_logistics_sn]);
    			$orderInfo->return_logistics_sn = $model->logistics_sn;
    			$orderInfo->save();
//     			if ($orderInfo->order_sn) {
//                     $modelLogisticsOrder->editYoujianOrder('/mobile/index.php?act=login&op=update_order_money', 
//                         array('orderSn'=>$orderInfo->order_sn, 'money' => $model->goods_price));
//     			}
//     			$modelUserBalance->addReturnBalanceInfo($model->goods_price, $model->logistics_sn, $orderInfo->member_id);
                $orderRemark->addEditRemark(['order_id'=>$model->order_id,'edit_content'=>Yii::$app->request->post('ReturnOrderRemark')['edit_content']]);
    			$tr -> commit();
    			return $this->redirect(['view', 'id' => $model->order_id,'print'=>1]);
    		} else {
    		    if(!empty(Yii::$app->request->get('order_id'))&&$model->isExistOrder(Yii::$app->request->get('order_id'))){
    		        $model = $model->getReturnCreate(Yii::$app->request->get('order_id'),$model);
    		    }else{
    		        $this->goHome();
    		    }
    			return $this->render('create', [
    					'model' => $model,
                        'orderRemark' => $orderRemark,
                        'menus' => $this->_getMenus(),
    			]);
            }
        } catch (Exception $e) {
            $tr->rollBack();
            Yii::$app->getSession()->setFlash('error', '订单生成失败,信息不全');
            return $this->render('create', [
                'model' => $model,
                'orderRemark'=> $orderRemark,
                'menus' => $this->_getMenus(),
            ]);
    	}
    }
    
    /**
     * 退货生成订单
     * 朱鹏飞
     * Creates a new LogisticsReturnOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate2()
    {
    	try {
    		$model = new LogisticsReturnOrder(['scenario' => 'create2']);
    		$modelReturnGoods = new ReturnGoods();
    		$modelReturnOrderTime = new ReturnOrderTime();
    		$modelReturnInfo = new ReturnInfo();
            $orderRemark = new ReturnOrderRemark();
    		if ($model->load(Yii::$app->request->post())) {
    			$tr = Yii::$app->db->beginTransaction();
    			$model = $this->getReceivingArea($model);
    			$model = $model->fillLogisticsInfo($model);//填补物流信息
    			if($model == false){
    				throw new Exception('用户注册失败', '400');
    			}
    			$model->return_type =2;//订单类型为退货 1反货 2退货
    			$r1= $model->save();
    			if(!$r1){
    				throw new Exception('订单生成失败', '401');
    			}
    			$goodsSn = $model->getReturnGoodsSn();//生成货号
    			$r2 = $modelReturnGoods->addReturnGoodsInfo($model->order_id, $goodsSn, Yii::$app->request->post()['LogisticsReturnOrder']['goods_num']);//增加商品表
    			if(!$r2)
    			{
    				throw new Exception('订单生成失败', '402');
    			}
    			$model->logistics_sn = $model->getReturnLogisticsSn($model->order_id);//票号
    			$model->goods_sn =$goodsSn.'_'.$model->goods_num;//货号
    			$r4 = $modelReturnInfo->setReturnInfo($model->order_id, Yii::$app->request->post()['ReturnInfo']['name'], Yii::$app->request->post()['ReturnInfo']['number'], Yii::$app->request->post()['ReturnInfo']['price']);
    			if($r4 === false)
    			{
    				throw new Exception('订单生成失败', '404');
    			}
    			$model->orderInfo($model);
    			$model->save();
    			$modelReturnOrderTime->order_id=$model->order_id;
    			$modelReturnOrderTime->price_time=time();
    			$r3 = $modelReturnOrderTime->save();//增加订单时间表
    			if(!$r3){
    				throw new Exception('订单生成失败', '403');
    			}
                $orderRemark->addEditRemark(['order_id'=>$model->order_id,'edit_content'=>Yii::$app->request->post('ReturnOrderRemark')['edit_content']]);
    			$tr -> commit();
    			return $this->redirect(['view2', 'id' => $model->order_id,'print'=>1]);
    		} else {
    			return $this->render('create2', [
    					'model' => $model,
    			        'orderRemark'=> $orderRemark,
    			        'menus' => $this->_getMenus(),
    			]);
    		}
    	} catch (Exception $e) {
    		$tr->rollBack();
    		Yii::$app->getSession()->setFlash('error', '订单生成失败,信息不全');
            return $this->render('create2', [
    		    'model' => $model,
    		    'orderRemark'=> $orderRemark,
    		    'menus' => $this->_getMenus(),
    		]);
    	}
    }
    
    /**
     * 追回生成订单
     * 朱鹏飞
     * Creates a new LogisticsReturnOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate3()
    {
        try {
            $model = new LogisticsReturnOrder(['scenario' => 'create']);
            $modelReturnGoods = new ReturnGoods();
            $modelReturnOrderTime = new ReturnOrderTime();
            $modelReturnInfo = new ReturnInfo();
            $modelLogisticsOrder = new LogisticsOrder();
            $modelUserBalance = new UserBalance();
            $orderRemark = new ReturnOrderRemark();
            if(!empty($modelLogisticsOrder::findOne($_GET['order_id'])->return_logistics_sn)){
                Yii::$app->getSession()->setFlash('error', '订单已追回!');
                return $this->redirect(['terminus/over']);
            }
            if ($model->load(Yii::$app->request->post())) {
                $tr = Yii::$app->db->beginTransaction();
                $model = $model->fillLogisticsInfo($model);//填补物流信息
                $model->return_type = 3;
                $r1= $model->save();
                if(!$r1){
                    throw new Exception('订单生成失败', '401');
                }
                $goodsSn = $model->getReturnGoodsSn('Z');//生成货号
                 
                $r2 = $modelReturnGoods->addReturnGoodsInfo($model->order_id, $goodsSn, Yii::$app->request->post()['LogisticsReturnOrder']['goods_num']);//增加商品表
                 
                if(!$r2)
                {
                    throw new Exception('订单生成失败', '402');
                }
                $model->logistics_sn = $model->getReturnLogisticsSn($model->order_id, 'Z');//票号
                $model->goods_sn =$goodsSn.'_'.$model->goods_num;//货号
//                 $r4 = $modelReturnInfo->setReturnInfo($model->order_id, Yii::$app->request->post()['ReturnInfo']['name'], Yii::$app->request->post()['ReturnInfo']['number'], Yii::$app->request->post()['ReturnInfo']['price']);
//                 if($r4 === false)
//                 {
//                     throw new Exception('订单生成失败', '404');
//                 }
//                 if($r4 >0)
//                 {
//                     $model->goods_price = $r4;//部分反货商品价钱由反货详细信息生成
//                     $model->return_all = 2;
//                 }
                if($model->collection==2){
                    $model->goods_price = 0;//不代收，代收款为零
                }
    
                $model->orderInfo($model);
                $model->save();
                $modelReturnOrderTime->order_id=$model->order_id;
                $modelReturnOrderTime->price_time=time();
                $r3 = $modelReturnOrderTime->save();//增加订单时间表
                if(!$r3){
                    throw new Exception('订单生成失败', '403');
                }
                $orderInfo = $modelLogisticsOrder->findOne(['logistics_sn' =>$model->ship_logistics_sn]);
                $orderInfo->return_logistics_sn = $model->logistics_sn;
                $orderInfo->save();
                $advanceOrder = OrderAdvance::findOne(['order_id' => $orderInfo->order_id]);
                if(!$this->_recoverOrder($orderInfo, $advanceOrder)){
                    throw new Exception('订单生成失败', '404');
                }
//                 $orderRemark->addEditRemark(['order_id'=>$model->order_id,'edit_content'=>Yii::$app->request->post('ReturnOrderRemark')['edit_content']]);
                $tr -> commit();
                return $this->redirect(['view', 'id' => $model->order_id,'print'=>1]);
            } else {
                if(!empty(Yii::$app->request->get('order_id'))&&$model->isExistOrder(Yii::$app->request->get('order_id'))){
                    $model = $model->getReplevyCreate(Yii::$app->request->get('order_id'),$model);
                }else{
                    $this->goHome();
                }
                return $this->render('create3', [
                        'model' => $model,
                        'orderRemark' => $orderRemark,
                        'menus' => $this->_getMenus(),
                ]);
            }
        } catch (Exception $e) {
            $tr->rollBack();
            Yii::$app->getSession()->setFlash('error', '订单生成失败,信息不全');
            return $this->render('create3', [
                    'model' => $model,
                    'orderRemark'=> $orderRemark,
                    'menus' => $this->_getMenus(),
            ]);
        }
    }
    

    /**
     * 反货修改信息
     * 朱鹏飞
     * Updates an existing LogisticsReturnOrder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario='create';
        $modelReturnGoods = new ReturnGoods();
        $modelReturnInfo = new ReturnInfo();
        $orderRemark = new ReturnOrderRemark();
        $temp = $orderRemark::findOne($id);
        if($temp) {
            $orderRemark = $temp;
        }
        if ($model->load(Yii::$app->request->post())) {
        	try {
        		$tr = Yii::$app->db->beginTransaction();
        		if($model->getOldAttribute('goods_num') != $model->goods_num){//判断修改后的商品数量与原来的数量是否一致
        			$goodsSn = $model->getReturnGoodsSn();
        			$r1 = $modelReturnGoods->isUpdateReturnGoods($model, $goodsSn);//删除旧的
        			if(!$r1){
        				throw new Exception('订单修改失败', '403');
        			}
        			$model->goods_sn = $goodsSn.'_'.$model->goods_num;
        		}
        		$r2 = $modelReturnInfo->delReturnInfoByOrderId($model->order_id);//删除货品详细信息
        		if(!$r2){
        			throw new Exception('订单修改失败', '403');
        		}
        		print_r(Yii::$app->request->post()['ReturnInfo']['name']);
        		$r4 = $modelReturnInfo->setReturnInfo($model->order_id, Yii::$app->request->post()['ReturnInfo']['name'], Yii::$app->request->post()['ReturnInfo']['number'], Yii::$app->request->post()['ReturnInfo']['price']);
    			if($r4 === false)
    			{
    				throw new Exception('订单修改失败', '404');
    			}
    			$model->return_all = 1;
    			if($r4 >0)
    			{
    				$model->goods_price = $r4;//部分反货商品价钱由反货详细信息生成
    				$model->return_all = 2;
    			}
        		$model->orderInfo($model);
        		$model->save();
                $orderRemark->addEditRemark(['order_id'=>$id,'edit_content'=>Yii::$app->request->post('ReturnOrderRemark')['edit_content']]);
        		$tr->commit();
        		return $this->redirect(['view', 'id' => $model->order_id]);
        	} catch (Exception $e) {
        		$tr->rollBack();
        		Yii::$app->getSession()->setFlash('error', $e->getMessage());
                return $this->render('update', [
                    'model' => $model,
                    'returnInfo' =>$returnInfo,
                    'orderRemark' => $orderRemark,
                    'menus' => $this->_getMenus(),
                ]);
            }
        } else {
            if($model->return_type==3){
                Yii::$app->getSession()->setFlash('error', '订单不可修改');
                return $this->redirect(['index']);
            }
        	$returnInfo = $modelReturnInfo->findAll(['order_id'=>$id]);
        	return $this->render('update', [
        			'model' => $model,
        			'returnInfo' =>$returnInfo,
                    'orderRemark' => $orderRemark,
        	        'menus' => $this->_getMenus(),
        	        
        	]);
        }
//         if ($model->load(Yii::$app->request->post()) && $model->save()) {
//             return $this->redirect(['view', 'id' => $model->order_id]);
//         } else {
//             return $this->render('update', [
//                 'model' => $model,
//                 'menus' => $this->_getMenus(),
//             ]);
//         }
    }

    
    /**
     * 退货修改信息
     * 朱鹏飞
     * Updates an existing LogisticsReturnOrder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate2($id)
    {
    	$model = $this->findModel($id);
    	$modelReturnGoods = new ReturnGoods();
    	$modelReturnInfo = new ReturnInfo();
        $orderRemark = new ReturnOrderRemark();
        $temp = $orderRemark::findOne($id);
        if($temp) {
            $orderRemark = $temp;
        }
    	if ($model->load(Yii::$app->request->post())) {
    		try {
    			$tr = Yii::$app->db->beginTransaction();
    			$model = $this->getReceivingArea($model);
    			if($model->getOldAttribute('goods_num') != $model->goods_num){//判断修改后的商品数量与原来的数量是否一致
    				$goodsSn = $model->getReturnGoodsSn();
    				$r1 = $modelReturnGoods->isUpdateReturnGoods($model, $goodsSn);//删除旧的
    				if(!$r1){
    					throw new Exception('订单修改失败', '403');
    				}
    				$model->goods_sn = $goodsSn.'_'.$model->goods_num;
    			}
    			$r2 = $modelReturnInfo->delReturnInfoByOrderId($model->order_id);//删除货品详细信息
    			if(!$r2){
    				throw new Exception('订单修改失败', '403');
    			}
    			$r4 = $modelReturnInfo->setReturnInfo($model->order_id, Yii::$app->request->post()['ReturnInfo']['name'], Yii::$app->request->post()['ReturnInfo']['number'], Yii::$app->request->post()['ReturnInfo']['price']);
    			if($r4 === false)
    			{
    				throw new Exception('订单生成失败', '404');
    			}
    			$model->orderInfo($model);
    			$model->save();
                $orderRemark->addEditRemark(['order_id'=>$id,'edit_content'=>Yii::$app->request->post('ReturnOrderRemark')['edit_content']]);
    			$tr->commit();
    			return $this->redirect(['view2', 'id' => $model->order_id]);
    		} catch (Exception $e) {
    			$tr->rollBack();
    			Yii::$app->getSession()->setFlash('error', $e->getMessage());
    			return $this->render('update2', [
    			    'model' => $model,
    			    'returnInfo' =>$returnInfo,
    			    'orderRemark' => $orderRemark,
    			    'menus' => $this->_getMenus(),
    			]);
    		}
    	} else {
    	    if($model->return_type==3){
    	        Yii::$app->getSession()->setFlash('error', '订单不可修改');
    	        return $this->redirect(['index']);
    	    }
    		$returnInfo = $modelReturnInfo->findAll(['order_id'=>$id]);
    		return $this->render('update2', [
    				'model' => $model,
    				'returnInfo' =>$returnInfo,
                    'orderRemark' => $orderRemark,
    		        'menus' => $this->_getMenus(),
    		]);
    	}
//     	if ($model->load(Yii::$app->request->post()) && $model->save()) {
//     		return $this->redirect(['view2', 'id' => $model->order_id]);
//     	} else {
//     		return $this->render('update2', [
//     				'model' => $model,
//     		]);
//     	}
    }
    /**
     * Deletes an existing LogisticsReturnOrder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
       /* $this->findModel($id)->delete();
        return $this->redirect(['index']);*/

        $model = $this->findModel($id);
        $ordermodel = new LogisticsReturnOrderDelete();

        $ordermodel->order_id = $model->order_id;
        $ordermodel->logistics_sn = $model->logistics_sn;
        $ordermodel->ship_logistics_sn = $model->ship_logistics_sn;
        $ordermodel->goods_sn = $model->goods_sn;
        $ordermodel->order_sn = $model->order_sn;
        $ordermodel->freight = $model->freight;
        $ordermodel->goods_price = $model->goods_price;
        $ordermodel->make_from_price = $model->make_from_price;
        $ordermodel->goods_num = $model->goods_num;
        $ordermodel->order_state = $model->order_state;
        $ordermodel->state = $model->state;
        $ordermodel->freight_state = $model->freight_state;
        $ordermodel->goods_price_state = $model->goods_price_state;
        $ordermodel->same_city = $model->same_city;
        $ordermodel->abnormal = $model->abnormal;
        $ordermodel->collection = $model->collection;
        $ordermodel->collection_poundage_two = $model->collection_poundage_two;
        $ordermodel->order_type = $model->order_type;
        $ordermodel->return_type = $model->return_type;
        $ordermodel->return_all = $model->return_all;
        $ordermodel->add_time = $model->add_time;
        $ordermodel->member_name = $model->member_name;
        $ordermodel->member_id = $model->member_id;
        $ordermodel->member_cityid = $model->member_cityid;
        $ordermodel->member_phone = $model->member_phone;
        $ordermodel->receiving_name = $model->receiving_name;
        $ordermodel->receiving_phone = $model->receiving_phone;
        $ordermodel->receiving_name_area = $model->receiving_name_area;
        $ordermodel->receiving_provinceid = $model->receiving_provinceid;
        $ordermodel->receiving_cityid = $model->receiving_cityid;
        $ordermodel->receiving_areaid = $model->receiving_areaid;
        $ordermodel->terminus_id = $model->terminus_id;
        $ordermodel->employee_id = $model->employee_id;
        $ordermodel->shipping_type = $model->shipping_type;
        $ordermodel->storage_id = $model->storage_id;
        $ordermodel->return_manage_id = $model->return_manage_id;
        $ordermodel->scale = $model->scale;
        $ordermodel->test = $model->test;
        $ordermodel->shipping_sale = $model->shipping_sale;

        if(($model->order_state==10 && $model->return_type==2)&&$model->employee_id==Yii::$app->user->id)
        {
            $this->findModel($id)->delete();
            $ordermodel->save();
            Yii::$app->getSession()->setFlash('success', '删除成功');
            return $this->redirect(['index']);
        }
        else
        {
            Yii::$app->getSession()->setFlash('error', '删除失败,您没有权限删除此订单');
            return $this->redirect(['index']);
        }
    }

    /**
     * ajax
     * 退货打印标签tag
     * 靳健
     */
    public function actionOrderPrint(){
        $return = new LogisticsReturnOrder();
        $list = $return->tagOrderPrint(Yii::$app->request->post('order_id'));
        return json_encode($list);
    }
    /**
     * @author 靳健
     * @desc ajax 取得发件人信息
     * @return json
     */
    public function actionMemberInfo() {
        $user = new User();
        $userInfo = $user->getMemberInfo(array('member_phone' =>Yii::$app->request->post('phone')));
    
        $return = array();
        if($userInfo) {
            $return['user_truename'] = $userInfo->user_truename;
            $return['member_cityid'] = $userInfo->member_cityid;
            $return['member_areaid'] = $userInfo->member_areaid;
            $return['member_areainfo'] = $userInfo->member_areainfo;
        }
    
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['code' => 200, 'msg' => '成功', 'datas' => $return];
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
            'index' => ['menu' => '/return/index', 'item' => '/return/index'],
            'index-over' => ['menu' => '/return/index', 'item' => '/return/index-over'],
            'create' => ['menu' => '/return/index', 'item' => false],
            'create2' => ['menu' => '/return/index', 'item' => false],
            'create3' => ['menu' => '/return/index', 'item' => false],
            'view' => ['menu' => '/return/index', 'item' => false],
            'update' => ['menu' => '/return/index', 'item' => false],
            'update2' => ['menu' => '/return/index', 'item' => false],
            'view2' => ['menu' => '/return/index', 'item' => false],     );
        
        return $arr[Yii::$app->controller->action->id];
    }
    private function getReceivingArea($model){
        $model->receiving_provinceid = 6;
        $model->receiving_cityid = 107;
        $model->receiving_areaid = 1531;
        $userInfo = User::findOne(['username'=>$model->receiving_phone]);
        if($userInfo){
            $userInfo->member_areainfo = $model->receiving_name_area;
            $userInfo->save();
        }
        return $model;
    }

    /**
     * Finds the LogisticsReturnOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LogisticsReturnOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LogisticsReturnOrder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
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
    private function _downloadExcel($dataProvider) {
        $size = 5000;
        $count = $dataProvider->query->count();
        if($count > $size && !Yii::$app->request->get('page')) {
            $logisticsReturnOrderSearch = new LogisticsReturnOrderSearch();
            $page = ceil($count/$size);
            $result = array();
            for($i=0;$i<$page;$i++) {
                $temp = array();
                $begin = $i * $size + 1;
                $end = ($i+1) * $size > $count ? $count : ($i+1) * $size;
                $temp['content'] = '（' . $begin. '--' . $end. '）';
                $temp['url'] = $logisticsReturnOrderSearch->_getObjectUrlParameter('return/index', ['page'=>$i+1]);
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
            ->setCellValue('A1', '票号')
            ->setCellValue('B1', '发货票号')
            ->setCellValue('C1', '运费')
            ->setCellValue('D1', '代收款')
            ->setCellValue('E1', '订单类型')
            ->setCellValue('F1', '订单状态')
            ->setCellValue('G1', '退货人')
            ->setCellValue('H1', '退货人电话')
            ->setCellValue('I1', '收货人')
            ->setCellValue('J1', '收货人电话')
            ->setCellValue('K1', '开单时间')
            ->setCellValue('L1', '开单员')
            ->setCellValue('M1', '送货人');
            $i = 2;
            $count = 0;
            $amount = 0;
            $freight = 0;
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('C')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('D')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            foreach ($datas as $model) {
                // Add some data
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, $model->logistics_sn);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, $model->ship_logistics_sn);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, $model->freight);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, $model->goods_price);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, LogisticsOrder::getOrderType($model->order_type));
                switch ($model->order_state)
                {
                    case '10':
                        $order_state = '已开单';
                        break;
                    case '20':
                        $order_state = '待分拨';
                        break;
                    case '30':
                        $order_state = '待入库';
                        break;
                    case '50':
                        $order_state = '待送货';
                        break;
                    case '70':
                        $order_state = '已收款';
                        break;
                    default:
                        $order_state = '未知';
                }
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, $order_state);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$i, $model->member_name);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$i, $model->member_phone);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$i, $model->receiving_name);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$i, $model->receiving_phone);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$i, date('Y-m-d H:i:s',$model->add_time));
                $userModel = User::findOne($model->employee_id);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$i, $userModel?$userModel->user_truename:'');
                $returnMOdel = ReturnOrderRemark::findOne($model->order_id);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M'.$i, $returnMOdel?$returnMOdel->sender:'');
                $count += 1;
                $amount += $model->goods_price;
                $freight += $model->freight;
                $i++;
            }
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, '总和：');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$i, '票数：'.$count);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$i, '代收款：'.$amount);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M'.$i, '运费：'.$freight);
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
    private function _recoverOrder($order, $orderAdvance) {
        if (strpos($order->return_logistics_sn, 'Z') === 0 ) {
            // 收款 未进余额
            if($order->goods_price_state == 8) {
                $order->goods_price_state = $order->goods_price_state | 1;
                $userBalance = new UserBalance();
                if (!$userBalance->editUserWithdrawalAmountInfo($order->order_id)) {
                    return false;
                }
                return $order->save();
            }
    
            // 进入余额 未提现
            if(($order->goods_price_state & 1) && !($order->goods_price_state & 4)) {
                // 修改提现订单表信息
                $wOrder = WithdrawalOrder::findOne(['order_sn'=>$order->logistics_sn]);
                $wOrder->amount = 0;
                if(!$wOrder->save()) {
                    return false;
                }
    
                // 原始log记录
                $log = WithdrawalLog::find()->where(['order_sn'=>$order->logistics_sn])->one();
    
                // 添加追回订单log
                $userBalance = UserBalance::findOne($order->member_id);
                $log1 = new WithdrawalLog();
                $log1->uid = $order->member_id;
                $log1->amount = $log->amount;
                $log1->before_amount = $userBalance->withdrawal_amount;
                $log1->after_amount = $userBalance->withdrawal_amount - $log->amount;
                $log1->content = '追回订单扣除';
                $log1->type = 4;
                $log1->order_sn = '-'.$order->logistics_sn;
                $log1->add_time = time();
                if(!$log1->save()) {
                    return false;
                }
                // 扣除余额
                $userBalance->withdrawal_amount -= $log->amount;
                if(!$userBalance->save()) {
                    throw new Exception('减可提现余额失败！');
                }
                return true;
            }
    
            // 用户已经提现
            if($order->goods_price_state & 4) {
                $orderAdvance->recover_state = 1;
                return $orderAdvance->save();
            }
        }
        return true;
    }
    


}
