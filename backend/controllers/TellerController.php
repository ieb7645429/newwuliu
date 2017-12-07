<?php

namespace backend\controllers;

use Yii;
use backend\models\PayDealer;
use backend\models\PayTerminus;
use backend\models\IncomeTerminus;
use backend\models\IncomeTerminusNot;
use backend\models\IncomeEmployee;
use backend\models\IncomeLogisticsSn;
use backend\models\ReturnIncomeTerminus;
use backend\models\ReturnIncomeTerminusNot;
use backend\models\ReturnIncomeDealer;
use backend\models\ReturnPayTerminus;
use backend\models\IncomeDriver;
use backend\models\ReturnIncomeEmployee;
use backend\models\TellerLogSearch;
use backend\models\AdminAuthAssignment;
use backend\models\TellerSearch;
use backend\models\OrderAdvanceSearch;
use backend\models\OrderAdvance;
use backend\models\TellerIncomeSnLog;
use backend\models\TellerIncomeSnLogSearch;
use backend\models\OrderTellerRemark;
use backend\models\ReturnOrderTellerRemark;
use backend\models\AdminUser;
use backend\models\TellerThirdAdvance;
use common\models\LogisticsOrderSearch;
use common\models\LogisticsOrder;
use common\models\LogisticsReturnOrderSearch;
use common\models\BankInfo;
use common\models\ApplyForWithdrawalSearch;
use common\models\ApplyForWithdrawal;
use common\models\StatisticalOrder;
use common\models\Driver;
use common\models\OrderThirdAdvance;
use yii\helpers\Url;
use yii\base\Exception;
use yii\web\Response;
use mdm\admin\components\MenuHelper;
use mdm\admin\components\Helper;
use yii\helpers\ArrayHelper;
use yii\data\Pagination;
use common\models\Area;
use yii\web\NotFoundHttpException;

class TellerController extends \yii\web\Controller
{
    public $layout = 'teller';

    public function actionIndex() {
        if(Yii::$app->user->can('/teller/apply')) {
            $this->redirect(['apply']);
        } else if(Helper::checkRoute('/teller/income-driver')) {
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
            $tr = Yii::$app->db->beginTransaction();
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
            $tr = Yii::$app->db->beginTransaction();
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
            $tr = Yii::$app->db->beginTransaction();
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
            $tr = Yii::$app->db->beginTransaction();
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
        $amounts = Yii::$app->request->post('amount');
        $relAmounts = Yii::$app->request->post('rel_amount');
        $advance = Yii::$app->request->post('advance', 0);
        $number = TellerIncomeSnLog::getNumber();
        if ($orderIds) {
            $tr = Yii::$app->db->beginTransaction();
            try {
                $rOrders = array();
                $all_amount = 0;
                for ($i=0; $i<count($orderIds);$i++) {
                    $order = IncomeLogisticsSn::findOne($orderIds[$i]);
                    // 封车 收款
                    if($order->order_state > Yii::$app->params['orderStateDriver']) {
                        if($advance == 0) {
                            $orderAdvance = new OrderAdvance();
                            $advanceLog = $orderAdvance->getByOrderId($orderIds[$i]);
                            if ($advanceLog && $advanceLog->state == 2) {
                                if(!$orderAdvance->edit($advanceLog->id)){
                                    throw new Exception();
                                }
                            }
                        }
                        if($order->same_city == 1) {
                            $model = new IncomeDriver();
                            if (!$model->getIsConfirm(ArrayHelper::toArray($order))) {
                                $return = $model->terminusConfirmCollection([$orderIds[$i]], $advance);
                                if ($return === false) {
                                    throw new Exception();
                                }
                            }
                        } else {
                            $model = new IncomeTerminusNot();
                            if (!$model->getIsConfirm(ArrayHelper::toArray($order))) {
                                $return = $model -> terminusConfirmCollection($orderIds[$i], $advance);
                                if($return == false) {
                                    throw new Exception();
                                }
                            }
                        }
                    }
                    $log = new TellerIncomeSnLog();
                    $params = array();
                    $params['number'] = $number;
                    $params['order_id'] = $orderIds[$i];
                    $params['order_sn'] = $order->logistics_sn;
                    $params['rel_amount'] = $relAmounts[$i];
                    $params['amount'] = $amounts[$i];
                    $params['receiving'] = Yii::$app->request->post('receiving');
                    if(!$log->addLog($params)) {
                        throw new Exception();
                    }
                    $temp = array();
                    $temp['order_sn'] = $order->logistics_sn;
                    $temp['rel_amount'] = $relAmounts[$i];
                    $rOrders[] = $temp;
                    $all_amount += $temp['rel_amount'];
                }
                $tr->commit();
                return array(
                    'code' => 200,
                    'msg' => '收款成功',
                    'datas' => array(
                        'number' => $number,
                        'date' => date('Y-m-d H:i:s'),
                        'receiving' => Yii::$app->request->post('receiving'),
                        'user' => AdminUser::findOne(Yii::$app->user->id)->user_truename,
                        'all_amount' => $all_amount,
                        'orders' => $rOrders
                    )
                );
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
        
        if (Yii::$app->request->get('download_type', '0')) {
            return $this->_downloadSnLogExcel($dataProvider);
        }
        
        return $this->render('income-logisticssn-log', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'receivingList' => $searchModel->getReceivingList(),
            'menus' => $this->_getMenus()
        ]);
    }
    
    private function _downloadSnLogExcel($dataProvider) {
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
        $datas = $dataProvider->query->all();
        if ($datas) {
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '编号')
            ->setCellValue('B1', '票号')
            ->setCellValue('C1', '金额')
            ->setCellValue('D1', '实收金额')
            ->setCellValue('E1', '交款人')
            ->setCellValue('F1', '收款人')
            ->setCellValue('G1', '收款时间')
            ->setCellValue('H1', '送货状态')
            ->setCellValue('I1', '收款状态')
            ->setCellValue('J1', '备注');
            $i = 2;
            foreach ($datas as $model) {
                // Add some data
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, $model->number);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, $model->order_sn);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('C'.$i, ''.$model->amount);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, $model->rel_amount);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, $model->receiving);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, ArrayHelper::getValue($model, 'userTrueName.user_truename'));
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$i, date("Y-m-d H:i", $model->add_time));
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$i, $model->getOrderState());
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$i, $model->getGoodsPriceState());
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$i, $model->getRemarkExcel());
                $i++;
            }
        }
        
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('票号收款列表');
        
        
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        
        
        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="票号收款列表.xls"');
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
    
    /**
     * 票号收款打印
     * @return string
     */
    public function actionIncomeLogisticssnLogPrint()
    {
        Yii::$app->response->format=Response::FORMAT_JSON;
        $logs = TellerIncomeSnLog::findAll(['number' => Yii::$app->request->post('id', 0)]);
        if($logs) {
            $return = array();
            $all_amount = 0;
            foreach ($logs as $log) {
                $data = array();
                $data['order_sn'] = $log['order_sn'];
                $data['rel_amount'] = $log['rel_amount'];
                $return[] = $data;
                $all_amount += $data['rel_amount'];
            }
            return array(
                'code' => 200,
                'msg' => '成功！',
                'datas' => array(
                    'number' => Yii::$app->request->post('id', 0),
                    'date' => date('Y-m-d H:i:s', $logs[0]['add_time']),
                    'receiving' => $logs[0]['receiving'],
                    'user' => AdminUser::findOne($logs[0]['user_id'])->user_truename,
                    'all_amount' => $all_amount,
                    'orders' => $return
                )
            );
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
            $tr = Yii::$app->db->beginTransaction();
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
            $tr = Yii::$app->db->beginTransaction();
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
            $tr = Yii::$app->db->beginTransaction();
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
            $tr = Yii::$app->db->beginTransaction();
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
            $tr = Yii::$app->db->beginTransaction();
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
            $tr = Yii::$app->db->beginTransaction();
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
            $tr = Yii::$app->db->beginTransaction();
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
        
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);
        
        if (Yii::$app->request->get('download_type', '0')) {
            return $this->_downloadApplyExcel($dataProvider);
        }
        
        return $this->render('apply', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus(),
        ]);
    }
    
    /**
     * 提现记录查询
     * @return string
     */
    public function actionApply1() {
        $searchModel = new ApplyForWithdrawalSearch();
        
        $params = Yii::$app->request->queryParams;
        $params['ApplyForWithdrawalSearch']['type'] = 1;
        
        $dataProvider = $searchModel->search($params);
        
        if (Yii::$app->request->get('download_type', '0')) {
            return $this->_downloadApplyExcel($dataProvider);
        }
        
        return $this->render('apply', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus(),
        ]);
    }
    
    /**
     * 提现记录查询
     * @return string
     */
    public function actionApply2() {
        $searchModel = new ApplyForWithdrawalSearch();
        
        $params = Yii::$app->request->queryParams;
        $params['ApplyForWithdrawalSearch']['type'] = 2;
        
        $dataProvider = $searchModel->search($params);
        
        if (Yii::$app->request->get('download_type', '0')) {
            return $this->_downloadApplyExcel($dataProvider);
        }
        
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
            return ['code'=>200, 'msg'=>'成功', 'datas' => ['id' => $id, 'statusName' => '已付款', 'pay_time' => date('Y-m-d H:i'), 'pay_user'=>AdminUser::findOne(Yii::$app->user->id)->username]];
        } else {
            return ['code'=>201, 'msg'=>'确认失败'];
        }
    }
    
    private function _downloadApplyExcel($dataProvider) {
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
        $datas = $dataProvider->query->all();
        if ($datas) {
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '申请人')
            ->setCellValue('B1', '开户行')
            ->setCellValue('C1', '状态')
            ->setCellValue('D1', '申请时间')
            ->setCellValue('E1', '会员号')
            ->setCellValue('F1', '开户名')
            ->setCellValue('G1', '银行卡号')
            ->setCellValue('H1', '银行')
            ->setCellValue('I1', '金额')
            ->setCellValue('J1', '付款时间')
            ->setCellValue('K1', '付款人');
            $i = 2;
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('E')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('G')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('I')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            foreach ($datas as $model) {
                // Add some data
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, ArrayHelper::getValue($model, 'userTrueName.user_truename'));
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, ArrayHelper::getValue($model, 'bankInfo.bank_info_bank_address'));
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, $model->getStatusName());
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, date('Y-m-d H:i', $model->add_time));
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('E'.$i, ''.ArrayHelper::getValue($model, 'userTrueName.username'));
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, ArrayHelper::getValue($model, 'bankInfo.bank_info_account_name'));
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('G'.$i, ''.ArrayHelper::getValue($model, 'bankInfo.bank_info_card_no'));
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$i, ArrayHelper::getValue($model, 'bankInfo.bank_info_bank_name'));
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('I'.$i, ''.$model->amount);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$i, $model->pay_time?date('Y-m-d H:i', $model->pay_time):'');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$i, $model->pay_user_id?ArrayHelper::getValue($model, 'adminUserName.username'):'');
                $i++;
            }
        }
        
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('提现记录');
        
        
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        
        
        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="提现记录.xls"');
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
     * 收款详细查询
     * @return string
     */
    public function  actionThirdAdvance() {
        $searchModel = new TellerThirdAdvance();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('third-advance', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus(),
        ]);
    }
    
    public function actionThirdAdvanceConfirm() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $order_id = Yii::$app->request->post('order_id');
        $orderAdvance = new OrderThirdAdvance();
        $temp = $orderAdvance::findOne(['order_id' => $order_id]);
        if($temp) {
            return ['code' => 300, 'msg' => '已经垫付！'];
        }
        
        $order = LogisticsOrder::findOne($order_id);
        if($order->same_city == 1) {
            $model = new IncomeDriver();
        } else if ($order->same_city == 2) {
            $model = new IncomeTerminus();
        }

        $advanceData = array();
        $advanceData['order_id'] = $order_id;
        $advanceData['member_id'] = $order->member_id;
        $advanceData['amount'] = ArrayHelper::getValue($model->_getAmount(ArrayHelper::toArray($order)), 'all_amount', 0);
        $advanceData['logistics_sn'] = $order->logistics_sn;
        if($orderAdvance->addAdvance($advanceData)) {
            $searchModel = new TellerThirdAdvance();
            $searchModel->same_city = $order->same_city;
            return [
                'code' => 200,
                'msg' => '保存成功',
                'datas' => [array(
                    'order_id' => $order_id,
                    'order_state' => $order->order_state,
                    'url' => $searchModel->getGoodsFreightUrl(),
                    'advance_state' => '已垫付',
                    'advance_time' => date('Y-m-d H:i'),
                    'advance_user' => Yii::$app->user->identity->user_truename,
                )]
            ];
        }
        return ['code' => 300, 'msg' => '保存失败'];
    }
    
    public function actionOrderRemarkInit() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $remarks = OrderTellerRemark::findAll(['order_id' => Yii::$app->request->post('id')]);
        if ($remarks) {
            $return = array();
            foreach ($remarks as $remark) {
                $temp = ArrayHelper::toArray($remark);
                if ($temp['user_id'] == Yii::$app->user->id) {
                    $temp['edit'] = true;
                } else {
                    $temp['edit'] = false;
                }
                $temp['user_name'] = AdminUser::findOne($remark->user_id)->user_truename;
                $return[] = $temp;
            }
            
            return ['code' => 200, 'msg' => '成功', 'datas' => ['remarks' => $return, 'user_name'=> AdminUser::findOne(Yii::$app->user->id)->user_truename]];
        } else {
            return ['code' => 200, 'msg' => '成功', 'datas' => ['remarks' => '', 'user_name'=> AdminUser::findOne(Yii::$app->user->id)->user_truename]];;
        }
    }
    
    public function actionOrderRemarkSave() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params['order_id'] = Yii::$app->request->post('id');
        $params['content'] = Yii::$app->request->post('content');
        
        $remark = OrderTellerRemark::findOne(['order_id' => $params['order_id'], 'user_id' => Yii::$app->user->id]);
        if(!$remark) {
            $remark = new OrderTellerRemark();
        }
        $result = $remark->addRemark($params);
        if($result) {
            return ['code' => 200, 'msg' => '保存成功'];
        } else {
            return ['code' => 300, 'msg' => '保存失败'];
        }
    }
    
    public function actionReturnOrderRemarkInit() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $remarks = ReturnOrderTellerRemark::findAll(['order_id' => Yii::$app->request->post('id')]);
        if ($remarks) {
            $return = array();
            foreach ($remarks as $remark) {
                $temp = ArrayHelper::toArray($remark);
                if ($temp['user_id'] == Yii::$app->user->id) {
                    $temp['edit'] = true;
                } else {
                    $temp['edit'] = false;
                }
                $temp['user_name'] = AdminUser::findOne($remark->user_id)->user_truename;
                $return[] = $temp;
            }
            
            return ['code' => 200, 'msg' => '成功', 'datas' => ['remarks' => $return, 'user_name'=> AdminUser::findOne(Yii::$app->user->id)->user_truename]];
        } else {
            return ['code' => 200, 'msg' => '成功', 'datas' => ['remarks' => '', 'user_name'=> AdminUser::findOne(Yii::$app->user->id)->user_truename]];;
        }
    }
    
    public function actionReturnOrderRemarkSave() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params['order_id'] = Yii::$app->request->post('id');
        $params['content'] = Yii::$app->request->post('content');
        
        $remark = ReturnOrderTellerRemark::findOne(['order_id' => $params['order_id'], 'user_id' => Yii::$app->user->id]);
        if(!$remark) {
            $remark = new ReturnOrderTellerRemark();
        }
        $result = $remark->addRemark($params);
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


    /*0.0
     * 后台添加功能 查询全国发货单情况
     */

    /*0.0
     *得到 沈阳市 发货单情况
   */
    public function actionNationwide()
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


//        $City = Yii::$app->request->get('a')?Yii::$app->request->get('a'):"无用";
//        var_dump($City);die();

//        $searchModel->memberCityName="";
        $type = '';
        return $this->render('nationwide', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus(),
            "a"=> "沈阳市",
            'indexOver'=>$type,
            'count' => $count,
        ]);
    }

    /*
    *0.0
     * Displays a single LogisticsOrder model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
//        var_dump($id);exit();
        return $this->render('view', [
            'model' => $this->findModel($id),
            'menus' => $this->_getMenus(),
        ]);
    }

    /*
    *0.0
     * Finds the LogisticsOrderEdit model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LogisticsOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */

    protected function findModel($id)
    {
        $res = Yii::$app->request->queryParams;
//        var_dump($res);exit();
//        沈阳市 view
        if ($res["area_id"] == 107)
        {
            if (($model = LogisticsOrder::findOne($id)) !== null) {
                return $model;
            } else {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }
//        黑龙江 view
        elseif ($res["area_id"] == 130)
        {
            if (($model = \frontend\modules\hlj\models\LogisticsOrder::findOne($id)) !== null) {
                return $model;
            } else {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }
//        大连市 view
        elseif ($res["area_id"] == 108)
        {
            if (($model = \frontend\modules\dl\models\LogisticsOrder::findOne($id)) !== null) {
                return $model;
            } else {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }

    }

    /*0.0
    *得到 沈阳市 发货单情况
    */
    /*public function actionShenyang()
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


//        $searchModel->memberCityName="沈阳市";
        $type = '';
        return $this->render('nationwide', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus(),
            "a"=>"沈阳市",
            'indexOver'=>$type,
            'count' => $count,
        ]);
    }*/

    /*0.0
     * 得到 哈尔滨市 发货单情况
     */
    public function actionHeilongjiang()
    {
        $searchModel = new \frontend\modules\hlj\models\LogisticsOrderSearch();
        $statisticalOrder = new \frontend\modules\hlj\models\StatisticalOrder();
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


//        $searchModel->memberCityName="哈尔滨市";
        $type = '';
        return $this->render('nationwide', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus(),
            "a"=>"哈尔滨市",
            'indexOver'=>$type,
            'count' => $count,
        ]);
    }

    /*0.0
     * 得到 大连市 发货单情况
     */
    public function actionDalian()
    {
        $searchModel = new \frontend\modules\dl\models\LogisticsOrderSearch();
        $statisticalOrder = new \frontend\modules\dl\models\StatisticalOrder();
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


//        $searchModel->memberCityName="大连市";
        $type = '';
        return $this->render('nationwide', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus(),
            "a"=>"大连市",
            'indexOver'=>$type,
            'count' => $count,
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