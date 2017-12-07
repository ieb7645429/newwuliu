<?php

namespace backend\modules\dl\controllers;

use Yii;
use backend\modules\dl\models\PayDealer;
use backend\modules\dl\models\PayTerminus;
use backend\modules\dl\models\IncomeTerminus;
use backend\modules\dl\models\IncomeTerminusNot;
use backend\modules\dl\models\IncomeEmployee;
use backend\modules\dl\models\IncomeLogisticsSn;
use backend\modules\dl\models\ReturnIncomeTerminus;
use backend\modules\dl\models\ReturnIncomeTerminusNot;
use backend\modules\dl\models\ReturnIncomeDealer;
use backend\modules\dl\models\ReturnPayTerminus;
use backend\modules\dl\models\IncomeDriver;
use backend\modules\dl\models\ReturnIncomeEmployee;
use backend\modules\dl\models\TellerLogSearch;
use backend\modules\dl\models\AdminAuthAssignment;
use backend\modules\dl\models\TellerSearch;
use backend\modules\dl\models\OrderRemark;
use backend\modules\dl\models\ReturnOrderRemark;
use backend\modules\dl\models\OrderAdvanceSearch;
use backend\modules\dl\models\OrderAdvance;
use backend\modules\dl\models\TellerIncomeSnLog;
use backend\modules\dl\models\TellerIncomeSnLogSearch;
use frontend\modules\dl\models\LogisticsOrderSearch;
use frontend\modules\dl\models\LogisticsOrder;
use frontend\modules\dl\models\LogisticsReturnOrderSearch;
use frontend\modules\dl\models\BankInfo;
use frontend\modules\dl\models\ApplyForWithdrawalSearch;
use frontend\modules\dl\models\ApplyForWithdrawal;
use frontend\modules\dl\models\Driver;
use yii\helpers\Url;
use yii\base\Exception;
use yii\web\Response;
use mdm\admin\components\MenuHelper;
use mdm\admin\components\Helper;
use yii\helpers\ArrayHelper;
use yii\data\Pagination;

class TellerController extends \yii\web\Controller
{
    public $layout = 'teller';

    public function actionIndex() {
        if(Yii::$app->user->can('/dl/teller/apply')) {
            $this->redirect(['apply']);
        } else if(Helper::checkRoute('/dl/teller/income-driver')) {
            $this->redirect(['income-driver']);
        } else {
            $this->redirect(['search']);
        }
    }

    /**
     * 收款 落地点（货款、运费）
     * @author 暴闯
     * @return string
     */
    public function actionIncomeTerminus() {
        $model = new IncomeTerminus();
        $returnModel = new ReturnIncomeTerminus();
        $orderList = $model->getLogisticsOrderList(Yii::$app->request->queryParams);
        $returnOrderList = $returnModel->getLogisticsOrderList(Yii::$app->request->queryParams);

        return $this->render('income-terminus', [
            'searchModel' => new LogisticsOrderSearch(),
            'orderList' => $model->formatData($orderList),
            'returnOrderList' => $returnModel->formatData($returnOrderList),
            'menus' => $this->_getMenus()
        ]);
    }

    /**
     * 收入 落地点 详细
     * @return string
     */
    public function actionIncomeTerminusDetails() {
        $model = new IncomeTerminus();
        $returnModel = new ReturnIncomeTerminus();
        
        $orderList = $model->formatDetailsData($model->getLogisticsOrderList(Yii::$app->request->queryParams));
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
        
        $returnOrderList = $returnModel->formatDetailsData($returnModel->getLogisticsOrderList(Yii::$app->request->queryParams));
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
        $pages = new Pagination(['totalCount' =>count($orderList), 'pageSize' => Yii::$app->params['pagesize']]);
        $orderList = array_slice($orderList, $pages->offset, $pages->limit);
        
        //分页
        $returnPages = new Pagination(['totalCount' =>count($returnOrderList), 'pageSize' => Yii::$app->params['pagesize'], 'pageParam' => 'repage']);
        $returnOrderList = array_slice($returnOrderList, $returnPages->offset, $returnPages->limit);
        
        return $this->render('income-terminus-details', [
            'searchModel' => new LogisticsOrderSearch(),
            'orderList' => $orderList,
            'returnOrderList' => $returnOrderList,
            'total' => $total,
            'returnTotal' => $returnTotal,
            'menus' => $this->_getMenus(),
            'pages' => $pages,
            'returnPages' => $returnPages
        ]);
    }
    
    /**
     * 落地点确认收货订单状态修改
     * 靳健
     */
    public function actionIncomeTerminusConfirm(){
        $orderIds = Yii::$app->request->post('order_id');
        $advance = Yii::$app->request->post('advance', 0);
        $result = ['code'=>200,'msg'=>'确认成功'];
        if ($orderIds) {
            $tr = Yii::$app->db_dl->beginTransaction();
            try {
                $model = new IncomeTerminus();
                foreach ($orderIds as $orderId) {
                    $res = $model->terminusConfirmCollection($orderId, $advance);
                    if($res){
                        $result['datas'][] = $res;
                    }else{
                        throw new Exception();
                    }
                }
                $tr->commit();
            }catch (Exception $e) {
                $tr->rollBack();
                $result = ['code'=>300,'msg'=>'确认失败'];
            }
        }
        return json_encode($result);
    }
    
    /**
     * 收款 落地点（货款、运费）
     * @author 暴闯
     * @return string
     */
    public function actionIncomeTerminusNot() {
        $model = new IncomeTerminusNot();
        $returnModel = new ReturnIncomeTerminusNot();
        $orderList = $model->getLogisticsOrderList(Yii::$app->request->queryParams);
        $returnOrderList = $returnModel->getLogisticsOrderList(Yii::$app->request->queryParams);
        
        return $this->render('income-terminus-not', [
            'searchModel' => new LogisticsOrderSearch(),
            'orderList' => $model->formatData($orderList),
            'returnOrderList' => $returnModel->formatData($returnOrderList),
            'menus' => $this->_getMenus()
        ]);
    }
    
    /**
     * 收入 落地点 详细
     * @return string
     */
    public function actionIncomeTerminusDetailsNot() {
        $model = new IncomeTerminusNot();
        $returnModel = new ReturnIncomeTerminusNot();
        
        $orderList = $model->formatDetailsData($model->getLogisticsOrderList(Yii::$app->request->queryParams));
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
        
        $returnOrderList = $returnModel->formatDetailsData($returnModel->getLogisticsOrderList(Yii::$app->request->queryParams));
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
        $pages = new Pagination(['totalCount' =>count($orderList), 'pageSize' => Yii::$app->params['pagesize']]);
        $orderList = array_slice($orderList, $pages->offset, $pages->limit);
        
        //分页
        $returnPages = new Pagination(['totalCount' =>count($returnOrderList), 'pageSize' => Yii::$app->params['pagesize'], 'pageParam' => 'repage']);
        $returnOrderList = array_slice($returnOrderList, $returnPages->offset, $returnPages->limit);
        
        return $this->render('income-terminus-details-not', [
            'searchModel' => new LogisticsOrderSearch(),
            'orderList' => $orderList,
            'returnOrderList' => $returnOrderList,
            'total' => $total,
            'returnTotal' => $returnTotal,
            'menus' => $this->_getMenus(),
            'pages' => $pages,
            'returnPages' => $returnPages
        ]);
    }
    
    /**
     * 落地点确认收货订单状态修改
     * 靳健
     */
    public function actionIncomeTerminusConfirmNot(){
        $orderIds = Yii::$app->request->post('order_id');
        $advance = Yii::$app->request->post('advance', 0);
        $result = ['code'=>200,'msg'=>'确认成功'];
        if ($orderIds) {
            $tr = Yii::$app->db_dl->beginTransaction();
            try {
                $model = new IncomeTerminusNot();
                foreach ($orderIds as $orderId) {
                    $res = $model->terminusConfirmCollection($orderId, $advance);
                    if($res){
                        $result['datas'][] = $res;
                    }else{
                        throw new Exception();
                    }
                }
                $tr->commit();
            }catch (Exception $e) {
                $tr->rollBack();
                $result = ['code'=>300,'msg'=>'确认失败'];
            }
        }
        return json_encode($result);
    }
    
    /**
     * 收款 同城司机（货款、运费）
     * @return string
     */
    public function actionIncomeDriver() {
        $model = new IncomeDriver();
        
        $orderList = $model->getLogisticsOrderList(Yii::$app->request->queryParams);
        
        return $this->render('income-driver', [
            'searchModel' => new LogisticsOrderSearch(),
            'orderList' => $model->formatData($orderList),
            'menus' => $this->_getMenus()
        ]);
    }
    
    /**
     * 收入 同城司机 详细
     * @return string
     */
    public function actionIncomeDriverDetails() {
        $model = new IncomeDriver();
        
        $orderList = $model->formatDetailsData($model->getLogisticsOrderList(Yii::$app->request->queryParams));
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
        $pages = new Pagination(['totalCount' =>count($orderList), 'pageSize' => Yii::$app->params['pagesize']]);
        $orderList = array_slice($orderList, $pages->offset, $pages->limit);
        
        return $this->render('income-driver-details', [
            'searchModel' => new LogisticsOrderSearch(),
            'orderList' => $orderList,
            'total' => $total,
            'menus' => $this->_getMenus(),
            'pages' => $pages
        ]);
    }
    
    /**
     * 落地点确认收货订单状态修改
     * 靳健
     */
    public function actionIncomeDriverConfirm(){
        $orderIds = Yii::$app->request->post('order_id');
        $advance = Yii::$app->request->post('advance', 0);
        $result = ['code'=>200,'msg'=>'确认成功'];
        if ($orderIds) {
            $tr = Yii::$app->db_dl->beginTransaction();
            try {
                $model = new IncomeDriver();
                $return = $model->terminusConfirmCollection($orderIds, $advance);
                if($return === false) {
                    throw new Exception();
                } else {
                    $result['datas'] = $return;
                }
                $tr->commit();
            }catch (Exception $e) {
                $tr->rollBack();
                $result = ['code'=>300,'msg'=>'确认失败'];
            }
        }
        return json_encode($result);
    }

    /**
     * 收款 开单员（运费）
     * @return string
     */
    public function actionIncomeEmployee() {
        $model = new IncomeEmployee();
        $returnModel = new ReturnIncomeEmployee();

        $orderList = $model->getLogisticsOrderList(Yii::$app->request->queryParams);
        $returnOrderList = $returnModel->getLogisticsOrderList(Yii::$app->request->queryParams);
        
        return $this->render('income-employee', [
            'searchModel' => new LogisticsOrderSearch(),
            'orderList' => $model->formatData($orderList),
            'returnOrderList' => $returnModel->formatData($returnOrderList),
            'menus' => $this->_getMenus()
        ]);
    }
    
    /**
     * 收款 开单员 详细
     * @return string
     */
    public function actionIncomeEmployeeDetails() {
        $model = new IncomeEmployee();
        $returnModel = new ReturnIncomeEmployee();
        
        $orderList = $model->formatDetailsData($model->getLogisticsOrderList(Yii::$app->request->queryParams));
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
        
        $returnOrderList = $returnModel->formatDetailsData($returnModel->getLogisticsOrderList(Yii::$app->request->queryParams));
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
        $pages = new Pagination(['totalCount' =>count($orderList), 'pageSize' => Yii::$app->params['pagesize']]);
        $orderList = array_slice($orderList, $pages->offset, $pages->limit);
        
        //分页
        $returnPages = new Pagination(['totalCount' =>count($returnOrderList), 'pageSize' => Yii::$app->params['pagesize'], 'pageParam' => 'repage']);
        $returnOrderList = array_slice($returnOrderList, $returnPages->offset, $returnPages->limit);
        
        return $this->render('income-employee-details', [
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
     * 财务开单员确认收款
     * xiaoyu
     * 2017-07-29
     */
    public function actionIncomeEmployeeConfirm() {
        Yii::$app->response->format=Response::FORMAT_JSON;
        
        $orderIds = Yii::$app->request->post('order_id');
        
        $datas = array();
        if ($orderIds) {
            $tr = Yii::$app->db_dl->beginTransaction();
            try {
                $model = new IncomeEmployee();
                $result = $model->setFreightState($orderIds);
                if($result === false) {
                    throw new Exception();
                } else {
                    $datas = $result;
                }
                $tr->commit();
            }catch (Exception $e) {
                $tr->rollBack();
                return array('code'=>300, 'msg'=>'收款失败');
            }
        }
        return array('code'=>200, 'msg'=>'成功', 'datas'=>$datas);
    }
    
    /**
     * 按票号收款
     */
    public function actionIncomeLogisticssn() {
        return $this->render('income-logisticssn', [
            'menus' => $this->_getMenus()
        ]);
    }
    
    /**
     * 取得订单详细
     * @return number[]|string[]|unknown[]|number[]|string[]
     */
    public function actionIncomeLogisticssnDetails() {
        Yii::$app->response->format=Response::FORMAT_JSON;
        
        $logisticsSn= Yii::$app->request->post('logistics_sn');
        if($logisticsSn) {
            $model = new IncomeLogisticsSn();
            $order = $model->getLogisticsOrder($logisticsSn);
            return $model->formatData($order);
        } else {
            return array('code'=>300, 'msg'=>'票号不能为空！');
        }
        
    }
    
    /**
     * 票号收款 保存
     * @throws Exception
     * @return number[]|string[]
     */
    public function actionIncomeLogisticssnConfirm() {
        Yii::$app->response->format=Response::FORMAT_JSON;
        
        $orderIds = Yii::$app->request->post('order_id');
        $advance = Yii::$app->request->post('advance', 0);
        if ($orderIds) {
            $tr = Yii::$app->db_dl->beginTransaction();
            try {
                $orderSnArray = array();
                $orderIdArray = array();
                $amount = 0;
                foreach ($orderIds as $orderId) {
                    $order = IncomeLogisticsSn::findOne($orderId);
                    if($order->same_city == 1) {
                        $model = new IncomeDriver();
                        $return = $model->terminusConfirmCollection([$orderId], $advance);
                        if($return === false) {
                            throw new Exception();
                        } else if(!empty($return)) {
                            $orderSnArray[] = $order->logistics_sn;
                            $orderIdArray[] = $order->order_id;
                            $amount += ArrayHelper::getValue($model->_getAmount(ArrayHelper::toArray($order)), 'all_amount', 0);
                        }
                    } else {
                        $model = new IncomeTerminusNot();
                        $return = $model -> terminusConfirmCollection([$orderId], $advance);
                        if($return === false) {
                            throw new Exception();
                        } else if(!empty($return)) {
                            $orderSnArray[] = $order->logistics_sn;
                            $orderIdArray[] = $order->order_id;
                            $amount += ArrayHelper::getValue($model->_getAmount(ArrayHelper::toArray($order)), 'all_amount', 0);
                        }
                    }
                }
                $log = new TellerIncomeSnLog();
                $params = array();
                $params['order_id'] = implode(',', $orderIdArray);
                $params['order_sn'] = implode(',', $orderSnArray);
                $params['count'] = count($orderSnArray);
                $params['amount'] = $amount;
                $params['receiving'] = Yii::$app->request->post('receiving');
                if(!$log->addLog($params)) {
                    throw new Exception();
                }
                $tr->commit();
                return array('code'=>200,'msg'=>'收款成功');
            } catch (Exception $e) {
                $tr->rollBack();
                return array('code'=>300, 'msg'=>'收款失败');
            }
        }
    }
    
    /**
     * Lists all TellerIncomeSnLog models.
     * @return mixed
     */
    public function actionIncomeLogisticssnLog()
    {
        $searchModel = new TellerIncomeSnLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('income-logisticssn-log', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus()
        ]);
    }
    
    /**
     * 票号收款打印
     * @return string
     */
    public function actionIncomeLogisticssnLogPrint()
    {
        Yii::$app->response->format=Response::FORMAT_JSON;
        $log = TellerIncomeSnLog::findOne(Yii::$app->request->post('id', 0));
        if($log) {
            $return = array();
            $orderIds = explode(',', $log->order_id);
            $orderSns = explode(',', $log->order_sn);
            for ($i=0;$i<count($orderIds);$i++) {
                $order = IncomeLogisticsSn::findOne($orderIds[$i]);
                if ($order->same_city == 1) {
                    $model = new IncomeDriver();
                } else {
                    $model = new IncomeTerminusNot();
                }
               
                $amount = $model->_getAmount(ArrayHelper::toArray($order));
                $data = array();
                $data['logistics_sn'] = $orderSns[$i];
                $data['amount'] = $amount['all_amount'];
                $return[] = $data;
            }
            return array('code'=>200, 'msg'=>'成功！', 'datas'=>$return);
        } else {
            return array('code'=>404, 'msg'=>'未取得数据！');
        }
    }

    /**
     * @desc 付款 经销商（货款）
     * @author 暴闯
     */
    public function actionPayDealer() {
        $model = new PayDealer();

        $orderList = $model->getLogisticsOrderList(Yii::$app->request->queryParams);

        return $this->render('pay-dealer', [
            'searchModel' => new LogisticsOrderSearch(),
            'orderList' => $model->formatData($orderList),
            'menus' => $this->_getMenus()
        ]);
    }
    
    /**
     * @desc 付款 经销商 详细
     * @author 暴闯
     * @return string
     */
    public function actionPayDealerDetails() {
        $model = new PayDealer();
        
        $orderList = $model->getLogisticsOrderList(Yii::$app->request->queryParams);
        $bankModel = null;
        if($orderList) {
            $bankModel = BankInfo::getBankInfoByUser($orderList[0]['member_id']);
        }
        return $this->render('pay-dealer-details', [
            'bankModel' => $bankModel,
            'searchModel' => new LogisticsOrderSearch(),
            'orderList' => $model->formatDetailsData($orderList),
            'menus' => $this->_getMenus()
        ]);
    }
    
    /**
     * 确认付款经销商
     * @author 暴闯
     * @throws Exception
     * @return number[]|string[]|number[]|string[]|\backend\models\true[][]
     */
    public function actionPayDealerConfirm() {
        Yii::$app->response->format=Response::FORMAT_JSON;
        
        $orderIds = Yii::$app->request->post('order_id');
        
        $datas = array();
        if ($orderIds) {
            $tr = Yii::$app->db_dl->beginTransaction();
            try {
                $model = new PayDealer();
                foreach ($orderIds as $orderId) {
                    $return['order_id'] = $orderId;
                    $return['goods_price_state_name'] = $model->setGoodsPriceState($orderId);
                    if($return['goods_price_state_name'] === false) {
                        throw new Exception();
                    }
                    $datas[] = $return;
                }
                $tr->commit();
            }catch (Exception $e) {
                $tr->rollBack();
                return array('code'=>300, 'msg'=>'付款失败');
            }
        }
        return array('code'=>200, 'msg'=>'成功', 'datas'=>$datas);
    }
    
    /**
     * @desc 付款 落地点（运费）
     * @author 暴闯
     */
    public function actionPayTerminus() {
        $model = new PayTerminus();
        $returnModel = new ReturnPayTerminus();
        
        $orderList = $model->getLogisticsOrderList(Yii::$app->request->queryParams);
        $returnOrderList = $returnModel->getLogisticsOrderList(Yii::$app->request->queryParams);
        
        return $this->render('pay-terminus', [
            'searchModel' => new LogisticsOrderSearch(),
            'orderList' => $model->formatData($orderList),
            'returnOrderList' => $returnModel->formatData($returnOrderList),
            'menus' => $this->_getMenus()
        ]);
    }
    
    /**
     * 付款 落地点 详细
     * @return string
     */
    public function actionPayTerminusDetails() {
        $model = new PayTerminus();
        $returnModel = new ReturnPayTerminus();
        
        $orderList = $model->formatDetailsData($model->getLogisticsOrderList(Yii::$app->request->queryParams));
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
        
        $returnOrderList = $returnModel->formatDetailsData($returnModel->getLogisticsOrderList(Yii::$app->request->queryParams));
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
        
        $bankModel = null;
        if($orderList) {
            $bankModel = BankInfo::getBankInfoByTerminus($orderList[0]['terminus_id']);
        }else if($returnOrderList) {
            $bankModel = BankInfo::getBankInfoByTerminus($returnOrderList[0]['terminus_id']);
        }
        
        //分页
        $pages = new Pagination(['totalCount' =>count($orderList), 'pageSize' => Yii::$app->params['pagesize']]);
        $orderList = array_slice($orderList, $pages->offset, $pages->limit);
        
        //分页
        $returnPages = new Pagination(['totalCount' =>count($returnOrderList), 'pageSize' => Yii::$app->params['pagesize'], 'pageParam' => 'repage']);
        $returnOrderList = array_slice($returnOrderList, $returnPages->offset, $returnPages->limit);
        
        return $this->render('pay-terminus-details', [
            'bankModel' => $bankModel,
            'searchModel' => new LogisticsOrderSearch(),
            'orderList' => $orderList,
            'returnOrderList' => $returnOrderList,
            'total' => $total,
            'returnTotal' => $returnTotal,
            'menus' => $this->_getMenus(),
            'pages' => $pages,
            'returnPages' => $returnPages
        ]);
    }
    
    /**
     * ajax 确认付款给落地点
     * @author 暴闯
     */
    public function actionPayTerminusConfirm() {
        Yii::$app->response->format=Response::FORMAT_JSON;
        
        $orderIds = Yii::$app->request->post('order_id');
        
        $datas = array();
        if ($orderIds) {
            $tr = Yii::$app->db_dl->beginTransaction();
            try {
                $model = new PayTerminus();
                foreach ($orderIds as $orderId) {
                   $return['order_id'] = $orderId;
                   $return['freight_state_name'] = $model->setFreightState($orderId);
                   if($return['freight_state_name'] === false) {
                       throw new Exception();
                   }
                   $datas[] = $return;
                }
                $tr->commit();
            }catch (Exception $e) {
                $tr->rollBack();
                return array('code'=>300, 'msg'=>'付款失败');
            }
        }
        return array('code'=>200, 'msg'=>'成功', 'datas'=>$datas);
    }
    
    /**
     * @desc 确认收款 落地点 ajax
     * @throws Exception
     * @return number[]|string[]|number[]|string[]|\backend\models\true[][]
     */
    public function actionReturnIncomeTerminusConfirm() {
        Yii::$app->response->format=Response::FORMAT_JSON;
        
        $orderIds = Yii::$app->request->post('order_id');

        $datas = array();
        if ($orderIds) {
            $tr = Yii::$app->db_dl->beginTransaction();
            try {
                $model = new ReturnIncomeTerminus();
                foreach ($orderIds as $orderId) {
                    $return['order_id'] = $orderId;
                    $return['freight_state_name'] = $model->setFreightStateInfo($orderId);
                    if($return['freight_state_name'] === false) {
                        throw new Exception();
                    }
                    $datas[] = $return;
                }
                $tr->commit();
            }catch (Exception $e) {
                $tr->rollBack();
                return array('code'=>300, 'msg'=>'收款失败');
            }
        }
        return array('code'=>200, 'msg'=>'成功', 'datas'=>$datas);
    }
    
    /**
     * @desc 确认收款 落地点 ajax
     * @throws Exception
     * @return number[]|string[]|number[]|string[]|\backend\models\true[][]
     */
    public function actionReturnIncomeTerminusConfirmNot() {
        Yii::$app->response->format=Response::FORMAT_JSON;
        
        $orderIds = Yii::$app->request->post('order_id');
        
        $datas = array();
        if ($orderIds) {
            $tr = Yii::$app->db_dl->beginTransaction();
            try {
                $model = new ReturnIncomeTerminusNot();
                foreach ($orderIds as $orderId) {
                    $return['order_id'] = $orderId;
                    $return['freight_state_name'] = $model->setFreightStateInfo($orderId);
                    if($return['freight_state_name'] === false) {
                        throw new Exception();
                    }
                    $datas[] = $return;
                }
                $tr->commit();
            }catch (Exception $e) {
                $tr->rollBack();
                return array('code'=>300, 'msg'=>'收款失败');
            }
        }
        return array('code'=>200, 'msg'=>'成功', 'datas'=>$datas);
    }
    
    /**
     * @desc 确认收款 同城开单员 ajax
     * @throws Exception
     * @return number[]|string[]|number[]|string[]|\backend\models\true[][]
     */
    public function actionReturnIncomeEmployeeConfirm() {
        Yii::$app->response->format=Response::FORMAT_JSON;
        
        $orderIds = Yii::$app->request->post('order_id');
        
        $datas = array();
        if ($orderIds) {
            $tr = Yii::$app->db_dl->beginTransaction();
            try {
                $model = new ReturnIncomeEmployee();
                $result = $model->setFreightStateInfo($orderIds);
                
                if ($result === false) {
                    throw new Exception();
                } else {
                    $datas = $result;
                }
                $tr->commit();
            }catch (Exception $e) {
                $tr->rollBack();
                return array('code'=>300, 'msg'=>'收款失败');
            }
        }
        return array('code'=>200, 'msg'=>'成功', 'datas'=>$datas);
    }
    
    /**
     * 退货 收款  退货员（货款 运费）
     * @author 暴闯
     * @return string
     */
    public function actionReturnIncomeDealer() {
        $model = new ReturnIncomeDealer();

        $orderList = $model->getLogisticsOrderList(Yii::$app->request->queryParams);

        return $this->render('return-income-dealer', [
            'searchModel' => new LogisticsReturnOrderSearch(),
            'orderList' => $model->formatData($orderList),
            'menus' => $this->_getMenus()
        ]);
    }
    
    /**
     * 退货 收款  退货员 详细
     * @author 暴闯
     * @return string
     */
    public function actionReturnIncomeDealerDetails() {
        $model = new ReturnIncomeDealer();
        
        $orderList = $model->formatDetailsData($model->getLogisticsOrderList(Yii::$app->request->queryParams));
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
        $pages = new Pagination(['totalCount' =>count($orderList), 'pageSize' => Yii::$app->params['pagesize']]);
        $orderList = array_slice($orderList, $pages->offset, $pages->limit);
        
        return $this->render('return-income-dealer-details', [
            'searchModel' => new LogisticsReturnOrderSearch(),
            'orderList' => $orderList,
            'total' => $total,
            'menus' => $this->_getMenus(),
            'pages' => $pages
        ]);
    }
    /**
     * 退货 收款  打印页
     * @author 小雨
     * @return string
     */
    public function actionReturnIncomeDealerDetailsPrint() {
        $model = new ReturnIncomeDealer();        
        $orderList = $model->getLogisticsOrderList(Yii::$app->request->queryParams);        
        return $this->render('return-income-dealer-details-print', [
            'searchModel' => new LogisticsReturnOrderSearch(),
            'orderList' => $model->formatDetailsData($orderList),
            'menus' => $this->_getMenus()
        ]);
    }
    
    /**
     * @desc 确认收款 退货员 ajax
     * @throws Exception
     * @return array|mixed|number[]|string[]|number[]|string[]|\backend\models\true[][]
     */
    public function actionReturnIncomeDealerConfirm() {
        Yii::$app->response->format=Response::FORMAT_JSON;
        
        $orderIds = Yii::$app->request->post('order_id');
        $datas = array();
        if ($orderIds) {
            $tr = Yii::$app->db_dl->beginTransaction();
            try {
                $model = new ReturnIncomeDealer();
                    $res = $model->upGoodsPriceStateInfo($orderIds);
                    if($res === false) {
                        throw new Exception();
                    }
                    $datas = $res;
                $tr->commit();
            }catch (Exception $e) {
                $tr->rollBack();
                return array('code'=>300, 'msg'=>'收款失败');
            }
        }
        return array('code'=>200, 'msg'=>'成功', 'datas'=>$datas);
    }
    
    /**
     * @desc 退货 付款 落地点 确认
     * @throws Exception
     * @return array|mixed|number[]|string[]|number[]|string[]|boolean[][]
     */
    public function actionReturnPayTerminusConfirm() {
        Yii::$app->response->format=Response::FORMAT_JSON;
        
        $orderIds = Yii::$app->request->post('order_id');

        $datas = array();
        if ($orderIds) {
            $tr = Yii::$app->db_dl->beginTransaction();
            try {
                $model = new ReturnPayTerminus();
                foreach ($orderIds as $orderId) {
                    $return['order_id'] = $orderId;
                    $res = $model->setPayTerminus($orderId);
                    if($res === false) {
                        throw new Exception();
                    }
                    $return = array_merge($return, $res);
                    $datas[] = $return;
                }
                $tr->commit();
            }catch (Exception $e) {
                $tr->rollBack();
                return array('code'=>300, 'msg'=>'付款失败');
            }
        }
        return array('code'=>200, 'msg'=>'成功', 'datas'=>$datas);
    }
    
    /**
     * 取得menu
     * @return string[][]
     */
    private function _getMenus() {
       $menus = MenuHelper::getAssignedMenu(Yii::$app->user->id);

       $actionId = Yii::$app->controller->action->id;
       $actionId = str_replace('-details', '', $actionId);
       $actionId = str_replace('-print', '', $actionId);
       foreach ($menus as &$menu) {
           $itemAction = explode('/', $menu['url'][0]);
           if($itemAction[count($itemAction) - 1] == $actionId) {
               $menu['active'] = 'active';
               break;
           }
       }

       return ['menus' => $menus];
    }
	/**
	*  同城打印页面
	*  小雨
	*  2018-08-06
	*  打印列表
	**/
	public function actionIncomeDriverDetailsPrint(){
	    $model = new IncomeDriver();
        $orderList = $model->getLogisticsOrderList(Yii::$app->request->queryParams);
        return $this->render('income-driver-details-print', [
            'searchModel' => new LogisticsOrderSearch(),
            'orderList' => $model->formatDetailsData($orderList),
            'menus' => $this->_getMenus()
        ]);
	}
	
    /**
     * 落地点 退货 收款 打印页面
     * 小雨
     * 2018-08-06
     */
  /*  public function actionReturnIncomeTerminusDetailsPrint() {
        $model = new IncomeTerminus();
        $returnModel = new ReturnIncomeTerminus();
        $returnOrderList = $returnModel->getLogisticsOrderList(Yii::$app->request->queryParams);
        return $this->render('terminus-income-employee-details-print', [
            'searchModel' => new LogisticsReturnOrderSearch(),
            'orderList' => $model->formatDetailsData($returnOrderList),
            'menus' => $this->_getMenus(),
        ]);
    }*/
	/**
     * 落地点 发货 收款 打印页面
     * 小雨
     * 2018-08-06
     */
   /* public function actionIncomeTerminusDetailsPrint() {

        $model = new IncomeTerminus();
        $orderList = $model->getLogisticsOrderList(Yii::$app->request->queryParams);
      //  $model = new IncomeTerminus();
      //  $returnModel = new ReturnIncomeTerminus();
        
      // var_dump($model->formatDetailsData($orderList));
	///	die();
    //    $returnOrderList = $returnModel->getLogisticsOrderList(Yii::$app->request->queryParams);
        return $this->render('terminus-income-employee-details-print', [
            'searchModel' => new LogisticsReturnOrderSearch(),
            'orderList' => $model->formatDetailsData($orderList),
            'menus' => $this->_getMenus(),
        ]);
    }*/
	/**
     * 开单员 发货 收款 打印页面
     * 小雨
     * 2018-08-06
     */
    public function actionIncomeEmployeeDetailsPrint() {
        $model = new IncomeEmployee();
        $orderList = $model->getLogisticsOrderList(Yii::$app->request->queryParams);
        
        //var_dump($model->formatDetailsData($orderList));
		//die();
        return $this->render('return-income-employee-details-print', [
            'searchModel' => new LogisticsReturnOrderSearch(),
            'orderList' => $model->formatDetailsData($orderList),
            'menus' => $this->_getMenus(),
			'status' => 'send'
        ]);
    }
	/**
     * 开单员 退货 收款 打印页面
     * 小雨
     * 2018-08-06
     */
    public function actionReturnIncomeEmployeeDetailsPrint() {
        $model = new ReturnIncomeEmployee();
        $orderList = $model->getLogisticsOrderList(Yii::$app->request->queryParams);
        
       // var_dump($model->formatDetailsData($orderList));
		//die();
        return $this->render('return-income-employee-details-print', [
            'searchModel' => new LogisticsReturnOrderSearch(),
            'orderList' => $model->formatDetailsData($orderList),
            'menus' => $this->_getMenus(),
			'status' => 'return'
        ]);
    }
	/**
	*  打印确认
	*  小雨
	*  2018-08-06
	*  打印
	**/
	public function actionPrintConfirm(){
	    $list = new LogisticsOrder();
        $order_arr = explode(',',Yii::$app->request->post('order_sn'));
        $where = array('in','logistics_order.order_id',$order_arr);
		$orderList = $list->UserPrint($where);
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

    /**
     * 收款员收款log
     * @return mixed
     */
    public function actionIncomeLog() {
        $searchModel = new TellerLogSearch();
        
        $model = new AdminAuthAssignment();
        $userList = ArrayHelper::map($model->getUserByRoles(), 'id', 'user_truename');
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $userList);
        
        $allAmount = $searchModel->getIncomeAllAmount($dataProvider->query);
        $sameCityAmount = $searchModel->getSameCityIncomeAllAmount($dataProvider->query);
        $amountArr = ['allAmount'=>$allAmount,'sameCityAmount'=>$sameCityAmount,'wAmount'=>$allAmount-$sameCityAmount];

        $userList = ['' => '全部'] +  $userList;
        
        return $this->render('income-log', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'userList' => $userList,
            'amountArr' => $amountArr,
            'menus' => $this->_getMenus(),
        ]);
    }
    
    /**
     * 提现记录查询
     * @return string
     */
    public function actionApply() {
        $searchModel = new ApplyForWithdrawalSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('apply', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus(),
        ]);
    }
    
    /**
     * 确认提现付款
     * @return number[]|string[]|string[][]|array[][]|mixed[][]|number[]|string[]
     */
    public function actionApplyConfirm() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $model = new ApplyForWithdrawal();
        $id = Yii::$app->request->post('id');
        if($model->edit($id)) {
            return ['code'=>200, 'msg'=>'成功', 'datas' => ['id' => $id, 'statusName' => '已付款']];
        } else {
            return ['code'=>201, 'msg'=>'确认失败'];
        }
    }
    
    /**
     * Lists all OrderAdvance models.
     * @return mixed
     */
    public function actionAdvance()
    {
        $searchModel = new OrderAdvanceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('advance', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus(),
        ]);
    }
    
    /**
     * 确认提现付款
     * @return number[]|string[]|string[][]|array[][]|mixed[][]|number[]|string[]
     */
    public function actionAdvanceConfirm() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $model = new OrderAdvance();
        $id = Yii::$app->request->post('id');
        if($model->edit($id)) {
            return ['code'=>200, 'msg'=>'成功', 'datas' => ['id' => $id, 'stateName' => '已收款', 'incomeTime' => date('Y-m-d H:i')]];
        } else {
            return ['code'=>201, 'msg'=>'确认失败'];
        }
    }
    
    /**
     * 收款详细查询
     * @return string
     */
    public function  actionSearch() {
        $searchModel = new TellerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('search', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus(),
        ]);
    }
    
    /**
     * 订单备注保存
     * @return number[]|string[]
     */
    public function actionOrderRemark() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params['order_id'] = Yii::$app->request->post('orderId');
        $params['content'] = Yii::$app->request->post('content');
        
        $orderRemark = OrderRemark::findOne($params['order_id']);
        if(!$orderRemark) {
            $orderRemark = new OrderRemark();
        }
        $result = $orderRemark->addRemark($params);
        if($result) {
            return ['code' => 200, 'msg' => '保存成功'];
        } else {
            return ['code' => 300, 'msg' => '保存失败'];
        }
    }
    
    /**
     * 返货订单备注保存
     * @return number[]|string[]
     */
    public function actionReturnOrderRemark() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params['order_id'] = Yii::$app->request->post('orderId');
        $params['content'] = Yii::$app->request->post('content');
        
        $returnOrderRemark= ReturnOrderRemark::findOne($params['order_id']);
        if(!$returnOrderRemark) {
            $returnOrderRemark= new ReturnOrderRemark();
        }
        $result = $returnOrderRemark->addRemark($params);
        if($result) {
            return ['code' => 200, 'msg' => '保存成功'];
        } else {
            return ['code' => 300, 'msg' => '保存失败'];
        }
    }
    
    /**
     * Lists all LogisticsOrder models.
     * @return mixed
     */
    public function actionOrder()
    {
        $searchModel = new LogisticsOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if (Yii::$app->request->get('download_type', '0')) {
           return $this->_downloadExcel($dataProvider);
        }
        
        $driver = new Driver();
        $driverList = $driver->getDriverDropList();
        
        //统计代码
        $count['order_num'] = $searchModel->getEmployeeOrderNum($searchModel->search(Yii::$app->request->queryParams));
        $count['goods_num'] = $searchModel->getEmployeeGoodsNum($searchModel->search(Yii::$app->request->queryParams));
        $count['price'] = $searchModel->getEmployeePrice($searchModel->search(Yii::$app->request->queryParams));
        $count['price_count'] = $searchModel->getEmployeePriceCount($searchModel->search(Yii::$app->request->queryParams));
        $count['same_city_order'] = $searchModel->getEmployeeSameCityOrder($searchModel->search(Yii::$app->request->queryParams));
        $count['same_city_goods'] = $searchModel->getEmployeeSameCityGoods($searchModel->search(Yii::$app->request->queryParams));
        $count['same_city_price'] = $searchModel->getEmployeeSameCityPrice($searchModel->search(Yii::$app->request->queryParams));
        $count['same_city_price_count'] = $searchModel->getEmployeeSameCityPriceCount($searchModel->search(Yii::$app->request->queryParams));

        return $this->render('order', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus(),
            'count' => $count,
            'driverList' => $driverList
        ]);
        
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
                $temp['url'] = $logisticsOrderSearch->_getObjectUrlParameter('teller/order', ['page'=>$i+1]);
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
                        ->setCellValue('B1', '运费')
                        ->setCellValue('C1', '代收款')
                        ->setCellValue('D1', '开单时间')
                        ->setCellValue('E1', '发货人')
                        ->setCellValue('F1', '收货人')
                        ->setCellValue('G1', '收货人电话')
                        ->setCellValue('H1', '司机')
                        ->setCellValue('I1', '代收款状态')
                        ->setCellValue('J1', '收款时间');
            $i = 2;
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('G')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            foreach ($datas as $model) {
                // Add some data
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, $model->return_logistics_sn?$model->logistics_sn."(已原返)":$model->logistics_sn);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, $model->freight);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, $model->goods_price);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, date('Y-m-d H:i:s',$model->add_time));
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('E'.$i, ArrayHelper::getValue($model, 'userName.user_truename'));
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, $model->receiving_name);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('G'.$i, "".$model->receiving_phone);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$i, $model->getDriverName());
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$i, $model->getGoodsPriceStateName($model->goods_price_state));
                $time = ArrayHelper::getValue($model, 'orderTime.income_price_time');
                $time = $time ? date('Y-m-d H:i:s', $time) : '';
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$i, $time);
                $i++;
            }
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